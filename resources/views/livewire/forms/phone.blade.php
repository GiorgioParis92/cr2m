
<div  class=" {{$conf['class'] ?? 'col-lg-12'}}">
    @if($check_condition)
    <label>{{ $conf['title'] ?? '' }}</label>
    <input type="text"  name="{{ $conf['name'] }}"  class="form-control"  wire:model.debounce.500ms="value"  placeholder="">
    @error('value')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    @endif
    </div>


