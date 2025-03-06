<?php
$conn = new mysqli('localhost', 'root', '', 'Clinica');

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

if (isset($_POST['specialitate'])) {
    $specialitate = $_POST['specialitate'];
    $stmt = $conn->prepare("SELECT Id_doctor, Nume, Prenume FROM Doctori WHERE Nume_specialitate = ?");
    $stmt->bind_param("s", $specialitate);
    $stmt->execute();
    $result = $stmt->get_result();



    echo '<option value="">Selectați medicul</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['Id_doctor'] . '">' . $row['Nume'] . " " . $row['Prenume'] . '</option>';
    }

    $stmt->close();
}

$conn->close();
?>
