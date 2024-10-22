
    <div wire:poll="refresh" class="card mb-4">
        <div class="card-body p-3">
            <div class="row">
                <div class="">
                    <div class=" ">
                        <div class="card-header pb-0 p-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-2">
                                    A vérifier ({{count($liste)}})

                                </h6>
                            </div>
                        </div>
                        <div class="table-responsive no-overflow">
                            <table class="table align-items-center ">
                                <tbody>
                                    @foreach ($liste as $dossier)
                                        <tr>
                                            <td class="w-30">
                                                <div class="d-flex px-2 py-1 align-items-center">

                                                    <div class="ms-4">
                                                        <h6 class="text-sm mb-0">
                                                         
                                                            {{ $dossier->beneficiaire->nom }}
                                                            {{ $dossier->beneficiaire->prenom }}</h6>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ $dossier->beneficiaire->numero_voie }}
                                                            {{ $dossier->beneficiaire->adresse }}<br />{{ $dossier->beneficiaire->cp }}
                                                            {{ $dossier->beneficiaire->ville }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div style="position: relative !important;left: -50px;">
                                                        <a target="_blank"
                                                            href="{{ route('dossiers.show', $dossier->folder) }}">
                                                            <span  class="badge badge-primary badge_button">{{ $dossier->etape->etape_icon }}</span>
                                                            <div style="margin-top: 13px; max-width: 80px; text-wrap: wrap; font-size: 9px; padding: 8px !important; background-size: 0; padding-top: 13px !important; width: 100%; max-width: 100%;"
                                                                class="btn btn-success">
                                                                {{ $dossier->etape->etape_desc }}
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <span>
                                                        <a target="_blank"
                                                            href="{{ route('dossiers.show', $dossier->folder) }}">
                                                            <div class="btn btn-{{ $dossier->status->status_style }}">

                                                                {{ $dossier->status->status_desc }}

                                                            </div>
                                                        </a>

                                                    </span>
                                                </div>
                                            </td>
                                        
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

