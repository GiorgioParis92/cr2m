@props(['docs'])

{{--  @foreach ($docs as $doc)

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


@if (!empty($docs))
<div class="">
    <a href="{{ route('download.all.docs', ['dossier_id' => $dossier->id]) }}" class="btn btn-primary mt-3 btn-sm  ">
        <i class="fa fa-download"></i> Telecharger tous les Documents
    </a>
</div>
@endif --}}
<div wire:ignore>
@php
$grouped_docs = [];
@endphp

@foreach ($docs as $doc)
    @if ($doc['required'] == 1 || ($doc['required'] == 0 && isset($doc['meta_value']) && !empty($doc['meta_value'])))
        @php 
            $data = [];
            $text = '';
            $status = '';

            // Determine the status and color
            if (isset($doc['meta_value']) && $doc['meta_value'] != null) {
                $color = 'success';
                $status = 'Documents';

                if (isset($doc['options']) && isset($doc['options']['signable']) && $doc['options']['signable'] == 'true') {
                    $data = $doc['additional_data'];
                    foreach ($doc['additional_data'] as $additional_data) {
                        $data[$additional_data->meta_key] = $additional_data->meta_value;
                    }

                    if (!empty($data) && isset($data['signature_request_id'])) {
                        if (isset($data['signature_status'])) {
                            if ($data['signature_status'] == 'finish') {
                                $color = 'success';
                                $status = 'Documents';
                            } elseif ($data['signature_status'] == 'ongoing') {
                                $color = 'warning';
                                $status = 'En attente de signature';
                                $text = '<br/>En attente de signature';
                            }
                        }
                    } else {
                        $color = 'danger';
                        $status = 'Signature à demander au bénéficiaire';
                        $text = '<br/>Signature à demander au bénéficiaire';
                    }
                } else {
                    
                }
            } else {
                $color = 'btn-outline-danger';
                $status = 'Document non chargé ou non généré';
                $text = '<br/>Document non chargé ou non généré';
            }

            // Group documents by status
            if ($doc['last_etape_order'] >= $doc['order_column']) {
                $grouped_docs[$status][] = [
                    'doc' => $doc,
                    'color' => $color,
                    'text' => $text
                ];
            }
        @endphp
    @endif
@endforeach

<!-- Use <details> and <summary> to create collapsible sections -->
@foreach ($grouped_docs as $status => $docs_in_group)
    <details class="mb-2">
        <summary class="btn btn-{{$color}}">
            {{ $status }} ({{ count($docs_in_group) }})
        </summary>
        <ul class="list-group mt-2">
            @foreach ($docs_in_group as $doc_info)
                @php
                    $doc = $doc_info['doc'];
                    $text = $doc_info['text'];
                    $color = $doc_info['color'];
                @endphp
                <li class="list-group-item">
                    @if (isset($doc['meta_value']) && !empty($doc['meta_value']))
                        <span class="pdfModal" data-toggle="modal" data-img-src="{{ asset('storage/' . $doc['meta_value']) }}?time={{ time() }}" target="_blank">
                            {!! $doc['title'] . ' '  !!}
                        </span>
                    @else
                        {!! $doc['title'] . ' '  !!}
                    @endif
                </li>
            @endforeach
        </ul>
    </details>
@endforeach

@if (!empty($docs))
    <div class="mt-3">
        <a href="{{ route('download.all.docs', ['dossier_id' => $dossier->id]) }}" class="btn btn-primary btn-sm">
            <i class="fa fa-download"></i> Télécharger tous les Documents
        </a>
    </div>
@endif
<style>
    details summary {
    list-style: none;
}

details summary::-webkit-details-marker {
    display: none;
}

details summary:focus {
    outline: none;
}
details.mb-2 {
    display: inline-block;
    vertical-align: top;
}
ul.list-group.mt-2 {
    position: absolute;
    z-index: 10000;
}
li span.pdfModal {
    cursor: pointer;
}
</style>
<script>
    $(document).ready(function() {
        $('details > summary').on('click', function() {
            var $clickedDetails = $(this).parent();
    
            // Use a timeout to ensure the 'open' attribute is updated
            setTimeout(function() {
                if ($clickedDetails.attr('open')) {
                    // Close all other details elements
                    $('details').not($clickedDetails).removeAttr('open');
                }
            }, 0);
        });
    });
    </script>
</div>

