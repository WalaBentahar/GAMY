<?php
require_once '../../config.php';
require_once '../../lib/fpdf186/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Liste des Guides et Videos', 0, 1, 'C');
$pdf->Ln(10);

// En-têtes
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Titre', 1);
$pdf->Cell(40, 10, 'Auteur', 1);
$pdf->Cell(90, 10, 'Type', 1);
$pdf->Ln();

// Données des guides textuels
$stmt = config::getConnexion()->query("SELECT title, author FROM guides");
$guides = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf->SetFont('Arial', '', 12);
foreach ($guides as $guide) {
    $pdf->Cell(60, 10, $guide['title'], 1);
    $pdf->Cell(40, 10, $guide['author'], 1);
    $pdf->Cell(90, 10, 'Texte', 1);
    $pdf->Ln();
}

// Données des guides vidéo
$stmt = config::getConnexion()->query("SELECT title, author FROM videoguides");
$videoGuides = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($videoGuides as $video) {
    $pdf->Cell(60, 10, $video['title'], 1);
    $pdf->Cell(40, 10, $video['author'], 1);
    $pdf->Cell(90, 10, 'Video', 1);
    $pdf->Ln();
}

$pdf->Output('D', 'guides_videos_export.pdf');
?>