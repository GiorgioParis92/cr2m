<?php
namespace App\View\Components;

use Illuminate\View\Component;

class DocumentTableComponent extends Component
{
    public $docs;

    public function __construct($docs)
    {
        $this->docs = $docs;
    }

    public function render()
    {
        return view('components.document-table-component');
    }
}
