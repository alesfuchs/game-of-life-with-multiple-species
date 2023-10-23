<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Exceptions\CannotAddSurroundingCellToMatrixCellException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MatrixCellTest extends TestCase
{

    public function testConstruct(): void
    {
        $matrixCoordinates = new MatrixCoordinates(1,1);
        $matrixCell = new MatrixCell($matrixCoordinates);

        self::assertTrue($matrixCell->getCoordinates()->equalsTo($matrixCoordinates));
        self::assertEmpty($matrixCell->getSurroundingCells());
        self::assertNull($matrixCell->getCurrentBreed());
        self::assertEmpty($matrixCell->getFuturePossibleBreeds());
    }

    public function testAddSurroundingCellSuccess(): void
    {
        $matrixCoordinates1 = new MatrixCoordinates(1,1);
        $matrixCoordinates2 = new MatrixCoordinates(1,2);

        $matrixCell1 = new MatrixCell($matrixCoordinates1);
        $matrixCell2 = new MatrixCell($matrixCoordinates2);

        self::assertFalse($matrixCell1->containsSurroundingCell($matrixCell2));

        $matrixCell1->addSurroundingCell($matrixCell2);
        self::assertSame([$matrixCell2], $matrixCell1->getSurroundingCells());
        self::assertTrue($matrixCell1->containsSurroundingCell($matrixCell2));
    }

    /**
     * @param list<MatrixCoordinates> $coordinatesList
     */
    #[DataProvider('addSurroundingCellFailDataProvider')]
    public function testAddSurroundingCellFail(
        array $coordinatesList,
        string $exceptionMessage,
    ): void
    {
        $matrixCoordinates1 = new MatrixCoordinates(1,1);
        $matrixCell1 = new MatrixCell($matrixCoordinates1);

        $this->expectException(CannotAddSurroundingCellToMatrixCellException::class);
        $this->expectExceptionMessage($exceptionMessage);

        foreach ($coordinatesList as $coordinates) {
            $surroundingMatrixCell = new MatrixCell($coordinates);
            $matrixCell1->addSurroundingCell($surroundingMatrixCell);
        }
    }

    /**
     * @return array<string, array{coordinates: list<MatrixCoordinates>, exceptionMessage: string}>
     */
    public static function addSurroundingCellFailDataProvider(): array
    {
        return [
            'addingSelf' => [
                'coordinates' => [new MatrixCoordinates(1,1)],
                'exceptionMessage' => 'Cannot add a Matrix Cell to the list of surrounding cells to for the cell with equal coordinates [1,1].',
            ],
            'duplicateMatrixCell' => [
                'coordinates' => [
                    new MatrixCoordinates(1,2),
                    new MatrixCoordinates(1,2),
                ],
                'exceptionMessage' => 'Cannot add two Matrix Cells with the same coordinates [1,2] to the list of surrounding cells.',
            ],
            'tooFar' => [
                'coordinates' => [new MatrixCoordinates(1,3)],
                'exceptionMessage' => 'Cannot add a Matrix Cell [1,3] as a surrounding cell to the central Matrix Cell [1,1] because it is too far away.',
            ],
        ];
    }

    public function testSetCurrentBreed(): void
    {
        $matrixCoordinates = new MatrixCoordinates(1,1);
        $matrixCell = new MatrixCell($matrixCoordinates);

        $matrixCell->setCurrentBreed('breed1');
        self::assertSame('breed1', $matrixCell->getCurrentBreed());

        $matrixCell->setCurrentBreed('breed2');
        self::assertSame('breed2', $matrixCell->getCurrentBreed());

        $matrixCell->setCurrentBreed(null);
        self::assertNull($matrixCell->getCurrentBreed());
    }

    public function testSetFuturePossibleBreeds(): void
    {
        $matrixCoordinates = new MatrixCoordinates(1,1);
        $matrixCell = new MatrixCell($matrixCoordinates);

        $matrixCell->addFuturePossibleBreed('breed1');
        self::assertSame(['breed1'], $matrixCell->getFuturePossibleBreeds());

        $matrixCell->addFuturePossibleBreed('breed1');
        self::assertSame(['breed1'], $matrixCell->getFuturePossibleBreeds());

        $matrixCell->addFuturePossibleBreed('breed2');
        self::assertSame(['breed1','breed2'], $matrixCell->getFuturePossibleBreeds());

        $matrixCell->resetFuturePossibleBreeds();
        self::assertSame([], $matrixCell->getFuturePossibleBreeds());
    }

}
