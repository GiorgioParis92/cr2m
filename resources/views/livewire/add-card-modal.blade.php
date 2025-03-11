<div>
    <!-- Modal Structure -->
    <div wire:ignore.self class="modal fade" id="addCardModal" tabindex="-1" aria-labelledby="addCardModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Body -->
                <div class="modal-body">

                    <div class="form-group">
                        <label for="card-name">Titre</label>
                        <input type="text" id="card-name" class="form-control" wire:model="newCardName">
                        @error('newCardName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Assign Users -->
                    <div class="form-group">
                        <label for="assignedUsers">Assigner à un ou des utilisateurs</label>
                        <select id="assignedUsers" multiple class="form-control select2">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('assignedUsers') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Assign User Types -->
                    <div class="form-group">
                        <label for="type_users_selected">ET/OU à un type d'utilisateur</label>
                        <select id="type_users_selected" multiple class="form-control select2">
                            @foreach($type_users as $userType)
                                <option value="{{ $userType->id }}">{{ $userType->type_desc }}</option>
                            @endforeach
                        </select>
                        @error('type_users_selected') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#addCardModal').modal('hide');" data-dismiss="modal">Fermer</button>
                    <!-- Use JavaScript function for saving -->
                    <button type="button" class="btn btn-primary" onclick="saveCard()">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Define Variables -->
<script>
    var assignedUsers = @json($assignedUsers ?? []);
    var typeUsersSelected = @json($type_users_selected ?? []);
</script>

<!-- JavaScript Code -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let addCardModal = null;

    function initializeSelect2() {
        console.log('initializeSelect2 called');

        $('#assignedUsers').select2({
            dropdownParent: $('#addCardModal')
        });

        $('#type_users_selected').select2({
            dropdownParent: $('#addCardModal')
        });
    }

    window.addEventListener('show-add-card-modal', () => {
        $('#addCardModal').modal('show');

        initializeSelect2();
    });

    window.addEventListener('hide-add-card-modal', () => {
        $('#addCardModal').modal('hide');

    });
    Livewire.hook('message.processed', (message, component) => {
        // Only reinitialize Select2 when necessary (i.e., when specific data is updated)
        if (message.updateQueue && message.updateQueue.some(update => update.name === 'newCardName' || update.name === 'assignedUsers' || update.name === 'type_users_selected')) {
            initializeSelect2(); // Ensure Select2 is reinitialized if relevant fields are updated
        }
    });
});

// // JavaScript function to handle 'Save Card' action
function saveCard() {
    // Get the selected values from the select2 elements
    var assignedUsers = $('#assignedUsers').val();
    var typeUsersSelected = $('#type_users_selected').val();

    @this.saveCard(assignedUsers, typeUsersSelected);
   
    // Call the Livewire method and pass the selected values
    // Livewire.emit('saveCard', assignedUsers, typeUsersSelected);
}


</script>

<!-- Include Scripts and Stylesheets -->
<!-- Place your script inclusions here as shown in step 7 -->
