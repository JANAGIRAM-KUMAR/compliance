<?php
require_once __DIR__ . '/fpdf/pdf.php';
include_once __DIR__ . '/db.php';

$status = $_GET['status'] ?? 'pending';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);

$pdf->Cell(25,8,'IIR',1);
$pdf->Cell(40,8,'Recommendation',1);
$pdf->Cell(25,8,'Resp',1);
$pdf->Cell(25,8,'Date',1);
$pdf->Cell(40,8,'Remarks',1);
$pdf->Cell(25,8,'Status',1);
$pdf->Ln();

$sql = "
SELECT ir.iir_no, r.*
FROM incident_reports ir
JOIN incident_recommendations r ON ir.id = r.incident_id
WHERE r.status='$status'
";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(25,8,$row['iir_no'],1);
    $pdf->Cell(40,8,substr($row['recommendation'],0,20),1);
    $pdf->Cell(25,8,$row['resp'],1);
    $pdf->Cell(25,8,$row['target_date'],1);
    $pdf->Cell(40,8,substr($row['remarks'],0,20),1);
    $pdf->Cell(25,8,strtoupper($row['status']),1);
    $pdf->Ln();
}

$pdf->Output();