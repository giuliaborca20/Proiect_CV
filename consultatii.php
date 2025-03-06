<?php
// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'Clinica');

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Variabile de filtrare
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sql = "SELECT 
            Consultatii.Id_consult, 
            Pacienti.Nume AS PacientNume, 
            Pacienti.Prenume AS PacientPrenume,
            Doctori.Nume AS DoctorNume, 
            Doctori.Prenume AS DoctorPrenume,
            Doctori.Nume_specialitate AS Specialitate,
            Consultatii.Data_consult AS Data,
            Consultatii.Diagnostic, 
            Consultatii.Medicamente,
            icd10_codes.description AS DiagnosticDescription
        FROM Consultatii 
        JOIN Pacienti ON Consultatii.CNP = Pacienti.CNP
        JOIN Doctori ON Consultatii.Id_doctor = Doctori.Id_doctor
        LEFT JOIN icd10_codes ON Consultatii.Diagnostic = icd10_codes.code"; // Corelare corectă cu icd10_codes

// Interogare pentru a selecta consultațiile pe baza căutării
if ($search) {
    $sql .= " WHERE Pacienti.Nume LIKE '%$search%' OR Pacienti.CNP LIKE '%$search%'";
}

$result = $conn->query($sql);

// Verificarea dacă interogarea a returnat un obiect valid
if ($result === false) {
    die("Error in query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizare Consultații</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-container input {
            width: 80%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-container button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #f1f1f1;
        }
        th {
            background-color: #343a40;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .edit-btn, .pdf-btn {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 12px;
            margin: 2px;
        }
        .edit-btn:hover, .pdf-btn:hover {
            background-color: #0056b3;
        }
        .action-btns {
            display: flex;
            justify-content: space-around;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="sidebar">
            <h3>Opțiuni</h3>
            <a href="index1.php">Acasă</a>
	<a href="afisare_programari.php">Programări consultații</a>            
<a href="pacienti.php">Pacienți</a>
            <a href="generare_statistici2.php">Generare Statistici</a>
        </div>
		
        <div class="content">
            <h2>Vizualizare Consultații</h2>
            <div class="search-container">
                <form action="consultatii.php" method="get" style="display: flex; gap: 10px; width: 100%;">
                    <input type="text" name="search" placeholder="Căutați după pacient sau medic" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Căutare</button>
                    <a href="adaugare_consultatie.php" class="btn btn-success" style="margin-left: 20px;">Adăugare Consultatie</a>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID Consult</th>
                        <th>Pacient</th>
                        <th>Medic</th>
                        <th>Specialitate</th>
                        <th>Data</th>
                        <th>Diagnostic</th>
                        <th>Medicamente</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['Id_consult']}</td>
                                <td>{$row['PacientNume']} {$row['PacientPrenume']}</td>
                                <td>{$row['DoctorNume']} {$row['DoctorPrenume']}</td>
                                <td>{$row['Specialitate']}</td>
                                <td>{$row['Data']}</td>
                                <td>{$row['Diagnostic']} - {$row['DiagnosticDescription']}</td> <!-- Afișăm și descrierea diagnosticului -->
                                <td>{$row['Medicamente']}</td>
                                <td class='action-btns'>
                                  <form action='modificare_consultatie.php' method='get' style='display:inline-block;'>
                                        <input type='hidden' name='ID Consult' value='{$row['Id_consult']}'>
                                        <input type='submit' value='Editează' class='edit-btn'>
                                    </form>

                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>Nu există consultații disponibile.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
