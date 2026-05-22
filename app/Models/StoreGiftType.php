<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreGiftType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function gifts(): HasMany
    {
        return $this->hasMany(StoreGift::class, 'gift_type_id');
    }
}
