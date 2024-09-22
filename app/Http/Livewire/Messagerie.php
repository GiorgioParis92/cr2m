<?php 
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Message;
use App\Models\Dossier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\WithFileUploads;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Messagerie extends Component
{

    use WithFileUploads;
    public $messageContent;
    public $dossier_set;
    public $lastMessages;
    public $grouped_messages;
    public $dossier_messages;
    public $not_seen=[];
    public $searchTerm = ''; // New property for search term
    private $lastChecked;
    public $count_messages_dossier;
    public $file;


    public function mount()
    {
        $this->lastChecked = Carbon::now();
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $client_id = Auth::user()->client_id;

        // Fetch messages conditionally based on `client_id`
        $messages = Message::with(['user', 'dossier.beneficiaire'])
            ->when($client_id != 0, function ($query) use ($client_id) {
                return $query->whereHas('dossier', function ($query) use ($client_id) {
                    $query->where('client_id', $client_id)
                        ->orWhere('installateur', $client_id)
                        ->orWhere('mar', $client_id)
                        ->orWhere('mandataire_financier', $client_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($messages->isNotEmpty()) {

            foreach($messages as $message) {
                $this->not_seen[$message->dossier_id]=0;
                }
                foreach($messages as $message) {
                    $seen=DB::table('messages_suivi')
                    ->where('message_id',$message->id)
                    ->where('user_id',auth()->user()->id)
                    ->first();

                    if($seen && $seen->seen==0) {
                        $this->not_seen[$message->dossier_id]=$this->not_seen[$message->dossier_id]+1;
                    }
                }


            // Group messages by `dossier_id`
            $grouped = $messages->groupBy('dossier_id');

            // Sort `grouped_messages` by the latest message's `created_at`
            $this->grouped_messages = $grouped->map(function ($group) {
                return $group->sortByDesc('created_at');
            })->sortByDesc(function ($group) {
                return $group->first()->created_at;
            })->toArray();

            // Fetch the latest message for each dossier and order by `created_at` descending
            $this->lastMessages = Message::with(['user', 'dossier.beneficiaire'])
                ->select('messages.*')
                ->join(DB::raw('(SELECT MAX(id) as last_id FROM messages GROUP BY dossier_id) as latest'), 'messages.id', '=', 'latest.last_id')
                ->when($client_id != 0, function ($query) use ($client_id) {
                    return $query->whereHas('dossier', function ($query) use ($client_id) {
                        $query->where('client_id', $client_id)
                            ->orWhere('installateur', $client_id)
                            ->orWhere('mar', $client_id)
                            ->orWhere('mandataire_financier', $client_id);
                    });
                })
                ->orderBy('messages.created_at', 'desc') // Order by `created_at` descending
                ->get();
               

                // Get messages for the selected dossier and sort by `created_at` ascending
            if ($this->dossier_set) {
                $dossier = Dossier::find($this->dossier_set);

                if ($dossier && $this->isUserAuthorized($dossier, $client_id)) {
                    $this->dossier_messages = $messages->where('dossier_id', $this->dossier_set)
                                                       ->sortBy('created_at')
                                                       ->values()
                                                       ->toArray();

                    // Emit event if new messages are detected in the selected dossier
                    if (count($this->dossier_messages) > $this->count_messages_dossier) {
                        $this->emit('messageSent');
                    }
                    $this->count_messages_dossier = count($this->dossier_messages);
                } else {
                    $this->dossier_messages = [];
                }
            } else {
                $this->dossier_messages = [];
            }
        } else {
            $this->grouped_messages = [];
            $this->lastMessages = collect();
            $this->dossier_messages = [];
        }
    }
    public function filterMessages()
    {
        $searchTerm = strtolower($this->searchTerm);
    
        $this->lastMessages = $this->lastMessages->filter(function ($message) use ($searchTerm) {
            $dossier = $message->dossier;
            $beneficiaire = $dossier ? $dossier->beneficiaire : null;
    
            return str_contains(strtolower($message->user->name), $searchTerm) ||
                   str_contains(strtolower($message->content), $searchTerm) ||
                   ($beneficiaire && str_contains(strtolower($beneficiaire->nom), $searchTerm));
        });
    }
    public function sendMessage($dossier_id)
    {
        $user_id = Auth::user()->id;
        $client_id = Auth::user()->client_id;

        $dossier = Dossier::find($dossier_id);
        if ($dossier && $this->isUserAuthorized($dossier, $client_id)) {
            $filePath = null;

        if ($this->file) {
            // Get original filename
            $originalFilename = $this->file->getClientOriginalName();

            // Sanitize filename
            $safeFilename = pathinfo($originalFilename, PATHINFO_FILENAME);
            $extension = $this->file->getClientOriginalExtension();

            // Option 1: Use Str::slug to replace spaces and special characters
            $safeFilename = Str::slug($safeFilename);

            // Option 2: Remove unwanted characters but keep spaces and dots
            // $safeFilename = preg_replace('/[^\w\s.-]+/', '', $safeFilename);
            // $safeFilename = trim($safeFilename);

            // Limit filename length
            $safeFilename = Str::limit($safeFilename, 50, '');

            // Reconstruct safe filename
            $filename = $safeFilename . '.' . $extension;

            // Check for filename conflicts
            $i = 1;
            $filePath = 'chat_files/' . $filename;
            while (Storage::disk('public')->exists($filePath)) {
                $filename = $safeFilename . '_' . $i . '.' . $extension;
                $filePath = 'chat_files/' . $filename;
                $i++;
            }

            // Store the file
            $this->file->storeAs('chat_files', $filename, 'public');
        }

       $message= Message::create([
            'user_id' => auth()->user()->id,
            'dossier_id' => $this->dossier_id,
            'form_id' => 0,
            'content' => $this->messageContent,
            'file_path' => $filePath,
        ]);

            $this->messageContent = '';

            // Reload messages
            $this->refresh();

            // Emit event to scroll down
            $this->emit('messageSent');
        } else {
            // Handle unauthorized access
            $this->emit('unauthorized');
        }
    }

    public function set_dossier($dossier_id)
    {
        $client_id = Auth::user()->client_id;
        $dossier = Dossier::find($dossier_id);
        $this->dossier=$dossier;

        $messages=DB::table('messages')->where('dossier_id',$this->dossier->id)
        ->get();

        foreach($messages as $message) {
            DB::table('messages_suivi')->where('user_id',auth()->user()->id)
            ->where( 'message_id',$message->id)
            ->delete();
        }

    

        if ($dossier && $this->isUserAuthorized($dossier, $client_id)) {
            $this->dossier_set = $dossier_id;
            $this->refresh();
            $this->emit('messageSent');
        } else {
            // Handle unauthorized access
            $this->dossier_set = null;
            $this->dossier_messages = [];
            $this->emit('unauthorized');
        }
    }

    public function refresh()
    {
        $this->loadMessages();
    }

    public function message_received()
    {
        $client_id = Auth::user()->client_id;

        $newMessages = Message::with(['user', 'dossier.beneficiaire'])
            ->when($client_id != 0, function ($query) use ($client_id) {
                return $query->whereHas('dossier', function ($query) use ($client_id) {
                    $query->where('client_id', $client_id)
                        ->orWhere('installateur', $client_id)
                        ->orWhere('mar', $client_id)
                        ->orWhere('mandataire_financier', $client_id);
                });
            })
            ->where('created_at', '>', $this->lastChecked)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($newMessages->isNotEmpty()) {
            // Update the lastChecked time
            $this->lastChecked = Carbon::now();
            $this->emit('messageSent');

            // Update lastMessages and sort by `created_at` descending
            $this->lastMessages = Message::with(['user', 'dossier.beneficiaire'])
                ->select('messages.*')
                ->join(DB::raw('(SELECT MAX(id) as last_id FROM messages GROUP BY dossier_id) as latest'), 'messages.id', '=', 'latest.last_id')
                ->when($client_id != 0, function ($query) use ($client_id) {
                    return $query->whereHas('dossier', function ($query) use ($client_id) {
                        $query->where('client_id', $client_id)
                            ->orWhere('installateur', $client_id)
                            ->orWhere('mar', $client_id)
                            ->orWhere('mandataire_financier', $client_id);
                    });
                })
                ->orderBy('messages.created_at', 'desc') // Order by `created_at` descending
                ->get();

            // Update messages for the selected dossier and sort by `created_at` ascending
            if ($this->dossier_set) {
                $dossier = Dossier::find($this->dossier_set);

                if ($dossier && $this->isUserAuthorized($dossier, $client_id)) {
                    $this->dossier_messages = $newMessages->where('dossier_id', $this->dossier_set)
                                                          ->sortBy('created_at')
                                                          ->values()
                                                          ->toArray();
                } else {
                    $this->dossier_messages = [];
                }
            }
        }
    }

    private function isUserAuthorized($dossier, $client_id)
    {
        // Check if the user is authorized based on `client_id` or if `client_id` is `0`
        return $client_id === 0 ||
               $dossier->client_id === $client_id ||
               $dossier->installateur === $client_id ||
               $dossier->mar === $client_id ||
               $dossier->mandataire_financier === $client_id;
    }

    public function render()
    {
        return view('livewire.messagerie');
    }
}
