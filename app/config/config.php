<?php

namespace App\Config;

class Config
{
    // Environment variables
    private static $config = [];

    public static function init()
    {
        // Load environment variables from .env file
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    self::$config[trim($key)] = trim($value);
                }
            }
        }
    }

    public static function get($key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }

    // Hydrogen Pay API configuration
    public static function getHydrogenPayConfig()
    {
        $environment = self::get('APP_ENV', 'test');
        
        return [
            'auth_key' => self::get('HYDROGEN_AUTH_KEY', ''),
            'api_url' => $environment === 'production' 
                ? 'https://api.hydrogenpay.com/api/v3/account/virtual-account'
                : 'https://qa-api.hydrogenpay.com/bevpay/api/v3/account/virtual-account',
        ];
    }
    public static function env($key, $default = null)
    {
        $env = parse_ini_file(__DIR__ . '/../../.env');
        return $env[$key] ?? $default;
    }

    public static function getBaseUrl()
    {
        return self::env('BASE_URL');
    }

    public static function getVeluxApiKey()
    {
        return self::env('VELUX_API_KEY');
    }
}

Config::init();