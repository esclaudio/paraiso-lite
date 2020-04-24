<?php

namespace App\Auth;

use App\Models\User;
use App\Cache\CacheInterface;

class Auth
{
    const SESSION_USER_KEY = 'login_user';
    const SESSION_REDIRECT_KEY = 'login_redirect';
    
    protected $user = null;

    /**
     * Get the currently authenticated user
     *
     * @return App\Models\User|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;
        if (isset($_SESSION[self::SESSION_USER_KEY])) {
            $user = User::find($_SESSION[self::SESSION_USER_KEY]);
        }

        return $this->user = $user;
    }

    /**
     * Get the ID for the currently authenticated user
     *
     * @return int|null
     */
    public function id()
    {
        if ($user = $this->user()) {
            return $user->id;
        }

        if (isset($_SESSION[self::SESSION_USER_KEY])) {
            return $_SESSION[self::SESSION_USER_KEY];
        }

        return null;
    }

    /**
     * Determine if the current user is authenticated
     *
     * @return bool
     */
    public function check() : bool
    {
        return !is_null($this->user());
    }

    /**
     * Attempt to authenticate a user using the given credentials
     *
     * @param  string $username
     * @param  string $password
     * @return bool
     */
    public function attempt(string $username, string $password): bool
    {
        $user = User::active()->where('username', $username)->first();

        if ($user && password_verify($password, $user->password)) {
            $_SESSION[self::SESSION_USER_KEY] = $user->id;
            return true;
        }

        return false;
    }

    /**
     * Attempt to authenticate a user using the given remember cookie value
     * @param  string $rememberToken [description]
     * @return bool                  [description]
     */
    public function attemptRemember(string $rememberCookie): bool
    {
        $segments = explode('|', $rememberCookie);

        // Determine if the recaller has both segments

        if (count($segments) == 2 && trim($segments[0]) !== '' && trim($segments[1]) !== '') {
            $id = $segments[0];
            $token = $segments[1];

            $user = User::byRememberToken($id, $token)->first();

            if ($user) {
                $_SESSION[self::SESSION_USER_KEY] = $user->id;
                return true;
            }
        }

        return false;
    }

    /**
     * Get remember cookie value
     *
     * @return string
     */
    public function getRememberCookieValue(): string
    {
        $user = $this->user();

        if ($user && $user->remember_token) {
            return $user->id.'|'.$user->remember_token;
        }

        return '';
    }

    /**
     * Get remember cookie name
     * @return string
     */
    public function getRememberCookieName(): string
    {
        return 'remember';
    }

    /**
     * Set redirect url
     *
     * @param string $path Url path
     */
    public function setRedirectUrl(string $path)
    {
        $_SESSION[self::SESSION_REDIRECT_KEY] = $path;
    }

    /**
     * Get redirect url
     *
     * @return null|string
     */
    public function getRedirectUrl()
    {
        $url = null;

        if (isset($_SESSION[self::SESSION_REDIRECT_KEY])) {
            $url = $_SESSION[self::SESSION_REDIRECT_KEY];
            unset($_SESSION[self::SESSION_REDIRECT_KEY]);
        }

        return $url;
    }

    /**
     * Log the user out of the application
     *
     * @return void
     */
    public function logout()
    {
        unset($_SESSION[self::SESSION_USER_KEY]);
    }
}
