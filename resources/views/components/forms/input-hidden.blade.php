
    <input class="form-control" type="hidden" name="{{ $config->name }}"
        @if ($config->required) required @endif
        value="{{ $formData[$config->name] ?? '' }}">
