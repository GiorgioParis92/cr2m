<div>
    <!-- Button to open the modal -->

    <!-- Modal Structure -->
    <div wire:ignore.self class="modal fade" id="addCardModal" tabindex="-1" role="dialog" aria-labelledby="addCardModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCardModalLabel">Add New Card</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Card Name Input -->
                    <div class="form-group">
                        <label for="card-name">Card Name</label>
                        <input type="text" id="card-name" class="form-control" wire:model="newCardName">
                        @error('newCardName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Assign Users -->
                    <div class="form-group">
                        <label for="assignedUsers">Assign Users</label>
                        <select id="assignedUsers" wire:model="assignedUsers" multiple class="form-control">
                            @foreach($this->users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('assignedUsers') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Dossier ID Display -->
                    @if(isset($this->dossier_id))
                    <div class="form-group" >
                        <input type="hidden" id="dossier_id" class="form-control" wire:model="dossier_id" disabled>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="saveCard">Save Card</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.addEventListener('show-add-card-modal', event => {
            $('#addCardModal').modal('show');
        });
        window.addEventListener('hide-add-card-modal', event => {
            $('#addCardModal').modal('hide');
        });
    });
</script>
