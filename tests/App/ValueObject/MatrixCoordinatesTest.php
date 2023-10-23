<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Exceptions\CannotGetSurroundingCoordinatesListException;
use App\Exceptions\InvalidCoordinatesException;
use App\Exceptions\InvalidMatrixSizeException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

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

    /**
     * @param list<MatrixCoordinates> $expectedResult
     */
    #[DataProvider('getSurroundingCoordinatesListSuccessDataProvider')]
    public function testGetSurroundingCoordinatesListSuccess(
        MatrixCoordinates $coordinates,
        int $matrixSize,
        array $expectedResult,
    ): void
    {
        $surroundingCoordinatesList = $coordinates->getSurroundingCoordinatesList($matrixSize);

        self::assertCount(count($expectedResult), $surroundingCoordinatesList);

        foreach ($surroundingCoordinatesList as $key => $surroundingCoordinates) {
            self::assertTrue($surroundingCoordinates->equalsTo($expectedResult[$key]));
        }
    }

    /**
     * @return array<string, array{coordinates: MatrixCoordinates, matrixSize: int, expectedResult: list<MatrixCoordinates>}>
     */
    public static function getSurroundingCoordinatesListSuccessDataProvider(): array
    {
        return [
            'middle' => [
                'coordinates' => new MatrixCoordinates(5, 5),
                'matrixSize' => 10,
                'expectedResult' => [
                    new MatrixCoordinates(4, 4),
                    new MatrixCoordinates(5, 4),
                    new MatrixCoordinates(6, 4),
                    new MatrixCoordinates(4, 5),
                    new MatrixCoordinates(6, 5),
                    new MatrixCoordinates(4, 6),
                    new MatrixCoordinates(5, 6),
                    new MatrixCoordinates(6, 6),
                ]
            ],
            'left_top' => [
                'coordinates' => new MatrixCoordinates(1, 1),
                'matrixSize' => 10,
                'expectedResult' => [
                    new MatrixCoordinates(2, 1),
                    new MatrixCoordinates(1, 2),
                    new MatrixCoordinates(2, 2),
                ]
            ],
            'middle_top' => [
                'coordinates' => new MatrixCoordinates(5, 1),
                'matrixSize' => 10,
                'expectedResult' => [
                    new MatrixCoordinates(4, 1),
                    new MatrixCoordinates(6, 1),
                    new MatrixCoordinates(4, 2),
                    new MatrixCoordinates(5, 2),
                    new MatrixCoordinates(6, 2),
                ]
            ],
            'right_top' => [
                'coordinates' => new MatrixCoordinates(5, 1),
                'matrixSize' => 5,
                'expectedResult' => [
                    new MatrixCoordinates(4, 1),
                    new MatrixCoordinates(4, 2),
                    new MatrixCoordinates(5, 2),
                ]
            ],
            'left_bottom' => [
                'coordinates' => new MatrixCoordinates(1, 5),
                'matrixSize' => 5,
                'expectedResult' => [
                    new MatrixCoordinates(1, 4),
                    new MatrixCoordinates(2, 4),
                    new MatrixCoordinates(2, 5),
                ]
            ],
            'right_bottom' => [
                'coordinates' => new MatrixCoordinates(5, 5),
                'matrixSize' => 5,
                'expectedResult' => [
                    new MatrixCoordinates(4, 4),
                    new MatrixCoordinates(5, 4),
                    new MatrixCoordinates(4, 5),
                ]
            ],
        ];
    }

    /**
     * @param class-string<Throwable> $exceptionClass
     */
    #[DataProvider('getSurroundingCoordinatesListFailDataProvider')]
    public function testGetSurroundingCoordinatesListFail(
        MatrixCoordinates $coordinates,
        int $matrixSize,
        string $exceptionClass,
        string $exceptionMessage,
    ): void
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        $coordinates->getSurroundingCoordinatesList($matrixSize);
    }

    /**
     * @return array<string, array{coordinates: MatrixCoordinates, matrixSize: int, exceptionClass: class-string<Throwable>, exceptionMessage: string}>
     */
    public static function getSurroundingCoordinatesListFailDataProvider(): array
    {
        return [
            'outOfScope' => [
                'coordinates' => new MatrixCoordinates(5, 5),
                'matrixSize' => 0,
                'exceptionClass' => InvalidMatrixSizeException::class,
                'exceptionMessage' => 'Matrix size must be a positive integer, 0 provided.',
            ],
            'coordinatesNotOccupied' => [
                'coordinates' => new MatrixCoordinates(5, 5),
                'matrixSize' => 3,
                'exceptionClass' => CannotGetSurroundingCoordinatesListException::class,
                'exceptionMessage' => 'Cannot get surrounding coordinates of [5,5] for Matrix size 3 because it\'s too small.',
            ],
        ];

    }
}
