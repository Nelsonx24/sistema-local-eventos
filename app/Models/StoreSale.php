<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSale extends Model
{
    protected $fillable = [
        'store_product_id',
        'quantity',
        'unit_cost',
        'unit_price',
        'total_amount',
        'profit',
        'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class, 'store_product_id');
    }
}
