
<div class=" {{$conf['class'] ?? 'col-lg-12'}}">
    @if($check_condition)
    <label>{{ $conf['title'] ?? '' }}</label>
    <input type="text" {{$readonly ? 'readonly' : ''}}  name="{{ $conf['name'] }}"  class="form-control {{$readonly ? 'prediction' : ''}}"  wire:model.debounce.500ms="value"  placeholder="">
    @error('value')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    @endif
</div>
