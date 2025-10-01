<?php
namespace App\Livewire;

use App\Models\Notification;
use App\Models\Order;
use App\Services\ChatService;
use App\Services\NotificationService;
use App\Services\TgService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\Chat;
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
        $newMessages = ChatService::getNewMessages($this->orderId, $this->lastMessageId);

        if ($newMessages->isEmpty()) {
            return;
        }

        $this->messages = $this->messages->merge($newMessages);
        $this->lastMessageId = $newMessages->last()->id;

        $this->dispatch('newMessageReceived', ['senderId' => $newMessages->last()->sender_id]);
    }

    public function sendMessage(): void
    {
        $this->validate([
            'message' => 'required|string',
            'orderId' => 'required|integer',
        ]);

        $newMessage = Chat::query()->create([
            'sender_id' => auth()->id(),
            'order_id' => $this->orderId,
            'message' => $this->message,
        ]);

        $order = Order::find($this->orderId);
        $fio = ChatService::getFioSummary($order);
        $text = 'Получено новое сообщение в заказе: ' . $order->id . ' ('. $order->title . "). \n" .
            'Ссылка на заказ ' . route('orders.show', $order->id) . "\n" .
            $fio . "\n";

        $users = ChatService::getNotificationRecipients($order);
        foreach ($users as $user) {
            $hasUnreadChatNotifications = ChatService::hasUnreadChatNotifications($order, $user);

            if (!$hasUnreadChatNotifications) {
                NotificationService::create(
                    Notification::TYPE_CHAT,
                    'Новое сообщение в чате.',
                    'Текст сообщения: ' . $this->message,
                    $user->id,
                    auth()->id(),
                    $order->id
                );
            }

            if (empty($user->tg_id) || $user->isOnline()) {
                continue;
            }

            Log::info('Отправлено сообщение в телеграм (tg_id: ' . $user->tg_id . "): \n" . $text );

            TgService::sendMessage(
                $user->tg_id,
                $text
            );
        }

        $this->messages->push($newMessage);
        $this->lastMessageId = $newMessage->id;
        $this->message = '';

        $this->dispatch('scrollToBottom');
    }

    public function render()
    {
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
