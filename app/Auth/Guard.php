<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;
use Illuminate\Cookie\CookieJar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use PHPOpenSourceSaver\JWTAuth\JWT;

class Guard implements \Illuminate\Contracts\Auth\Guard {

	use GuardHelpers, Macroable;

	/**
	 * @var JWT
	 */
	protected JWT $jwt;

	/**
	 * @var UserProvider
	 */
	protected $provider;

	/**
	 * @var Request
	 */
	protected Request $request;

	/**
	 * @var Session
	 */
	protected Session $session;

	/**
	 * @var CookieJar
	 */
	protected CookieJar $cookie;

	/**
	 * @var User|null
	 */
	protected $user = null;

    protected $token = null;

	/**
	 * @var bool
	 */
	protected bool $loggedOut = false;

	public function __construct(
		JWT $jwt, UserProvider $provider, Request $request,
		Session $session, CookieJar $cookie
	) {

		$this->jwt = $jwt;
		$this->provider = $provider;
		$this->request = $request;
		$this->session = $session;
		$this->cookie = $cookie;

		$this->user = null;

	}

	/**
	 * Get the currently authenticated user.
	 *
	 * @return User|null
	 */
	public function user(): ?User {

		if($this->loggedOut)
			return null;

		if($this->user !== null)
			return $this->user;

		$token = $this->getAuthToken();
		if(empty($token))
			return null;

		$this->jwt->setToken($token);

		if($payload = $this->jwt->check(true)) {
			$user = $this->provider->retrieveById($payload['sub']);
			if($user === null)
				return null;

			$this->user = $user;

			return $this->user;

		}

		return null;

	}

	/**
	 * Get the ID for the currently authenticated user.
	 *
	 * @return int|null
	 * @noinspection PhpUndefinedFieldInspection
	 */
	public function id(): ?int {
		if($this->loggedOut || !$this->check())
			return null;
		return $this->user->id;
	}

	/**
	 * Attempt to authenticate a user using the given credentials.
	 *
	 * @param array $credentials
	 * @param bool $remember Set whether the user should be remembered. Ignored if <b>$login</b> is false.
	 * @param bool $login Set whether the user should be logged in. Used by <b>validate</b>.
	 * @param bool $storeAuthToken Set whether the token should be stored in session or cache.
	 *   This variable is used by the API. Ignored if <b>$login</b> is false.
	 * @return bool
	 */
	public function attempt(
		array $credentials = [],
		bool $remember = true,
		bool $login = true,
		bool $storeAuthToken = true
	): bool {
		$user = $this->provider->retrieveByCredentials($credentials);
		if($this->hasValidCredentials($user, $credentials)) {
			if($login) {
				$this->login($user, $remember, $storeAuthToken);
			}
			return true;
		}
		return false;
	}

	/**
	 * Validate a user's credentials.
	 *
	 * @param array $credentials
	 * @return bool
	 */
	public function validate(array $credentials = []): bool {
		return $this->attempt($credentials, false, false);
	}

	/**
	 * Log a user into the application.
	 *
	 * @param Authenticatable|Model|null $user
	 * @param bool $remember
	 * @param bool $storeAuthToken
	 * @return void
	 */
	public function login($user, bool $remember = true, bool $storeAuthToken = true) {
		$ttl = 60 * 24;
		if($remember) {
			$ttl = 60 * 24 * 365;
		}
		$this->setTTL($ttl);

		/** @noinspection PhpParamsInspection */
		$token = $this->jwt->fromUser($user);

		if($storeAuthToken) {
			// Store the token
			if($remember) {
				// Remove the auth token from session
				$this->session->forget('auth_token');
				$this->cookie->queue(
					$this->cookie->make('auth_token', $token, 60 * 24 * 365)
				);

			} else {
				// Delete the auth token from cookies
				$this->cookie->queue(
					$this->cookie->forget('auth_token')
				);
				$this->session->put('auth_token', $token);
			}
		}

		$this->setToken($token)->setUser($user);

	}

    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

    public function getToken() {
        return $this->token;
    }

	/**
	 * Log the user out of the application.
	 *
	 * @return void
	 */
	public function logout() {

		$this->session->forget('auth_token');
		$this->cookie->queue(
			$this->cookie->forget('auth_token')
		);

		$this->user = null;
		$this->loggedOut = true;

	}

	protected function hasValidCredentials($user, $credentials): bool {
		return $user !== null && $this->provider->validateCredentials($user, $credentials);
	}

	/**
	 * Set the token ttl.
	 *
	 * @param int $ttl TTL minutes.
	 * @return $this
	 */
	public function setTTL(int $ttl): Guard {
		$this->jwt->factory()->setTTL($ttl);
		return $this;
	}

	/**
	 * Add any custom claims.
	 *
	 * @param array $claims
	 * @return $this
	 */
	public function claims(array $claims): Guard {
		$this->jwt->claims($claims);
		return $this;
	}

	/**
	 * Set the current user session.
	 *
	 * @param $userSession
	 * @return $this
	 */
	public function setUserSession($userSession): Guard {
		$this->userSession = $userSession;
		return $this;
	}

	/**
	 * Get the current user session.
	 *
	 * @return mixed
	 */
	public function getUserSession() {
		return $this->userSession;
	}

	/**
	 * Set the current user.
	 *
	 * @param Authenticatable|Model|null $user
	 * @return $this
	 */
	public function setUser($user): Guard {

		$this->user = $user;
		$this->loggedOut = false;

		return $this;

	}

	/**
	 * Get the current user.
	 *
	 * @return User|null
	 */
	public function getUser(): User|null {
		return $this->user;
	}

	/**
	 * Get the auth token from the current session or request.
	 *
	 * @return string|null
	 */
	public function getAuthToken(): ?string {
		$token = $this->session->get('auth_token');
		if(empty($token)) {
			$token = $this->request->cookie('auth_token');
		}
		if(empty($token)) {
			$token = $this->request->query('auth_token');
		}
		if(empty($token)) {
			$token = $this->request->input('auth_token');
		}
		if(empty($token)) {
			$token = $this->request->bearerToken();
		}
		return $token;
	}

}
