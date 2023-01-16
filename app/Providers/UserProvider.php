<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\UserProvider as BaseUserProvider;

class UserProvider implements BaseUserProvider {

	/**
	 * @param mixed $identifier
	 * @return Authenticatable|Model|null
	 */
	public function retrieveById($identifier) {
		return User::query()->where('_id', $identifier)->first();
	}

	public function retrieveByToken($identifier, $token) {
		return User::query()->where('token', $token)->first();
	}

	public function updateRememberToken(Authenticatable $user, $token) {}

	/**
	 * @param array $credentials
	 * @return Authenticatable|Model|null
	 */
	public function retrieveByCredentials(array $credentials) {
		if(!Arr::exists($credentials, 'email_address')) {
            return null;
        }
		return User::query()->where('email_address', $credentials['email_address'])->first();
	}

	public function validateCredentials($user, array $credentials): bool {

		if(
			$user === null ||
			!Arr::exists($credentials, 'email_address')
		) return false;

		return Hash::check($credentials['password'], $user->password);

	}
}
