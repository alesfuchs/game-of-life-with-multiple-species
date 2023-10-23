<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Exceptions\CannotAddCellToMatrixException;
use App\Exceptions\CannotGetCellFromMatrixException;
use App\Exceptions\MatrixCellIndexException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

class MatrixTest extends TestCase
{

    public function testConstruct(): void
    {
        $matrix = new Matrix(3);

        self::assertSame(3, $matrix->getSize());
        self::assertSame([], $matrix->getCells());
    }

    /**
     * @param list<MatrixCoordinates> $coordinatesList
     */
    #[DataProvider('getCellIndexFromCoordinatesSuccessDataProvider')]
    public function testGetCellIndexFromCoordinatesSuccess(
        int $size,
        MatrixCoordinates $coordinates,
        int $expectedIndex,
    ): void
    {
        $matrix = new Matrix($size);
        self::assertSame($expectedIndex, $matrix->getCellIndexFromCoordinates($coordinates));
    }

    /**
     * @return list<array{size: int, coordinates: MatrixCoordinates, expectedIndex: int}>
     */
    public static function getCellIndexFromCoordinatesSuccessDataProvider(): array
    {
        return [
            [
                'size' => 3,
                'coordinates' => new MatrixCoordinates(1,1),
                'expectedIndex' => 0,
            ],
            [
                'size' => 100,
                'coordinates' => new MatrixCoordinates(1,1),
                'expectedIndex' => 0,
            ],
            [
                'size' => 3,
                'coordinates' => new MatrixCoordinates(3,1),
                'expectedIndex' => 2,
            ],
            [
                'size' => 3,
                'coordinates' => new MatrixCoordinates(1,2),
                'expectedIndex' => 3,
            ],
            [
                'size' => 3,
                'coordinates' => new MatrixCoordinates(3,3),
                'expectedIndex' => 8,
            ],
        ];
    }

    /**
     * @param list<MatrixCoordinates> $coordinatesList
     */
    #[DataProvider('getCellIndexFromCoordinatesFailDataProvider')]
    public function testGetCellIndexFromCoordinatesFail(
        int $size,
        MatrixCoordinates $coordinates,
    ): void
    {
        $matrix = new Matrix($size);

        $this->expectException(MatrixCellIndexException::class);
        $this->expectExceptionMessage("Matrix cell index cannot be resolved. The coordinates {$coordinates} are out of matrix scope {$size}.");

        $matrix->getCellIndexFromCoordinates($coordinates);
    }

    /**
     * @return list<array{size: int, coordinates: MatrixCoordinates}>
     */
    public static function getCellIndexFromCoordinatesFailDataProvider(): array
    {
        return [
            [
                'size' => 3,
                'coordinates' => new MatrixCoordinates(1,5),
            ],
            [
                'size' => 3,
                'coordinates' => new MatrixCoordinates(5,1),
            ],
        ];
    }

    public function testCellManipulationSuccess(): void
    {
        $matrix = new Matrix(3);

        $matrixCoordinates1 = new MatrixCoordinates(3,3);
        $matrixCoordinates2 = new MatrixCoordinates(1,1);

        $matrixCell1 = new MatrixCell($matrixCoordinates1);
        $matrixCell2 = new MatrixCell($matrixCoordinates2);

        $matrix->addCell($matrixCell1);
        self::assertTrue($matrix->containsCell($matrixCell1));
        self::assertFalse($matrix->containsCell($matrixCell2));
        self::assertSame($matrixCell1, $matrix->getCell($matrixCoordinates1));
        self::assertSame([8 => $matrixCell1], $matrix->getCells());

        $matrix->addCell($matrixCell2);
        self::assertTrue($matrix->containsCell($matrixCell1));
        self::assertTrue($matrix->containsCell($matrixCell2));
        self::assertSame($matrixCell1, $matrix->getCell($matrixCoordinates1));
        self::assertSame($matrixCell2, $matrix->getCell($matrixCoordinates2));
        self::assertSame([0 => $matrixCell2, 8 => $matrixCell1], $matrix->getCells());
    }

    /**
     * @param list<MatrixCoordinates> $coordinatesList
     * @param class-string<Throwable> $exceptionClass
     */
    #[DataProvider('addCellFailDataProvider')]
    public function testAddCellFail(
        array $coordinatesList,
        string $exceptionClass,
        string $exceptionMessage,
    ): void
    {
        $matrix = new Matrix(3);

        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        foreach ($coordinatesList as $coordinates) {
            $matrixCell = new MatrixCell($coordinates);
            $matrix->addCell($matrixCell);
        }
    }

    /**
     * @return array<string, array{coordinates: list<MatrixCoordinates>, exceptionClass: class-string<Throwable>, exceptionMessage: string}>
     */
    public static function addCellFailDataProvider(): array
    {
        return [
            'outOfScope' => [
                'coordinates' => [new MatrixCoordinates(1,5)],
                'exceptionClass' => MatrixCellIndexException::class,
                'exceptionMessage' => 'Matrix cell index cannot be resolved. The coordinates [1,5] are out of matrix scope 3.',
            ],
            'coordinatesOccupied' => [
                'coordinates' => [
                    new MatrixCoordinates(1,2),
                    new MatrixCoordinates(1,2),
                ],
                'exceptionClass' => CannotAddCellToMatrixException::class,
                'exceptionMessage' => 'Cannot add a cell to the Matrix. The coordinates [1,2] are already occupied.',
            ],
        ];
    }

    /**
     * @param class-string<Throwable> $exceptionClass
     */
    #[DataProvider('getCellFailDataProvider')]
    public function testGetCellFail(
        MatrixCoordinates $coordinatesToGet,
        string $exceptionClass,
        string $exceptionMessage,
    ): void
    {
        $matrixCell = new MatrixCell(new MatrixCoordinates(1,1));

        $matrix = new Matrix(3);
        $matrix->addCell($matrixCell);

        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        $matrix->getCell($coordinatesToGet);
    }

    /**
     * @return array<string, array{coordinatesToGet: MatrixCoordinates, exceptionClass: class-string<Throwable>, exceptionMessage: string}>
     */
    public static function getCellFailDataProvider(): array
    {
        return [
            'outOfScope' => [
                'coordinatesToGet' => new MatrixCoordinates(1,5),
                'exceptionClass' => MatrixCellIndexException::class,
                'exceptionMessage' => 'Matrix cell index cannot be resolved. The coordinates [1,5] are out of matrix scope 3.',
            ],
            'coordinatesNotOccupied' => [
                'coordinatesToGet' => new MatrixCoordinates(3,2),
                'exceptionClass' => CannotGetCellFromMatrixException::class,
                'exceptionMessage' => 'Cannot get a cell from the Matrix. The coordinates [3,2] are not occupied.',
            ],
        ];
    }

}
