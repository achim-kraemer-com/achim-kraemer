<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Project;
use App\Entity\TimeEntry;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\TimeEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/project', name: 'admin_project_')]
final class ProjectController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form    = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('admin_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form'    => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form'    => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_project_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/create-invoice/{id}', name: 'create_invoice', methods: ['POST'])]
    public function createInvoice(
        Request $request,
        Project $project,
        TimeEntryRepository $timeEntryRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $selectedTimeEntries = $request->get('options', []);

        if (!empty($selectedTimeEntries)) {
            $invoice = new Invoice();
            $invoice->setInvoiceDate(new \DateTime());
            $invoice->setCustomer($project->getCustomer());
            $invoice->setStatus(Invoice::STATUS_OPEN);
            $timeEntries = $timeEntryRepository->getInvoices($selectedTimeEntries);
            $totalAmount = 0;
            foreach ($timeEntries as $timeEntry) {
                $totalAmount += $timeEntry->getHours() * Invoice::HOURLY_RATE;
                $timeEntry->setStatus(TimeEntry::STATUS_IN_WORK);
                $entityManager->persist($timeEntry);
            }
            $invoice->setTotalAmount((string) $totalAmount);
            $entityManager->persist($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_project_show', ['id' => $project->getId()]);
    }
}
