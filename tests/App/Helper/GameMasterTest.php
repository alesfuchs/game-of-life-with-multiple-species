<?php declare(strict_types = 1);

namespace App\Helper;

use App\ValueObject\Matrix;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertInstanceOf;

class GameMasterTest extends TestCase
{

    public function testRunGame(): void
    {
        $data = [
            'dimension' => 5,
            'speciesCount' => 3,
            'iterationsCount' => 10,
            'organisms' => [
                [
                    'x_pos' => 1,
                    'y_pos' => 1,
                    'species' => 'blue',
                ],
                [
                    'x_pos' => 1,
                    'y_pos' => 1,
                    'species' => 'red',
                ],
                [
                    'x_pos' => 4,
                    'y_pos' => 1,
                    'species' => 'blue',
                ],
                [
                    'x_pos' => 1,
                    'y_pos' => 2,
                    'species' => 'blue',
                ],
                [
                    'x_pos' => 2,
                    'y_pos' => 2,
                    'species' => 'blue',
                ],
                [
                    'x_pos' => 3,
                    'y_pos' => 3,
                    'species' => 'red',
                ],
                [
                    'x_pos' => 3,
                    'y_pos' => 3,
                    'species' => 'green',
                ],
                [
                    'x_pos' => 1,
                    'y_pos' => 3,
                    'species' => 'blue',
                ],
            ],
        ];

//        echo json_encode($data);die;

        $matrix = MatrixInputDataAdapter::parseDataIntoMatrix($data)->matrix;

        GameMaster::runGame($matrix, 10, true);

        echo $matrix->debugDataWithCurrentBreed();

        assertInstanceOf(Matrix::class, $matrix);
    }

}
