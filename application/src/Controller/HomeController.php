<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verarbeitung des Formulars, z.B. Senden einer E-Mail
            $data = $form->getData();
            $email = (new Email())
                ->from($data['email'])
                ->to('kontakt@achim-kraemer.com')
                ->subject('Contact Form Message')
                ->text($data['message']);

            $mailer->send($email);

            $this->addFlash('success', 'Your message has been sent!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
        ]);
    }
}
