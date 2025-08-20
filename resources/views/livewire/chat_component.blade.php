<div class="card card-primary card-outline direct-chat direct-chat-primary">
    <div class="card-header">
        <h3 class="card-title">Чат</h3>
        <div class="card-tools">
            <span class="badge bg-primary">{{ $messages->count() }} сообщений</span>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="card-body">
        {!! $buttonShowAllMessage ?? '' !!}

        <div class="direct-chat-messages" id="chat-box" style="height: 500px; overflow-y: auto;">
            @foreach($messages as $message)
                @php $isSender = auth()->id() === $message->sender_id @endphp

                <div class="direct-chat-msg @if($isSender) right @endif"
                     @if($loop->last) id="last-message" @endif>
                    <div class="direct-chat-infos clearfix">
                        <span class="direct-chat-name @if($isSender) float-right @else float-left @endif">
                            {{ $message->sender->name }} {{ $message->sender->last_name }}
                        </span>
                        <span class="direct-chat-timestamp @if($isSender) float-left @else float-right @endif">
                            {{ $message->created_at->format('H:i d.m.Y') }}
                        </span>
                    </div>

                    <div class="direct-chat-text">
                        <p style="white-space: pre-line">{{ $message->message }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card-footer">
        <form wire:submit.prevent="sendMessage">
            <div class="input-group">
                <textarea wire:model.defer="message"
                          name="message"
                          placeholder="Напишите сообщение..."
                          class="form-control"
                          rows="2"
                          required></textarea>
                <div class="text-end ml-1">
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </div>
            </div>
        </form>
    </div>

    @script
    <script>
        window.currentUserId = {{ auth()->id() }};

        let shouldShowNewMessageIndicator = false;

        setInterval(() => {
            $wire.getNewMessages();
        }, 5000);

        window.scrollToBottom = function () {
            setTimeout(() => {
                const chatBox = document.getElementById('chat-box');
                const footer = document.querySelector('.card-footer');
                const indicator = document.getElementById('new-message-indicator');

                if (!chatBox) return;

                const footerHeight = footer ? footer.offsetHeight : 0;
                chatBox.scrollTop = chatBox.scrollHeight + footerHeight;

                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth'
                });

                if (isScrolledToBottom(chatBox) && indicator) {
                    shouldShowNewMessageIndicator = false;
                    indicator.style.display = 'none';
                }
            }, 50);
        };

        function isScrolledToBottom(el) {
            const scrollTop = Math.round(el.scrollTop);
            const clientHeight = Math.round(el.clientHeight);
            const scrollHeight = Math.round(el.scrollHeight);
            const distanceFromBottom = scrollHeight - (scrollTop + clientHeight);
            return distanceFromBottom <= 0;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const chatBox = document.getElementById('chat-box');
            const indicator = document.getElementById('new-message-indicator');
            if (!chatBox) return;

            if (isScrolledToBottom(chatBox)) {
                shouldShowNewMessageIndicator = false;
                if (indicator) indicator.style.display = 'none';
            }

            chatBox.addEventListener('scroll', () => {
                if (isScrolledToBottom(chatBox)) {
                    shouldShowNewMessageIndicator = false;
                    if (indicator) indicator.style.display = 'none';
                }
            });
        });

        document.addEventListener('newMessageReceived', (event) => {
            const senderId = event.detail?.senderId;
            const chatBox = document.getElementById('chat-box');
            const indicator = document.getElementById('new-message-indicator');

            if (!chatBox || !indicator) return;

            if (senderId !== window.currentUserId) {
                shouldShowNewMessageIndicator = true;
                indicator.style.display = 'block';
            } else {
                shouldShowNewMessageIndicator = false;
                indicator.style.display = 'none';
            }
        });

        document.addEventListener('scrollToBottom', () => {
            if (typeof window.scrollToBottom === 'function') {
                window.scrollToBottom();
            }
        });
    </script>
    @endscript
</div>
