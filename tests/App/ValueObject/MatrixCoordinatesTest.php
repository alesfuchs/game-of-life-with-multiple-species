<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Exceptions\InvalidCoordinatesException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MatrixCoordinatesTest extends TestCase
{

    public function testConstructSuccess(): void
    {
        $matrixCoordinates = new MatrixCoordinates(1,2);

        self::assertSame(1, $matrixCoordinates->coordinateX);
        self::assertSame(2, $matrixCoordinates->coordinateY);
    }

    #[DataProvider('constructFailDataProvider')]
    public function testConstructFail(
        int $coordinateX,
        int $coordinateY,
    ): void
    {
        $this->expectException(InvalidCoordinatesException::class);
        $this->expectExceptionMessage('Matrix coordinates must be positive integers');

        new MatrixCoordinates($coordinateX,$coordinateY);
    }

    /**
     * @return list<array{int, int}>
     */
    public static function constructFailDataProvider(): array
    {
        return [
            [0, 1],
            [1, 0],
            [-1, 1],
        ];
    }

    #[DataProvider('equalsToDataProvider')]
    public function testEqualsTo(
        int $coordinateX,
        int $coordinateY,
        bool $expectedResult,
    ): void
    {
        $matrixCoordinates1 = new MatrixCoordinates(5,5);
        $matrixCoordinates2 = new MatrixCoordinates($coordinateX,$coordinateY);

        self::assertSame($expectedResult, $matrixCoordinates1->equalsTo($matrixCoordinates2));
    }

    /**
     * @return list<array{int, int, bool}>
     */
    public static function equalsToDataProvider(): array
    {
        return [
            [5, 5, true],
            [5, 6, false],
            [6, 5, false],
            [4, 5, false],
            [5, 4, false],
            [1, 1, false],
        ];
    }

    #[DataProvider('distanceDataProvider')]
    public function testGetMaxCoordinateDistanceFromCoordinates(
        int $coordinateX,
        int $coordinateY,
        int $expectedResult,
    ): void
    {
        $matrixCoordinates1 = new MatrixCoordinates(5,5);
        $matrixCoordinates2 = new MatrixCoordinates($coordinateX,$coordinateY);

        self::assertSame($expectedResult, $matrixCoordinates1->getMaxCoordinateDistanceFromCoordinates($matrixCoordinates2));
    }

    /**
     * @return list<array{int, int, int}>
     */
    public static function distanceDataProvider(): array
    {
        return [
            [5, 5, 0],
            [5, 6, 1],
            [6, 5, 1],
            [4, 5, 1],
            [5, 4, 1],
            [1, 1, 4],
            [1, 11, 6],
        ];
    }

}
