<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Exceptions\CannotAddSurroundingCellToMatrixCellException;

class MatrixCell
{

    private readonly MatrixCoordinates $coordinates;

    /**
     * @var list<MatrixCell>
     */
    private array $surroundingCells;

    private ?string $currentBreed;

    /**
     * @var list<string>
     */
    private array $futurePossibleBreeds;

    public function __construct(
        MatrixCoordinates $coordinates,
    )
    {
        $this->coordinates = $coordinates;

        $this->surroundingCells = [];
        $this->currentBreed = null;
        $this->futurePossibleBreeds = [];
    }

    public function getCoordinates(): MatrixCoordinates
    {
        return $this->coordinates;
    }

    public function getSurroundingCells(): array
    {
        return $this->surroundingCells;
    }

    public function containsSurroundingCell(MatrixCell $cell): bool
    {
        foreach ($this->surroundingCells as $surroundingCell) {
            if ($surroundingCell->getCoordinates()->equalsTo($cell->getCoordinates())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws CannotAddSurroundingCellToMatrixCellException
     */
    public function addSurroundingCell(MatrixCell $cell): void
    {
        if ($this->coordinates->equalsTo($cell->getCoordinates())) {
            throw CannotAddSurroundingCellToMatrixCellException::addingSelf($cell);
        }

        if ($this->containsSurroundingCell($cell)) {
            throw CannotAddSurroundingCellToMatrixCellException::duplicateMatrixCell($cell);
        }

        if ($this->coordinates->getMaxCoordinateDistanceFromCoordinates($cell->getCoordinates()) > 1) {
            throw CannotAddSurroundingCellToMatrixCellException::tooFar($this, $cell);
        }

        $this->surroundingCells[] = $cell;
    }

    public function getCurrentBreed(): ?string
    {
        return $this->currentBreed;
    }

    public function setCurrentBreed(?string $currentBreed): void
    {
        $this->currentBreed = $currentBreed;
    }

    public function getFuturePossibleBreeds(): array
    {
        return $this->futurePossibleBreeds;
    }

    public function addFuturePossibleBreed(string $breed): void
    {
        if (!in_array($breed, $this->futurePossibleBreeds, true)) {
            $this->futurePossibleBreeds[] = $breed;
        }
    }

    public function resetFuturePossibleBreeds(): void
    {
        $this->futurePossibleBreeds = [];
    }

}
