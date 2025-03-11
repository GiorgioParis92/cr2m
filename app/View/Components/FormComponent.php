<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\App;

class FormComponent extends Component
{
    public $defaultOutput;
    public $configurations;
    public $id;
    public $dossierId;
    public $formData;

    public function __construct($id, $dossierId)
    {
     
        $defaultForm = App::make('defaultform');
        $defaultForm->getFormById($id);
        $this->id = $id;
        $this->dossierId = $dossierId;
        $this->defaultOutput = $defaultForm->render();
        $this->configurations = $defaultForm->getConfigurations();
        $this->formData = $defaultForm->getFormData($id, $dossierId);

    }

    public function render()
    {
        return view('components.form', [
            'defaultOutput' => $this->defaultOutput,
            'id' => $this->id,
            'dossierId' => $this->dossierId,
            'configurations' => $this->configurations,
            'formData' => $this->formData
        ]);
    }
}
