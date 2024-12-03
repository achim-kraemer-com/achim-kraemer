<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Project;
use App\Entity\TimeEntry;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\TimeEntryRepository;
use App\Service\PdfService;
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
        PdfService $pdfService,
    ): Response {
        $selectedTimeEntries = $request->get('options', []);

        if (!empty($selectedTimeEntries)) {
            $projectTimeEntries   = $project->getTimeEntries();
            $projectInvoicesArray = [];
            $invoiceDate          = \date('M./Y');
            foreach ($projectTimeEntries as $projectTimeEntry) {
                foreach ($projectTimeEntry->getInvoices() as $projectInvoice) {
                    $projectInvoicesArray[$projectInvoice->getId()] = $projectInvoice->getId();
                    $invoiceDate                                    = $projectTimeEntry->getDate()->format('M./Y');
                }
            }
            $invoiceName = \date('y').'-'.($project->getId() + 1000).'-'.\count($projectInvoicesArray) + 1;
            $invoice     = new Invoice();
            $invoice->setInvoiceDate(new \DateTime());
            $invoice->setCustomer($project->getCustomer());
            $invoice->setStatus(Invoice::STATUS_OPEN);
            $invoice->setName($invoiceName);
            $timeEntries = $timeEntryRepository->getInvoices($selectedTimeEntries);
            $customer    = $project->getCustomer();
            $textOverlay = [
                1 => [
                    ['x' => 25, 'y' => 45, 'text' => \mb_convert_encoding($customer->getCompanyName(), 'ISO-8859-1', 'UTF-8')],
                    ['x' => 25, 'y' => 50, 'text' => \mb_convert_encoding($customer->getFirstname(), 'ISO-8859-1', 'UTF-8').' '.\mb_convert_encoding($customer->getLastname(), 'ISO-8859-1', 'UTF-8')],
                    ['x' => 25, 'y' => 55, 'text' => \mb_convert_encoding($customer->getStreet(), 'ISO-8859-1', 'UTF-8').' '.$customer->getHousenumber()],
                    ['x' => 25, 'y' => 60, 'text' => $customer->getPlz().' '.\mb_convert_encoding($customer->getCity(), 'ISO-8859-1', 'UTF-8')],
                    ['x' => 52, 'y' => 102.5, 'text' => $invoiceName],
                    ['x' => 172, 'y' => 66, 'text' => $invoiceDate],
                    ['x' => 170, 'y' => 75, 'text' => \date('d.m.Y'), 'B' => 'B'],
                ],
            ];
            $totalAmount          = 0;
            $overlayTimeEntriesNr = 123;
            $position             = 1;
            foreach ($timeEntries as $timeEntry) {
                if ($timeEntry->getHours() !== null && $timeEntry->getPrice() === null) {
                    $totalAmount += $timeEntry->getHours() * Invoice::HOURLY_RATE;
                } elseif ($timeEntry->getPrice() !== null) {
                    $totalAmount += $timeEntry->getPrice();
                }
                $textOverlay[1][] = ['x' => 27, 'y' => $overlayTimeEntriesNr, 'text' => $position];
                if ($timeEntry->getHours() !== null) {
                    $textOverlay[1][] = ['x' => 124, 'y' => $overlayTimeEntriesNr, 'text' => \str_replace('.', ',', $timeEntry->getHours()).' Std.'];
                }
                if ($timeEntry->getHours() !== null) {
                    $textOverlay[1][] = ['x' => 149, 'y' => $overlayTimeEntriesNr, 'text' => Invoice::HOURLY_RATE.' €'];
                }
                if ($timeEntry->getPrice() !== null) {
                    $textOverlay[1][] = ['x' => 173, 'y' => $overlayTimeEntriesNr, 'text' => \number_format((float) $timeEntry->getPrice(), 2, '.', '').' €', 'R' => 'R'];
                }
                $lines = \explode("\n", \mb_convert_encoding($timeEntry->getDescription(), 'ISO-8859-1', 'UTF-8'));
                foreach ($lines as $line) {
                    $textOverlay[1][] = ['x' => 42, 'y' => $overlayTimeEntriesNr, 'text' => $line];
                    $overlayTimeEntriesNr += 5;
                }
                ++$position;
                $overlayTimeEntriesNr += 5;
                $timeEntry->setStatus(TimeEntry::STATUS_IN_WORK);
                $entityManager->persist($timeEntry);
                $timeEntry->addInvoice($invoice);
            }
            $textOverlay[1][] = ['x' => 173, 'y' => 194, 'text' => \number_format((float) $totalAmount, 2, '.', '').' €', 'B' => 'B', 'R' => 'R'];
            $invoice->setTotalAmount((string) $totalAmount);
            $pdfService->modifyPdf($textOverlay, $invoiceName);
            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('admin_invoice_show', ['id' => $invoice->getId()]);
        }

        return $this->redirectToRoute('admin_project_show', ['id' => $project->getId()]);
    }
}
