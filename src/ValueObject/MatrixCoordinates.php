<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Exceptions\InvalidCoordinatesException;
use JetBrains\PhpStorm\Immutable;

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

    public function equalsTo(MatrixCoordinates $coordinates): bool
    {
        return $this->getMaxCoordinateDistanceFromCoordinates($coordinates) === 0;
    }

    public function getMaxCoordinateDistanceFromCoordinates(MatrixCoordinates $coordinates): int
    {
        return max(
            abs($this->coordinateX - $coordinates->coordinateX),
            abs($this->coordinateY - $coordinates->coordinateY)
        );
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
