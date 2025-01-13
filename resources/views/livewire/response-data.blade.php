<div  wire:init="loadResponseData">

    @if($responseData)

        @include('partials.responseData', ['responseData' => $responseData])
    @else
        <p><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>En cours de chargement des informations auprès de l'ANAH<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></p>
    @endif
</div>
