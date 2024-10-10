@if ($responseData)
    @php $empty=true; @endphp
    @foreach ($responseData as $tag => $data)
        @if (!empty($data['elements']))
            @php
                $empty = false;
                break;
            @endphp
        @endif
    @endforeach
    @if ($empty)
        <p>Erreur lors du chargement. Veuillez vérifier le numéro CLAVIS et le MAR associé</p>
    @endif
    @foreach ($responseData as $tag => $data)
        @if (array_key_exists('error', $data))
            <p>Erreur lors du chargement. Veuillez vérifier le numéro CLAVIS et le MAR associé</p>
        @break
    @endif
    @if (!empty($data['elements']) && !$empty)
        <div class="col-12">
            <div class="p_thumbnail">
                <h6 class="mb-0 =">{{ $tag }}</h6>

                @foreach ($data['elements'] as $element)
                    @php $prefix='';$suffix='';$color='primary' @endphp 
                    <p class="">
                        <a href="{{ $data['url'] ?? '' }}" target="_blank">
                            @if($tag=='Statut du dossier') 
                            @if(strpos($element['text'],'rejet')) 
                            @php $color='danger' @endphp
                            @endif
                            @php $prefix='<div class="btn btn-'.$color.'">';$suffix='</div>' @endphp 
                            @endif


                            @if((isset($data['initial_data']['display']) && $data['initial_data']['display']!='false') || !isset($data['initial_data']['display']))
                           
                            @php 
                            if(isset($data['initial_data']['split'])) {
                                $explode=explode($data['initial_data']['split']['split_char'],$element['text']);
                                $text='';
                                foreach($data['initial_data']['split']['element_to_keep'] as $split_element) {
                                    $index=(int)$split_element['index'];
                                    if($index>=0 && $index<count($explode)) {
                                        $text.='<p>'.$split_element['title'].' : '.$explode[$index].'</p>';
                                    }
                                }
                            } else {
                                $text=$element['text'];
                            }

                            @endphp
                            {!! $prefix.nl2br($text).$suffix !!}
                           
                           
                            @endif


                        </a>
                        @if (!empty($element['screenshot']))
                            <img class="thumbnail_hover" src="data:image/png;base64,{{ $element['screenshot'] }}"
                                alt="Screenshot" />
                        @endif
                    </p>
                @endforeach
            </div>
        </div>
    @endif
@endforeach
@else
<p>Aucun lien ANAH trouvé avec cette référence et ce MAR</p>
@endif
