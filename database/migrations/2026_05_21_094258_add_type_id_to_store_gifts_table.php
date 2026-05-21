<?php

use App\Models\StoreGift;
use App\Models\StoreGiftType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_gifts', function (Blueprint $table) {
            $table->foreignId('gift_type_id')->nullable()->constrained('store_gift_types')->nullOnDelete();
        });

        StoreGift::chunk(100, function ($gifts) {
            foreach ($gifts as $gift) {
                if ($gift->category) {
                    $type = StoreGiftType::firstOrCreate(['name' => $gift->category]);
                    $gift->gift_type_id = $type->id;
                    $gift->save();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('store_gifts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gift_type_id');
        });
    }
};
