<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales';

    protected $fillable = [
        'event_id',
        'client_name',
        'amount',
        'cash_received',
        'change_given',
        'date',
        'payment_method',
        'status',
        'seller_name',
        'is_printed',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
            'cash_received' => 'decimal:2',
            'change_given' => 'decimal:2',
            'is_printed' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getClientNameAttribute(?string $value): ?string
    {
        return $value !== null ? mb_strtoupper($value) : null;
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum('subtotal');
    }

    public function markAsPrinted(): void
    {
        $this->update(['is_printed' => true]);
    }
}
