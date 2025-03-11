<div class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
    @if($check_condition)
        <label>{{ $conf['title'] ?? '' }}</label>
        <textarea
            name="{{ $conf['name'] }}"
            class="form-control"
            style="min-height: 100px;"
            wire:ignore.self
            wire:model.lazy="value"
            placeholder=""
            onfocus="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';"
            oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';"
        >{{ $value }}</textarea>
        
        @error('value')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    @endif
</div>
