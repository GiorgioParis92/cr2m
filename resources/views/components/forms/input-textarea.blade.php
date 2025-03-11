<div class="form-group {{ $config->class ?? '' }}">
    <label>{{ $config->title }}</label>
        <textarea class="form-control" name="{{ $config->name }}">{{ $formData[$config->name] ?? '' }}</textarea>
</div>