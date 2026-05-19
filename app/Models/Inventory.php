<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'category',
        'boxes',
        'units_per_box',
        'loose_units',
        'price_per_box',
        'price_per_unit',
        'status',
        'image_box',
        'image_unit',
    ];

    protected function casts(): array
    {
        return [
            'boxes' => 'integer',
            'units_per_box' => 'integer',
            'loose_units' => 'integer',
            'price_per_box' => 'decimal:2',
            'price_per_unit' => 'decimal:2',
        ];
    }

    public function isLowStock(): bool
    {
        return $this->boxes <= 2;
    }

    public function getTotalUnitsAttribute(): int
    {
        return ($this->boxes * $this->units_per_box) + $this->loose_units;
    }

    public function subtractStock(int $quantity, string $type): void
    {
        if ($type === 'Caja') {
            $this->boxes = max(0, $this->boxes - $quantity);
        } else {
            $remaining = $quantity;
            while ($remaining > 0) {
                if ($this->loose_units >= $remaining) {
                    $this->loose_units -= $remaining;
                    $remaining = 0;
                } else {
                    if ($this->boxes > 0) {
                        $this->boxes -= 1;
                        $this->loose_units += $this->units_per_box;
                    } else {
                        $this->loose_units = 0;
                        $remaining = 0;
                    }
                }
            }
        }
        $this->save();
    }
}
