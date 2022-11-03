<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MultipleUrlsRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        foreach (explode("\n", $value) as $url) {
            if (
                false !== filter_var($url, FILTER_VALIDATE_URL) &&
                preg_match('#\Ahttps?://#', $url)
            ) {
                continue;
            } else {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return __('validation.multiple_urls');
    }
}
