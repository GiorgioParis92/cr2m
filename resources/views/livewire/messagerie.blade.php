<main class="content" wire:poll="refresh">
    <div class="container p-0">

        <h1 class="h3 mb-3">Discussions</h1>

        <div class="card">
            <div class="row g-0" style="min-height: 80vh">
                <div class="col-12 col-lg-5 col-xl-3 border-right">

                    <div class="px-4 d-none d-md-block">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <input type="text" class="form-control my-3" placeholder="Recherche..."
                                    wire:model.debounce.500ms="searchTerm" wire:keydown="filterMessages">
                            </div>
                        </div>
                    </div>
                    @php $count=0 @endphp
                    @if ($lastMessages)
                        @foreach ($lastMessages as $last)
                            @if ($last->dossier)
                                @php $count++ @endphp
                                <a wire:click="set_dossier({{ $last->dossier->id }})" style="cursor:pointer"
                                    class="list-group-item list-group-item-action border-0">
                                    {{-- <div class="badge bg-success float-right">5</div> --}}
                                    <div class="d-flex align-items-start">
                                        <img src="https://bootdey.com/img/Content/avatar/avatar5.png"
                                            class="rounded-circle mr-1" alt="Vanessa Tucker" width="40"
                                            height="40" style="    margin-right: 12px;">
                                        <div class="flex-grow-1 ml-3">
                                            @if($not_seen[$last->dossier->id])
                                            <span class="badge badge-danger bg-danger">{{$not_seen[$last->dossier->id]}}</span>
                                            
                                            @endif
                                            Dossier: <b>{{ $last->dossier->beneficiaire->nom }}
                                                {{ $last->dossier->beneficiaire->prenom }}</b>
                                            <div class="small">
                                                {{-- <span class="fas fa-circle chat-online"></span>  --}}
                                                <span class="text-muted">Le
                                                    {{ date('d/m/Y à H:i', strtotime($last->created_at)) }}</span><br />
                                                @if($last->content)
                                                {{ $last->user->name }} a dit :<br />
                                                {{ $last->content }}
                                                @elseif($last->file_path)
                                                {{ $last->user->name }} a envoyé un fichier
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @else
                            @endif
                        @endforeach
                    @else
                        <p>Aucun message.</p>
                    @endif
                    @if ($count == 0)
                        <p>Aucune discussion active.</p>
                        <p>Pour démarrer une discussion veuillez vous rendre sur le dossier concerné</p>
                    @endif


                    <hr class="d-block d-lg-none mt-1 mb-0">
                </div>
                @if ($dossier_set)

                    <div class="col-12 col-lg-7 col-xl-9">
                        <div class="py-2 px-4 border-bottom d-none d-lg-block">
                            <div class="d-flex align-items-center py-1" style="max-height:50px">
                                <div class="position-relative">
                                    <a class="btn btn-primary" href="{{ route('dossiers.show', $dossier->folder) }}">
                                      
                                        Dossier :
                                        {{ $dossier->beneficiaire->nom }}
                                        {{ $dossier->beneficiaire->prenom }} <i class="fa fa-eye"></i></a>
                                </div>
                                {{-- <div class="flex-grow-1 pl-3">
                                <strong>Sharon Lessman</strong>
                                <div class="text-muted small"><em>Typing...</em></div>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-lg mr-1 px-3"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-phone feather-lg">
                                        <path
                                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                        </path>
                                    </svg></button>
                                <button class="btn btn-info btn-lg mr-1 px-3 d-none d-md-inline-block"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-video feather-lg">
                                        <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2">
                                        </rect>
                                    </svg></button>
                                <button class="btn btn-light border btn-lg px-3"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-more-horizontal feather-lg">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="19" cy="12" r="1"></circle>
                                        <circle cx="5" cy="12" r="1"></circle>
                                    </svg></button>
                            </div> --}}
                            </div>
                        </div>

                        <div class="position-relative">
                            <div class="chat-messages p-4">



                                @foreach ($dossier_messages as $message)
                                @if (auth()->user()->id == $message['user_id'])
                                    <div class="chat-message-right pb-4">
                                        <!-- Existing code -->
                                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                            <div class="font-weight-bold mb-1">Moi</div>
                                            {{ $message['content'] }}
                                            @if ($message['file_path'])
                                                <div>
                                                    @php
                                                        $extension = strtolower(pathinfo($message['file_path'], PATHINFO_EXTENSION));
                                                        $filename = basename($message['file_path']);
                                                    @endphp
                
                                                    @if ($extension === 'pdf')
                                                        <div class="btn btn-success btn-view pdfModal" data-toggle="modal"
                                                            data-img-src="{{ Storage::url($message['file_path']) }}"
                                                            data-name="{{ $filename }}">
                                                            <i class="fa fa-file-pdf fa-2x"></i> {{ $filename }}
                                                        </div>
                                                    @elseif (in_array(strtolower($extension), ['webp', 'jpg', 'jpeg', 'png', 'gif']))
                                                        <a href="{{ Storage::url($message['file_path']) }}" target="_blank">
                                                            <img class="chat_img" src="{{ Storage::url($message['file_path']) }}"
                                                                alt="Image" />
                                                            {{ $filename }}
                                                        </a>
                                                    @elseif (in_array(strtolower($extension), ['xls', 'xlsx', 'csv']))
                                                        <a href="{{ Storage::url($message['file_path']) }}" target="_blank">
                                                            <i class="fa fa-file-excel fa-2x"></i>
                                                            {{ $filename }}
                                                        </a>
                                                    @else
                                                        <a class=" bg-success text-success"
                                                            href="{{ Storage::url($message['file_path']) }}" target="_blank">
                                                            <i class="fa fa-file fa-2x "></i>
                                                            {{ $filename }}
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                
                                        </div>
                                    </div>
                                @else
                                    <div class="chat-message-left pb-4">
                                        <!-- Existing code -->
                                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3">
                                            <div class="font-weight-bold mb-1">
                                                {{ $message['user']['name'] }}
                                            </div>
                                            {{ $message['content'] }}
                                            @if ($message['file_path'])
                                                <div>
                                                    @php
                                                        $extension = strtolower(pathinfo($message['file_path'], PATHINFO_EXTENSION));
                                                        $filename = basename($message['file_path']);
                                                    @endphp
                
                                                    @if ($extension === 'pdf')
                                                        <div class="btn btn-success btn-view pdfModal" data-toggle="modal"
                                                            data-img-src="{{ Storage::url($message['file_path']) }}"
                                                            data-name="{{ $filename }}">
                                                            <i class="fa fa-file-pdf fa-2x"></i> {{ $filename }}
                                                        </div>
                                                    @elseif (in_array(strtolower($extension), ['webp', 'jpg', 'jpeg', 'png', 'gif']))
                                                        <a href="{{ Storage::url($message['file_path']) }}" target="_blank">
                                                            <img class="chat_img" src="{{ Storage::url($message['file_path']) }}"
                                                                alt="Image" />
                                                            {{ $filename }}
                                                        </a>
                                                    @elseif (in_array(strtolower($extension), ['xls', 'xlsx', 'csv']))
                                                        <a href="{{ Storage::url($message['file_path']) }}" target="_blank">
                                                            <i class="fa fa-file-excel fa-2x"></i>
                                                            {{ $filename }}
                                                        </a>
                                                    @else
                                                        <a class=" bg-success text-success"
                                                            href="{{ Storage::url($message['file_path']) }}" target="_blank">
                                                            <i class="fa fa-file fa-2x "></i>
                                                            {{ $filename }}
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @endforeach

                @endif


            </div>
        </div>
        @if ($dossier_set)
            <div class="flex-grow-0 py-3 px-4 border-top">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Type your message"
                        wire:model="messageContent" wire:keydown.enter="sendMessage({{ $dossier_set }})">
                        <input type="file" id="file_chat" wire:model="file">

                    <button class="btn btn-primary" wire:click="sendMessage({{ $dossier_set }})"><i
                            class="fa fa-paper-plane"></i></button>
                </div>
            </div>
        @endif
    </div>
    </div>
    </div>
    </div>
