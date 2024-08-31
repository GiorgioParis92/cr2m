<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('frontend/assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('frontend/assets/img/favicon.ico') }}">
    <title>
        GENIUS MARKET
    </title>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('frontend/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('frontend/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="{{ asset('frontend/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <link href="{{ asset('frontend/assets/css/custom_css.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('frontend/assets/css/soft-ui-dashboard.css?v=1.0.7') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/dropzone.min.css" rel="stylesheet">
    <link id="pagestyle" href="{{ asset('frontend/assets/css/jquery-radiocharm.css') }}" rel="stylesheet" />
    <script src="{{ asset('frontend/assets/js/jquery-radiocharm.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/bootstrap/main.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.flash.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    {{-- <script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzcaFvxwi1XLyRHmPRnlKO4zcJXPOT5gM&loading=async&callback=initMap"></script> --}}
    @livewireStyles
    @include('partials.css_variables')
</head>

<body class="g-sidenav-show bg-gray-100">
    {{-- @include('frontend.sidebar')  --}}
    <main class="main-content position-relative  h-100 border-radius-lg">
        @include('frontend.navbar')
        <div class="pt-5">
            @yield('content')
        </div>
        @include('frontend.footer')
    </main>
    <div id="pdfModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <iframe id="pdfFrame" frameborder="0"></iframe>
        </div>
    </div>
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="" id="imageInModal" class="img-fluid" alt="Image">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="calendar_modal" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog" style="min-width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Calendrier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Title:</strong> <span id="eventTitle"></span></p>
                    <p><strong>Description:</strong> <span id="eventDescription"></span></p>
                    <p><strong>Start:</strong> <span id="eventStart"></span></p>
                    <p><strong>End:</strong> <span id="eventEnd"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rdv_modal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="min-width: 50%;">
            <div class="modal-content" style="height: auto; min-height: auto !important;">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Rdv</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <input class="form-control" type="hidden" id="rdv_id" name="rdv_id">
                            <input class="form-control" type="hidden" id="rdv_dossier_id" name="dossier_id">
                            <input class="form-control" type="hidden" id="rdv_client_id" name="client_id">
                            <input class="form-control" type="hidden" id="rdv_telephone" name="telephone">
                            <input class="form-control" type="hidden" id="rdv_telephone_2" name="telephone_2">
                            <input class="form-control" type="hidden" id="rdv_email" name="email">
                            <input class="form-control" type="hidden" id="rdv_nom" name="nom">
                            <input class="form-control" type="hidden" id="rdv_prenom" name="prenom">
                            <input class="form-control" type="hidden" id="rdv_adresse" name="adresse">
                            <input class="form-control" type="hidden" id="rdv_cp" name="cp">
                            <input class="form-control" type="hidden" id="rdv_ville" name="ville">
                            <input class="form-control" type="hidden" id="rdv_lat" name="lat">
                            <input class="form-control" type="hidden" id="rdv_lng" name="lng">
                            <input class="form-control" type="hidden" id="rdv_type_rdv" name="type_rdv">
                            <label>Date</label>
                            <input class="form-control datepicker" type="text" id="rdv_french_date" name="date_rdv">
                        </div>
                        <div class="form-group" id="new_day" style="display:none">
                             <label>Date de fin</label>
                            <input class="form-control datepicker" type="text" id="rdv_french_date_end" name="date_rdv_end">
                        </div>
                        <div class="form-group" id="hour_group">
                            <label>Heure</label>
                            <div class="row">
                                <div class="col-6">
                                    <select class="form-control" id="rdv_hour" name="hour">
                                        @for ($hour = 0; $hour < 24; $hour++) <option value="{{ sprintf('%02d', $hour) }}">{{ sprintf('%02d', $hour) }}</option>
                                            @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="form-control" id="rdv_minute" name="minute">
                                        @for ($minute = 0; $minute < 60; $minute +=5) <option value="{{ sprintf('%02d', $minute) }}">{{ sprintf('%02d', $minute) }}</option>
                                            @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if(isset($auditeurs))
                        <div class="form-group">
                            <label>Auditeur</label>
                            <select class="form-control" id="rdv_user_id" name="user_id">
                                <option value="">Choisir un auditeur</option>
                                @foreach ($auditeurs as $auditeur)
                                <option value="{{ $auditeur->id }}">{{ $auditeur->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if(isset($rdv_status))
                        <div class="form-group">
                            <label>Statut</label>
                            <select class="form-control" id="rdv_status" name="status">
                                <option value="">Changer de status</option>
                                @foreach ($rdv_status as $status)
                                <option value="{{ $status->id }}">{{ $status->rdv_desc }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="form-group">
                            <label>Observations</label>
                            <textarea class="form-control" name="observations" id="rdv_observations"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div id="save_rdv" class="btn btn-primary">Enregistrer</div>
                    <button type="button" class="btn btn-secondary close_button" data-dismiss="modal">Fermer</button>
                    
                    @if(auth()->user()->id==1)    
                    <button type="button" class="btn btn-info" data-toggle="modal" id="open_auto_planification" data-target="#auto_planification_modal">Planification Auto</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="auto_planification_modal" tabindex="-1" aria-labelledby="schedulingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="schedulingModalLabel">Options de Planification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form id="schedulingForm" method="GET" action="..">
                        <div>
                            <label for="dateTime" class="form-label">Sélectionner la date du RDV</label>
                            <input type="date" class="form-control" id="dateTime" name="dateTime" value="auto">
                        </div>
                        <div>
                            <label for="inspectorSelect" class="form-label">Sélectionner l'Inspecteur</label>
                            <select class="form-select" id="inspectorSelect" name="inspector">
                                <option value="auto" selected>Automatique</option>
                                @if(isset($auditeurs))
                                @foreach ($auditeurs as $auditeur)
                                <option value="{{ $auditeur->id }}">{{ $auditeur->name }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label for="rdvTime" class="form-label">Heure de Rendez-vous (HH:mm)</label>
                            <input type="time" class="form-control" id="rdvTime" name="rdvTime" value="auto">
                            <div class="form-text">Laissez "--:--" pour une attribution automatique de l'heure</div>
                        </div>
                        <div>
                            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedOptions" aria-expanded="false" aria-controls="advancedOptions">
                                Options Avancées
                            </button>
                        </div>
                        <div class="collapse" id="advancedOptions">
                            <div class="card card-body">
                                <p class="text-muted">Ces options permettent un contrôle précis de la planification des visites. Ajustez-les avec précaution pour optimiser les itinéraires.</p>
                                <div>
                                    <label for="startingHourMin" class="form-label">Heure de début minimale</label>
                                    <input type="time" class="form-control" id="startingHourMin" name="startingHourMin" value="05:20">
                                    <div class="form-text">Par défaut 05:20. C'est la première heure possible pour commencer les visites.</div>
                                </div>
                                <div>
                                    <label for="startingHourMax" class="form-label">Heure de début maximale</label>
                                    <input type="time" class="form-control" id="startingHourMax" name="startingHourMax" value="10:00">
                                    <div class="form-text">Par défaut 10:00. Les visites ne peuvent pas commencer après cette heure.</div>
                                </div>
                                <div>
                                    <label for="hoursMinForUnknown" class="form-label">Heure minimale pour les visites à horaire inconnu</label>
                                    <select class="form-select" id="hoursMinForUnknown" name="hoursMinForUnknown">
                                        <option value="0">00:00</option>
                                        <option value="1">01:00</option>
                                        <option value="2">02:00</option>
                                        <option value="3">03:00</option>
                                        <option value="4">04:00</option>
                                        <option value="5">05:00</option>
                                        <option value="6">06:00</option>
                                        <option value="7">07:00</option>
                                        <option value="8">08:00</option>
                                        <option value="9">09:00</option>
                                        <option value="10">10:00</option>
                                        <option value="11" selected>11:00</option>
                                        <option value="12">12:00</option>
                                        <option value="13">13:00</option>
                                        <option value="14">14:00</option>
                                        <option value="15">15:00</option>
                                        <option value="16">16:00</option>
                                        <option value="17">17:00</option>
                                        <option value="18">18:00</option>
                                        <option value="19">19:00</option>
                                        <option value="20">20:00</option>
                                        <option value="21">21:00</option>
                                        <option value="22">22:00</option>
                                        <option value="23">23:00</option>
                                    </select>
                                    <div class="form-text">Par défaut 11:00. Les visites sans horaire fixe seront planifiées au plus tôt à cette heure.</div>
                                </div>
                                <div>
                                    <label for="hoursMaxForUnknown" class="form-label">Heure maximale pour les visites à horaire inconnu</label>
                                    <select class="form-select" id="hoursMaxForUnknown" name="hoursMaxForUnknown">
                                        <option value="0">00:00</option>
                                        <option value="1">01:00</option>
                                        <option value="2">02:00</option>
                                        <option value="3">03:00</option>
                                        <option value="4">04:00</option>
                                        <option value="5">05:00</option>
                                        <option value="6">06:00</option>
                                        <option value="7">07:00</option>
                                        <option value="8">08:00</option>
                                        <option value="9">09:00</option>
                                        <option value="10">10:00</option>
                                        <option value="11">11:00</option>
                                        <option value="12">12:00</option>
                                        <option value="13">13:00</option>
                                        <option value="14">14:00</option>
                                        <option value="15">15:00</option>
                                        <option value="16">16:00</option>
                                        <option value="17">17:00</option>
                                        <option value="18" selected>18:00</option>
                                        <option value="19">19:00</option>
                                        <option value="20">20:00</option>
                                        <option value="21">21:00</option>
                                        <option value="22">22:00</option>
                                        <option value="23">23:00</option>
                                    </select>
                                    <div class="form-text">Par défaut 18:00. Les visites sans horaire fixe seront planifiées au plus tard à cette heure.</div>
                                </div>
                                <div>
                                    <label for="hoursDividerAddForUnknown" class="form-label">Diviseur pour l'ajout d'heures pour les visites inconnues</label>
                                    <input type="number" class="form-control" id="hoursDividerAddForUnknown" name="hoursDividerAddForUnknown" value="2" min="1">
                                    <div class="form-text">Par défaut, cette valeur est 2. Par exemple, pour une heure de 15h, si la valeur est 2, les créneaux seront 15h et 15h30. Si la valeur est 4, les créneaux seront 15h, 15h15, 15h30 et 15h45. Avec une valeur de 6, les créneaux seront 15h, 15h10, 15h20, etc. Cette option permet de répartir les visites à horaire inconnu sur la plage horaire définie.</div>
                                </div>
                                <div>
                                    <label for="timeWindowSize" class="form-label">Taille de la fenêtre de temps</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="timeWindowSize" name="timeWindowSize" value="60" min="1">
                                        <span class="input-group-text">minutes</span>
                                    </div>
                                    <div class="form-text">Par défaut 60 minutes. Définit la flexibilité autour de l'heure prévue pour chaque visite.</div>
                                </div>
                                <div>
                                    <label for="maxIterationToSolve" class="form-label">Nombre maximum d'itérations pour résoudre le VRP</label>
                                    <input type="number" class="form-control" id="maxIterationToSolve" name="maxIterationToSolve" value="25" min="1">
                                    <div class="form-text">Par défaut 25. Augmentez ce nombre pour des solutions potentiellement meilleures, mais avec un temps de calcul plus long.</div>
                                </div>
                                <div class="alert alert-info" role="alert">
                                    <h4 class="alert-heading">Informations supplémentaires</h4>
                                    <p>Le VRP (Vehicle Routing Problem) est un problème d'optimisation complexe. Les paramètres ci-dessus influencent directement la qualité et l'efficacité des itinéraires générés.</p>
                                    <hr>
                                    <p class="mb-0">Ajustez ces valeurs avec précaution. Des valeurs extrêmes peuvent conduire à des temps de calcul très longs ou à des solutions sous-optimales.</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <div id="predict_rdv" class="btn btn-primary">Predire</div>
                </div>
            </div>
        </div>
    </div>
    {{-- @include('frontend.fixed-plugin') --}}
    <!-- Core JS Files -->
    {{-- <script src="{{ asset('frontend/assets/js/core/popper.min.js') }}"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="{{ asset('frontend/assets/js/core/bootstrap.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('frontend/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/plugins/smooth-scrollbar.min.js') }}"></script> --}}
    <script src="{{ asset('frontend/assets/js/plugins/chartjs.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/dropzone.min.js"></script>
    <!-- Bootstrap CSS -->
    <script src="{{ asset('frontend/assets/js/soft-ui-dashboard.min.js?v=1.0.7') }}"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/i18n/datepicker-fr.js"></script>

    @yield('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {



            let isTouchDevice = 'ontouchstart' in document.documentElement;
            let submenuTimeout;

            document.querySelectorAll('.navbar .nav-item').forEach(function(everyitem) {
                let el_link = everyitem.querySelector('a[data-bs-toggle]');
                if (el_link != null) {
                    let nextEl = el_link.nextElementSibling;

                    everyitem.addEventListener('mouseenter', function(e) {
                        if (!isTouchDevice) {
                            el_link.classList.add('show');
                            nextEl.classList.add('show');
                        }
                    });

                    everyitem.addEventListener('mouseleave', function(e) {
                        if (!isTouchDevice) {
                            submenuTimeout = setTimeout(function() {
                                el_link.classList.remove('show');
                                nextEl.classList.remove('show');
                            }, 100); // delay of 100ms before hiding
                        }
                    });

                    nextEl.addEventListener('mouseenter', function(e) {
                        if (!isTouchDevice) {
                            clearTimeout(submenuTimeout); // prevent hiding if submenu is hovered
                            el_link.classList.add('show');
                            nextEl.classList.add('show');
                        }
                    });

                    nextEl.addEventListener('mouseleave', function(e) {
                        if (!isTouchDevice) {
                            submenuTimeout = setTimeout(function() {
                                el_link.classList.remove('show');
                                nextEl.classList.remove('show');
                            }, 100); // delay of 100ms before hiding
                        }
                    });

                    if (isTouchDevice) {
                        el_link.addEventListener('click', function(e) {
                            e.preventDefault(); // prevent the default action
                            el_link.classList.toggle('show');
                            nextEl.classList.toggle('show');
                        });

                        nextEl.addEventListener('click', function(e) {
                            e.stopPropagation(); // prevent hiding when submenu is tapped
                        });

                        document.addEventListener('click', function(e) {
                            // Hide submenu if clicked outside
                            if (!everyitem.contains(e.target)) {
                                el_link.classList.remove('show');
                                nextEl.classList.remove('show');
                            }
                        });
                    }
                }
            });

            // Handle navbar-toggler for mobile devices
            let navbarToggler = document.querySelector('.navbar-toggler');
            let mainNav = document.querySelector('#main_nav');

            navbarToggler.addEventListener('click', function(e) {
                mainNav.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!navbarToggler.contains(e.target) && !mainNav.contains(e.target)) {
                    mainNav.classList.remove('show');
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            if (window.innerWidth > 992) {
                let submenuTimeout;

                document.querySelectorAll('.navbar .nav-item').forEach(function(everyitem) {
                    let el_link = everyitem.querySelector('a[data-bs-toggle]');
                    if (el_link != null) {
                        let nextEl = el_link.nextElementSibling;

                        everyitem.addEventListener('mouseenter', function(e) {
                            el_link.classList.add('show');
                            nextEl.classList.add('show');
                        });

                        everyitem.addEventListener('mouseleave', function(e) {
                            submenuTimeout = setTimeout(function() {
                                el_link.classList.remove('show');
                                nextEl.classList.remove('show');
                            }, 100); // delay of 100ms before hiding
                        });

                        nextEl.addEventListener('mouseenter', function(e) {
                            clearTimeout(submenuTimeout); // prevent hiding if submenu is hovered
                            el_link.classList.add('show');
                            nextEl.classList.add('show');
                        });

                        nextEl.addEventListener('mouseleave', function(e) {
                            submenuTimeout = setTimeout(function() {
                                el_link.classList.remove('show');
                                nextEl.classList.remove('show');
                            }, 100); // delay of 100ms before hiding
                        });
                    }
                });
            }
        });


        $(document).ready(function() {
            $('#open_auto_planification').on('click', function() {
                $('#rdv_modal').modal('hide');
                $('#auto_planification_modal').modal('show');
            });

            $('#auto_planification_modal').on('hidden.bs.modal', function() {
                $('#rdv_modal').modal('show');
            });

            $('.close').on('click', function() {

                $('.modal').modal('hide');
            });
            $('.close_button').on('click', function() {

                $('.modal').modal('hide');
            });
            $('select').each(function() {
                if ($(this).closest('.modal').length === 0) {
                    $(this).select2();
                }
            });
            $('.datepicker').datepicker({
                language: 'fr',
                dateFormat: 'dd/mm/yy', // See format options on parseDate

            });

            jQuery(function($){
	$.datepicker.regional['fr'] = {
		closeText: 'Fermer',
		prevText: '&#x3c;Préc',
		nextText: 'Suiv&#x3e;',
		currentText: 'Aujourd\'hui',
		monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
		'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
		monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
		'Jul','Aou','Sep','Oct','Nov','Dec'],
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
		weekHeader: 'Sm',
		dateFormat: 'dd-mm-yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: '',
		
		numberOfMonths: 1,
		showButtonPanel: true
		};
	$.datepicker.setDefaults($.datepicker.regional['fr']);
});
            $('.datatable').DataTable()


            $('#save_rdv').click(function() {
                alert('ok')

                // Collect form data
                var formData = {
                    rdv_id: $('#rdv_id').val(),
                    date_rdv: $('#rdv_french_date').val(),
                    hour: $('#rdv_hour').val(),
                    minute: $('#rdv_minute').val(),
                    user_id: $('#rdv_user_id').val(),
                    type_rdv: $('#rdv_type_rdv').val(),
                    nom: $('#rdv_nom').val(),
                    prenom: $('#rdv_prenom').val(),
                    adresse: $('#rdv_adresse').val(),
                    cp: $('#rdv_cp').val(),
                    ville: $('#rdv_ville').val(),
                    telephone: $('#rdv_telephone').val(),
                    telephone_2: $('#rdv_telephone_2').val(),
                    email: $('#rdv_email').val(),
                    dossier_id: $('#rdv_dossier_id').val(),
                    status: $('#rdv_status').val(),
                    observations: $('#rdv_observations').val(),
                    client_id: $('#rdv_client_id').val(),
                    lat: $('#rdv_lat').val(),
                    lng: $('#rdv_lng').val(),
                    // Include additional fields if present
                };
                // Perform AJAX call
                updateRdv(formData);
            });

            function get_rdv_from_api(parameters) {
                var token = $('meta[name="api-token"]').attr('content');

                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '/api/rdvs',
                        type: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        data: parameters,
                        success: function(data) {
                            resolve(data);
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error: ' + status + error);
                            reject(error);
                        }
                    });
                });
            }

            async function get_inspector_rdv(inspector_id, date) {
                try {
                    const api_data = await get_rdv_from_api({
                        user_id: inspector_id,
                        start_date: date,
                        end_date: date,
                    });

                    if (!api_data.length) {
                        return null;
                    }

                    return api_data;
                } catch (error) {
                    return null;
                }
            }

            async function convert_rdv_data_vrp() {
                let final_data = {
                    "address_to_visit": [],
                    "inspectors": {},
                    "forced_visits": {}
                };

                const promises = $("#inspectorSelect option").map(async function() {
                    let inspector_id = $(this).val();
                    if (inspector_id !== 'auto') {
                        final_data["inspectors"][inspector_id] = await get_inspector_position(inspector_id, $('#dateTime').val());
                        inspector_rdv = await get_inspector_rdv(inspector_id, $('#dateTime').val());
                        if (inspector_rdv !== null) {
                            for (rdv in inspector_rdv) {
                                final_data["address_to_visit"].push({
                                    "loc": rdv["adresse"] + " " + rdv["ville"] + " " + rdv["cp"],
                                    "time": "2000-06-06 " + rdv["hour"] + ":" + rdv["minute"] + ":00",
                                    "duration": "1:00"
                                });
                                final_data["forced_visits"][inspector_id] = final_data["address_to_visit"].length - 1
                            }
                        }
                    }
                }).get();

                await Promise.all(promises);

                return final_data;
            }

            function getDayBefore(dateString) {
                const [year, month, day] = dateString.split('-').map(Number);
                const date = new Date(year, month - 1, day);

                date.setDate(date.getDate() - 1);

                const newDay = String(date.getDate()).padStart(2, '0');
                const newMonth = String(date.getMonth() + 1).padStart(2, '0');
                const newYear = date.getFullYear();

                return {
                    date: `${newYear}-${newMonth}-${newDay}`,
                    dayIndex: date.getDay()
                }
            }

            function getLastRdvOfTheDay(rdvArray) {
                if (!rdvArray || rdvArray.length === 0) {
                    return null;
                }

                let lastRdv = rdvArray[0];

                rdvArray.forEach(rdv => {
                    if (new Date(rdv.date_rdv) > new Date(lastRdv.date_rdv)) {
                        lastRdv = rdv;
                    }
                });

                return lastRdv;
            }

            async function get_inspector_position(inspector_id, date) {
                const inspector_address = "28 Rue de Solférino 92100 Boulogne-Billancourt"; // TODO ADRESSE INSPECTEUR
                const date_info = getDayBefore(date);

                if (date_info['dayIndex'] == 0) {
                    return inspector_address;
                }

                try {
                    const api_data = await get_rdv_from_api({
                        user_id: inspector_id,
                        start_date: date_info["date"],
                        end_date: date_info["date"],
                    });

                    if (!api_data.length) {
                        return inspector_address;
                    }

                    const lastRdv = await getLastRdvOfTheDay(api_data);
                    return lastRdv["adresse"] + " " + lastRdv["ville"] + " " + lastRdv["cp"];
                } catch (error) {
                    return inspector_address;
                }
            }


            $('#predict_rdv').click(function() {
                var formData = {
                    dateTime: $('#dateTime').val(),
                    inspector: $('#inspectorSelect').val() === 'auto' ? null : $('#inspectorSelect').val(),
                    rdvTime: $('#rdvTime').val() === '' ? null : $('#rdvTime').val(),
                    startingHourMin: $('#startingHourMin').val(),
                    startingHourMax: $('#startingHourMax').val(),
                    hoursMinForUnknown: $('#hoursMinForUnknown').val(),
                    hoursMaxForUnknown: $('#hoursMaxForUnknown').val(),
                    hoursDividerAddForUnknown: $('#hoursDividerAddForUnknown').val(),
                    timeWindowSize: $('#timeWindowSize').val(),
                    maxIterationToSolve: $('#maxIterationToSolve').val()
                };
                if ($('#dateTime').val() == '') {
                    $('#dateTime').addClass('is-invalid');
                    return;
                } else {
                    $('#dateTime').removeClass('is-invalid');
                }

                let converted_data = convert_rdv_data_vrp();
                console.log(converted_data);

            });

            function updateRdv(data) {
                $.ajax({
                    url: '/api/rdvs/update', // Adjust the URL to match your endpoint
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            // Close modal and refresh parent page or redirect
                            $('#rdv_modal').modal('hide');
                            location.reload(); // Or redirect to previous page
                        } else {
                            // Handle error
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        // Handle AJAX error
                        console.log(xhr)
                        alert('An error occurred: ' + xhr.message);
                    }
                });
            }
            $(document).on('click', '.fillPDF', function(event) {
                var form_id = $(this).data('form_id');
                var dossier_id = $(this).data('dossier_id');
                var name = $(this).data('name');

                $.ajax({
                    url: '/api/fill-pdf',
                    type: 'GET',
                    data: {
                        dossier_id: dossier_id,
                        form_id: form_id,
                        name: name
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {

                        if (response.file_path) {
                            $('#pdfFrame').attr('src', response.file_path);
                            $('#pdfModal').css('display', 'block');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error generating PDF:', error);
                    }
                });
            });
            $('.generatePdfButton').click(function() {
                var template = $(this).data('template'); // Get the template from data attribute
                var form_id = $(this).data('form_id'); // Get the template from data attribute
                var dossier_id = $(this).data('dossier_id'); // Get the dossier ID from data attribute

                $.ajax({
                    url: '/api/generate-pdf', // Adjust this URL to your actual API endpoint
                    type: 'GET',
                    data: {
                        dossier_id: dossier_id,
                        form_id: form_id,
                        template: template
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Include CSRF token if using Laravel's CSRF protection
                    },
                    success: function(response) {
                        if (response.file_path) {
                            // Display the PDF in an iframe if a file path is returned
                            $('#pdfFrame').attr('src', response.file_path);
                            $('#pdfModal').css('display', 'block');
                        } else {
                            // Handle the response where the PDF content is returned directly
                            var blob = new Blob([response], {
                                type: 'application/pdf'
                            });
                            var url = URL.createObjectURL(blob);
                            var link = document.createElement('a');
                            link.href = url;
                            link.download = 'document.pdf';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error generating PDF:', error);
                    }
                });
            });
            $('.close').on('click', function() {
                $('#pdfModal').css('display', 'none');
                $('#imageModal').modal('hide');
                $('#pdfModal').modal('hide');
            });

            // Close the modal when the user clicks anywhere outside of the modal
            $(window).on('click', function(event) {
                if (event.target == $('#pdfModal')[0]) {
                    $('#pdfModal').css('display', 'none');
                    $('#imageModal').modal('hide');
                    $('#pdfModal').modal('hide');
                }
            });

            $('.pdfModal').click(function() {

                $('#pdfFrame').attr('src', '');

                var imgSrc = $(this).data('img-src');
                imgSrc += `?time=${new Date().getTime()}`;
                $('#pdfFrame').attr('src', imgSrc);
                $('#pdfModal').css('display', 'block');
            });

            // $(document).on('click', '.imageModal', function(event) {
            //     $('#imageInModal').attr('src', '');
            //     var imgSrc = $(this).data('img-src');
            //     imgSrc += `?time=${new Date().getTime()}`;

            //     $('#imageInModal').attr('src', imgSrc);
            //     $('#imageModal').modal('show');
            // });
        });

        $(document).ready(function() {




            $('#global').on('keyup', function() {
                let query = $(this).val();
                if (query == '') {
                    $('#search-results').hide()
                }
                console.log(query)
                if (query.length > 2) {
                    $.ajax({
                        url: '{{ route("search") }}',
                        type: 'GET',
                        data: {
                            query: query
                        },
                        success: function(data) {
                            let resultsContainer = $('#search-results');
                            resultsContainer.empty();

                            $.each(data, function(type, items) {
                                if (items.length > 0) {

                                    $.each(items, function(index, item) {
                                        resultsContainer.append('<div><a href="' + item.url + '">' + (item.beneficiaire.nom + ' ' + item.beneficiaire.prenom) + '</a></div>');
                                    });
                                    $('#search-results').show()
                                } else {
                                    $('#search-results').hide()
                                }
                            });

                        }
                    });
                }
            });
        });

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    @livewireScripts
</body>

</html>