<page_header style="font-family:dejavusans">

        <div class="logo_header" style="">
  
            @if(isset($dossier->mar_client->main_logo) && file_exists(storage_path('app/public/' . $dossier->mar_client->main_logo)))

            <img src="{{ asset('storage/' . $dossier->mar_client->main_logo) }}" alt="Logo">

            @endif
        </div>

<style>
.logo_header {
    position:absolute;
    max-width:180px;
    left:10px;
    top:-10px
}
    </style>

</page_header>
