<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Find duplicate pending follow-ups having same client_id, due_date and status = 'pending'
        $duplicates = DB::table('follow_ups')
            ->select('client_id', 'due_date', DB::raw('MIN(id) as keep_id'))
            ->where('status', 'pending')
            ->groupBy('client_id', 'due_date')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('follow_ups')
                ->where('client_id', $dup->client_id)
                ->where('due_date', $dup->due_date)
                ->where('status', 'pending')
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }
    }

    public function down(): void
    {
        // Deduplication cannot and need not be reversed
    }
};
