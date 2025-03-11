<div class="form-group {{ $config->class ?? '' }}">
    <label>{{ $config->title }}</label>
    <input class="form-control datepicker" type="text" name="{{ $config->name }}"
        @if ($config->required) required @endif
        value="{{ $formData[$config->name] ?? '' }}">
</div>