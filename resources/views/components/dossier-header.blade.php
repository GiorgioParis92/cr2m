

<div class="card form-register" wire:ignore>
    <div class="card-body pb-0 clearfix">
        <div class="d-lg-flex">
            <div>
                <div class="btn btn-primary" id="copyButton" data-folder="{{ $dossier->folder }}">
                    Réf dossier : {{ $dossier->folder }} (cliquez pour copier la référence)
                </div>

                <h5 class="mb-0">
                    <b>{{ $dossier->beneficiaire->nom }} {{ $dossier->beneficiaire->prenom }}</b><br />
                    {{ strtoupper_extended(($dossier->beneficiaire->numero_voie ?? '') . ' ' . $dossier->beneficiaire->adresse . ' ' . $dossier->beneficiaire->cp . ' ' . $dossier->beneficiaire->ville) }}<br />
                    @if ($dossier->beneficiaire->lat == 0)
                        <span class="invalid-feedback" style="font-size:9px;display:block">Adresse non géolocalisée</span>
                    @endif
                </h5>

                <h6 class="mb-0">
                    <b>Tél : {{ $dossier->beneficiaire->telephone }}</b> -
                    Email : {{ $dossier->beneficiaire->email }}<br />
                </h6>

                <div class="btn bg-primary bg-{{ couleur_menage($dossier->beneficiaire->menage_mpr) }}">
                    {{ strtoupper(texte_menage($dossier->beneficiaire->menage_mpr)) }}
                </div>
                @if (auth()->user()->client_id == 0)
                    <div>
                        @if ($technicien)
                            Technicien RDV MAR 1 : {{ $technicien->user->name ?? '' }}
                        @endif
                    </div>
                @endif
                @isset($dossier->mar)
                    @if (Storage::disk('public')->exists($dossier->mar->main_logo))
                        <img style="max-width: 30%" src="{{ asset('storage/' . $dossier->mar->main_logo) }}" alt="Logo">
                    @endif
                    {{ $dossier->mar->client_title }}
                @endisset
            </div>
            <div class="ms-auto my-auto mt-lg-0 mt-4">
                <div class="ms-auto my-auto">
                    <div class="btn btn-primary">{{ $dossier->fiche->fiche_name }}</div>
                    @if (auth()->user()->client_id == 0)
                        <a href="{{ route('dossiers.delete', ['id' => $dossier->id]) }}" class="btn btn-danger">Supprimer le dossier</a>
                        <form class="form-control" method="get">
                            <label>Installateur</label>
                            <select wire:ignore class="form-control" name="installateur" onchange="this.form.submit()">
                                <option value="">Choisir un installateur</option>
                                @foreach ($installateurs as $installateur)
                                    <option value="{{ $installateur->id }}" {{ $dossier->installateur == $installateur->id ? 'selected' : '' }}>
                                        {{ $installateur->client_title }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
