@extends('layouts.app')

@section('content')
    @php
        $errors = [];
        $is_valid = true;

    @endphp

    <div class="container">

      
        @if(auth()->user()->id>0)

        @livewire('dossier-livewire-new', ['id' => $id])
        @else 
        @livewire('dossier-livewire', ['id' => $id])
        @endif
        @if (!$is_valid)
            <div class="alert alert-danger">
                Erreur dans le remplissage de votre formulaire
            </div>
        @endif
        @if (session('result') && $is_valid)
            <div class="alert alert-success">
                Données sauvegardées
            </div>
        @endif
        @if(isset($dossier))
            @if(is_user_allowed('chat'))
        @livewire('chat',['dossier_id' => $dossier['id']])
        @endif
        @endif
    </div>
    <script>
        @if (session('etape_id'))
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector('[data-index="{{ session('etape_id') }}"]').click();
            });
            alert({{ session('etape_id') }})
        @endif
    </script>
@endsection
@section('scripts')
    <script>
        @if (session('form_id'))
            alert({{ session('form_id') }})
        @endif
    </script>
@endsection
