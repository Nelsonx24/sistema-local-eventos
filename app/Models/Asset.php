<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assets';

    protected $fillable = [
        'name',
        'category',
        'quantity',
        'condition',
        'last_maintenance',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'last_maintenance' => 'date',
        ];
    }

    public function isInGoodCondition(): bool
    {
        return $this->condition === 'Bueno';
    }

    public function needsMaintenance(): bool
    {
        return $this->condition === 'Malo' || $this->condition === 'Regular';
    }
}
