<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Jamband\Ripple\Ripple;

class RippleUrlRule implements Rule
{
    public function __construct(
        private Ripple $ripple,
    ) {
    }

    public function passes($attribute, $value): bool
    {
        return null !== $this->ripple->url();
    }

    public function message(): string
    {
        $message = __('validation.ripple.url');
        assert(is_string($message));

        return $message;
    }
}
