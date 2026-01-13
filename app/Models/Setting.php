<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];
    
    // Helper to get a setting safely
    public static function get($key, $default = null)
    {
        return self::find($key)?->value ?? $default;
    }
}