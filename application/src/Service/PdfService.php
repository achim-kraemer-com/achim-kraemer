<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class PdfService
{
    public function __construct(readonly private ContainerBagInterface $param)
    {
    }

    public function modifyPdf(array $textOverlay, string $outputFilename): void
    {
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();

        $pageCount = $pdf->setSourceFile($this->param->get('pdf_storage_path').'Musterrechnung.pdf');

        for ($pageNo = 1; $pageNo <= $pageCount; ++$pageNo) {
            // Hole die Seitenvorlage
            $templateId = $pdf->importPage($pageNo);
            $size       = $pdf->getTemplateSize($templateId);

            // Erstelle eine neue Seite basierend auf der Vorlage
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Hinzufügen des Textes
            if (isset($textOverlay[$pageNo])) {
                $pdf->SetFont('Helvetica', '', 11);
                $pdf->SetTextColor(0, 0, 0);
                foreach ($textOverlay[$pageNo] as $textItem) {
                    $fontStyle = '';
                    if (\array_key_exists('B', $textItem)) {
                        $fontStyle = 'B';
                    }
                    $pdf->SetFont('Helvetica', $fontStyle, 11);
                    $pdf->SetXY($textItem['x'], $textItem['y']);
                    $textAlign = 'L';
                    $pdf->Write(0, $textItem['text'], '', false, $textAlign);
                }
            }
        }

        // Speichere das neue PDF
        $pdf->Output($this->param->get('invoices_path').'Rechnung-'.$outputFilename.'.pdf', 'F');
    }
}
