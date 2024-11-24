<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

interface Taggable extends ValidationRule
{
    public const string PATTERN = '/\A[\w\s-]{2,30}\z/';
}
