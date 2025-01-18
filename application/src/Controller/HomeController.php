<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verarbeitung des Formulars, z.B. Senden einer E-Mail
            $data  = $form->getData();
            $email = (new TemplatedEmail())
                ->from($data['email'])
                ->to('kontakt@achim-kraemer.com')
                ->subject('Contact Form Message')
                ->htmlTemplate('emails/contact.html.twig')
                ->context(
                    [
                        'name'    => $data['name'],
                        'phone'   => $data['phone'],
                        'mail'    => $data['email'],
                        'message' => $data['message'],
                    ]
                );

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $email = (new Email())
                    ->from('kontakt@achim-kraemer.com')
                    ->to('kontakt@achim-kraemer.com')
                    ->subject('Es gab anscheinend einen Fehler beim Versenden einer KontaktMail')
                    ->text('Es gab anscheinend einen Fehler beim Versenden einer KontaktMail: '.$e->getMessage());

                $mailer->send($email);
            }

            $this->addFlash('success', $translator->trans('app.contact.email_send'));

            return $this->redirect($this->generateUrl('app_home').'#contact');
        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
        ]);
    }
}
