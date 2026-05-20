<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function ($table) {
            $table->softDeletes();
        });
        Schema::table('inventory', function ($table) {
            $table->softDeletes();
        });
        Schema::table('sales', function ($table) {
            $table->softDeletes();
        });
        Schema::table('sale_items', function ($table) {
            $table->softDeletes();
        });
        Schema::table('staff', function ($table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function ($table) {
            $table->dropSoftDeletes();
        });
        Schema::table('inventory', function ($table) {
            $table->dropSoftDeletes();
        });
        Schema::table('sales', function ($table) {
            $table->dropSoftDeletes();
        });
        Schema::table('sale_items', function ($table) {
            $table->dropSoftDeletes();
        });
        Schema::table('staff', function ($table) {
            $table->dropSoftDeletes();
        });
    }
};
