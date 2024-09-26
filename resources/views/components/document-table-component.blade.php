@props(['docs'])
@foreach ($docs as $doc)

    @if ($doc['required'] == 1 || ($doc['required'] == 0 && isset($doc['meta_value']) && !empty($doc['meta_value'])))

        @php $data=[] @endphp
        @php $text='' @endphp

        @if (isset($doc['meta_value']) && $doc['meta_value'] != null)
            @php $color ='success' @endphp
        @else
            @php $color ='danger' @endphp
        @endif


        @if (isset($doc['options']))
            @if (isset($doc['options']['signable']) && $doc['options']['signable'] == 'true')
                @php $data=($doc['additional_data']) @endphp
                @php $color ='danger' @endphp

                @if (!empty($data) && isset($doc['signature_request_id']))
                    @if (isset($doc['signature_status']))
                        @if ($doc['signature_status'] == 'finish')
                            @php $color='success' @endphp
                        @endif
                        @if ($doc['signature_status'] == 'ongoing')
                            @php $color='warning' @endphp
                            @php $text='<br/>En attente de signature' @endphp

                        @endif
                    @endif
                @endif
                @if (!empty($data) && !isset($doc['signature_request_id']))
                    @php $color=' btn-outline-danger' @endphp
                    @php $text="\n".'<br/>Non signé' @endphp
                @endif

            @endif
        @endif

        <div class="btn-sm btn btn-{{ $color }} btn-view @if (isset($doc['meta_value']) && !empty($doc['meta_value'])) pdfModal @endif"
            @if (isset($doc['meta_value']) && !empty($doc['meta_value'])) data-toggle="modal" data-img-src="{{ asset('storage/' . $doc['meta_value']) }}?time=1727288485"
        data-name="Fiche navette" @endif>
            <i class="fas fa-eye"></i> {!! $doc['title']. ' '.$text !!}
        </div>
    @endif
@endforeach
