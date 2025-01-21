<div class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">

    @if($check_condition)
 
    <label>{{ $conf['title'] ?? '' }}</label>
    <select name="{{ $conf['name'] }}" class="form-control no_select2" wire:model.debounce.500ms="value">
        <option value="">Choisir</option>

        @foreach($request as $key => $val)
        <option value="{{ $val[$options['value']] }}">{{ $val[$options['label']] }}</option>
        @endforeach

    </select>

    @error('value')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    @endif
</div>
