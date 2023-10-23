<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;
use App\ValueObject\MatrixCell;

class CannotAddCellToMatrixException extends RuntimeException
{

    public static function coordinatesOccupied(MatrixCell $cell): self
    {
        return new self("Cannot add a cell to the Matrix. The coordinates {$cell->getCoordinates()} are already occupied.");
    }

}
