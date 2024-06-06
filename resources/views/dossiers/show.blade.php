@extends('layouts.app')

@section('content')
    @php
        $errors = [];
        $is_valid = true;
        if (session('result')) {
            $result = json_decode(session('result'));

            foreach ($result as $key => $value) {
                $errors[$key] = $value;
                if ($value == false) {
                    $is_valid = false;
                }
            }
        }

    @endphp

    <div class="container">
     

        @if (!$is_valid)
            <div class="alert alert-danger">
                Erreur dans le remplissage de votre formulaire
            </div>
        @endif
        @if (session('result') && $is_valid)
            <div class="alert alert-success">
                Données sauvegardées
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card form-register">
                    <div class="card-header pb-0 clearfix">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">
                                    <b>{{ $dossier->beneficiaire->nom }} {{ $dossier->beneficiaire->prenom }}</b><br />
                                    {{ strtoupper_extended($dossier->beneficiaire->adresse . ' ' . $dossier->beneficiaire->cp . ' ' . $dossier->beneficiaire->ville) }}<br />
                                </h5>

                                <h6 class="mb-0">
                                    <b>Tél : {{ $dossier->beneficiaire->telephone }}</b> -
                                    Email : {{ $dossier->beneficiaire->email }}<br />
                                </h6>

                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                                <div class="ms-auto my-auto ">
                                    <div class="btn btn-primary">{{ $dossier->fiche->fiche_name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

                <div class="card form-register">
                    <div class="steps clearfix">
                        <ul role="tablist" id="etapeTabs">

                            @foreach ($etapes as $index => $etape)
                                @php
                                    $isClickable = $etape->etape_number <= $dossier->etape_number;
                                    $isActive =
                                        $etape->etape_number <=
                                        array_search(
                                            $dossier->etape_number,
                                            array_column($etapes->toArray(), 'etape_number'),
                                        );
                                    $isCurrent = $etape->etape_number == $dossier->etape_number;
                                @endphp

                                <li data-index="{{ $etape->etape_number }}" role="tab" aria-disabled="false"
                                    class="nav-link {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }}"
                                    aria-selected="true"><a id="form-total-t-0" href="#form-total-h-0"
                                        aria-controls="form-total-p-0"><span class="current-info audible nav-link"> </span>
                                        <div class="title">
                                            <span class="step-icon">{{ $etape->etape_number }}</span>
                                            <span class="step-text">
                                                {{ strtoupper_extended($etape->etape_desc) }}
                                                <small>
                                                    @if ($dossier->etape_number == $etape->etape_number)
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


                    <div id="smartwizard" class="sw-main sw-theme-arrows ">
                        <ul class="nav nav-tabs step-anchor">

                        </ul>
                        <div class="sw-container tab-content mt-5" style="min-height: 166.45px;">
                            @foreach ($etapes as $index => $etape)
                                @include('dossiers.partials.etape_content', [
                                    'etape' => $etape,
                                    'dossier' => $dossier,
                                    'errors' => $errors,
                                    'isActive' =>
                                        $etape->etape_number ==
                                        array_search(
                                            $dossier->etape_number,
                                            array_column($etapes->toArray(), 'etape_number')),
                                ])
                         
                            @endforeach
                        </div>

                    </div>
                    <div class="card-body px-0 pb-0">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        @if(session('form_id'))
           alert({{session('form_id')}})
        @endif
        
        
            </script>
@endsection
@section('scripts')
    <script>
@if(session('form_id'))
   alert({{session('form_id')}})
@endif


    </script>
@endsection
