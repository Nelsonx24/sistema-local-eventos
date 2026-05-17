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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->integer('boxes')->default(0);
            $table->integer('units_per_box')->default(1);
            $table->integer('loose_units')->default(0);
            $table->decimal('price_per_box', 10, 2);
            $table->decimal('price_per_unit', 10, 2);
            $table->string('status')->default('In Stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
