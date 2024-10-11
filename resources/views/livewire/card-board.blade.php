<div class="board" id="board">
    @foreach ($columns as $columnIndex => $column)
        <div class="column" wire:key="column-{{ $columnIndex }}">
            <div class="column-header">{{ $column['name'] }}</div>
            @foreach ($column['tickets'] as $ticket)
                <div class="ticket" draggable="true" data-id="{{ $ticket['id'] }}" wire:key="ticket-{{ $ticket['id'] }}">
                    {{ $ticket['title'] }}
                    <div>Assigned to: {{ $ticket['user_id'] }}</div>
                    <button wire:click="removeCard({{ $columnIndex }}, {{ $ticket['id'] }})" class="btn btn-sm btn-danger float-right">Delete</button>
                </div>
            @endforeach
            <button class="add-ticket" wire:click="$emit('openAddCardModal', {{ $columnIndex }})">+ Add a card</button>
        </div>
    @endforeach
    {{-- <div id="new-column" wire:key="new-column">
        <button class="add-column" wire:click="addColumn">+ Add another list</button>
    </div> --}}
</div>
<div>
    <button wire:click="testFunction">Test Button</button>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="modal" id="addCardModal" tabindex="-1" role="dialog">
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
                <button type="button" onclick="console.log('Button clicked')" wire:click="addCardWithDetails">Add Card</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Listen for the browser event to show the modal
        window.addEventListener('show-add-card-modal', event => {
            $('#addCardModal').modal('show');
        });
    });
</script>


<style>
    .board {
    display: flex;
    padding: 20px;
    overflow-x: auto;
    height: calc(100vh - 60px);
  }
  .column {
    background-color: #ebecf0;
    border-radius: 3px;
    width: 272px;
    margin-right: 10px;
    padding: 10px;
    flex-shrink: 0;
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
    box-shadow: 0 1px 0 rgba(9,30,66,.25);
  }
  .add-column, .add-ticket {
    background-color: rgba(9,30,66,.04);
    color: #172b4d;
    border: none;
    padding: 10px;
    border-radius: 3px;
    cursor: pointer;
    width: 100%;
    text-align: left;
  }
  .add-column:hover, .add-ticket:hover {
    background-color: rgba(9,30,66,.08);
  }
  #new-column {
    width: 272px;
    margin-right: 10px;
  }
  </style>
<script>
document.addEventListener('livewire:load', function () {
    let draggedTicket = null;

    function handleDragStart(e) {
        draggedTicket = e.target;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', e.target.innerHTML);
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
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
            const newColumnIndex = Array.from(targetColumn.parentNode.children).indexOf(targetColumn);

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
});
</script>
