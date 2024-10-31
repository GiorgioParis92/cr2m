<?php 
return [
    'Dossier' => [
        'model' => \App\Models\Dossier::class,
        'fields' => ['id', 'etape_number', 'beneficiaire_id', 'etape_id', 'status_id', 'folder', 'installateur'],
        'relations' => [
            'beneficiaire' => ['id', 'nom', 'prenom', 'numero_voie', 'adresse', 'cp', 'ville'],
            'etape' => ['id', 'etape_icon', 'etape_desc'],
            'status' => ['id', 'status_style', 'status_desc'],
            'formsData' => ['meta_key', 'meta_value'],
        ],
    ],
    'Rdv' => [
        'model' => \App\Models\Rdv::class,
        'fields' => ['id', 'dossier_id', 'date_rdv'],
        'relations' => [
            'dossiers' => ['id', 'installateur'],
        ],
    ],
    // Add other models as needed
];
