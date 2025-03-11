
<div wire:poll="loadCards">
 
    <button class="btn btn-success" wire:click="openAddCardModal(0, {{ $dossier_id ?? null }})">Ajouter une notification</button>


    <div class="container-fluid py-4">
        <div class="row">

            @foreach ($columns as $columnIndex => $column)
            <div class="col-12 col-md-4 mb-3 column"  data-column_id="{{ $column['index'] }}" wire:key="column-{{ $columnIndex }}">
                <div class="kanban-column">
                    <div class="column-header">{{ $column['name'] }}@if(count($column['tickets'])>0)<span class="badge badge-danger bg-danger">{{count($column['tickets'])}}</span>@endif</div>
                    @foreach ($column['tickets'] as $ticket)

                    <div class="card ticket" draggable="true" data-id="{{ $ticket['id'] }}" wire:key="ticket-{{ $ticket['id'] }}">
                        <div class="card-header pb-0">
                          <div class="d-flex">
                            <p>{{ $ticket['title'] }}</p>
                            <div class="ms-auto">
                                @if (isset($ticket['dossier']) && $display_dossier)
                              <span class="badge badge-primary"><a href="{{ route('dossiers.show', ['id' => $ticket['dossier']['folder']]) }}" target="_blank">{{ $ticket['dossier']['beneficiaire']['nom'] }} {{ $ticket['dossier']['beneficiaire']['prenom'] }}</span></a>
                                @endif
                            </div>
                          </div>
                        </div>
                        @if(isset($ticket['comment']))
                        <div class="card-body pt-0">
                          <p class="mb-0">What matters is the people who are sparked by it. And the people who are liked.</p>
                        </div>
                        @endif
                    </div>

                    @endforeach
                    
                </div>
            </div>

            @endforeach

    

    
  
        </div>
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
                    wire:click="addCardWithDetails">Ajouter une notification</button>
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

    /* max-height: 280px; */
    /* overflow-x: hidden; */
    /* overflow-y: scroll; */
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


<style>
    .kanban-column {
        background-color: #ebecf0;
        border-radius: 3px;
        padding: 8px;
        min-height: 80vh;
    }
    
    .task-card {
        background: white;
        border-radius: 3px;
        padding: 10px;
        margin-bottom: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .add-card {
        color: #5e6c84;
        cursor: pointer;
        padding: 10px;
        border-radius: 3px;
    }
    
    .add-card:hover {
        background-color: rgba(9,30,66,0.08);
        color: #172b4d;
    }
    
    .column-header {
        font-weight: bold;
        margin-bottom: 12px;
        font-size: 1.1em;
    }
    
    .modal-card-details {
        background: #f4f5f7;
        border-radius: 5px;
        padding: 15px;
        margin-top: 10px;
    }
    
    .card-description {
        background: white;
        padding: 10px;
        border-radius: 3px;
        margin-top: 10px;
    }
    
    .card-metadata {
        color: #5e6c84;
        font-size: 0.9em;
        margin-top: 10px;
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
