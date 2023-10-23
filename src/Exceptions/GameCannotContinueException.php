<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;

class GameCannotContinueException extends RuntimeException
{

    public static function emptyMatrix(): self
    {
        return new self('The Matrix is completely dead.');
    }

}
