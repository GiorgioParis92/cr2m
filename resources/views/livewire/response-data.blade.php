<div wire:init="loadResponseData">
    @if($responseData)
        @include('partials.responseData', ['responseData' => $responseData])
    @else
        <p>En cours de chargement des informations auprès de l'ANAH</p>
    @endif
</div>
