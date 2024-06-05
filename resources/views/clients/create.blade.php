@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h4>@lang('forms.new_client')</h4>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data" id="client-form">
                @csrf
                <div class="mb-3">
                    <label for="client_title" class="form-label">{{ __('forms.client_title') }}</label>
                    <input type="text" class="form-control" id="client_title" name="client_title" required>
                </div>
                <div class="mb-3">
                    <label for="type_client" class="form-label">{{ __('forms.type_client') }}</label>
                    <select class="form-control" id="type_client" name="type_client" required>
                        <option value="">-- Select Client Type --</option>
                        @foreach($clientTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->type_desc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="main_logo" class="form-label">{{ __('forms.main_logo') }}</label>
                    <div class="dropzone" id="mainLogoDropzone"></div>
                </div>
                <div class="mb-3">
                    <label for="adresse" class="form-label">{{ __('forms.address') }}</label>
                    <input type="text" class="form-control" id="adresse" name="adresse">
                </div>
                <div class="mb-3">
                    <label for="cp" class="form-label">{{ __('forms.postal_code') }}</label>
                    <input type="text" class="form-control" id="cp" name="cp">
                </div>
                <div class="mb-3">
                    <label for="ville" class="form-label">{{ __('forms.city') }}</label>
                    <input type="text" class="form-control" id="ville" name="ville">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('forms.email') }}</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="mb-3">
                    <label for="telephone" class="form-label">{{ __('forms.telephone') }}</label>
                    <input type="text" class="form-control" id="telephone" name="telephone">
                </div>
                <div class="mb-3">
                    <label for="siret" class="form-label">{{ __('forms.siret') }}</label>
                    <input type="text" class="form-control" id="siret" name="siret">
                </div>
                <div class="mb-3">
                    <label for="siren" class="form-label">{{ __('forms.siren') }}</label>
                    <input type="text" class="form-control" id="siren" name="siren">
                </div>
                <div class="mb-3">
                    <label for="tva_intracomm" class="form-label">{{ __('forms.tva_intracommunautaire') }}</label>
                    <input type="text" class="form-control" id="tva_intracomm" name="tva_intracomm" required>
                </div>
                <div class="mb-3">
                    <label for="type_societe" class="form-label">{{ __('forms.type_societe') }}</label>
                    <input type="text" class="form-control" id="type_societe" name="type_societe" required>
                </div>
                <div class="mb-3">
                    <label for="rcs" class="form-label">{{ __('forms.rcs') }}</label>
                    <input type="text" class="form-control" id="rcs" name="rcs" required>
                </div>
                <div class="mb-3">
                    <label for="naf" class="form-label">{{ __('forms.naf') }}</label>
                    <input type="text" class="form-control" id="naf" name="naf" required>
                </div>
                <div class="mb-3">
                    <label for="agrement" class="form-label">{{ __('forms.agrement') }}</label>
                    <input type="text" class="form-control" id="agrement" name="agrement" required>
                </div>
                <input type="hidden" name="main_logo" id="main_logo_input">
                <button type="submit" class="btn btn-primary">{{ __('forms.submit') }}</button>
            </form>
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
        maxFilesize: 2, // MB
        acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf,.heic",
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function(file, response) {
            // Update the hidden input field with the path of the uploaded file
            document.getElementById('main_logo_input').value = response.file_path;
        }
    });
</script>
@endsection
