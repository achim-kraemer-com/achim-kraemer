<?php

declare(strict_types=1);

namespace App\Service;

use setasign\Fpdi\Fpdi;

class PdfService
{
    public function modifyPdf(string $inputPath, string $outputPath, array $textOverlay): void
    {
        $pdf = new Fpdi();

        $pageCount = $pdf->setSourceFile($inputPath);

        for ($pageNo = 1; $pageNo <= $pageCount; ++$pageNo) {
            // Hole die Seitenvorlage
            $templateId = $pdf->importPage($pageNo);
            $size       = $pdf->getTemplateSize($templateId);

            // Erstelle eine neue Seite basierend auf der Vorlage
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Hinzufügen des Textes
            if (isset($textOverlay[$pageNo])) {
                $pdf->SetFont('Arial', '', 12);
                $pdf->SetTextColor(0, 0, 0);
                foreach ($textOverlay[$pageNo] as $textItem) {
                    $pdf->SetXY($textItem['x'], $textItem['y']);
                    $pdf->Write(0, $textItem['text']);
                }
            }
        }

        // Speichere das neue PDF
        $pdf->Output('F', $outputPath);
    }
}
