<?php
// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'Clinica');

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Interogare pentru a obține numărul de pacienți pe specialitate
$sql_specialitati = "
    SELECT Doctori.Nume_specialitate, COUNT(*) AS numar_pacienti
    FROM Consultatii
    INNER JOIN Doctori ON Consultatii.Id_doctor = Doctori.Id_doctor
    GROUP BY Doctori.Nume_specialitate";
$result_specialitati = $conn->query($sql_specialitati);

// Interogare pentru a obține numărul de pacienți cu boli cronice
$sql_boli_cronice = "
    SELECT
        COUNT(CASE WHEN Diagnostic LIKE '%Diabet%' THEN 1 END) AS Diabet,
        COUNT(CASE WHEN Diagnostic LIKE '%Hipertensiune%' THEN 1 END) AS Hipertensiune,
        COUNT(CASE WHEN Diagnostic LIKE '%Astm bronșic%' THEN 1 END) AS Astm_bronsic,
        COUNT(CASE WHEN Diagnostic LIKE '%Cancer%' THEN 1 END) AS Cancer
    FROM Consultatii";
$result_boli_cronice = $conn->query($sql_boli_cronice);

// Pregătirea datelor pentru grafice
$specialitati = [];
$valori_specialitati = [];
if ($result_specialitati && $result_specialitati->num_rows > 0) {
    while ($row = $result_specialitati->fetch_assoc()) {
        $specialitati[] = $row['Nume_specialitate'];
        $valori_specialitati[] = $row['numar_pacienti'];
    }
}

$boli_cronice = ['Diabet', 'Hipertensiune', 'Astm bronșic', 'Cancer'];
$valori_boli_cronice = [0, 0, 0, 0];
if ($result_boli_cronice && $result_boli_cronice->num_rows > 0) {
    $row_boli_cronice = $result_boli_cronice->fetch_assoc();
    $valori_boli_cronice[0] = $row_boli_cronice['Diabet'];
    $valori_boli_cronice[1] = $row_boli_cronice['Hipertensiune'];
    $valori_boli_cronice[2] = $row_boli_cronice['Astm_bronsic'];
    $valori_boli_cronice[3] = $row_boli_cronice['Cancer'];
}

// Închidem conexiunea la baza de date
$conn->close();

// Includerea bibliotecii JPGraph
require_once('src/jpgraph.php');
require_once('src/jpgraph_pie.php');
require_once('src/jpgraph_pie3d.php');


// Funcție pentru generarea unui pie chart
function genereaza_pie_chart($date, $etichete, $titlu, $nume_fisier) {
    $grafic = new PieGraph(600, 400);
    $grafic->SetShadow();
    $grafic->title->Set($titlu);

    $p1 = new PiePlot3D($date);
    $p1->SetLegends($etichete);
    $p1->ExplodeAll();
    $grafic->Add($p1);

    $grafic->Stroke($nume_fisier);
}

// Generăm graficele
genereaza_pie_chart($valori_specialitati, $specialitati, "Pacienți pe specialități", "specialitati.png");
genereaza_pie_chart($valori_boli_cronice, $boli_cronice, "Pacienți cu boli cronice", "boli_cronice.png");
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistici Clinică</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
        }
        img {
            margin: 20px auto;
            display: block;
            border: 1px solid #ccc;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <h1>Statistici Clinică</h1>

    <h2>Distribuția pacienților pe specialități</h2>
    <img src="specialitati.png" alt="Pacienți pe specialități">

    <h2>Distribuția pacienților cu boli cronice</h2>
    <img src="boli_cronice.png" alt="Pacienți cu boli cronice">
</body>
</html>
