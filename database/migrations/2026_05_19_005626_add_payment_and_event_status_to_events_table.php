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
        Schema::table('events', function (Blueprint $table) {
            $table->string('payment_status', 20)->default('pending')->after('total_amount');
            $table->string('event_status', 20)->default('upcoming')->after('payment_status');
            $table->dropColumn('guests');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'event_status']);
            $table->integer('guests')->default(0);
        });
    }
};
