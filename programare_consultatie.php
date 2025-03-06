<?php
// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'Clinica');

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Preluarea informațiilor pentru pacienți, medici și specializări
$pacienti_result = $conn->query("SELECT * FROM Pacienti");
if (!$pacienti_result) {
    die("Eroare la interogarea SQL pentru pacienți: " . $conn->error);
}

$specializari_result = $conn->query("SELECT DISTINCT Nume_specialitate FROM Doctori");
if (!$specializari_result) {
    die("Eroare la interogarea SQL pentru specializări: " . $conn->error);
}

// Dacă formularul a fost trimis, preluăm datele și le inserăm în baza de date
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pacient_cnp = $_POST['pacient_cnp'];
    $doctor_id = $_POST['Id_doctor'];
    $data_consultatie = $_POST['data_consultatie'];

    $pacient_result = $conn->query("SELECT Nume, Prenume FROM Pacienti WHERE CNP = '$pacient_cnp'");
    if ($pacient_result->num_rows === 0) {
        die("Pacientul cu CNP-ul $pacient_cnp nu există.");
    }
    $pacient = $pacient_result->fetch_assoc();
    $nume_pacient = $pacient['Nume'] . ' ' . $pacient['Prenume'];

    // Obținem specializarea doctorului
    $doctor_result = $conn->query("SELECT Nume_specialitate FROM Doctori WHERE Id_doctor = '$doctor_id'");
    if ($doctor_result->num_rows === 0) {
        die("Medicul selectat nu există.");
    }
    $doctor = $doctor_result->fetch_assoc();
    $specialitate = $doctor['Nume_specialitate'];

    // Ora consultației
    $ora_consult = date('H:i:s', strtotime($data_consultatie));

    // Inserare date în tabelul programariviitoare
    $sql = "INSERT INTO programariviitoare (CNP, NumePacient, Data_consult, Ora_consult, Specialitate, Id_doctor)
            VALUES ('$pacient_cnp', '$nume_pacient', '$data_consultatie', '$ora_consult', '$specialitate', '$doctor_id')";

    if ($conn->query($sql) === TRUE) {
        echo "Consultația a fost programată cu succes!";
    } else {
        echo "Eroare: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programare Consultatie</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: url('images/8.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .main-container {
            display: flex;
            height: 100vh;
            padding: 20px;
        }
        .sidebar {
            width: 250px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding-top: 20px;
            padding-left: 15px;
            border-radius: 10px;
        }
        .sidebar a {
            display: block;
            color: #b0b0b0;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #007bff;
            color: #fff;
        }
        .content {
            flex: 1;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="sidebar">
            <h3>Opțiuni</h3>
            <a href="index1.php">Acasă</a>
            <a href="pacienti.php">Pacienți</a>
            <a href="consultatii.php" class="sidebar-button">Consultatii</a>
        </div>
        
        <div class="content">
            <h2>Programare Consultatie</h2>
            <form action="programare_consultatie.php" method="POST">
                <div class="form-group">
                    <label for="pacient_cnp">Pacient</label>
                    <select name="pacient_cnp" class="form-control" required>
                        <option value="">Alege pacientul</option>
                        <?php while ($pacient = $pacienti_result->fetch_assoc()) { ?>
                            <option value="<?php echo $pacient['CNP']; ?>"><?php echo $pacient['Nume'] . ' ' . $pacient['Prenume']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="specialitate">Specializare</label>
                    <select name="specialitate" id="specialitate" class="form-control" required>
                        <option value="">Alege specializarea</option>
                        <?php while ($specializare = $specializari_result->fetch_assoc()) { ?>
                            <option value="<?php echo $specializare['Nume_specialitate']; ?>"><?php echo $specializare['Nume_specialitate']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="Id_doctor">Medic</label>
                    <select name="Id_doctor" id="Id_doctor" class="form-control" required>
                        <option value="">Selectați medicul</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data_consultatie">Data Consultației</label>
                    <input type="datetime-local" name="data_consultatie" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Programează Consultație</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#specialitate").change(function() {
                var specialitate = $(this).val();
                if (specialitate !== "") {
                    $.ajax({
                        url: "get_medici2.php",
                        method: "POST",
                        data: { specialitate: specialitate },
                        success: function(data) {
                            $("#Id_doctor").html(data);
                        }
                    });
                } else {
                    $("#Id_doctor").html('<option value="">Selectați medicul</option>');
                }
            });
        });
    </script>

</body>
</html>
