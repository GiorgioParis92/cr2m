@props(['docs'])
@foreach ($docs as $doc)

    @if ($doc['required'] == 1 || ($doc['required'] == 0 && isset($doc['meta_value']) && !empty($doc['meta_value'])))

        @php $data=[] @endphp
        @php $text='' @endphp

        @if (isset($doc['meta_value']) && $doc['meta_value'] != null)
            @php $color ='success' @endphp



            @if (isset($doc['options']))
                @if (isset($doc['options']['signable']) && $doc['options']['signable'] == 'true')
                    @php $data=($doc['additional_data']) @endphp
                    @foreach ($doc['additional_data'] as $addtional_data)
                        @php $data[$addtional_data->meta_key]=$addtional_data->meta_value @endphp
                    @endforeach


                    @if (!empty($data) && isset($data['signature_request_id']))
                        @if (isset($data['signature_status']))
                            @if ($data['signature_status'] == 'finish')
                                @php $color='success' @endphp
                            @endif
                            @if ($data['signature_status'] == 'ongoing')
                                @php $color='warning' @endphp
                                @php $text='<br/>En attente de signature' @endphp
                            @endif
                        @endif
                    @else
                        @php $color='danger' @endphp
                        @php $text='<br/>Signature à demander au bénéficiaire' @endphp
                    @endif

                @endif
            @endif
        @else
            @php $color =' btn-outline-danger' @endphp
            @php $text='<br/>Document non chargé ou non généré' @endphp

        @endif
        @if($doc['last_etape_order']>=$doc['order_column'])
        <div class="btn-sm btn btn-{{ $color }} btn-view @if (isset($doc['meta_value']) && !empty($doc['meta_value'])) pdfModal @endif"
            @if (isset($doc['meta_value']) && !empty($doc['meta_value'])) data-toggle="modal" data-img-src="{{ asset('storage/' . $doc['meta_value']) }}?time=1727288485"
        data-name="Fiche navette" @endif>
            <span
                class="badge badge-md badge-circle badge-floating badge-danger  btn-outline-success @if ($color == ' btn-outline-danger') text-danger border-danger @else border-white @endif"
                style="border: 1px solid white;">{{ $doc['order_column'] }}</span> {!! $doc['title'] . ' ' . $text !!}
        </div>
        @endif
    @endif
@endforeach
