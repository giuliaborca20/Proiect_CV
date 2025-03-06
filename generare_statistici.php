<?php
$conn = new mysqli('localhost', 'root', '', 'Clinica');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql_specialitati = "
    SELECT Doctori.Nume_specialitate, COUNT(*) AS numar_pacienti
    FROM Consultatii
    INNER JOIN Doctori ON Consultatii.Id_doctor = Doctori.Id_doctor
    GROUP BY Doctori.Nume_specialitate";
$result_specialitati = $conn->query($sql_specialitati);
$sql_boli_cronice = "
    SELECT
        COUNT(CASE WHEN Diagnostic LIKE '%Diabet%' THEN 1 END) AS Diabet,
        COUNT(CASE WHEN Diagnostic LIKE '%Hipertensiune%' THEN 1 END) AS Hipertensiune,
        COUNT(CASE WHEN Diagnostic LIKE '%Astm bronșic%' THEN 1 END) AS Astm_bronsic,
        COUNT(CASE WHEN Diagnostic LIKE '%Cancer%' THEN 1 END) AS Cancer
    FROM Consultatii";
$result_boli_cronice = $conn->query($sql_boli_cronice);
require_once('jpgraph/src/jpgraph.php');
require_once('jpgraph/src/jpgraph_pie.php');
require_once('jpgraph/src/jpgraph_bar.php');
$specialitati = [];
$valori_specialitati = [];
if ($result_specialitati->num_rows > 0) {
    while ($row = $result_specialitati->fetch_assoc()) {
        $specialitati[] = $row['Nume_specialitate'];
        $valori_specialitati[] = $row['numar_pacienti'];
}
$boli_cronice = ['Diabet', 'Hipertensiune', 'Astm bronșic', 'Cancer'];
$valori_boli_cronice = [0, 0, 0, 0];
if ($result_boli_cronice->num_rows > 0) {
    $row_boli_cronice = $result_boli_cronice->fetch_assoc();
    $valori_boli_cronice[0] = $row_boli_cronice['Diabet'];
    $valori_boli_cronice[1] = $row_boli_cronice['Hipertensiune'];
    $valori_boli_cronice[2] = $row_boli_cronice['Astm_bronsic'];
    $valori_boli_cronice[3] = $row_boli_cronice['Cancer'];
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistici Clinică</title>
</head>
<body>
    <h1>Statistici Clinică</h1>
    <h2>Distribuția pacienților pe specialități (Pie Chart)</h2>
    <?php
    $pie_chart = new PieGraph(600, 400);
    $pie_chart->SetShadow();
    $p1 = new PiePlot($valori_specialitati);
    $p1->SetLegends($specialitati);
    $pie_chart->Add($p1);
    $pie_chart->Stroke();
    ?>
    <h2>Distribuția pacienților cu boli cronice (Bar Chart)</h2>
    <?php
    $bar_chart = new Graph(600, 400);
    $bar_chart->SetScale("textlin");
    $b1 = new BarPlot($valori_boli_cronice);
    $b1->SetLegend('Pacienți cu boli cronice');
    $bar_chart->Add($b1);
    $bar_chart->Stroke();
    ?>
</body>
</html>
<?php
$conn->close();
?>
