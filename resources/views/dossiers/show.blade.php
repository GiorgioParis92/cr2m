@extends('layouts.app')

@section('content')
    @php
      $errors = [];
$is_valid = true;


    @endphp

    <div class="container">
     
@livewire('dossier-livewire',['id' => $id])

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
       
    </div>
    <script>
        @if(session('etape_id'))
        document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('[data-index="{{session('etape_id')}}"]').click();
});
           alert({{session('etape_id')}})
        @endif
        
        
            </script>
@endsection
@section('scripts')
    <script>
@if(session('form_id'))
   alert({{session('form_id')}})
@endif


    </script>
@endsection
