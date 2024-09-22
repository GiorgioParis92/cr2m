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

        img.chat_img {
            max-width: 30%;
            display: block;
            text-align: right;
            position: relative;
        }

        .chat-message-right {
            flex-direction: row-reverse;
            margin-left: auto;
            text-align: right;
        }
    </style>
    <div class="chat-container collapsed" id="chat-container">
        <div class="chat-header bg-primary btn-primary" style="background-position: 0!important" id="chat-toggle">
            <div><i class="fa fa-comments"></i> Discussion</div>
            <div class="chat-toggle"><i class="fa fa-arrow-up"></i></div>
        </div>
        

        <div class="chat-messages" id="chat-messages" wire:poll="refresh">
            @foreach ($chatMessages as $message)
                @if (auth()->user()->id == $message->user->id)
                    <div class="chat-message-right pb-4">
                        <!-- Existing code -->
                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                            <div class="font-weight-bold mb-1">Moi</div>
                            {{ $message->content }}
                            @if ($message->file_path)
                                <div>
                                    @php
                                        $extension = strtolower(pathinfo($message->file_path, PATHINFO_EXTENSION));
                                        $filename = basename($message->file_path);
                                    @endphp

                                    @if ($extension === 'pdf')
                                        <div class="btn btn-success btn-view pdfModal" data-toggle="modal"
                                            data-img-src="{{ Storage::url($message->file_path) }}"
                                            data-name="{{ $filename }}">
                                            <i class="fa fa-file-pdf fa-2x"></i> {{ $filename }}
                                        </div>
                                    @elseif (in_array(strtolower($extension), ['webp', 'jpg', 'jpeg', 'png', 'gif']))
                                        <a href="{{ Storage::url($message->file_path) }}" target="_blank">
                                            <img class="chat_img" src="{{ Storage::url($message->file_path) }}"
                                                alt="Image" />
                                            {{ $filename }}
                                        </a>
                                    @elseif (in_array(strtolower($extension), ['xls', 'xlsx', 'csv']))
                                        <a href="{{ Storage::url($message->file_path) }}" target="_blank">
                                            <i class="fa fa-file-excel fa-2x"></i>
                                            {{ $filename }}
                                        </a>
                                    @else
                                        <a class=" bg-success text-success"
                                            href="{{ Storage::url($message->file_path) }}" target="_blank">
                                            <i class="fa fa-file fa-2x "></i>
                                            {{ $filename }}
                                        </a>
                                    @endif
                                </div>
                            @endif

                        </div>
                    </div>
                @else
                    <div class="chat-message-left pb-4">
                        <!-- Existing code -->
                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3">
                            <div class="font-weight-bold mb-1">
                                {{ $message->user->name }}
                            </div>
                            {{ $message->content }}
                            @if ($message->file_path)
                                <div>
                                    @php
                                        $extension = strtolower(pathinfo($message->file_path, PATHINFO_EXTENSION));
                                        $filename = basename($message->file_path);
                                    @endphp

                                    @if ($extension === 'pdf')
                                        <div class="btn btn-success btn-view pdfModal" data-toggle="modal"
                                            data-img-src="{{ Storage::url($message->file_path) }}"
                                            data-name="{{ $filename }}">
                                            <i class="fa fa-file-pdf fa-2x"></i> {{ $filename }}
                                        </div>
                                    @elseif (in_array(strtolower($extension), ['webp', 'jpg', 'jpeg', 'png', 'gif']))
                                        <a href="{{ Storage::url($message->file_path) }}" target="_blank">
                                            <img class="chat_img" src="{{ Storage::url($message->file_path) }}"
                                                alt="Image" />
                                            {{ $filename }}
                                        </a>
                                    @elseif (in_array(strtolower($extension), ['xls', 'xlsx', 'csv']))
                                        <a href="{{ Storage::url($message->file_path) }}" target="_blank">
                                            <i class="fa fa-file-excel fa-2x"></i>
                                            {{ $filename }}
                                        </a>
                                    @else
                                        <a class=" bg-success text-success"
                                            href="{{ Storage::url($message->file_path) }}" target="_blank">
                                            <i class="fa fa-file fa-2x "></i>
                                            {{ $filename }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="chat-input">
            <input type="text" id="message-input" wire:model="messageContent" placeholder="Tapez votre message..."
                wire:keydown.enter="sendMessage">
            @error('messageContent')
                <span class="error">{{ $message }}</span>
            @enderror

            <input type="file" id="file_chat" wire:model="file">
            @error('file')
                <span class="error">{{ $message }}</span>
            @enderror

            <button class="btn btn-primary" wire:click="sendMessage"><i class="fa fa-paper-plane"></i></button>
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

                const isCollapsed = chatContainer.classList.contains('collapsed');

                // Call Livewire function when the chat is collapsed or expanded
                if (isCollapsed) {
                    // Chat is collapsed
                    Livewire.emit('chatCollapsed'); 
                } else {
                    // Chat is expanded
                    Livewire.emit('chatExpanded'); // Or use Livewire.call('chatExpandedFunction')
                }

                localStorage.setItem('chatCollapsed', isCollapsed);

                if (!isCollapsed) {
                    scrollToBottom();
                }
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
                $('.chat-toggle').html('<i class="fa fa-arrow-down"></i>');
                Livewire.emit('chatExpanded'); // Or use Livewire.call('chatExpandedFunction')

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
