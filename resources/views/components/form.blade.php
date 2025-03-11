<div>

    <form action="{{ route('form.save', ['dossierId' => $dossierId]) }}" method="POST">
        @csrf

        

        {{-- <div class="row">
            @foreach ($configurations as $config)
                @php
                    $filePath = resource_path('views/components/forms/input-' . $config->type . '.blade.php');
                @endphp
                @if (File::exists($filePath))
                    @include('components.forms.input-' . $config->type)
                @else
                    <p class="btn btn-danger">Erreur de configuration pour : "{{ $config->type }}" </p>
                @endif
            @endforeach
        </div> --}}
        <div class="row">
            <div class="form-group">
                <button class="btn btn-secondary" type="submit">Enregistrer</button>
            </div>
        </div>
    </form>
</div>
