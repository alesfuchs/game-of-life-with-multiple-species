<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;
use App\ValueObject\MatrixCoordinates;

class CannotGetSurroundingCoordinatesListException extends RuntimeException
{

    public static function matrixSizeTooSmall(
        MatrixCoordinates $coordinates,
        int $matrixSize,
    ): self
    {
        return new self("Cannot get surrounding coordinates of {$coordinates} for Matrix size {$matrixSize} because it's too small.");
    }

}
