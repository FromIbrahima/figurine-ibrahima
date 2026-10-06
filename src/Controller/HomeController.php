<?php

namespace App\Controller;

use App\Repository\FigurineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(FigurineRepository $figurineRepository): Response
    {
        return $this->render('home/index.html.twig', [
            // Aperçu des dernières figurines uniquement pour les membres connectés
            'latestFigurines' => $this->getUser() ? $figurineRepository->findLatest(4) : [],
            'figurineCount' => $figurineRepository->count([]),
        ]);
    }
}
