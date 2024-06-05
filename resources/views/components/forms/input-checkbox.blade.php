@php
    // Assuming $config->options contains a properly formatted JSON string
    $jsonString = str_replace(["\n", ' ', "\r"], '', $config->options);
    $optionsArray = json_decode($jsonString, true);

    if (!is_array($optionsArray)) {
        echo "The variable is not an array.\n";
        $optionsArray = [];
    }
    $colors = ['3498DB', 'F1C40F', 'C0392B'];
@endphp

<div class="form-group {{ $config->class ?? '' }}">
    <label>{{ $config->title }}</label><br />

    @if (is_array($optionsArray))
        <label class="switch">
            <input type="hidden" name="{{ $config->name }}" value="{{ $optionsArray[0]['value'] }}">
            <input type="checkbox" id="{{ $config->name }}" name="{{ $config->name }}"
                value="{{ $optionsArray[1]['value'] }}" @if (isset($formData[$config->name]) && $formData[$config->name] == $optionsArray[1]['value']) checked @endif>
            <span class="slider round"></span>
        </label>
        <label class="custom-control-label" for="{{ $config->name }}">{{ $optionsArray[1]['label'] }}</label>
    @endif

</div>
