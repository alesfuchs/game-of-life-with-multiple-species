<?php declare(strict_types = 1);

namespace App\Helper;

use PHPUnit\Framework\TestCase;
use Throwable;

class MatrixInputDataParserTest extends TestCase
{

    // TODO add fail tests
    public function testParseDataSuccess(): void
    {
        $data = [
            'dimension' => 3,
            'speciesCount' => 2,
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
            ],
        ];

        $matrix = MatrixInputDataParser::parseData($data);

        self::assertSame("\n"
            ."[1,1]-blue-red [2,1]- [3,1]- \n"
            ."[1,2]- [2,2]-blue [3,2]- \n"
            ."[1,3]- [2,3]- [3,3]-red-green ",
            $matrix->printForDebugWithFuturePossibleBreeds(),
        );
    }

}
