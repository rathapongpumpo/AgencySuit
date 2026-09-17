<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->date('due_date');
            $table->string('note', 300)->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
            $table->index(['user_id', 'status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
