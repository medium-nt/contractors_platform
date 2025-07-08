<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class ChatComponent extends Component
{
    public Collection $messages;
    public string $message = '';
    public int $lastMessageId = 0;
    public ?int $orderId = null;
    public bool $hasUnread = false;


    public function mount(int $orderId = null): void
    {
        $this->messages = collect();
        $this->orderId = $orderId ?? 0;
        $this->loadMessages();
    }

    public function loadMessages(): void
    {
        $this->messages = Chat::with('sender')
            ->where('order_id', $this->orderId)
            ->latest()
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        if ($this->messages->isNotEmpty()) {
            $this->lastMessageId = $this->messages->last()->id;
        }
    }

    public function getNewMessages(): void
    {
        $new = Chat::with('sender')
            ->where('order_id', $this->orderId)
            ->where('id', '>', $this->lastMessageId)
            ->orderBy('id')
            ->get();

        if ($new->isNotEmpty()) {
            $this->messages = $this->messages->merge($new);
            $this->lastMessageId = $new->last()->id;

            // ✅ Только получатель получит это событие
            $this->dispatch('newMessageReceived', ['senderId' => $new->last()->sender_id]);
        }
    }

    public function sendMessage(): void
    {
        $this->validate([
            'message' => 'required|string|max:255',
            'orderId' => 'required|integer',
        ]);

        $newMessage = Chat::create([
            'sender_id' => Auth::id(),
            'order_id' => $this->orderId,
            'message' => $this->message,
        ]);

//        $this->message = '';
//        $this->getNewMessages();

        $this->messages->push($newMessage);
        $this->lastMessageId = $newMessage->id;
        $this->message = '';

        $this->dispatch('scrollToBottom');
    }

    public function render()
    {
        logger('ChatComponent render called');

        return view('livewire.chat_component');
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Пожалуйста, введите сообщение.',
            'message.max' => 'Сообщение не должно превышать 255 символов.',
        ];
    }
}
