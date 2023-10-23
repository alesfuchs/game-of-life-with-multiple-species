<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;

class InvalidMatrixSizeException extends RuntimeException
{

    public static function notPositive(
        int $size,
    ): self
    {
        return new self("Matrix size must be a positive integer, {$size} provided.");
    }

}
