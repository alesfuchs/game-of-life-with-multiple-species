<?php declare(strict_types = 1);

namespace App\Controller;

use App\Exceptions\CannotGetCellFromMatrixException;
use App\Exceptions\GameCannotContinueException;
use App\Exceptions\InvalidInputDataException;
use App\Exceptions\InvalidMatrixSizeException;
use App\Exceptions\MatrixCellIndexException;
use App\Form\GameAssignmentType;
use App\Helper\GameMaster;
use App\Helper\MatrixInputDataAdapter;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
final class GameSetupController extends AbstractController
{
    #[Route('/', name: 'game_of_life_get', methods: ['GET'])]
    #[Route('/', name: 'game_of_life_post', methods: ['POST'])]
    public function search(Request $request): Response
    {
        $form = $this->createForm(GameAssignmentType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('game_setup.html', [
                'form' => $form,
            ]);
        }

        $formData = $form->getData();

        $jsonDataToBeParsed = $formData['currentIterationData'] !== null
            ? $formData['currentIterationData']
            : $formData['initialData'];

        $currentIterationNumber = $formData['currentIterationNumber'];

        try {
            $parsedGameAssignment = MatrixInputDataAdapter::parseDataIntoMatrix(json_decode($jsonDataToBeParsed, true));
        } catch (GameCannotContinueException $e) {
            return $this->render('game_over.html', [
                'totalProcessedIterations' => $currentIterationNumber ?? 0,
                'matrix' => null,
            ]);
        } catch (
            InvalidInputDataException |
            MatrixCellIndexException |
            CannotGetCellFromMatrixException $e
        ) {
            $this->addFlash('error', $e->getMessage());

            return $this->render('game_setup.html', [
                'form' => $form,
            ]);
        }

        $iterationsToBeProcessed = $currentIterationNumber === null
            ? 0
            : min(
                $formData['pauseAfterIterationsCount'],
                $parsedGameAssignment->maxIterationsCount - $currentIterationNumber,
            );

        try {
            GameMaster::runGame($parsedGameAssignment->matrix, $iterationsToBeProcessed);
        } catch (InvalidMatrixSizeException $e) {
            throw new LogicException($e->getMessage(), 0, $e);
        }

        $totalProcessedIterations = (int) $currentIterationNumber + $iterationsToBeProcessed;

        $this->addFlash(
            'success',
            $currentIterationNumber === null
                ? 'Successfully parsed initial data.'
                : "{$totalProcessedIterations} iterations processed.",
        );

        $form = $this->createForm(GameAssignmentType::class, array_merge(
            $formData,
            [
                'currentIterationNumber' => $totalProcessedIterations,
                'currentIterationData' => json_encode(MatrixInputDataAdapter::getDataFromMatrix($parsedGameAssignment)),
            ]
        ));


        if ($totalProcessedIterations >= $parsedGameAssignment->maxIterationsCount) {
            return $this->render('game_over.html', [
                'totalProcessedIterations' => $totalProcessedIterations,
                'matrix' => $parsedGameAssignment->matrix,
            ]);
        }

        return $this->render('game_running.html', [
            'form' => $form,
            'matrix' => $parsedGameAssignment->matrix,
        ]);
    }
}
