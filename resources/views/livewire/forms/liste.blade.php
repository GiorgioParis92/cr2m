<div class="col-sm-12 {{ $conf['class'] ?? 'col-lg-12' }}">
 
    @if($check_condition)
    <div class="card-header">
        <h5>{{ $conf['title'] ?? '' }}</h5>
    </div>


    @php print_r($value) @endphp
    @php print_r($request) @endphp

    @endif



</div>


