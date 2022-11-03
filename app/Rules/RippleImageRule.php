<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Jamband\Ripple\Ripple;

class RippleImageRule implements Rule
{
    public function __construct(
        private Ripple $ripple,
    ) {
    }

    public function passes($attribute, $value): bool
    {
        return null !== $this->ripple->image();
    }

    public function message(): string
    {
        return __('validation.ripple.image');
    }
}
