<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;

class InvalidInputDataException extends RuntimeException
{

    public static function create(string $message): self
    {
        return new self($message);
    }

}
