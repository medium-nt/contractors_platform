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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('type_work_id');
            $table->foreign('type_work_id')
                ->references('id')
                ->on('types_work')
                ->onDelete('restrict');
            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('restrict');
            $table->text('description');
            $table->string('hidden_field');

            $table->unsignedBigInteger('manager_id');
            $table->foreign('manager_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
            $table->unsignedBigInteger('expert_id')
                ->nullable();
            $table->foreign('expert_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->unsignedBigInteger('plagiarism_platform_id')
                ->nullable();
            $table->foreign('plagiarism_platform_id')
                ->references('id')
                ->on('plagiarism_platforms')
                ->onDelete('restrict');
            $table->integer('text_uniqueness')
                ->nullable();

            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('status_id')
                ->default(1);
            $table->foreign('status_id')
                ->references('id')
                ->on('statuses')
                ->onDelete('restrict');
            $table->datetime('deadline_at');
            $table->date('warranty_up_to')
                ->nullable();
            $table->datetime('completed_at')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
