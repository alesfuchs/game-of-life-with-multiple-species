<?php declare(strict_types = 1);

namespace App\Helper;

use App\Exceptions\CannotAddCellToMatrixException;
use App\Exceptions\CannotAddSurroundingCellToMatrixCellException;
use App\Exceptions\CannotGetCellFromMatrixException;
use App\Exceptions\CannotGetSurroundingCoordinatesListException;
use App\Exceptions\InvalidCoordinatesException;
use App\Exceptions\InvalidMatrixSizeException;
use App\Exceptions\MatrixCellIndexException;
use App\ValueObject\Matrix;
use App\ValueObject\MatrixCell;
use App\ValueObject\MatrixCoordinates;
use LogicException;

class MatrixWithCellsBuilder
{

    /**
     * @throws InvalidMatrixSizeException
     */
    private static function buildForMatrixSize(int $matrixSize): Matrix
    {
        try {
            $matrix = new Matrix($matrixSize);

            foreach (range(1, $matrixSize) as $coordinateY) {
                foreach (range(1, $matrixSize) as $coordinateX) {
                    $matrix->addCell(
                        new MatrixCell(
                            new MatrixCoordinates($coordinateX, $coordinateY),
                        ),
                    );
                }
            }

            return $matrix;
        } catch (InvalidCoordinatesException $e) {
            throw new LogicException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws InvalidMatrixSizeException
     */
    public static function buildWithSurroundingCellsForMatrixSize(int $matrixSize): Matrix
    {
        $matrix = self::buildForMatrixSize($matrixSize);

        try {
            foreach ($matrix->getCells() as $cell) {
                foreach ($cell->getCoordinates()->getSurroundingCoordinatesList($matrixSize) as $surroundingCoordinates) {
                    $cell->addSurroundingCell(
                        $matrix->getCell($surroundingCoordinates),
                    );
                }
            }

            return $matrix;
        } catch (
            InvalidMatrixSizeException |
            CannotGetSurroundingCoordinatesListException |
            CannotAddSurroundingCellToMatrixCellException |
            MatrixCellIndexException |
            CannotGetCellFromMatrixException $e
        ) {
            throw new LogicException($e->getMessage(), 0, $e);
        }
    }

}
