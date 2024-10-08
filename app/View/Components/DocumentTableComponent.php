<?php
namespace App\View\Components;

use Illuminate\View\Component;
class DocumentTableComponent extends Component
{
    public $docs;
    public $dossier;

    public function __construct($docs, $dossier)
    {
        $this->docs = $docs;
        $this->dossier = $dossier;
    }

    public function render()
    {
        return view('components.document-table-component');
    }
}
