<?php
namespace App\Livewire;

use App\Models\Order;
use App\Services\TgService;
use Illuminate\Support\Facades\Log;
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
            'message' => 'required|string',
            'orderId' => 'required|integer',
        ]);

        $newMessage = Chat::query()->create([
            'sender_id' => auth()->id(),
            'order_id' => $this->orderId,
            'message' => $this->message,
        ]);

        $order = Order::query()->find($this->orderId);

        $manager = $order->manager;
        $expert = $order->expert;

        switch (auth()->id()) {
            case $manager->id:
                $tgIds = [$expert->tg_id];
                $fio = 'ФИО эксперта: ' . $expert->name . ' ' . $expert->last_name;
                break;
            case $expert->id:
                $tgIds = [$manager->tg_id];
                $fio = 'ФИО менеджера: ' . $manager->name . ' ' . $manager->last_name;
                break;
            default:
                $tgIds = [$manager->tg_id, $expert->tg_id];
                $fio = 'ФИО менеджера: ' . $manager->name . ' ' . $manager->last_name . " " .
                    'ФИО эксперта: ' . $expert->name . ' ' . $expert->last_name;
                break;
        }

        $text = 'Получено новое сообщение в заказе: ' . $order->id . ' ('. $order->title . "). \n" .
            'Ссылка на заказ ' . route('orders.show', $order->id) . "\n" .
            $fio . "\n";

        foreach ($tgIds as $tgId) {
            if (empty($tgId)) {
                continue;
            }

            Log::info('Отправлено сообщение в телеграм (tg_id: ' . $tgId . "): \n" . $text );

            TgService::sendMessage(
                $tgId,
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
