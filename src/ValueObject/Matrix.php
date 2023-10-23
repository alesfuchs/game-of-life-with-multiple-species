<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Exceptions\CannotAddCellToMatrixException;
use App\Exceptions\CannotGetCellFromMatrixException;
use App\Exceptions\InvalidMatrixSizeException;
use App\Exceptions\MatrixCellIndexException;
use function array_key_exists;
use function implode;
use function ksort;

class Matrix
{

    private int $size;

    /**
     * @var array<int, MatrixCell>
     */
    private array $cells;

    /**
     * @throws InvalidMatrixSizeException
     */
    public function __construct(
        int $size,
    )
    {
        if ($size <= 0) {
            throw InvalidMatrixSizeException::notPositive($size);
        }

        $this->size = $size;
        $this->cells = [];
    }

    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @throws MatrixCellIndexException
     */
    public function getCellIndexFromCoordinates(MatrixCoordinates $coordinates): int
    {
        if ($coordinates->coordinateX > $this->size || $coordinates->coordinateY > $this->size) {
            throw MatrixCellIndexException::outOfScope($this->size, $coordinates);
        }

        return $this->size * ($coordinates->coordinateY - 1)
            + ($coordinates->coordinateX - 1);
    }

    /**
     * @return array<int, MatrixCell>
     */
    public function getCells(): array
    {
        ksort($this->cells);
        return $this->cells;
    }

    /**
     * @throws MatrixCellIndexException
     * @throws CannotGetCellFromMatrixException
     */
    public function getCell(MatrixCoordinates $coordinates): MatrixCell
    {
        $coordinatesOccupied = array_key_exists(
            $this->getCellIndexFromCoordinates($coordinates),
            $this->cells,
        );

        if (!$coordinatesOccupied) {
            throw CannotGetCellFromMatrixException::coordinatesNotOccupied($coordinates);
        }

        return $this->cells[$this->getCellIndexFromCoordinates($coordinates)];
    }

    public function containsCell(MatrixCell $cell): bool
    {
        return array_key_exists(
            $this->getCellIndexFromCoordinates($cell->getCoordinates()),
            $this->cells,
        );
    }

    /**
     * @throws MatrixCellIndexException
     * @throws CannotAddCellToMatrixException
     */
    public function addCell(MatrixCell $cell): void
    {
        if ($this->containsCell($cell)) {
            throw CannotAddCellToMatrixException::coordinatesOccupied($cell);
        }

        $this->cells[$this->getCellIndexFromCoordinates($cell->getCoordinates())] = $cell;
    }

    public function debugDataWithCurrentBreed(): string
    {
        $output = "\ncurrent breeds";

        foreach ($this->getCells() as $cell) {
            if ($cell->getCoordinates()->coordinateX === 1) {
                $output .= "\n";
            }

            $currentBreed = $cell->getCurrentBreed() !== null
                ? "-{$cell->getCurrentBreed()}"
                : '';

            $output .= "{$cell->getCoordinates()}{$currentBreed} ";
        }

        return $output;
    }

    public function debugDataWithFuturePossibleBreeds(): string
    {
        $output = "\nfuture possible breeds";

        foreach ($this->getCells() as $cell) {
            if ($cell->getCoordinates()->coordinateX === 1) {
                $output .= "\n";
            }

            $futurePossibleBreeds = implode('-', $cell->getFuturePossibleBreeds());

            if ($futurePossibleBreeds !== '') {
                $futurePossibleBreeds = "-{$futurePossibleBreeds}";
            }

            $output .= "{$cell->getCoordinates()}{$futurePossibleBreeds} ";
        }

        return $output;
    }

}
