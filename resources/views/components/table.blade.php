

@if (!empty($value))
    @foreach ($value as $uniqueId => $element_data)
        <div class="row" wire:key="row-{{$uniqueId}}">
            <div class="col-lg-10 col-sm-12">
                @foreach ($optionsArray as $element_config)
                    @php
                        $baseNamespace = 'App\FormModel\FormData\\';
                        $className = $baseNamespace . ucfirst($element_config['type']);
                        $div_name = $name . '.value.' . $uniqueId . '.' . $element_config['name'];

                        if (class_exists($className)) {
                            $reflectionClass = new \ReflectionClass($className);
                            $element_instance = $reflectionClass->newInstance((object) $element_config, $div_name, $form_id, $dossier_id, false);
                        } else {
                            // Fallback to AbstractFormData if the class does not exist
                            $element_instance = new AbstractFormData((object) $element_config, $div_name, $form_id, $dossier_id, false);
                        }

                        $element_instance->set_dossier($dossier);

                        // Set the value from the stored data
                        $element_value = $element_data[$element_config['name']]['value'] ?? '';
                        $element_instance->value = $element_value;
                    @endphp

                    {!! $element_instance->render(false) !!}
                @endforeach
            </div>
            <div class="col-lg-2">
                <label></label>
                <div class="col-lg-12 col-sm-12 btn btn-danger" wire:click="remove_row('{{ $name }}', {{ $form_id }}, '{{ $uniqueId }}')">Supprimer</div>
            </div>
        </div>
    @endforeach
@endif

<div class="btn btn-success" wire:click="add_row('{{ $name }}', {{ $form_id }})">{{ $config->title }}</div>
