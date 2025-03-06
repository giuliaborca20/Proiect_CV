<?php
require('fpdf.php'); // Include FPDF (asigură-te că calea este corectă)

$conn = new mysqli('localhost', 'root', '', 'Clinica'); // Conectare la baza de date

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Preluarea CNP-ului pacientului din URL
if (isset($_GET['CNP'])) {
    $CNP = $_GET['CNP'];

    // Interogare pentru a obține datele pacientului
    $sql = "SELECT * FROM Pacienti WHERE CNP = '$CNP'";
    $result = $conn->query($sql);

    // Dacă găsim pacientul
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        echo "Pacientul nu a fost găsit!";
        exit;
    }
}

// Crearea obiectului FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Setăm fontul
$pdf->SetFont('Arial', 'B', 16);

// Titlu
$pdf->Cell(0, 10, 'Informatii pacient', 0, 1, 'C');

// Setăm fontul pentru conținut
$pdf->SetFont('Arial', '', 12);

// Introducerea datelor pacientului
$pdf->Ln(10); // Linie nouă
$pdf->Cell(40, 10, 'CNP:', 0, 0);
$pdf->Cell(0, 10, $patient['CNP'], 0, 1);

$pdf->Cell(40, 10, 'Nume:', 0, 0);
$pdf->Cell(0, 10, $patient['Nume'], 0, 1);

$pdf->Cell(40, 10, 'Prenume:', 0, 0);
$pdf->Cell(0, 10, $patient['Prenume'], 0, 1);

$pdf->Cell(40, 10, 'Adresa:', 0, 0);
$pdf->Cell(0, 10, $patient['Adresa'], 0, 1);

$pdf->Cell(40, 10, 'Email:', 0, 0);
$pdf->Cell(0, 10, $patient['Email'], 0, 1);

$pdf->Cell(40, 10, 'Telefon:', 0, 0);
$pdf->Cell(0, 10, $patient['Telefon'], 0, 1);

// Salvează PDF-ul
$pdf_output_name = 'Pacient_' . $patient['CNP'] . '.pdf';
$pdf->Output('I', $pdf_output_name); // 'I' înseamnă "încărcare directă în browser"
?>
