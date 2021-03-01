<?php

namespace App\Library\Auth;


use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class TokenGuard implements Guard
{
    use GuardHelpers;
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The name of the query string item from the request containing the API token.
     *
     * @var string
     */
    protected $inputKey;

    /**
     * The name of the token "column" in persistent storage.
     *
     * @var string
     */
    protected $storageKey;

    /**
     * the name of token decrypted
     * @var int
     */
    protected $uid;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider $provider
     * @param  \Illuminate\Http\Request                $request
     *
     * @return void
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;

        $this->inputKey = 'api_token';
        $this->storageKey = 'api_token';
    }

    /**
     * Get the currently authenticated user.
     * @throws \Exception
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $token = $this->getTokenForRequest();
        $user = $this->provider->retrieveByToken('', $token);
        if (is_null($user)) {
            throw new AuthenticationException('Unauthenticated.');
        }
        return $this->user = $user;
    }


    /**
     * Get the token for the current request.
     *
     * @return string
     * @throws \Exception
     */
    public function getTokenForRequest()
    {
        $token = $this->request->query($this->inputKey);

        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }

        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        if (empty($token)) {
            $token = $this->request->getPassword();
        }

        if (empty($token)) {
            throw new AuthenticationException('Unauthenticated.');
        }

        //$this->uid = $this->_validateToken($token);
        return $token;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if ($this->provider->retrieveById($this->uid)) {
            return true;
        }

        return false;
    }

    /**
     * Set the current request instance.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

}