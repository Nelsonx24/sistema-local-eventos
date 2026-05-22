<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id_new')->nullable()->after('event_id');
            $table->boolean('is_direct_sale')->default(false)->after('event_id_new');
        });

        DB::statement("
            UPDATE sales
            SET event_id_new = CASE
                    WHEN event_id IS NULL OR event_id LIKE 'Venta Directa%' THEN NULL
                    ELSE CAST(event_id AS UNSIGNED)
                END,
                is_direct_sale = CASE
                    WHEN event_id IS NULL OR event_id LIKE 'Venta Directa%' THEN 1
                    ELSE 0
                END
            WHERE id > 0
        ");

        DB::statement('
            UPDATE sales
            SET event_id_new = NULL
            WHERE is_direct_sale = 0
                AND event_id_new IS NOT NULL
                AND event_id_new NOT IN (SELECT id FROM events)
        ');

        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('event_id_new')
                ->references('id')
                ->on('events')
                ->onDelete('set null');

            $table->index('event_id_new');
            $table->index('is_direct_sale');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['event_id_new']);
            $table->dropIndex(['event_id_new']);
            $table->dropIndex(['is_direct_sale']);
            $table->dropColumn(['event_id_new', 'is_direct_sale']);
        });
    }
};
