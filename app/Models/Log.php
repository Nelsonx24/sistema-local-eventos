<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Log extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'type',
        'action',
        'description',
        'user_id',
        'user_name',
        'old_values',
        'new_values',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public static function record(string $type, string $action, string $description, ?array $oldValues = null, ?array $newValues = null): void
    {
        $description = str_replace(["\r", "\n", "\0"], ' ', $description);

        $data = [
            'type' => $type,
            'action' => $action,
            'description' => $description,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ];

        defer(fn () => static::create($data));
    }
}
