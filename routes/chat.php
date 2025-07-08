<?php

use App\Livewire\ChatComponent;

Route::middleware(['auth'])->group(function () {
    Route::get('/chat/{order_id}', ChatComponent::class)
        ->name('chat');
});
