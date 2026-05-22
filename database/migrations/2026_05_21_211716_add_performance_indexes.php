<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->index('date');
            $table->index('event_status');
            $table->index('status');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->index('event_id');
            $table->index('date');
            $table->index('payment_method');
            $table->index('client_name');
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->index('name');
            $table->index('category');
            $table->index('boxes');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->index(['type', 'action']);
        });

        Schema::table('store_products', function (Blueprint $table) {
            $table->index('category');
            $table->index('barcode');
            $table->index('stock');
        });

        Schema::table('store_gifts', function (Blueprint $table) {
            $table->index('category');
            $table->index('barcode');
            $table->index('stock');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['event_status']);
            $table->dropIndex(['status']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['event_id']);
            $table->dropIndex(['date']);
            $table->dropIndex(['payment_method']);
            $table->dropIndex(['client_name']);
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['category']);
            $table->dropIndex(['boxes']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex(['type', 'action']);
        });

        Schema::table('store_products', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['barcode']);
            $table->dropIndex(['stock']);
        });

        Schema::table('store_gifts', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['barcode']);
            $table->dropIndex(['stock']);
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['category']);
        });
    }
};
