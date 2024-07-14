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
            @if(auth()->user()->client->type_client!=3)
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
    <div class="row">
        <div class="col-12">
            <div id="calendar"></div>
        </div>
    </div>
   
    
</body>

</html>
