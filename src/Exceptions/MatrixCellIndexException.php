<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;
use App\ValueObject\MatrixCoordinates;

class MatrixCellIndexException extends RuntimeException
{

    public static function outOfScope(
        int $size,
        MatrixCoordinates $coordinates,
    ): self
    {
        return new self("Matrix cell index cannot be resolved. The coordinates {$coordinates} are out of matrix scope {$size}.");
    }

}
