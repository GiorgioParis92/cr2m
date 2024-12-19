<div class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
    @if($check_condition)
    <label>{{ $conf['title'] ?? '' }}</label><br/>
    @php
    $data='';
    $data .= '<div>';
    foreach ($options as $key => $element) {
                $isChecked = $value == $element['value'] ? 'checked' : '';
                $backgroundColor = $element['color'] ?? ($colors[$key-1] ?? '3498DB');
                $backgroundColor = '';
                // $data.=$element['value'];
                $data.='<div class="radio_line" style="background:#'.$backgroundColor.' ">';
                $data .= '<input id="'.$conf['name'].'_'.$key.'"

                  wire:change="update_value('.$element['value'].')"
                    value="'.$element['value'].'"
                    name="'.$conf['name'].'"
                    class="'.($value == $element['value'] ? 'choice_checked' : '').' "
                    data-radiocharm-background-color="'.$backgroundColor.'"
                    data-radiocharm-text-color="FFF" 
                    data-radiocharm-label="'.$element['label'].'" 
                    type="radio" '.$isChecked.'>';
                $data .= '<label  for="'.$conf['name'].'_'.$key.'">'.$element['label'].'</label><br>';
                $data .= '</div>';
            }
            $data .= '</div>';
    echo $data;
    @endphp


    @error('value')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    @endif
</div>
