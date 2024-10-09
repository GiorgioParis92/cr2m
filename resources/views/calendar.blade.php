<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-token" content="{{ Auth::user()->api_token }}"> <!-- Replace with your actual token -->
    <title>FullCalendar Example</title>
    <!-- FullCalendar CSS -->

</head>

<body>
    <div class="row">
        @if(auth()->user()->type_id==4)
        <input type="hidden" value="{{auth()->user()->id}}" id="form_config_user_id">
        @else
            @if((isset(auth()->user()->client->type_client) && auth()->user()->client->type_client!=3) || !isset(auth()->user()->client->type_client))
            <div class="form-group col-lg-6">
                <select class="form-control" id="form_config_user_id">
                    <option value="">Choisir un auditeur / Voir tous les auditeurs</option>
                    @foreach ($auditeurs as $auditeur)
                        <option value="{{ $auditeur->id }}">{{ $auditeur->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        @endif
        <div class="form-group col-lg-6">
            <select id="dpt" onchange="handleSelectionChange(this)">
                <option value=""  selected>Choisir un département</option>
               
                        @foreach ($departments as $department)
                            <option value="{{ $department['departement_code'] }}" data-type="department">
                                {{ $department['departement_code'] }} - {{ $department['departement_nom'] }}
                            </option>
                        @endforeach
       
            </select>
        </div>
    </div>
    <div data-rdv_id="" data-type_rdv="3" class="btn btn-secondary show_conge">Ajouter un congé/indisponibilité </div>
    <div class="row">
        <div class="col-12">
            <div id="calendar"></div>
        </div>
    </div>
   
    <script>
                $(document).on('click', '.show_conge', function(event) {
            var rdv_id = $(this).data('rdv_id');

            if (rdv_id == undefined || rdv_id == '') {
                rdv_id = 0
            }

            $.ajax({
                url: '/api/rdvs', // Adjust this URL to your actual API endpoint
                type: 'GET',
                data: {
                    rdv_id: rdv_id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // Include CSRF token if using Laravel's CSRF protection
                },
                success: function(response) {

                    $('#new_day').show();
                    $('#duration_group').show();

                    // Clear previous data
                    $('#rdv_id').val('0');
                    $('#rdv_french_date').val('');
                    $('#rdv_hour').val('');
                    $('#rdv_minute').val('');
                    $('#rdv_user_id').val('');
                    $('#rdv_status').val('');
                    $('#rdv_observations').val('');
                    $('#rdv_type_rdv').val($(this).data('type_rdv'));
                    $('#rdv_nom').val("{!! $dossier['beneficiaire']['nom'] ?? '' !!}");
                    $('#rdv_prenom').val("{!! $dossier['beneficiaire']['prenom'] ?? '' !!}");
                    $('#rdv_adresse').val("{!! $dossier['beneficiaire']['adresse'] ?? '' !!}");
                    $('#rdv_cp').val("{!! $dossier['beneficiaire']['cp'] ?? '' !!}");
                    $('#rdv_ville').val("{!! $dossier['beneficiaire']['ville'] ?? '' !!}");
                    $('#rdv_telephone').val("{!! $dossier['beneficiaire']['telephone'] ?? '' !!}");
                    $('#rdv_email').val("{!! $dossier['beneficiaire']['email'] ?? '' !!}");
                    $('#rdv_telephone_2').val("{!! $dossier['beneficiaire']['telephone_2'] ?? '' !!}");
                    $('#rdv_dossier_id').val("{!! $dossier['id'] ?? '' !!}");
                    $('#rdv_client_id').val("{!! $dossier['client_id'] ?? '' !!}");
                    $('#rdv_lat').val("{!! $dossier['beneficiaire']['lat'] ?? '' !!}");
                    $('#rdv_lng').val("{!! $dossier['beneficiaire']['lng'] ?? '' !!}");

                    if (response && response.length > 0) {
                        console.log(response)
                        var rdv = response[0];
                        $.each(rdv, function(key, value) {
                            console.log(key)
                            console.log(value)
                            // Populate form fields
                            $('#rdv_' + key).val(value);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading rdv data:', error);
                }
            });

            $('#rdv_modal').modal('show');
        });
</script>
</body>

</html>
