<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

interface Taggable extends Rule
{
    public const PATTERN = '/\A[\w\s-]{2,30}\z/';
}
