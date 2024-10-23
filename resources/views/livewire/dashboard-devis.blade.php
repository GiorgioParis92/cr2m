
            <div wire:poll="refresh" class="card mb-4">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="">
                            <div class=" ">
                                <div class="card-header pb-0 p-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-2">
                                            @if(auth()->user()->client_id>0 && auth()->user()->client->type_client==3)
                                            Devis à déposer
                                            @else
                                            En attente des devis installateurs  ({{count($liste)}})
                                            @endif

                                        </h6>
                                    </div>
                                </div>
                                <div class="table-responsive no-overflow">
                                    @foreach ($liste as $dossier)
                                        <div class="row">
                                            <div class="col-4">
            
                                                <div class="ms-4">
                                                    <h6 class="text-sm mb-0">
            
                                                        <a target="_blank" href="{{ route('dossiers.show', $dossier->folder) }}">
                                                            {{ $dossier->beneficiaire->nom }}
            
                                                            {{ $dossier->beneficiaire->prenom }}
                                                        </a>
                                                    </h6>
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $dossier->beneficiaire->numero_voie }}
                                                        {{ $dossier->beneficiaire->adresse }}<br />{{ $dossier->beneficiaire->cp }}
                                                        {{ $dossier->beneficiaire->ville }}</p>
                                                </div>
                                            </div>
                                            <div class="text-center col-4">
                                                <div style="">
                                                    <a target="_blank" href="{{ route('dossiers.show', $dossier->folder) }}">
                                                        <div style="" class="btn btn-success">
                                                            <span
                                                                class="badge badge-primary">{{ $dossier->etape->etape_icon }}</span>
            
                                                            {{ $dossier->etape->etape_desc }}
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="text-center col-4">
                                                <span>
                                                    <a target="_blank" href="{{ route('dossiers.show', $dossier->folder) }}">
                                                        <div class="btn btn-{{ $dossier->status->status_style }}">
            
                                                            {{ $dossier->status->status_desc }}
            
                                                        </div>
                                                    </a>
            
                                                </span>
                                            </div>
                                        </div>
                                        <hr>
                                    @endforeach
            
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

