<?php
// Activare erori PHP pentru debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'Clinica');

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Inițializare variabile
$cnp_pacient = $id_doctor = $data_consult = $diagnostic = $medicamente = "";

// Dacă formularul este trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cnp_pacient = $_POST['cnp_pacient'];
    $id_doctor = $_POST['id_doctor'];
    $data_consult = $_POST['data_consult'];
    $diagnostic = $_POST['diagnostic'];
    $medicamente = $_POST['medicamente'];

    // Pregătim interogarea pentru inserare
    $stmt = $conn->prepare("INSERT INTO Consultatii (CNP, Id_doctor, Data_consult, Diagnostic, Medicamente) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $cnp_pacient, $id_doctor, $data_consult, $diagnostic, $medicamente);

    if ($stmt->execute()) {
        header("Location: consultatii.php");
        exit();
    } else {
        echo "Eroare la inserare: " . $stmt->error;
    }

    $stmt->close();
}

// Obținerea listei de pacienți
$pacienti = $conn->query("SELECT CNP, Nume, Prenume FROM Pacienti");

// Obținerea listei de specialități
$specialitati = $conn->query("SELECT Nume_specialitate FROM Specialitati");

// Obținerea listei de coduri și diagnostice ICD
$coduri_diagnostice = $conn->query("SELECT code, description FROM icd10_codes");

$conn->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adăugare Consultație</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: url('images/8.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-back {
            background-color: #f0ad4e;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn-back:hover {
            background-color: #ec971f;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Adăugare Consultație</h2>
    <form action="adaugare_consultatie.php" method="POST">
        <div class="form-group">
            <label for="cnp_pacient">Pacient:</label>
            <select class="form-control" id="cnp_pacient" name="cnp_pacient" required>
                <option value="">Selectați pacientul</option>
                <?php while ($row = $pacienti->fetch_assoc()): ?>
                    <option value="<?= $row['CNP'] ?>"><?= $row['Nume'] . " " . $row['Prenume'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="specialitate">Specialitate:</label>
            <select class="form-control" id="specialitate" name="specialitate" required>
                <option value="">Selectați specialitatea</option>
                <?php while ($row = $specialitati->fetch_assoc()): ?>
                    <option value="<?= $row['Nume_specialitate'] ?>"><?= $row['Nume_specialitate'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="id_doctor">Medic:</label>
            <select class="form-control" id="id_doctor" name="id_doctor" required>
                <option value="">Selectați medicul</option>
            </select>
        </div>

        <div class="form-group">
            <label for="data_consult">Data Consultației:</label>
            <input type="date" class="form-control" id="data_consult" name="data_consult" required>
        </div>

        <div class="form-group">
            <label for="diagnostic">Diagnostic:</label>
            <select class="form-control" id="diagnostic" name="diagnostic" required>
                <option value="">Selectați diagnostic</option>
                <?php while ($row = $coduri_diagnostice->fetch_assoc()): ?>
                    <option value="<?= $row['code'] ?>"><?= $row['code'] . " - " . $row['description'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="medicamente">Medicamente:</label>
            <input type="text" class="form-control" id="medicamente" name="medicamente" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Adaugă Consultație</button>
    </form>

    <a href="consultatii.php" class="btn btn-back btn-block">Înapoi</a>
</div>

<script>
    $(document).ready(function() {
        $("#specialitate").change(function() {
            var specialitate = $(this).val();
            if (specialitate !== "") {
                $.ajax({
                    url: "get_medici.php",
                    method: "POST",
                    data: { specialitate: specialitate },
                    success: function(data) {
                        $("#id_doctor").html(data);
                    }
                });
            } else {
                $("#id_doctor").html('<option value="">Selectați medicul</option>');
            }
        });
    });
</script>

</body>
</html>
