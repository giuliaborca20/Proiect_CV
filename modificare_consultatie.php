<?php
// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'Clinica');

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Preluarea ID-ului consultației din URL
if (isset($_GET['ID_Consult'])) {
    $id_consultatie = $_GET['ID_Consult'];

    // Interogare pentru a obține datele consultației
    $sql = "SELECT 
                Consultatii.Id_consult, 
                Consultatii.CNP,
                Pacienti.Nume AS PacientNume, 
                Pacienti.Prenume AS PacientPrenume,
                Doctori.Nume AS DoctorNume, 
                Doctori.Prenume AS DoctorPrenume,
                Doctori.Nume_specialitate AS Specialitate,
                Consultatii.Data_consult AS Data,
                Consultatii.Diagnostic, 
                Consultatii.Medicamente
            FROM Consultatii 
            JOIN Pacienti ON Consultatii.CNP = Pacienti.CNP
            JOIN Doctori ON Consultatii.Id_doctor = Doctori.Id_doctor
            WHERE Consultatii.Id_consult = '$id_consultatie'";

    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $consultatie = $result->fetch_assoc();
    } else {
        die("Consultația nu a fost găsită.");
    }
} else {
    die("ID-ul consultației nu a fost furnizat.");
}

// Obține toate codurile și descrierile ICD10
$icd_sql = "SELECT code, description FROM icd10_codes";
$icd_result = $conn->query($icd_sql);

// Procesarea formularului de actualizare
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnostic_code = $_POST['diagnostic'];  // Codul diagnosticului selectat
    $medicamente = $_POST['medicamente'];
    $data_consultatie = $_POST['data_consultatie'];

    // Interogare pentru a obține descrierea diagnosticului
    $description_sql = "SELECT description FROM icd10_codes WHERE code = '$diagnostic_code'";
    $description_result = $conn->query($description_sql);
    $diagnostic_description = '';
    if ($description_result->num_rows > 0) {
        $diagnostic_row = $description_result->fetch_assoc();
        $diagnostic_description = $diagnostic_row['description'];
    }

    // Actualizarea consultației
    $update_sql = "UPDATE Consultatii 
                   SET Diagnostic = '$diagnostic_code - $diagnostic_description', 
                       Medicamente = '$medicamente', 
                       Data_consult = '$data_consultatie'
                   WHERE Id_consult = '$id_consultatie'";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Consultația a fost actualizată cu succes!'); window.location.href='consultatii.php';</script>";
    } else {
        echo "Eroare: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificare Consultatie</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: url('images/8.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin-top: 50px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            border-radius: 8px;
        }

        .btn-back, .btn-danger {
            margin-top: 10px;
            border-radius: 8px;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .alert {
            border-radius: 8px;
        }

        .d-flex {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Modifică Consultația</h2>
    <form action="modificare_consultatie.php?ID_Consult=<?php echo $id_consultatie; ?>" method="POST">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td><label for="CNP">CNP Pacient:</label></td>
                    <td><input type="text" class="form-control" id="CNP" value="<?php echo $consultatie['CNP']; ?>" readonly></td>
                </tr>
                <tr>
                    <td><label for="pacient">Pacient:</label></td>
                    <td><input type="text" class="form-control" id="pacient" value="<?php echo $consultatie['PacientNume'] . ' ' . $consultatie['PacientPrenume']; ?>" readonly></td>
                </tr>
                <tr>
                    <td><label for="medic">Medic:</label></td>
                    <td><input type="text" class="form-control" id="medic" value="<?php echo $consultatie['DoctorNume'] . ' ' . $consultatie['DoctorPrenume']; ?>" readonly></td>
                </tr>
                <tr>
                    <td><label for="specialitate">Specialitate:</label></td>
                    <td><input type="text" class="form-control" id="specialitate" value="<?php echo $consultatie['Specialitate']; ?>" readonly></td>
                </tr>
                <tr>
                    <td><label for="data_consultatie">Data Consultației:</label></td>
                    <td><input type="date" class="form-control" id="data_consultatie" name="data_consultatie" value="<?php echo $consultatie['Data']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="diagnostic">Diagnostic:</label></td>
                    <td>
                        <select class="form-control" id="diagnostic" name="diagnostic" required>
                            <option value="">Selectează un diagnostic</option>
                            <?php while ($row = $icd_result->fetch_assoc()): ?>
                                <option value="<?php echo $row['code']; ?>" <?php echo ($consultatie['Diagnostic'] == $row['code'] ? 'selected' : ''); ?>>
                                    <?php echo $row['code'] . ' - ' . $row['description']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="medicamente">Medicamente:</label></td>
                    <td><input type="text" class="form-control" id="medicamente" name="medicamente" value="<?php echo $consultatie['Medicamente']; ?>" required></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">
                        <button type="submit" class="btn btn-primary">Salvează Modificările</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <div class="d-flex justify-content-between">
        <a href="consultatii.php" class="btn-back">Înapoi la lista consultațiilor</a>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Șterge Consultația</button>
    </div>

    <!-- Modal pentru ștergere -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmare Ștergere</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="modificare_consultatie.php?ID_Consult=<?php echo $id_consultatie; ?>" method="POST">
                        <label for="password">Introduceți parola pentru confirmare:</label>
                        <input type="password" name="password" class="form-control" required>
                        <button type="submit" name="delete" class="btn btn-danger mt-2">Confirmă Ștergerea</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
