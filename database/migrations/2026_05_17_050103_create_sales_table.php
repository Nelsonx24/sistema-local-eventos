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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->nullable();
            $table->string('client_name');
            $table->decimal('amount', 12, 2);
            $table->decimal('cash_received', 12, 2)->default(0);
            $table->decimal('change_given', 12, 2)->default(0);
            $table->date('date');
            $table->string('payment_method');
            $table->string('status')->default('Paid');
            $table->string('seller_name')->nullable();
            $table->boolean('is_printed')->default(false);
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('quantity');
            $table->string('type');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
