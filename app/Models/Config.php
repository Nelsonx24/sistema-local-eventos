<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, $default = null)
    {
        $config = static::where('key', $key)->first();

        return $config ? $config->value : $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getQR(): string
    {
        return static::get('qr', 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=Demo-Payment');
    }

    public static function setQR(string $url): void
    {
        static::set('qr', $url);
    }

    public static function getEventTypes(): array
    {
        $types = static::get('event_types');

        return $types ? json_decode($types, true) : ['Boda', 'Corporativo', 'Cumpleaños', 'Social'];
    }

    public static function setEventTypes(array $types): void
    {
        static::set('event_types', json_encode($types));
    }

    public static function getContractSettings(): array
    {
        $settings = static::get('contract_settings');

        return $settings ? json_decode($settings, true) : [
            'salon_name' => 'Salón de Eventos GRAN CAÑAVERAL',
            'representative' => 'CINTHIA FLORES CHOQUE',
            'representative_ci' => '____________________',
            'city' => 'Cochabamba',
        ];
    }

    public static function setContractSettings(array $settings): void
    {
        static::set('contract_settings', json_encode($settings));
    }

    public static function getWatermark(): string
    {
        return static::get('watermark', '');
    }

    public static function setWatermark(string $path): void
    {
        static::set('watermark', $path);
    }
}
