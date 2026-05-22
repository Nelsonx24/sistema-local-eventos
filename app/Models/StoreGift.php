<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class StoreGift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'detail',
        'cost',
        'sale_price',
        'stock',
        'barcode',
        'category',
        'gift_type_id',
        'image',
    ];

    protected $appends = ['image_url'];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image ? Storage::url($this->image) : null,
        );
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(StoreGiftType::class, 'gift_type_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(StoreGiftSale::class);
    }
}
