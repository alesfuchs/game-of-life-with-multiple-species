<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;
use App\ValueObject\MatrixCoordinates;

class CannotGetCellFromMatrixException extends RuntimeException
{

    public static function coordinatesNotOccupied(MatrixCoordinates $coordinates): self
    {
        return new self("Cannot get a cell from the Matrix. The coordinates {$coordinates} are not occupied.");
    }

}
