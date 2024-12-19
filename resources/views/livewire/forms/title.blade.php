</div>
<div class="card mt-4 p-4">
<div class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
 
    @if($check_condition)
    <div class="col-12">
        <h6>{{ $conf['title'] ?? '' }}</h6>
    </div>
    @endif
</div>
