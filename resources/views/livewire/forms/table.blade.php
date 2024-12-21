<div style="" class="row col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
    @if ($check_condition)
        @if ($value && !empty($value))
            @foreach ($value as $rowIndex)
                {{-- Wrap each row in a container so we can place a delete button --}}
                <div class="row mb-2">
                    @foreach ($options as $optionIndex => $option)
                        @php
                            // Build a unique “meta key” for each column in this row
                            $option['name'] = $conf['name'] . '.value.' . $rowIndex . '.' . $option['name'];
                        @endphp



                        @if (View::exists('livewire.forms.' . $option['type']))
                            @livewire("forms.{$option['type']}", ['conf' => $option, 'form_id' => $form_id, 'dossier_id' => $dossier_id], key("{$rowIndex}-{$optionIndex}"))
                        @endif
                    @endforeach

                    <!-- Delete button -->
                    <div class="col-auto">
                        <button wire:click="remove_row('{{ $rowIndex }}')" class="btn btn-danger">
                            Supprimer
                        </button>
                    </div>
                </div>
            @endforeach
        @endif
    @endif

    <!-- Add Row button -->
    <div class="card mt-6">
        <div wire:click="add_row" class="btn btn-success">
            {{ $conf['title'] }}
        </div>
    </div>
</div>
