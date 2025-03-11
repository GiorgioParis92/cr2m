<div class="form-group">

    @php
        $jsonString = str_replace(["\n", ' ', "\r"], '', $config->options);
        $optionsArray = json_decode($jsonString, true);

        if (is_array($optionsArray)) {
        } else {
            echo "The variable is not an array.\n";
        }
        $colors = ['3498DB', 'F1C40F', 'C0392B'];
    @endphp

    <label>{{ $config->title }}</label>
    <div>
        @if (is_array($optionsArray))
            @foreach ($optionsArray as $key => $element)
                <input @if ($formData[$config->name] == $element['value']) checked @endif value="{{ $element['value'] }}" style="width:100%"
                    name="{{ $config->name }}"
                    class="@if ($formData[$config->name] == $element['value']) choice_checked @endif"
                    data-radiocharm-background-color="{{ $element['color'] ?? ($color[$key] ?? '3498DB') }}"
                    data-radiocharm-text-color="FFF" data-radiocharm-label="{{ $element['label'] }}" type="radio">
            @endforeach
        @endif

    </div>
</div>
