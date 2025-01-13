<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CurriculumVitaeController extends AbstractController
{
    #[Route('/cv', name: 'app_cv')]
    public function index(): Response
    {
        return $this->render('curriculum_vitae/index.html.twig');
    }
}
