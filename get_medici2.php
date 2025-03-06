<?php
// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'Clinica');

// Verificare conexiune
if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

// Preluarea specialității din cererea POST
if (isset($_POST['specialitate'])) {
    $specialitate = $_POST['specialitate'];

    // Obține medicii care au specialitatea respectivă
    $sql = "SELECT Id_doctor, Nume, Prenume FROM Doctori WHERE Nume_specialitate = '$specialitate'";
    $result = $conn->query($sql);

    // Verificăm dacă există medici pentru specialitatea respectivă
    if ($result->num_rows > 0) {
        echo "<option value=''>Selectați medicul</option>";
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['Id_doctor'] . "'>" . $row['Nume'] . " " . $row['Prenume'] . "</option>";
        }
    } else {
        echo "<option value=''>Nu există medici pentru această specialitate</option>";
    }
} else {
    echo "<option value=''>Selectați specialitatea mai întâi</option>";
}

$conn->close();
?>
