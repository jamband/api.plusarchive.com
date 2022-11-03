<?php

declare(strict_types=1);

namespace App\Rules;

class TaggableRule implements Taggable
{
    public function passes($attribute, $value): bool
    {
        if (is_string($value) && preg_match(self::PATTERN, $value)) {
            return true;
        }

        return false;
    }

    public function message(): string
    {
        return __('validation.taggable');
    }
}
