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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_id');
            $table->string('event_type');
            $table->date('date');
            $table->integer('guests');
            $table->string('status')->default('Pendiente');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('advance_payment', 12, 2);
            $table->decimal('balance_pending', 12, 2);
            $table->date('payment_due_date');
            $table->string('signed_contract_url')->nullable();
            $table->string('seller_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
