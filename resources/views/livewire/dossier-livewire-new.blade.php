<div>

    <div wire:loading wire:target="add_row,remove_row,display_form,setTab" class="loader-overlay">
        <!-- Your loader content, e.g., a spinner -->
        <div class="spinner"></div>
    </div>

    <div class="container-fluid my-3 py-3">
        @if (session()->has('message'))
            <div class="alert alert-success" id="flashMessage">
                {{ session('message') }}
            </div>
        @endif
        <div class="row mb-5">
            <div class="col-lg-3">
                <div class="card position-sticky top-1 p-2">

                    <div class="timeline timeline-one-side">
                        @foreach ($etapes as $index => $e)
                            @php
                                $isActive = false;
                                $isCurrent = false;
                                $isTab = false;
                                if ($e['order_column'] <= $dossier['etape']['order_column']) {
                                    $isActive = true;
                                }
                                if (
                                    $e['order_column'] == $dossier['etape']['order_column'] &&
                                    is_user_allowed($e['etape_name']) == true
                                ) {
                                    $isCurrent = true;
                                }

                                if ($e['id'] == $last_etape) {
                                    $isTab = true;
                                }
                                if (is_user_allowed($e['etape_name']) == false) {
                                    $isAllowed = false;
                                } else {
                                    $isAllowed = true;
                                }
                                if (is_user_forbidden($e['etape_name']) == true) {
                                    $isAllowed = false;
                                    $isCurrent = false;
                                }

                            @endphp
                            <div class="pe-auto cursor-pointer timeline-block mb-3 p-3 {{ $isCurrent ? 'bg-primary' : '' }} {{ $tab == $e['id'] ? 'bg-secondary' : '' }}"
                                @if ($isActive && $isAllowed) wire:click="setTab({{ $e['etape_number'] }})" @endif>
                                <span class="timeline-step">
                                    <span>{{ $e['etape_icon'] ?? '' }}</span>
                                </span>

                                <div class="timeline-content">
                                    <h6
                                        class="{{ $tab == $e['id'] ? 'text-white' : 'text-dark' }} text-sm font-weight-bold mb-0">
                                        {{ strtoupper_extended($e['etape_desc']) }}</h6>
                                    @if (!empty($steps) && isset($steps['step_' . $e['etape_number']]))
                                        <p
                                            class="{{ $tab == $e['id'] ? 'text-white' : 'text-secondary' }} font-weight-bold text-xs mt-1 mb-0">
                                            validée le :
                                            {{ format_date($steps['step_' . $e['etape_number']]['meta_value']) ?? '' }}
                                            par {{ $steps['step_' . $e['etape_number']]['user_name'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
            <div class="col-lg-9 mt-lg-0 mt-4">
                <!-- Card Profile -->
                <div class="card card-body" id="profile">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-sm-auto col-4">

                        </div>
                        <div class="col-sm-auto col-8 my-auto">
                            <div class="h-100">
                                <h4 class="mb-1 font-weight-bolder">
                                    {{ $dossier['beneficiaire']['nom'] }} {{ $dossier['beneficiaire']['prenom'] }}
                                </h4>
                                <p class="mb-0 font-weight-bold text-sm">
                                    {{ strtoupper_extended(($dossier['beneficiaire']['numero_voie'] ?? '') . ' ' . $dossier['beneficiaire']['adresse'] . ' ' . $dossier['beneficiaire']['cp'] . ' ' . $dossier['beneficiaire']['ville']) }}
                                </p>
                                <p class="mb-0 font-weight-bold text-sm">
                                    <b>Tél : {{ $dossier['beneficiaire']['telephone'] }}</b> -
                                    Email : {{ $dossier['beneficiaire']['email'] }}<br />
                                </p>
                                <p class="mb-0 font-weight-bold text-sm">
                                <div class="btn btn-primary">{{ $dossier['fiche']['fiche_name'] }}</div>
                                <div
                                    class="btn bg-primary bg-{{ couleur_menage($dossier->beneficiaire->menage_mpr) }}">
                                    {{ strtoupper(texte_menage($dossier['beneficiaire']['menage_mpr'])) }}
                                </div>
                                </p>

                            </div>
                        </div>
                        <div class="col-sm-auto col-4">

                        </div>
                        <div class="col-sm-auto col-8 my-auto">
                            <div>
                                @if (isset($dossier->mar_client))
                                    @if (Storage::disk('public')->exists($dossier->mar_client->main_logo))
                                        <img style="max-width: 150px"
                                            src="{{ asset('storage/' . $dossier->mar_client->main_logo) }}">
                                    @endif
                                    {{ $dossier->mar_client->client_title }}
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-auto ms-sm-auto mt-sm-0 mt-3">

                            <div class="">

                                @if (auth()->user()->client_id == 0 ||
                                        (auth()->user()->client_id != 3 && auth()->user()->type_id != 7 && auth()->user()->type_id != 4))
                                    @if ($dossier['annulation'] != 1)
                                        <a wire:click="toggleDossier({{ $dossier->id }})"
                                            class="btn btn-danger">Annuler le dossier</a>
                                    @else
                                        <a wire:click="toggleDossier({{ $dossier->id }})"
                                            class="btn btn-warning">Rétablir le dossier</a>
                                    @endif
                                @endif
                            </div>
                            <br />
                            <div>
                                <select class="no_select2 form-control" name="installateur"
                                    wire:change="update_installateur($event.target.value)">
                                    <option value="">Choisir un installateur</option>
                                    @foreach ($installateurs as $installateur)
                                        <option @if ($dossier['installateur'] == $installateur['id']) selected @endif
                                            value="{{ $installateur['id'] }}">
                                            {{ $installateur['client_title'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row mt-4">
                    <div class="col-12 col-lg-12">
                        <div class="card ">
                            <div class="card-header pb-0 p-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-2">Documents du dossier</h6>
                                </div>
                            </div>
                            <div class="table-responsive p-4">
                                <x-document-table-component :docs="$docs" :dossier="$dossier" />

                            </div>
                        </div>
                    </div>
                </div>

                @if ($forms)

                    <div class="row">
                        <div class="col-6">
                            <div class="card mt-4" id="basic-info">
                                <div class="card-header">
                                    <h5>Formulaires</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="nav-wrapper position-relative end-0">
                                            <ul class="nav nav-pills nav-fill p-1" role="tablist">
                                        
                                                @foreach ($forms as $form)
                                                    @if ($form->type == 'form')
                                                        <li class="nav-item active" wire:click="set_form({{$form->id}})">
                                                            
                                                            <a wire:click="set_form({{$form->id}})"
                                                                class="nav-link mb-0 px-0 py-1 {{ $form->id == $set_form ? 'active' : '' }}">
                                                                {{ $form->form_title }}

                                      
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card mt-4" id="basic-info">
                                <div class="card-header">
                                    <h5>Documents</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="nav-wrapper position-relative end-0">
                                            <ul class="nav nav-pills nav-fill p-1" role="tablist">
                                                @foreach ($forms as $form)
                                                    @if ($form->type == 'document')
                                                        <li class="nav-item">
                                                            {{ $form->form_title }}
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @endif
                <div class="row">
                    <div class="col-12">
                        <div class="card mt-4" id="basic-info">
                            <div class="card-header">
                                <h5>Titre Formulaire</h5>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                
                                    @if(isset($config))
                                    
                                   
                                    @foreach($config as $conf)
                                        @if(View::exists('livewire.forms.' . $conf->type))

                                        @livewire("forms.{$conf->type}", ['conf' => $conf,'form_id'=>$set_form,'dossier_id'=>$dossier->id], key($conf->id))
                                        
                                    
                                        @else
                                            <p>Component for type "{{ $conf->type }}" not found.</p>
                                        @endif
                                    @endforeach
                            
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>
