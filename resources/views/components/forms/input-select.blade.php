@php
    // Assuming $config->options contains a properly formatted JSON string
    $jsonString = str_replace(["\n", ' ', "\r"], '', $config->options);
    $optionsArray = json_decode($jsonString, true);

    if (!is_array($optionsArray)) {
        echo "The variable is not an array.\n";
        $optionsArray = [];
    }

@endphp

@if (isset($optionsArray[0]['table']))
    @php
        $results = \DB::table($optionsArray[0]['table'])->get();
        $label = $optionsArray[0]['label'];
        $value = $optionsArray[0]['value'] ?? $optionsArray[0]['label'];
    @endphp

    <div class="form-group {{ $config->class ?? '' }}">
        <label>{{ $config->title }}</label><br />
        <select @if($config->required==1) required @endif name="{{ $config->name }}" class="form-control">
            <option value="">Choisir</option>
            @foreach ($results as $result)
                <option @if(isset($formData[$config->name]) && $formData[$config->name] ==$result->$value) selected @endif value="{{ $result->$value }}">{{ $result->$label }}</option>
            @endforeach
        </select>

    </div>
@else
    <div class="form-group {{ $config->class ?? '' }}">
        <label>{{ $config->title }}</label><br />
        <select @if($config->required==1) required @endif  name="{{ $config->name }}" class="form-control">
            <option value="">Choisir</option>
            @if (is_array($optionsArray))
                @foreach ($optionsArray as $key => $element)
                    <option value="{{ $element['value'] }}" @if (isset($formData[$config->name]) && $formData[$config->name] == $element['value']) selected @endif>
                        {{ $element['label'] }}</option>
                @endforeach
            @endif
        </select>
    </div>

@endif
