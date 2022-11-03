<?php

declare(strict_types=1);

namespace App\Rules;

class TaggablesRule implements Taggable
{
    public function passes($attribute, $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $element) {
            if (is_string($element) && preg_match(self::PATTERN, $element)) {
                continue;
            } else {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return __('validation.taggables');
    }
}
