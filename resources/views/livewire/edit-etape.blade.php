<div class=" my-5">
    <div class="card">
        <div class="card-header">
            <h2>Modification de l'étape</h2>
            <div>
                <a class="btn btn-sm btn-primary me-2" href="{{ route('etapes-controller') }}">Retour à la liste</a>
            </div>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="updateEtape">
                <div class="mb-3">
                    <label for="etape_name" class="form-label">Etape Name</label>
                    <input type="text" class="form-control" id="etape_name" wire:model.defer="etapeData.etape_name">
                    @error('etapeData.etape_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="etape_desc" class="form-label">Description</label>
                    <input type="text" class="form-control" id="etape_desc" wire:model.defer="etapeData.etape_desc">
                    @error('etapeData.etape_desc')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="row">
                <div class="mb-3 col-6">
                    <label for="etape_style" class="form-label">Style</label>
                    <input type="text" class="form-control" id="etape_style"
                        wire:model.defer="etapeData.etape_style">
                    @error('etapeData.etape_style')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3 col-6">
                    <label for="etape_icon" class="form-label">Icon</label>
                    <input type="text" class="form-control" id="etape_icon" wire:model.defer="etapeData.etape_icon">
                    @error('etapeData.etape_icon')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Statuts de l'étape</h2>
            </div>
            <div class="card-body">
        
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statuts as $index=>$statut)
                            <tr>
                                <td>
                                    <input type="text" wire:model="statuts.{{ $index }}.status_name" class="form-control">

                                </td>
                                <td>
                                    <button class="btn btn-danger" wire:click="deleteStatus({{$statut['id']}})">Supprimer</button>

                                </td>

                            </tr>
                        @endforeach
                        <tr>
                            <td>
                                <input type="text" class="form-control" wire:model="newStatus">


                            </td>
                            <td>
                                <button class="btn btn-success" wire:click="addStatus">Ajouter un statut</button>

                            </td>

                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>



@section('scripts')
    <script></script>
@endsection
