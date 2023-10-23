<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;

class InvalidIterationsCountException extends RuntimeException
{

    public static function negative(
        int $iterations,
    ): self
    {
        return new self("Iterations count must be a non-negative integer, {$iterations} provided.");
    }

}
