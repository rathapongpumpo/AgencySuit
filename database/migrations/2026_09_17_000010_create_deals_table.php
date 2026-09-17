<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->string('stage', 20)->default('new');
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('co_agent_split', 5, 2)->default(0);
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
