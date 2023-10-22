<?php declare(strict_types = 1);

namespace App\Helper;

use App\Exceptions\InvalidIterationsCountException;
use App\Exceptions\InvalidMatrixSizeException;
use App\ValueObject\Matrix;
use App\ValueObject\MatrixCell;

class GameMaster
{

    /**
     * @throws InvalidMatrixSizeException
     */
    public static function runGame(
        Matrix $matrix,
        int $iterationsCount,
        bool $debug = false,
    ): void
    {
        if ($iterationsCount < 0) {
            InvalidIterationsCountException::negative($iterationsCount);
        }

        if ($debug) {
            echo $matrix->debugDataWithFuturePossibleBreeds()."\n";
        }

        self::selectWinningBreedsForNextIteration($matrix);

        for ($i=0; $i<$iterationsCount; $i++) {
            if ($debug) {
                echo $matrix->debugDataWithCurrentBreed() . "\n";
            }

            self::gatherFuturePossibleBreeds($matrix);

            if ($debug) {
                echo $matrix->debugDataWithFuturePossibleBreeds() . "\n";
            }

            self::selectWinningBreedsForNextIteration($matrix);
        }

        // TODO this data might be beneficial for output debugging or verification of correct behavior
        self::gatherFuturePossibleBreeds($matrix);
    }

    private static function gatherFuturePossibleBreeds(Matrix $matrix): void
    {
        foreach ($matrix->getCells() as $cell) {
            $countsOfBreeds = self::getCountsOfBreedsInSurroundingCells($cell);

            foreach ($countsOfBreeds as $breed => $count) {
                if ($count === 2 && $breed === $cell->getCurrentBreed()) {
                    $cell->addFuturePossibleBreed($breed);
                }

                if ($count === 3) {
                    $cell->addFuturePossibleBreed($breed);
                }
            }
        }
    }

    /**
     * @return array<string, int>
     */
    private static function getCountsOfBreedsInSurroundingCells(MatrixCell $cell): array
    {
        $countsOfBreeds = [];

        foreach ($cell->getSurroundingCells() as $cell) {
            $breed = $cell->getCurrentBreed();

            if ($breed === null) {
                continue;
            }

            if (!array_key_exists($breed, $countsOfBreeds)) {
                $countsOfBreeds[$breed] = 0;
            }

            $countsOfBreeds[$breed]++;
        }

        return $countsOfBreeds;
    }

    private static function selectWinningBreedsForNextIteration(Matrix $matrix): void
    {
        foreach ($matrix->getCells() as $cell) {
            $possibleBreeds = $cell->getFuturePossibleBreeds();

            $winningBreed = count($possibleBreeds) > 0
                ? $possibleBreeds[array_rand($possibleBreeds)]
                : null;

            $cell->setCurrentBreed($winningBreed);
            $cell->resetFuturePossibleBreeds();
        }
    }

}
