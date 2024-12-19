<div class="{{ $conf['class'] ?? 'col-lg-12' }}">
    @if($check_condition)
    {{-- <label>{{ $conf['title'] ?? '' }}</label> --}}

    <input 
        type="checkbox" 
        id="{{ $conf['name'] }}" 
        name="{{ $conf['name'] }}" 
        value="{{ $options[1]['value'] }}" 
        {{-- wire:model.debounce.500ms="value" --}}
        @if($options[1]['value'] == $value)
        wire:change="update_value({{$options[0]['value']}})"

        @else
        wire:change="update_value({{$options[1]['value']}})"
        @endif
        @if($options[1]['value'] == $value || $value!=0) checked @else unchecked @endif
        @if($value==0) unchecked  @endif
    >
    <label for="{{ $conf['name'] }}">{{$options[1]['label']}}</label>

    @error('value')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    @endif
</div>
