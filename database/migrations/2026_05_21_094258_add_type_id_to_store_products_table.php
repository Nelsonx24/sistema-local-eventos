<?php

use App\Models\StoreProduct;
use App\Models\StoreProductType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            $table->foreignId('product_type_id')->nullable()->constrained('store_product_types')->nullOnDelete();
        });

        StoreProduct::chunk(100, function ($products) {
            foreach ($products as $product) {
                if ($product->category) {
                    $type = StoreProductType::firstOrCreate(['name' => $product->category]);
                    $product->product_type_id = $type->id;
                    $product->save();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('store_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_type_id');
        });
    }
};
