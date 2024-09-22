<div>
  
    <div class="chat-container collapsed" id="custom-chat-container">
        <div class="chat-header bg-primary btn-primary" style="background-position: 0!important" id="chat-toggle">
            <div><i class="fa fa-comments"></i> Discussion</div>
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
          
        </div>
    </div>
</div>

