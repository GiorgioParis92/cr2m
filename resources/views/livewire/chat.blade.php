<div>
    <style>
        .chat-container {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: fixed;
            bottom: 0;
            right: 10px;
            transition: height 0.3s, padding 0.3s;
            z-index: 999999;
        }

        .chat-container.collapsed {
            height: 40px;
            padding: 0;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }

        .chat-messages {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            display: block;
        }

        .chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ddd;
            display: block;
        }

        .chat-toggle {
            cursor: pointer;
        }

        .chat-container.collapsed .chat-messages,
        .chat-container.collapsed .chat-input {
            display: none;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
            min-width: 80%;
        }

        .flex-shrink-1 {
            flex-shrink: 1 !important;
            color: black;
        }

        .flex-grow-1 {
            flex-grow: 1 !important;
            color: black;
        }

        .chat-online {
            color: #34ce57
        }

        .chat-offline {
            color: #e4606d
        }

        .chat-messages {
            display: flex;
            flex-direction: column;
            max-height: 40vh;
            overflow-y: scroll;
            min-height: 20vh;
        }

        .chat-message-left,
        .chat-message-right {
            display: flex;
            flex-shrink: 0
        }

        .chat-message-left {
            margin-right: auto
        }

        .chat-message-right {
            flex-direction: row-reverse;
            margin-left: auto
        }

        .py-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        .px-4 {
            padding-right: 1.5rem !important;
            padding-left: 1.5rem !important;
        }

        .flex-grow-0 {
            flex-grow: 0 !important;
        }

        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }


        .text-muted {
            color: #67748e !important;
            font-size: 10px;
            margin-right: 10px;
            margin-left: 10px;
            font-style: italic;
        }

        a.list-group-item.list-group-item-action.border-0 {
            margin-bottom: 18px;
            border-bottom: 1px solid #ccc !important;
            padding-bottom: 13px;
        }
    </style>
    <div class="chat-container collapsed" id="chat-container">
        <div class="chat-header bg-primary" id="chat-toggle">
            <div><i class="fa fa-comments"></i> Chat</div>
            <div class="chat-toggle"><i class="fa fa-arrow-up"></i></div>
        </div>


        <div class="chat-messages" id="chat-messages" wire:poll="refresh">
            @foreach ($messages as $message)
                {{-- <div class="message">
            <div>
                <span class="sender">{{ $message->user->name }}</span>
                <span class="timestamp">{{ $message->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
            <div class="content">{{ $message->content }}</div>
        </div> --}}

                @if (auth()->user()->id == $message->user->id)
                    <div class="chat-message-right pb-4">
                        <div>
                            <img src="https://bootdey.com/img/Content/avatar/avatar1.png" class="rounded-circle mr-1"
                                alt="Chris Wood" width="40" height="40">
                            <div class="text-muted small text-nowrap mt-2">
                                {{ date('d/m/Y à H:i', strtotime($message->created_at)) }}</div>
                        </div>
                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                            <div class="font-weight-bold mb-1">Moi</div>
                            {{ $message->content }}
                        </div>
                    </div>
                @else
                    <div class="chat-message-left pb-4">
                        <div>
                            <img src="https://bootdey.com/img/Content/avatar/avatar3.png" class="rounded-circle mr-1"
                                alt="Sharon Lessman" width="40" height="40">
                            <div class="text-muted small text-nowrap mt-2">
                                {{ date('d/m/Y à H:i', strtotime($message->created_at)) }}</div>
                        </div>
                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3">
                            <div class="font-weight-bold mb-1">
                                {{ $message->user->name }}
                            </div>
                            {{ $message->content }}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="chat-input">
            <input type="text" id="message-input" wire:model="messageContent" placeholder="Tapez votre message..."
                wire:keydown.enter="sendMessage">
            <button class="btn btn-primary" wire:click="sendMessage">Envoyer</button>
        </div>
    </div>
</div>
@section('scripts')
    <script>
       setupChat()
        function setupChat() {
            document.getElementById('chat-toggle').onclick = function() {
                const chatContainer = document.getElementById('chat-container');
                chatContainer.classList.toggle('collapsed');

                if (!chatContainer.classList.contains('collapsed')) {
                    scrollToBottom();
                }

                localStorage.setItem('chatCollapsed', chatContainer.classList.contains('collapsed'));
            };

            function scrollToBottom() {
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.scrollTop = chatMessages.scrollHeight;

            }

            // Set initial state from localStorage
            const chatContainer = document.getElementById('chat-container');
            const isCollapsed = localStorage.getItem('chatCollapsed') === 'true';
            if (isCollapsed) {
                chatContainer.classList.add('collapsed');
            } else {
                chatContainer.classList.remove('collapsed');
                $('.chat-toggle').html('<i class="fa fa-arrow-down"></i>')

            }
        }

        document.addEventListener('livewire:load', function() {
            setupChat();
            Livewire.on('new_message', function() {

                var chatMessages = document.getElementById('chat-messages');
                chatMessages.scrollTop = (chatMessages.scrollHeight) + 600;
                setupChat();
            });
        });

        document.addEventListener('livewire:update', function() {
            setupChat();
        });
    </script>
@endsection
