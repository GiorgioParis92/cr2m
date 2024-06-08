<div>
    <div class="row">
        <div class="col-12">
            <div class="card form-register">
                <div class="card-header pb-0 clearfix">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">
                                <b>{{ $dossier['beneficiaire']['nom'] }}
                                    {{ $dossier['beneficiaire']['prenom'] }}</b><br />
                                {{ strtoupper_extended($dossier['beneficiaire']['adresse'] . ' ' . $dossier['beneficiaire']['cp'] . ' ' . $dossier['beneficiaire']['ville']) }}<br />
                            </h5>

                            <h6 class="mb-0">
                                <b>Tél : {{ $dossier['beneficiaire']['telephone'] }}</b> -
                                Email : {{ $dossier['beneficiaire']['email'] }}<br />
                            </h6>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <div class="btn btn-primary">{{ $dossier['fiche']['fiche_name'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <div class="card form-register">
                <div class="steps clearfix">
                    <ul role="tablist" id="etapeTabs">
                        @foreach ($etapes as $index => $e)
                            @php
                                $isActive = false;
                                $isCurrent = false;
                                if ($e->etape_number <= $dossier['etape_number']) {
                                    $isActive = true;
                                }
                                if ($e->etape_number == $dossier['etape_number']) {
                                    $isCurrent = true;
                                }
                            @endphp

                            <li @if ($isActive) wire:click="setTab({{ $e->etape_number }})" @endif
                                role="tab" aria-disabled="false"
                                class="nav-link {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }}"
                                aria-selected="true">
                                <a id="form-total-t-0" href="#form-total-h-0" aria-controls="form-total-p-0">
                                    <span class="current-info audible nav-link"></span>
                                    <div class="title">
                                        <span class="step-icon">{{ $e->etape_number }}</span>
                                        <span class="step-text">
                                            {{ strtoupper_extended($e->etape_desc) }}
                                            <small>
                                                @if ($dossier->etape_number == $e->etape_number)
                                                    <p>Status: {{ $dossier->status->status_name }}</p>
                                                @endif
                                            </small>
                                        </span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body px-0 pb-0">
                    @if (isset($tab))
                        <div class="row">
                            <div class="col-lg-4">
                                <h3 class="border-bottom border-gray pb-2">{{ $etape_display['etape_desc'] }}</h3>
                            </div>
                            {{-- Uncomment if needed
                            @if ($etape_display->etape_number == $dossier->etape_number && $dossier->status->status_name != 'Refusé')
                                <div class="col-lg-6">
                                    <a class="btn btn-primary" href="{{ route('dossiers.next_step', $dossier->id) }}">Valider l'étape</a>
                                </div>
                            @endif --}}
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
                                                    @if ($form_handler->form->etape_number == $tab && $form_handler->form->type == 'form')
                                                        <li class="nav-item">
                                                            <a wire:click="display_form({{ $form_handler->form->id }})"
                                                                class="nav-link mb-0 px-0 py-1 {{ $form_handler->form->id == $form_id ? 'active' : '' }}">
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
                                <div class="card">
                                    <div class="card-header pb-0 p-3">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-2">Documents</h6>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table align-items-center">
                                            <tbody>
                                                @foreach ($forms_configs as $index => $form_handler)
                                                    @if ($form_handler->form->etape_number == $tab && $form_handler->form->type == 'document')
                                                        {!! $form_handler->render([]) !!} <!-- Render without error array -->
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card form-register container mt-5">
                @if (isset($form_id))
                    <div class="row">
                        <div class="">

                            @php $form=$forms_configs[$form_id] @endphp
                            <form
                                action="{{ route('form.save', ['dossierId' => $dossier->id, 'form_id' => $form->form->id]) }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="form_id" value="{{ $form->form->id }}">
                                <input type="hidden" name="dossier_id" value="{{ $dossier->id }}">


                                {!! $form->render([]) !!}


                                <div class="row">
                                    <div class="form-group">
                                        <button class="btn btn-secondary" type="submit">Enregistrer</button>
                                    </div>
                                </div>
                            </form>
                        </div>


                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
       document.addEventListener('DOMContentLoaded', function () {
        // Listen for the event from Livewire
        Livewire.on('pdfModalShow', (imgSrc) => {
            alert(imgSrc);
            // Show the modal with the PDF
            $('#pdfModal').css('display', 'block');
            $('#pdfModal').modal('show');
            $('#pdfFrame').attr('src', imgSrc);
        });
    });
</script>
