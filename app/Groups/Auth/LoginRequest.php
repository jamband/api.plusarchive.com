<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Events\Dispatcher as EventDispatcher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * @property string|null $email
 * @property string|null $password
 */
class LoginRequest extends FormRequest
{
    private AuthManager $auth;
    private RateLimiter $rateLimiter;
    private EventDispatcher $event;

    private const MAX_ATTEMPTS = 5;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(
        AuthManager $auth,
        RateLimiter $rateLimiter,
        EventDispatcher $event,
    ): array {
        $this->auth = $auth;
        $this->rateLimiter = $rateLimiter;
        $this->event = $event;

        return [
            'email' => [
                'bail',
                'required',
                'string',
                'email',
            ],
            'password' => [
                'bail',
                'required',
                'string',
            ],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!$this->auth->guard('web')->attempt(
            $this->validated(),
            $this->boolean('remember')
        )) {
            $this->rateLimiter->hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->rateLimiter->clear($this->throttleKey());
    }

    private function ensureIsNotRateLimited(): void
    {
        if ($this->rateLimiter->tooManyAttempts(
            $this->throttleKey(),
            self::MAX_ATTEMPTS
        )) {
            $this->event->dispatch(new Lockout($this));
            $seconds = $this->rateLimiter->availableIn($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }
    }

    private function throttleKey(): string
    {
        assert(is_string($this->email));

        return Str::lower($this->email).'|'.$this->ip();
    }
}
