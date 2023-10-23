<?php declare(strict_types = 1);

namespace App\ValueObject;

use JetBrains\PhpStorm\Immutable;
use App\Exceptions\CannotGetSurroundingCoordinatesListException;
use App\Exceptions\InvalidCoordinatesException;
use App\Exceptions\InvalidMatrixSizeException;
use function abs;
use function max;
use function min;
use function range;

#[Immutable]
class MatrixCoordinates
{

    public readonly int $coordinateX;

    public readonly int $coordinateY;

    /**
     * @throws InvalidCoordinatesException
     */
    public function __construct(
        int $coordinateX,
        int $coordinateY,
    )
    {
        if ($coordinateX <= 0 || $coordinateY <= 0) {
            throw InvalidCoordinatesException::notPositive(self::format($coordinateX, $coordinateY));
        }

        $this->coordinateX = $coordinateX;
        $this->coordinateY = $coordinateY;
    }

    public function equalsTo(self $coordinates): bool
    {
        return $this->getMaxCoordinateDistanceFromCoordinates($coordinates) === 0;
    }

    public function getMaxCoordinateDistanceFromCoordinates(self $coordinates): int
    {
        return max(
            abs($this->coordinateX - $coordinates->coordinateX),
            abs($this->coordinateY - $coordinates->coordinateY),
        );
    }

    /**
     * @return list<self>
     *
     * @throws InvalidMatrixSizeException
     * @throws CannotGetSurroundingCoordinatesListException
     */
    public function getSurroundingCoordinatesList(int $matrixSize): array
    {
        if ($matrixSize <= 0) {
            throw InvalidMatrixSizeException::notPositive($matrixSize);
        }

        if ($matrixSize < $this->coordinateX || $matrixSize < $this->coordinateY) {
            throw CannotGetSurroundingCoordinatesListException::matrixSizeTooSmall($this, $matrixSize);
        }

        $minCoordinateX = max(1, $this->coordinateX - 1);
        $maxCoordinateX = min($matrixSize, $this->coordinateX + 1);

        $minCoordinateY = max(1, $this->coordinateY - 1);
        $maxCoordinateY = min($matrixSize, $this->coordinateY + 1);

        $surroundingCoordinatesList = [];

        foreach (range($minCoordinateY, $maxCoordinateY) as $coordinateY) {
            foreach (range($minCoordinateX, $maxCoordinateX) as $coordinateX) {
                $coordinates = new self($coordinateX, $coordinateY);

                if (!$coordinates->equalsTo($this)) {
                    $surroundingCoordinatesList[] = $coordinates;
                }
            }
        }

        return $surroundingCoordinatesList;
    }

    public function __toString(): string
    {
        return self::format($this->coordinateX, $this->coordinateY);
    }

    private static function format(
        int $coordinateX,
        int $coordinateY,
    ): string
    {
        return "[{$coordinateX},{$coordinateY}]";
    }

}
