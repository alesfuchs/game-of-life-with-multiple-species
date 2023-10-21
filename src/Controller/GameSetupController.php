<?php declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
final class GameSetupController extends AbstractController
{
    #[Route('/', name: 'game_setup', methods: ['GET'])]
    public function search(Request $request): Response
    {
        return $this->render('game_setup.html');
    }
}
