<?php

// app/Http/Controllers/TacheController.php
namespace App\Http\Controllers;

use App\Models\Tache;
use App\Models\User;
use Illuminate\Http\Request;

class TacheController extends Controller
{
    public function index(Request $request)
    {
        $agents = User::all();
        $query = Tache::query()->with('agent');
    
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }
    
        if ($request->filled('type_demande')) {
            $search = $request->type_demande;
        
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('numero_dossier', 'like', "%{$search}%")
                  ->orWhere('objet', 'like', "%{$search}%")
                  ->orWhere('type_demande', 'like', "%{$search}%")
                  ->orWhere('commentaires', 'like', "%{$search}%");
            });
        }
        if ($request->filled('valide')) {
            if ($request->valide === '1') {
                $query->where('valide', true);
            } elseif ($request->valide === '0') {
                $query->where('valide', false);
            }
        }
                
    
        $taches = $query->latest()->get();
    
        return view('taches.index', compact('taches', 'agents'));
    }
    

    public function create()
    {
        $agents = User::all();
        return view('taches.create', compact('agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'numero_dossier' => 'required|string',
            'objet' => 'required|string',
            'date_reception' => 'required|date',
            'type_demande' => 'required|string',
            'agent_id' => 'required|exists:users,id',
            'commentaires' => 'nullable|string',
            'valide' => 'nullable|boolean',
        ]);

        Tache::create($request->all());

        return redirect()->route('taches.index')->with('success', 'Tâche créée avec succès.');
    }


// app/Http/Controllers/TacheController.php
public function edit(Tache $tache)
{
    $agents = User::all();
    return view('taches.edit', compact('tache', 'agents'));
}


public function update(Request $request, Tache $tache)
{
    $request->validate([
        'nom' => 'required|string',
        'prenom' => 'required|string',
        'numero_dossier' => 'required|string',
        'objet' => 'required|string',
        'date_reception' => 'required|date',
        'type_demande' => 'required|string',
        'agent_id' => 'required|exists:users,id',
        'commentaires' => 'nullable|string',
        'valide' => 'nullable|boolean',
    ]);

    $tache->update($request->all());

    return redirect()->route('taches.index')->with('success', 'Tâche mise à jour.');
}

public function destroy(Tache $tache)
{
    $tache->delete();

    return redirect()->route('taches.index')->with('success', 'Tâche supprimée.');
}


}
