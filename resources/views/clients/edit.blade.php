@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1>@lang('forms.edit_client')</h1>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('clients.update', $client->id) }}" method="POST" enctype="multipart/form-data" id="client-form">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="client_title" class="form-label">{{ __('forms.client_title') }}</label>
                    <input type="text" class="form-control" id="client_title" name="client_title" value="{{ $client->client_title }}" required>
                </div>
                <div class="mb-3">
                    <label for="type_client" class="form-label">{{ __('forms.type_client') }}</label>
                    <select class="form-control" id="type_client" name="type_client" required>
                        <option value="">-- Select Client Type --</option>
                        @foreach($clientTypes as $type)
                            <option value="{{ $type->id }}" @if($client->type_client == $type->id) selected @endif>{{ $type->type_desc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="main_logo" class="form-label">{{ __('forms.main_logo') }}</label>
                    <div class="dropzone" id="mainLogoDropzone"></div>
                    <input type="hidden" name="main_logo" id="main_logo_input" value="{{ $client->main_logo }}">
                </div>
                <div class="mb-3">
                    <label for="adresse" class="form-label">{{ __('forms.representant') }}</label>
                    <input type="text" class="form-control" id="representant" name="representant" value="{{ $client->representant }}">
                </div>
                <div class="mb-3">
                    <label for="adresse" class="form-label">{{ __('forms.address') }}</label>
                    <input type="text" class="form-control" id="adresse" name="adresse" value="{{ $client->adresse }}">
                </div>
                <div class="mb-3">
                    <label for="cp" class="form-label">{{ __('forms.postal_code') }}</label>
                    <input type="text" class="form-control" id="cp" name="cp" value="{{ $client->cp }}">
                </div>
                <div class="mb-3">
                    <label for="ville" class="form-label">{{ __('forms.city') }}</label>
                    <input type="text" class="form-control" id="ville" name="ville" value="{{ $client->ville }}">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('forms.email') }}</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $client->email }}">
                </div>

                <div class="mb-3">
                    <label for="mail_compta" class="form-label">Mail Comptabilité</label>
                    <input type="email" class="form-control" id="mail_compta" name="mail_compta" value="{{ $client->mail_compta }}">
                </div>

                <div class="mb-3">
                    <label for="telephone" class="form-label">{{ __('forms.telephone') }}</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" value="{{ $client->telephone }}">
                </div>
                <div class="mb-3">
                    <label for="siret" class="form-label">{{ __('forms.siret') }}</label>
                    <input type="text" class="form-control" id="siret" name="siret" value="{{ $client->siret }}">
                </div>
                <div class="mb-3">
                    <label for="siren" class="form-label">{{ __('forms.siren') }}</label>
                    <input type="text" class="form-control" id="siren" name="siren" value="{{ $client->siren }}">
                </div>
                <div class="mb-3">
                    <label for="tva_intracomm" class="form-label">{{ __('forms.tva_intracommunautaire') }}</label>
                    <input type="text" class="form-control" id="tva_intracomm" name="tva_intracomm" value="{{ $client->tva_intracomm }}" >
                </div>
                <div class="mb-3">
                    <label for="type_societe" class="form-label">{{ __('forms.type_societe') }}</label>
                    <input type="text" class="form-control" id="type_societe" name="type_societe" value="{{ $client->type_societe }}" >
                </div>
                <div class="mb-3">
                    <label for="rcs" class="form-label">{{ __('forms.rcs') }}</label>
                    <input type="text" class="form-control" id="rcs" name="rcs" value="{{ $client->rcs }}" >
                </div>
                <div class="mb-3">
                    <label for="naf" class="form-label">{{ __('forms.naf') }}</label>
                    <input type="text" class="form-control" id="naf" name="naf" value="{{ $client->naf }}" >
                </div>
                <div class="mb-3">
                    <label for="agrement" class="form-label">{{ __('forms.agrement') }}</label>
                    <input type="text" class="form-control" id="agrement" name="agrement" value="{{ $client->agrement }}" >
                </div>

                <div class="mb-3">
                    <label for="bank" class="form-label">Coordonnées bancaires</label>
                    <input type="text" class="form-control" id="bank" name="bank" value="{{ $client->bank }}" >
                </div>

                <button type="submit" class="btn btn-primary">{{ __('forms.submit') }}</button>
            </form>

            @if($client->type_client==3)
            <div class="mb-3">
                <h3>Clients Parents</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Client Parent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($has_parent as $parent)
                        <tr>
                            <td>{{ $parent['client_parent']['client_title'] }}</td>
                            <td>
                                <!-- Delete Button -->
                                <form action="{{ route('clients.remove_parent')}}" method="POST" style="display:inline-block;">
                                    @csrf
                                   
                                    <input type="hidden" name="id" value="{{$client->id}}">
                                    <input type="hidden" name="parent" value="{{$parent['client_parent']['id']}}">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <h3>Ajouter un parent</h3>
                <form action="{{ route('clients.add_parent', $client->id) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="input-group">
                        
                        <select name="client_parent" class="form-control">
                            <option value="">Choisir un client</option>
                            @foreach($installateurs as $install)
                            <option value="{{$install->id}}">{{$install->client_title}}</option>

                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
            @endif


            @if($client->type_client==4)
            <div class="mb-3">
                <h3>Clients "Enfants"</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Client Enfant</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($has_child as $parent)
                        <tr>
                            <td>{{ $parent['client_child']['client_title'] }}</td>
                            <td>
                                <!-- Delete Button -->
                                <form action="{{ route('clients.remove_parent')}}" method="POST" style="display:inline-block;">
                                    @csrf
                                   
                                    <input type="hidden" name="id" value="{{$parent['client_child']['id']}}">
                                    <input type="hidden" name="parent" value="{{$client->id}}">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <h3>Ajouter un client "Enfant"</h3>
                <form action="{{ route('clients.add_child', $client->id) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="input-group">
                        
                        <select name="client_child" class="form-control">
                            <option value="">Choisir un client</option>
                            @foreach($installateurs as $install)
                            <option value="{{$install->id}}">{{$install->client_title}}</option>

                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    Dropzone.autoDiscover = false;

    var mainLogoDropzone = new Dropzone("#mainLogoDropzone", {
        url: "{{ route('clients.upload_logo') }}", // Route to handle the file upload
        paramName: "file",
        maxFilesize: 1, // MB
        acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf,.heic",
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        params: {
        client_id: {{$client->id}},
    },
        success: function(file, response) {
            // Update the hidden input field with the path of the uploaded file
            document.getElementById('main_logo_input').value = response.file_path;
        }
    });

    @if($client->main_logo)
        var mockFile = { name: "{{ basename($client->main_logo) }}", size: 12345 }; // Mock file object
        mainLogoDropzone.emit("addedfile", mockFile);
        mainLogoDropzone.emit("thumbnail", mockFile, "{{ asset('storage/' . $client->main_logo) }}"); // Use asset() to generate the correct URL
        mainLogoDropzone.emit("complete", mockFile);

        // Prevent Dropzone from removing the file on click
        mainLogoDropzone.files.push(mockFile);
    @endif
</script>
@endsection
