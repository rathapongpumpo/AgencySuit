<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->string('owner_name')->nullable()->after('status');
            $table->string('owner_phone', 50)->nullable()->after('owner_name');
            $table->string('owner_line', 100)->nullable()->after('owner_phone');
            $table->decimal('size', 8, 2)->nullable()->after('owner_line');
            $table->string('floor', 20)->nullable()->after('size');
            $table->string('unit_number', 50)->nullable()->after('floor');
            $table->text('notes')->nullable()->after('unit_number');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->dropColumn([
                'owner_name',
                'owner_phone',
                'owner_line',
                'size',
                'floor',
                'unit_number',
                'notes',
            ]);
        });
    }
};
