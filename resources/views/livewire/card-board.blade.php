<div wire:poll="loadCards">
    <button class="btn btn-success" wire:click="openAddCardModal(0, {{ $dossier_id ?? null }})">Add Card</button>

    <div class="row mb-5">
        @foreach ($columns as $columnIndex => $column)
        <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4 column" data-column_id="{{ $column['index'] }}" wire:key="column-{{ $columnIndex }}">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">{{ $column['name'] }}</p>
                                @foreach ($column['tickets'] as $ticket)
                                <div class="ticket" draggable="true" data-id="{{ $ticket['id'] }}" wire:key="ticket-{{ $ticket['id'] }}">
                                    <h5>{{ $ticket['title'] }}</h5>
                            
                                    @if (isset($ticket['dossier']) && $display_dossier)
                                        <a href="{{ route('dossiers.show', ['id' => $ticket['dossier']['folder']]) }}" target="_blank">
                                            <div class="btn btn-primary">Dossier : {{ $ticket['dossier']['beneficiaire']['nom'] }} {{ $ticket['dossier']['beneficiaire']['prenom'] }}</div>
                                        </a>
                                    @endif
                            
                                    <!-- Display who assigned the card -->
                                    <div><b>Créé par :</b> {{ $ticket['assigned_by'] }}</div>
                            
                                    <!-- Display all users assigned to the card -->
                                    @if (count($ticket['assigned_to']) > 0)
                                    <div><b>Destiné à :</b> 
                                            {{ implode(', ', $ticket['assigned_to']) }}
                                       
                                    </div>
                                    @endif
                                    @if (!empty($ticket['archived_by']))
                                    <div><b>Déplacé par :</b> 
                                            {{ $ticket['archived_by'] }} le {{date('d/m/Y à H:i',strtotime($ticket['updated_at']))}}
                                       
                                    </div>
                                    @endif
                                    @if($ticket['status']!=0)
                                    <button wire:click="moveCard({{ $ticket['id'] }}, 0)" class="btn btn-sm btn-warning float-right">Fait</button>
                                    @endif
                                </div>
                            @endforeach
                            
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div
                                class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for the browser event to show the modal
        window.addEventListener('show-add-card-modal', event => {
            $('#addCardModal').modal('show');
        });

        // Listen for the browser event to hide the modal
        window.addEventListener('hide-add-card-modal', event => {
            $('#addCardModal').modal('hide');
        });
    });
    document.addEventListener('livewire:load', function() {
        // Make sure modals are re-initialized when Livewire updates the DOM
        Livewire.hook('message.processed', (message, component) => {
            $('#addCardModal').modal('handleUpdate'); // Update modal state
        });
    });
</script>

<div wire:ignore class="modal" id="addCardModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Card</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" wire:model="newCardName" placeholder="Card name" class="form-control mb-2" />
                <label for="assignedUser">Assign to:</label>
                <select wire:model="assignedUsers" multiple class="form-control">
                    <option value="">-- Select User(s) --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button id="addCardWithDetails" type="button" class="btn btn-primary"
                    wire:click="addCardWithDetails">Add Card</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<style>
    .board {
        display: block;
        padding: 20px;
        overflow-x: auto;
        height: auto;
        width:100%
    }

    .column {
        /* background-color: #ebecf0; */
    border-radius: 3px;
  
  
    padding: 10px;
    flex-shrink: 0;
    min-height: 40vh;
    max-height: 40vh;
    overflow-x: hidden;
    overflow-y: scroll;
    }

    .column-header {
        font-weight: bold;
        padding-bottom: 10px;
    }

    .ticket {
        background-color: white;
        border-radius: 3px;
        padding: 10px;
        margin-bottom: 10px;
        cursor: move;
        box-shadow: 0 1px 0 rgba(9, 30, 66, .25);
    }

    .add-column,
    .add-ticket {
        background-color: rgba(9, 30, 66, .04);
        color: #172b4d;
        border: none;
        padding: 10px;
        border-radius: 3px;
        cursor: pointer;
        width: 100%;
        text-align: left;
    }

    .add-column:hover,
    .add-ticket:hover {
        background-color: rgba(9, 30, 66, .08);
    }

    #new-column {
        width: 272px;
        margin-right: 10px;
    }
    
.column {

  
  /* Hide scrollbar for IE, Edge and Firefox */
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}

.column::-webkit-scrollbar {
  display: none;
}
</style>
<script>
    document.addEventListener('livewire:load', function() {
        let draggedTicket = null;

        function handleDragStart(e) {
            draggedTicket = e.target;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', e.target.innerHTML);
            console.log('moved start')
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
                console.log('moved hover')

            }
            e.dataTransfer.dropEffect = 'move';
            return false;
        }

        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();

            }

            const targetColumn = e.target.closest('.column');

            if (targetColumn && draggedTicket) {
                const ticketId = draggedTicket.getAttribute('data-id');
                const newColumnIndex = targetColumn.getAttribute('data-column_id');


                Livewire.emit('moveCard', ticketId, newColumnIndex);
            }

            draggedTicket = null;
            return false;
        }

        function setupColumnListeners(column) {
            column.addEventListener('dragover', handleDragOver);
            column.addEventListener('drop', handleDrop);
        }

        function setupTicketListeners(ticket) {
            ticket.addEventListener('dragstart', handleDragStart);
        }

        document.querySelectorAll('.column').forEach(setupColumnListeners);
        document.querySelectorAll('.ticket').forEach(setupTicketListeners);

        Livewire.hook('message.processed', (message, component) => {
            document.querySelectorAll('.column').forEach(setupColumnListeners);
            document.querySelectorAll('.ticket').forEach(setupTicketListeners);
        });

        $('#addCardWithDetails').click(function() {
            Livewire.emit('addCardWithDetails');

        });
    });
</script>
