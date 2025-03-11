    <div class="card">
        <div class="card-header">
            <h2>Gestion des étapes</h2>
        </div>
        <div class="card-body">
            <ul id="etapes-list" class="list-group mb-3">
                @foreach ($etapes as $etape)
                    <li class="list-group-item d-flex justify-content-between align-items-center"
                        data-id="{{ $etape->id }}">
                        <a href="{{ route('edit-etape', $etape->id) }}">{{ $etape->order_column + 1 }} -  {{ $etape->etape_desc }}</a>
                        <div>
                            <a class="btn btn-sm btn-primary me-2" href="{{ route('edit-etape', $etape->id) }}">Edit</a>
                        </div>
                    </li>
                @endforeach
            </ul>
            <button class="btn btn-success" wire:click="addEtape">Ajouter une étape</button>
        </div>
    </div>



    @section('scripts')
        <script>
            document.addEventListener('livewire:load', function() {
                var el = document.getElementById('etapes-list');
                Sortable.create(el, {
                    onEnd: function(evt) {
                        @this.reorderEtapes([...el.children].map(item => item.dataset.id));
                    },
                });

                // Listen for the event to trigger the modal
                Livewire.on('triggerEditModal', () => {
                    $('#editEtapeModal').show();
                    $('.modal-backdrop').show()

                });

                // Listen for the event to close the mod
                Livewire.on('closeEditModal', () => {

                    $('#editEtapeModal').modal('hide');
                    $('.modal-backdrop').hide()



                });
            });
        </script>
    @endsection




    <!-- Edit Etape Modal -->
    <div class="modal " id="editEtapeModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEtapeModalLabel">Edit Etape</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateEtape">
                        <div class="mb-3">
                            <label for="etape_name" class="form-label">Etape Name</label>
                            <input type="text" class="form-control" id="etape_name"
                                wire:model.defer="etapeData.etape_name">
                            @error('etapeData.etape_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="etape_desc" class="form-label">Description</label>
                            <input type="text" class="form-control" id="etape_desc"
                                wire:model.defer="etapeData.etape_desc">
                            @error('etapeData.etape_desc')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="etape_style" class="form-label">Style</label>
                            <input type="text" class="form-control" id="etape_style"
                                wire:model.defer="etapeData.etape_style">
                            @error('etapeData.etape_style')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="etape_icon" class="form-label">Icon</label>
                            <input type="text" class="form-control" id="etape_icon"
                                wire:model.defer="etapeData.etape_icon">
                            @error('etapeData.etape_icon')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
