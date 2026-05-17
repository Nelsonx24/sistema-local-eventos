<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'staff';

    protected $fillable = [
        'name',
        'role',
        'username',
        'password',
        'email',
        'status',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Administrador';
    }

    public function isSeller(): bool
    {
        return $this->role === 'Vendedor';
    }

    public function isCM(): bool
    {
        return $this->role === 'CM';
    }

    public function canAccess(): bool
    {
        return in_array($this->role, ['Administrador', 'Vendedor', 'CM']);
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'Administrador' => 'Administrador',
            'Vendedor' => 'Vendedor',
            'CM' => 'CM',
            default => 'Personal',
        };
    }
}