<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\User;

class AuditeursComposer
{
    public function compose(View $view)
    {
        $auditeurs = User::where('id','>',0);
        if(auth()->user()->client_id>0){
            $auditeurs = $auditeurs->where('client_id',auth()->user()->client_id);
        }
        $auditeurs = $auditeurs->get(); // Fetch all auditors
        
        $view->with('auditeurs', $auditeurs);
    }
}
 