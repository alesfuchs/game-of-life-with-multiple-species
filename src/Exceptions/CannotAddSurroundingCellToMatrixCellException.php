<?php declare(strict_types = 1);

namespace App\Exceptions;

use RuntimeException;
use App\ValueObject\MatrixCell;

class CannotAddSurroundingCellToMatrixCellException extends RuntimeException
{

    public static function addingSelf(MatrixCell $cell): self
    {
        return new self("Cannot add a Matrix Cell to the list of surrounding cells to for the cell with equal coordinates {$cell->getCoordinates()}.");
    }

    public static function duplicateMatrixCell(MatrixCell $cell): self
    {
        return new self("Cannot add two Matrix Cells with the same coordinates {$cell->getCoordinates()} to the list of surrounding cells.");
    }

    public static function tooFar(
        MatrixCell $centralCell,
        MatrixCell $addedCell,
    ): self
    {
        return new self("Cannot add a Matrix Cell {$addedCell->getCoordinates()} as a surrounding cell to the central Matrix Cell {$centralCell->getCoordinates()} because it is too far away.");
    }

}
