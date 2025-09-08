<?php

if (!function_exists('dynamic_config')) {
    /**
     * Get configuration value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function dynamic_config(string $key, $default = null)
    {
        return \App\Models\Configuration::get($key, $default);
    }
}