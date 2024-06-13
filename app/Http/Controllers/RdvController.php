<?php 
// app/Http/Controllers/CalendarController.php
namespace App\Http\Controllers;

use Livewire\Component;
use App\Models\Dossier;
use App\Models\Etape;
use App\Models\User;
use App\FormModel\FormConfigHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class RdvController extends Controller
{
    public function index()
    {


        $auditeurs=User::where('type_id',4);
        
        if(auth()->user()->client_id>0) {
            $auditeurs=$auditeurs->where('client_id',auth()->user()->client_id);

        }

        $auditeurs=$auditeurs->get();

        return view('rdv.index',compact('auditeurs'));
    }
}
