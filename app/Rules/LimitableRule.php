<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LimitableRule implements Rule
{
    public function __construct(
        private int $count,
        private int $limit,
    ) {
    }

    public function passes($attribute, $value): bool
    {
        if (false === $value) {
            return true;
        }

        return $this->limit > $this->count;
    }

    public function message(): string
    {
        return __('validation.limitable', ['limit' => $this->limit]);
    }
}
