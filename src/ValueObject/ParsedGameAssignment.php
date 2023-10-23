<?php declare(strict_types = 1);

namespace App\ValueObject;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class ParsedGameAssignment
{

    public readonly Matrix $matrix;

    public readonly int $maxIterationsCount;

    public function __construct(
        Matrix $matrix,
        int $maxIterationsCount,
    )
    {
        $this->matrix = $matrix;
        $this->maxIterationsCount = $maxIterationsCount;
    }

}
