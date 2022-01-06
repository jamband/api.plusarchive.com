<?php

declare(strict_types=1);

namespace app\tests\console;

trait StdOutBufferControllerTrait
{
    private string $stdOutBuffer = '';

    public function stdout($string): void
    {
        $this->stdOutBuffer .= $string;
    }

    public function flushStdOutBuffer(): string
    {
        $result = $this->stdOutBuffer;
        $this->stdOutBuffer = '';

        return $result;
    }
}
