<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'client_name',
        'client_id',
        'client_phone',
        'event_type',
        'date',
        'status',
        'payment_status',
        'event_status',
        'total_amount',
        'advance_payment',
        'balance_pending',
        'payment_due_date',
        'signed_contract_url',
        'registered_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'payment_due_date' => 'date',
            'total_amount' => 'decimal:2',
            'advance_payment' => 'decimal:2',
            'balance_pending' => 'decimal:2',
            'status' => 'string',
            'payment_status' => 'string',
            'event_status' => 'string',
        ];
    }

    public function getClientNameAttribute(?string $value): ?string
    {
        return $value !== null ? mb_strtoupper($value) : null;
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'event_id_new', 'id');
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'Confirmado';
    }

    public function isPending(): bool
    {
        return $this->status === 'Pendiente';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'Cancelado';
    }

    public function isClosed(): bool
    {
        return $this->status === 'Cerrado';
    }

    public function isPaid(): bool
    {
        return $this->balance_pending == 0;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Confirmado' => 'emerald',
            'Pendiente' => 'amber',
            'Cancelado' => 'red',
            'Cerrado' => 'slate',
            default => 'gray',
        };
    }
}
