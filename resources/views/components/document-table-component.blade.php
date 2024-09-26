@props(['docs'])
@foreach ($docs as $doc)
    @php $data=[] @endphp
    @if (isset($doc['options']))
        @if (isset($doc['options']['signable']) && $doc['options']['signable'] == 'true')
            @php $data=($doc['additional_data']) @endphp
        @endif
    @endif


    @if (isset($doc['meta_value']) && $doc['meta_value'] != null)
        @php $color ='success' @endphp
    @else
        @php $color ='danger' @endphp
    @endif

    @if (!empty($data) && isset($doc['signature_status']))
        @if($doc['signature_status']=='finish') @php $color='success' @endphp @endif
        @if($doc['signature_status']=='ongoing') @php $color='warning' @endphp @endif
    @endif

    <div class="btn btn-{{ $color }} btn-view @if (isset($doc['meta_value']) && !empty($doc['meta_value'])) pdfModal @endif"
        data-toggle="modal" data-img-src="{{ asset('storage/' . $doc['meta_value']) }}?time=1727288485"
        data-name="Fiche navette">
        <i class="fas fa-eye"></i> {{ $doc['title'] }}
    </div>
@endforeach
