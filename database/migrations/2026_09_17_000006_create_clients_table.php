<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('transaction_type', 10);
            $table->decimal('budget', 12, 2);
            $table->text('locations');
            $table->string('phone', 30)->nullable();
            $table->string('contact_channel', 100)->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->decimal('minimum_size', 8, 2)->nullable();
            $table->text('transit_preference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'transaction_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
