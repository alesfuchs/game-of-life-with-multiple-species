<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;

class InvalidIterationsException extends RuntimeException
{

    public static function notPositive(
        int $iterations,
    ): self
    {
        return new self("Iterations count must be a positive integer, {$iterations} provided.");
    }

}
