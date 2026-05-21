<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreGiftSale extends Model
{
    protected $fillable = [
        'store_gift_id',
        'quantity',
        'unit_cost',
        'unit_price',
        'total_amount',
        'profit',
        'date',
    ];

    public function gift(): BelongsTo
    {
        return $this->belongsTo(StoreGift::class, 'store_gift_id');
    }
}
