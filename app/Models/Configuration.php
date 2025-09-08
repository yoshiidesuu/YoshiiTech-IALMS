<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class Configuration extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
        'is_encrypted',
        'validation_rules',
        'sort_order'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_encrypted' => 'boolean',
        'validation_rules' => 'array',
        'sort_order' => 'integer'
    ];

    /**
     * Get the value attribute with proper type casting and decryption
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // Decrypt if encrypted
                if ($this->is_encrypted && $value) {
                    try {
                        $value = Crypt::decryptString($value);
                    } catch (\Exception $e) {
                        return null;
                    }
                }

                // Cast to appropriate type
                return $this->castValue($value, $this->type);
            },
            set: function ($value) {
                // Cast and serialize if needed
                $processedValue = $this->prepareValueForStorage($value, $this->type);
                
                // Encrypt if needed
                if ($this->is_encrypted && $processedValue !== null) {
                    $processedValue = Crypt::encryptString($processedValue);
                }

                return $processedValue;
            }
        );
    }

    /**
     * Cast value to appropriate type
     */
    private function castValue($value, $type)
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'array', 'json' => json_decode($value, true),
            default => (string) $value
        };
    }

    /**
     * Prepare value for storage
     */
    private function prepareValueForStorage($value, $type)
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'array', 'json' => json_encode($value),
            'integer' => (string) $value,
            'float' => (string) $value,
            default => is_array($value) ? json_encode($value) : (string) $value
        };
    }

    /**
     * Get configuration value by key with caching
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "config.{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $config = static::where('key', $key)->first();
            return $config ? $config->value : $default;
        });
    }

    /**
     * Set configuration value
     */
    public static function set(string $key, $value, array $options = [])
    {
        $config = static::updateOrCreate(
            ['key' => $key],
            array_merge([
                'value' => $value,
                'type' => static::detectType($value),
            ], $options)
        );

        // Clear cache
        Cache::forget("config.{$key}");
        
        return $config;
    }

    /**
     * Get all configurations by group
     */
    public static function getByGroup(string $group)
    {
        return static::where('group', $group)
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Get public configurations (safe for frontend)
     */
    public static function getPublic()
    {
        return static::where('is_public', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group')
            ->map(function ($configs) {
                return $configs->pluck('value', 'key');
            })
            ->toArray();
    }

    /**
     * Detect value type
     */
    private static function detectType($value)
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'array',
            default => 'string'
        };
    }

    /**
     * Clear configuration cache
     */
    public static function clearCache()
    {
        Cache::flush();
    }

    /**
     * Boot method to clear cache on model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($config) {
            Cache::forget("config.{$config->key}");
        });

        static::deleted(function ($config) {
            Cache::forget("config.{$config->key}");
        });
    }
}
