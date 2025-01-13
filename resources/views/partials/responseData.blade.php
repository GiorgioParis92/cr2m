@if ($responseData)

    @php
        $empty = true;
        function does_match($dossier, $value, $tags_to_match)
        {
            if (empty($tags_to_match)) {
                return true;
            }

            $concat = '';
            foreach ($tags_to_match as $tag_to_match) {
                $concat .= $dossier->beneficiaire->$tag_to_match ?? '';
            }

            $concat = str_replace([' ', ',', '-'], '', strtolower($concat));
            $value = str_replace([' ', ',', '-'], '', strtolower($value));

            return $concat === $value;
        }
    @endphp

    @if(isset($responseData) && is_array($responseData))
    @foreach ($responseData as $tag => $data)
        @if (isset($data['elements']) && is_array($data['elements']) && !empty($data['elements']))
            @php
                $empty = false;
                break;
            @endphp
        @endif
    @endforeach
@endif
    @if ($empty)
        <p>Erreur lors du chargement. Veuillez vérifier le numéro CLAVIS et le MAR associé ou rechargez la page.</p>
    @endif
    @if(isset($responseData) && is_array($responseData))
    @foreach ($responseData as $tag => $data)
        @if (array_key_exists('error', $data))
            <p>Erreur lors du chargement. Veuillez vérifier le numéro CLAVIS et le MAR associé ou rechargez la page.</p>
            @break
        @endif

        @if (isset($data['elements']) && is_array($data['elements']) && !$empty)
            <div class="col-12">
                <div class="p_thumbnail">
                    @if (
                        (isset($data['initial_data']['display_title']) && $data['initial_data']['display_title'] != 'false') ||
                            !isset($data['initial_data']['display_title']))
                        <h6 class="mb-0">{{ $tag }}</h6>
                    @endif

                    @foreach ($data['elements'] as $element)
                        @php
                            $prefix = '';
                            $suffix = '';
                            $color = 'primary';
                        @endphp

                        @if (!empty($element['screenshot']))
                            <img class="thumbnail_hover" src="data:image/png;base64,{{ $element['screenshot'] }}" alt="Screenshot" />
                        @endif

                        <p class="">
                            <a href="{{ $data['url'] ?? '' }}" target="_blank">
                                @if ($tag == 'Statut du dossier')
                                    @if (strpos($element['text'], 'rejet'))
                                        @php $color = 'danger'; @endphp
                                    @endif
                                    @php
                                        $prefix = '<div style="color:white" class="color-white btn btn-' . $color . '">';
                                        $suffix = '</div>';
                                    @endphp
                                @endif

                                @if (
                                    (isset($data['initial_data']['display']) && $data['initial_data']['display'] != 'false') ||
                                        !isset($data['initial_data']['display']))
                                    @php
                                        if (isset($data['initial_data']['split'])) {
                                            $explode = explode($data['initial_data']['split']['split_char'], $element['text']);
                                            $text = '';

                                            foreach ($data['initial_data']['split']['element_to_keep'] as $split_element) {
                                                $index = (int) $split_element['index'];

                                                if ($index >= 0 && $index < count($explode)) {
                                                    $text .= '<p>' . $split_element['title'] . ' : ' . $explode[$index];
                                                    if (!does_match($dossier, $explode[$index], $split_element['tags_to_match'] ?? [])) {
                                                        $text .= '<span style="color:#fd5c70 !important;margin-left:10px"><i class="fa fa-triangle-exclamation"></i>  Erreur</span>';
                                                    }
                                                    $text .= '</p>';
                                                }
                                            }
                                        } else {
                                            $text = $element['text'];
                                        }
                                    @endphp

                                    {!! $prefix . nl2br($text) . $suffix !!}
                                @endif
                            </a>
                        </p>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
    @endif
@else
    <p>Aucun lien ANAH trouvé avec cette référence et ce MAR</p>
@endif

<style>
    .color-white, .color-white a {
        color: white !important;
    }
</style>
