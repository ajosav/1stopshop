<?php

namespace App\Repositories\Password;

use Illuminate\Support\Str;
use Illuminate\Auth\Passwords\PasswordBrokerManager;

class CustomPasswordBrokerManager extends PasswordBrokerManager
{

    // Override the createTokenRepository function to return your
    // custom token repository instead of the standard one
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = isset($config['connection']) ? $config['connection'] : null;

        return new CustomDatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire']
        );
    }

}