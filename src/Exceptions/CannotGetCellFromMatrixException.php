<?php declare(strict_types = 1);

namespace App\Exceptions;

use App\ValueObject\MatrixCell;
use App\ValueObject\MatrixCoordinates;
use RuntimeException;

class CannotGetCellFromMatrixException extends RuntimeException
{

    public static function coordinatesNotOccupied(MatrixCoordinates $coordinates): self
    {
        return new self("Cannot get a cell from the Matrix. The coordinates {$coordinates} are not occupied.");
    }

}
