<?php

namespace App\Guards;

use App\Providers\ApiUserProvider;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class ApiGuard implements Guard
{
    use GuardHelpers;

    private $username;
    private $password;

    /**
     * ApiGuard constructor.
     * @param ApiUserProvider $provider
     * @param array $credentials
     */
    public function __construct(ApiUserProvider $provider, array $credentials)
    {
        $this->provider = $provider;
        $this->username = $credentials['username'];
        $this->password = $credentials['password'];
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->user)
        {
            return $this->user;
        }

        $user = null;

        $user = $this->provider->retrieveByCredentials([
            'username' => $this->username,
        ]);

        if ($user !== null)
        {
            if ($this->provider->validateCredentials($user, ['username' => $this->username, 'password' => $this->password]))
            {
                return $this->user = $user;
            }
        }

        return null;
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return $this->provider->validateCredentials($this->user, $credentials);
    }
}