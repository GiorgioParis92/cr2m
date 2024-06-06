<div id="step-{{ $etape->etape_number }}" class="tab-pane step-content"
    style="{{ $isActive ? 'display: block;' : 'display: none;' }}">

    <div class="row">

        <div class="col-lg-4">
            <h3 class="border-bottom border-gray pb-2">{{ $etape->etape_desc }}</h3>
        </div>
        @dd($dossier)
        @if ($etape->etape_number == $dossier->etape_number && $dossier->status_id != 15)
            <div class="col-lg-6">
                <a class="btn btn-primary" href="{{ route('dossiers.next_step', $dossier->id) }}">Valider l'étape</a>
            </div>
        @endif
    </div>


    <div class="row">

        <div class="col-lg-6 col-sm-12">
            <div class="card">
                <div class="card-header p-3 pb-0">
                    <h6 class="mb-0">Formulaires</h6>
                </div>
                <div class="card-body border-radius-lg p-3">
                    <div class="nav-wrapper position-relative end-0">
                        <ul class="nav nav-pills nav-fill p-1" role="tablist">


                            @foreach ($forms_configs as $index => $form_handler)
                                @if ($form_handler->form->etape_number == $etape->etape_number && $form_handler->form->type == 'form')
                                    <li class="nav-item">
                                        <a class="nav-link mb-0 px-0 py-1 {{ $index == 0 ? 'active' : '' }}"
                                            id="tab-{{ $form_handler->form->id }}-tab" data-bs-toggle="tab"
                                            href="#tab-{{ $form_handler->form->id }}" role="tab"
                                            aria-controls="tab-{{ $form_handler->form->id }}"
                                            aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                            {{ $form_handler->form->form_title }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach

                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-lg-6">
            <div class="card ">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-2">Documents</h6>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center ">
                        <tbody>

                            @foreach ($forms_configs as $index => $form)
                                @if ($form->form->etape_number == $etape->etape_number && $form->form->type == 'document')
                                    {!! $form->render($errors) !!}
                                @endif
                            @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>


    <div class="container row mt-3">
        <div class="">
            <div class="tab-content">
                @foreach ($forms_configs as $index => $form)
                    @if ($form->form->etape_number == $etape->etape_number && $form->form->type == 'form')
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}   "
                            id="tab-{{ $form->form->id }}" role="tabpanel"
                            aria-labelledby="tab-{{ $form->form->id }}-tab">
                            <div class="row">
                                <div class="">

                                    <form
                                        action="{{ route('form.save', ['dossierId' => $dossier->id, 'form_id' => $form->form->id]) }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="form_id" value="{{ $form->form->id }}">
                                        <input type="hidden" name="dossier_id" value="{{ $dossier->id }}">


                                        {!! $form->render($errors) !!}


                                        <div class="row">
                                            <div class="form-group">
                                                <button class="btn btn-secondary" type="submit">Enregistrer</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>


                            </div>
                        </div>

                        @if ($etape->etape_name == 'planification_mar')
                            @include('calendar')
                        @endif
                    @endif
                @endforeach
            </div>
        </div>



    </div>

</div>


{{-- @includeIf('components.etapes.' . strtolower($etape->etape_name), [
        'etape' => $etape,
        'dossier' => $dossier,
    ]) --}}






@section('scripts')
    <script>
        Dropzone.autoDiscover = false;
        $(document).ready(function() {

            @foreach ($forms_configs as $index => $form)
                @if ($form->form->etape_number == $etape->etape_number && $form->form->type == 'document')
                    @foreach ($form->formData as $key => $value)
                        var dropzone{{ $key }} = new Dropzone("#dropzone-{{ $key }}", {

                            method: 'post',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            paramName: 'file', // The name that will be used to transfer the file
                            sending: function(file, xhr, formData) {
                                formData.append('folder',
                                    'dossiers'); // Ensure folder is sent correctly
                                formData.append('template',
                                    '{{ $key }}'); // Include template name
                            },
                            init: function() {
                                this.on("success", function(file, response) {
                                    console.log('Successfully uploaded:', response);
                                });
                                this.on("error", function(file, response) {
                                    console.log('Upload error:', response);
                                });
                            }
                        });
                    @endforeach
                @endif
            @endforeach











        });
    </script>
@endsection
