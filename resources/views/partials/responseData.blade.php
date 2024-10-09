@if($responseData)
    @foreach ($responseData as $tag => $data)
        @if (array_key_exists('error', $data))
            <p>Erreur lors du chargement. Veuillez vérifier le numéro CLAVIS et le MAR associé</p>
            @break
        @endif
        <div class="col-12">
            <div>
                <h6 class="mb-0">{{ $tag }}</h6>
                @foreach ($data['elements'] as $element)
                    <p class="p_thumbnail">
                        <a href="{{ $data['url'] ?? '' }}" target="_blank">
                            {!! nl2br(e($element['text'])) !!}
                        </a>
                        @if (!empty($element['screenshot']))
                            <img class="thumbnail_hover" src="data:image/png;base64,{{ $element['screenshot'] }}" alt="Screenshot" />
                        @endif
                    </p>
                @endforeach
            </div>
        </div>
    @endforeach
@else
    <p>Aucun lien ANAH trouvé avec cette référence et ce MAR</p>
@endif