</main>
<style>
    .flex-shrink-1 {
        flex-shrink: 1 !important;
        color: black;
    }

    .flex-grow-1 {
        flex-grow: 1 !important;
        color: black;
    }

    .chat-online {
        color: #34ce57
    }

    .chat-offline {
        color: #e4606d
    }

    .chat-messages {
        display: flex;
        flex-direction: column;
        max-height: 800px;
        overflow-y: scroll;
        min-height: 60vh;
    }

    .chat-message-left,
    .chat-message-right {
        display: flex;
        flex-shrink: 0
    }

    .chat-message-left {
        margin-right: auto
    }

    .chat-message-right {
        flex-direction: row-reverse;
        margin-left: auto
    }

    .py-3 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }

    .px-4 {
        padding-right: 1.5rem !important;
        padding-left: 1.5rem !important;
    }

    .flex-grow-0 {
        flex-grow: 0 !important;
    }

    .border-top {
        border-top: 1px solid #dee2e6 !important;
    }

    @media all and (min-width: 992px) {
        main {
            overflow: hidden
        }
    }

    .text-muted {
        color: #67748e !important;
        font-size: 10px;
        margin-right: 10px;
        margin-left: 10px;
        font-style: italic;
    }

    a.list-group-item.list-group-item-action.border-0 {
        margin-bottom: 18px;
        border-bottom: 1px solid #ccc !important;
        padding-bottom: 13px;
    }
    img.chat_img {
    max-width: 30%;
    display: block;
    text-align: right;
    float: right;
}
</style>

<script>
  $('.pdfModal').click(function() {
alert('ok');
$('#pdfFrame').attr('src', '');

var imgSrc = $(this).data('img-src');
imgSrc += `?time=${new Date().getTime()}`;
$('#pdfFrame').attr('src', imgSrc);
$('#pdfModal').css('display', 'block');
});

    document.addEventListener('livewire:load', function() {

     
Livewire.on('loadmessages', function() {
    $('.pdfModal').click(function() {

$('#pdfFrame').attr('src', '');

var imgSrc = $(this).data('img-src');
imgSrc += `?time=${new Date().getTime()}`;
$('#pdfFrame').attr('src', imgSrc);
$('#pdfModal').css('display', 'block');
});
});
        Livewire.on('messageSent', function() {

            let chatMessages = document.querySelector('.chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });

        Livewire.on('messageReceived', function() {

            let chatMessages = document.querySelector('.chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });

    });
</script>
