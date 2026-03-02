<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BusinessCardController extends AbstractController
{
    #[Route('/business-card')]
    public function index(): Response
    {
        return $this->render('business_card/index.html.twig');
    }

    #[Route('/business-card2')]
    public function businessCard2(): Response
    {
        return $this->render('business_card/index2.html.twig');
    }

    #[Route('/business-card3')]
    public function businessCard3(): Response
    {
        return $this->render('business_card/index3.html.twig');
    }

    #[Route('/business-card4')]
    public function businessCard4(): Response
    {
        return $this->render('business_card/index4.html.twig');
    }

    #[Route('/business-card5')]
    public function businessCard5(): Response
    {
        return $this->render('business_card/index5.html.twig');
    }

    #[Route('/business-card6')]
    public function businessCard6(): Response
    {
        return $this->render('business_card/index6.html.twig');
    }

    #[Route('/business-card7')]
    public function businessCard7(): Response
    {
        return $this->render('business_card/index7.html.twig');
    }

    #[Route('/business-card8')]
    public function businessCard8(): Response
    {
        return $this->render('business_card/index8.html.twig');
    }
}
