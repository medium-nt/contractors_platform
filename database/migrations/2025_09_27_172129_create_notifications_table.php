<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiver_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('sender_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('type');
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->onDelete('cascade');
            $table->string('title');
            $table->text('body')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
