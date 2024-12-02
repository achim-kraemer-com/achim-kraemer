<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\EmailSignature;
use App\Form\EmailSignatureType;
use App\Repository\EmailSignatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/email/signature', name: 'admin_email_signature_')]
#[IsGranted('ROLE_ADMIN')]
class EmailSignatureController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(EmailSignatureRepository $emailSignatureRepository): Response
    {
        return $this->render('emails/signature/index.html.twig', [
            'email_signatures' => $emailSignatureRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EmailSignatureRepository $emailSignatureRepository,
        SluggerInterface $slugger,
        TranslatorInterface $translator,
    ): Response {
        $emailSignature = new EmailSignature();
        $form           = $this->createForm(EmailSignatureType::class, $emailSignature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $originalFilename = \pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename  = $safeFilename.'-'.\uniqid().'.'.$file->guessExtension();

                $imageFilePath = $this->getParameter('image_directory').'/email_signature/';
                if (!\file_exists($imageFilePath) && !\mkdir($imageFilePath, 0777, true) && !\is_dir($imageFilePath)) {
                    $this->addFlash('error', $translator->trans('app.email_signature.error_upload_path'));

                    return $this->redirectToRoute('admin_email_signature_index', [], Response::HTTP_SEE_OTHER);
                }

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $imageFilePath,
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', $translator->trans('app.email_signature.error_upload_image_file'));

                    return $this->redirectToRoute('admin_email_signature_index', [], Response::HTTP_SEE_OTHER);
                }

                $emailSignature->setImage($newFilename);
            }

            $emailSignatureRepository->add($emailSignature, true);

            return $this->redirectToRoute('admin_email_signature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emails/signature/new.html.twig', [
            'email_signature' => $emailSignature,
            'form'            => $form,
        ]);
    }

    #[Route(path: '/{id}/show', name: 'show', methods: ['GET', 'POST'])]
    public function show(EmailSignature $emailSignature): Response
    {
        $user = $this->getUser();

        return $this->render('emails/signature/show.html.twig', [
            'email_signature' => $emailSignature,
            'user'            => $user,
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EmailSignature $emailSignature,
        EmailSignatureRepository $emailSignatureRepository,
        SluggerInterface $slugger,
        TranslatorInterface $translator,
    ): Response {
        $form = $this->createForm(EmailSignatureType::class, $emailSignature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $originalFilename = \pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename  = $safeFilename.'-'.\uniqid().'.'.$file->guessExtension();

                $imageFilePath = $this->getParameter('image_directory').'/email_signature/';
                if (!\file_exists($imageFilePath) && !\mkdir($imageFilePath, 0777, true) && !\is_dir($imageFilePath)) {
                    $this->addFlash('error', $translator->trans('app.email_signature.error_upload_path'));

                    return $this->redirectToRoute('admin_email_signature_index', [], Response::HTTP_SEE_OTHER);
                }

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $imageFilePath,
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', $translator->trans('app.email_signature.error_upload_image_file'));

                    return $this->redirectToRoute('admin_email_signature_index', [], Response::HTTP_SEE_OTHER);
                }

                $emailSignature->setImage($newFilename);
            }
            $emailSignatureRepository->add($emailSignature, true);

            return $this->redirectToRoute('admin_email_signature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emails/signature/edit.html.twig', [
            'email_signature' => $emailSignature,
            'form'            => $form,
        ]);
    }

    #[Route(path: '/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, EmailSignature $emailSignature, EmailSignatureRepository $emailSignatureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emailSignature->getId(), $request->get('_token'))) {
            $emailSignatureRepository->remove($emailSignature, true);
        }

        return $this->redirectToRoute('admin_email_signature_index', [], Response::HTTP_SEE_OTHER);
    }
}
