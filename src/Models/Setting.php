<?php

namespace Webkraft\Cms\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'webkraft_settings';

    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    /** Read a setting, falling back to config('webkraft.*') then $default. */
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::query()->where('key', $key)->value('value');

        return $row ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
