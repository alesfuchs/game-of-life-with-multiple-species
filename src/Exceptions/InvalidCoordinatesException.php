<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;

class InvalidCoordinatesException extends RuntimeException
{

    public static function notPositive(
        string $coordinates,
    ): self
    {
        return new self("Matrix coordinates must be positive integers, {$coordinates} provided.");
    }

}
