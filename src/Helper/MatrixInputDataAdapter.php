<?php declare(strict_types = 1);

namespace App\Helper;

use App\Exceptions\CannotAddCellToMatrixException;
use App\Exceptions\CannotAddSurroundingCellToMatrixCellException;
use App\Exceptions\CannotGetCellFromMatrixException;
use App\Exceptions\CannotGetSurroundingCoordinatesListException;
use App\Exceptions\GameCannotContinueException;
use App\Exceptions\InvalidCoordinatesException;
use App\Exceptions\InvalidInputDataException;
use App\Exceptions\InvalidMatrixSizeException;
use App\Exceptions\MatrixCellIndexException;
use App\ValueObject\Matrix;
use App\ValueObject\MatrixCell;
use App\ValueObject\MatrixCoordinates;
use App\ValueObject\ParsedGameAssignment;
use LogicException;

class MatrixInputDataAdapter
{

    /**
     * @param mixed[]|null
     * @throws InvalidInputDataException
     * @throws MatrixCellIndexException
     * @throws CannotGetCellFromMatrixException
     * @throws GameCannotContinueException
     */
    public static function parseDataIntoMatrix(?array $data): ParsedGameAssignment
    {
        if ($data === null) {
            throw InvalidInputDataException::create('Invalid data format provided.');
        }

        $matrixSize = $data['dimension'] ?? null;
        $breedsCount = $data['speciesCount'] ?? null;
        $maxIterationsCount = $data['iterationsCount'] ?? null;
        $organisms = $data['organisms'] ?? null;

        if ($matrixSize === null) {
            throw InvalidInputDataException::create('Dimension must be provided.');
        }

        if (!is_int($matrixSize) || $matrixSize <= 0) {
            throw InvalidInputDataException::create('Dimension must be positive integer.');
        }

        if ($breedsCount === null) {
            throw InvalidInputDataException::create('SpeciesCount must be provided.');
        }

        if (!is_int($breedsCount) || $breedsCount <= 0) {
            throw InvalidInputDataException::create('SpeciesCount must be positive integer.');
        }

        // TODO probably a meaningless parameter, skipping the actual count validation
        if ($maxIterationsCount === null) {
            throw InvalidInputDataException::create('IterationsCount must be provided.');
        }

        if (!is_int($maxIterationsCount) || $maxIterationsCount <= 0) {
            throw InvalidInputDataException::create('IterationsCount must be positive integer.');
        }

        if ($organisms === null) {
            throw InvalidInputDataException::create('Organisms must be provided.');
        }

        if (!is_array($organisms)) {
            throw InvalidInputDataException::create('Organisms must be array.');
        }

        if (count($organisms) === 0) {
            throw GameCannotContinueException::emptyMatrix();
        }

        try {
            $matrix = MatrixWithCellsBuilder::buildWithSurroundingCellsForMatrixSize($matrixSize);
        } catch (InvalidMatrixSizeException $e) {
            throw new LogicException($e->getMessage(), 0, $e);
        }

        foreach ($organisms as $key => $organism) {
            $coordinateX = $organism['x_pos'] ?? null;
            $coordinateY = $organism['y_pos'] ?? null;
            $breed = $organism['species'] ?? null;

            if ($coordinateX === null) {
                throw InvalidInputDataException::create("X_pos must be provided for organism #{$key}");
            }

            if (!is_int($coordinateX) || $coordinateX <= 0) {
                throw InvalidInputDataException::create("X_pos must be positive integer for organism #{$key}");
            }

            if ($coordinateY === null) {
                throw InvalidInputDataException::create("Y_pos must be provided for organism #{$key}");
            }

            if (!is_int($coordinateY) || $coordinateY <= 0) {
                throw InvalidInputDataException::create("Y_pos must be positive integer for organism #{$key}");
            }

            if ($breed === null) {
                throw InvalidInputDataException::create("Species must be provided for organism #{$key}");
            }

            $breed = trim($breed);
            if (!is_string($breed) || strlen($breed) === 0) {
                throw InvalidInputDataException::create("Species must be non-empty string for organism #{$key}");
            }

            $coordinates = new MatrixCoordinates($coordinateX, $coordinateY);

            $matrix->getCell($coordinates)->addFuturePossibleBreed($breed);
        }

        return new ParsedGameAssignment($matrix, $maxIterationsCount);
    }

    public static function getDataFromMatrix(ParsedGameAssignment $parsedGameAssignment): array
    {
        $nonEmptyCellsData = [];

        foreach ($parsedGameAssignment->matrix->getCells() as $cell) {
            if ($cell->getCurrentBreed() === null) {
                continue;
            }

            $nonEmptyCellsData[] = [
                'x_pos' => $cell->getCoordinates()->coordinateX,
                'y_pos' => $cell->getCoordinates()->coordinateY,
                'species' => $cell->getCurrentBreed(),
            ];
        }

        return [
            'dimension' => $parsedGameAssignment->matrix->getSize(),
            'speciesCount' => 1, // TODO consider dropping this field
            'iterationsCount' => $parsedGameAssignment->maxIterationsCount,
            'organisms' => $nonEmptyCellsData,
        ];
    }
}
