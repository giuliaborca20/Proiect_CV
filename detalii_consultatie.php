<?php
// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'Clinica');

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Preluarea CNP-ului pacientului din URL (dacă este setat)
if (isset($_GET['CNP'])) {
    $CNP = $_GET['CNP'];

    // Interogare pentru a obține datele pacientului pe baza CNP-ului
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

// Verificăm dacă s-au trimis modificările prin formular
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Dacă butonul de actualizare a fost apăsat
    if (isset($_POST['update'])) {
        // Preluăm valorile din formular
        $nume = $_POST['nume'];
        $prenume = $_POST['prenume'];
        $adresa = $_POST['adresa'];
        $email = $_POST['email'];
        $telefon = $_POST['telefon'];

        // Actualizăm datele pacientului în baza de date
        $update_sql = "UPDATE Pacienti SET
                        Nume = '$nume', 
                        Prenume = '$prenume', 
                        Adresa = '$adresa', 
                        Email = '$email', 
                        Telefon = '$telefon' 
                        WHERE CNP = '$CNP'";

        if ($conn->query($update_sql) === TRUE) {
            echo "<div class='alert alert-success mt-3'>Datele pacientului au fost actualizate cu succes!</div>";
            // Adăugăm scriptul JavaScript pentru redirecționare
            echo "<script>
                    setTimeout(function(){
                        window.location.href = 'pacienti.php';
                    }, 3000); // Redirecționează după 3 secunde
                  </script>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Eroare la actualizarea datelor: " . $conn->error . "</div>";
        }
    }

    // Dacă butonul de ștergere a fost apăsat
    if (isset($_POST['delete'])) {
        $password = $_POST['password'];

        // Verifică dacă parola există în tabela Doctori
        $user_sql = "SELECT * FROM Doctori WHERE Parola = '$password'";
        $user_result = $conn->query($user_sql);

        if ($user_result && $user_result->num_rows > 0) {
            // Șterge consultațiile pacientului
            $conn->query("DELETE FROM Consultatii WHERE CNP_Pacient = '$CNP'");

            // Șterge pacientul din tabela Pacienti
            $delete_sql = "DELETE FROM Pacienti WHERE CNP='$CNP'";
            
            if ($conn->query($delete_sql) === TRUE) {
                echo "<div class='alert alert-success mt-3'>Pacientul și consultațiile aferente au fost șterse!</div>";
                echo "<script>setTimeout(function(){ window.location.href = 'pacienti.php'; }, 3000);</script>";
            } else {
                echo "<div class='alert alert-danger mt-3'>Eroare la ștergere: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger mt-3'>Parolă incorectă!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editează Pacient</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/8.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin-left: 250px; /* Distanța pentru sidebar */
        }
		
        .sidebar {
           position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding-top: 20px;
    padding-left: 15px;
    border-radius: 0;
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
	
        .container {
            max-width: 800px;
            margin-top: 50px;
            background-color: rgba(255, 255, 255, 0.8); /* Fundal semi-transparent */
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

<!-- Sidebar -->
<div class="sidebar">
    <h3>Opțiuni</h3>
        <a href="index1.php">Acasă</a>
        <a href="pacienti.php">Pacienți</a>
        <a href="generare_statistici2.php">Generare Statistici</a>
        <a href="programare_consultatie.php" class="sidebar-button">Programare Consultatie</a>
</div>

<!-- Content -->
<div class="container">
    <h2>Editează Informațiile Pacientului</h2>
    <form action="modificare_pacient.php?CNP=<?php echo $CNP; ?>" method="POST">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td><label for="nume">Nume:</label></td>
                    <td><input type="text" class="form-control" id="nume" name="nume" value="<?php echo $patient['Nume']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="prenume">Prenume:</label></td>
                    <td><input type="text" class="form-control" id="prenume" name="prenume" value="<?php echo $patient['Prenume']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="adresa">Adresa:</label></td>
                    <td><input type="text" class="form-control" id="adresa" name="adresa" value="<?php echo $patient['Adresa']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="email" class="form-control" id="email" name="email" value="<?php echo $patient['Email']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="telefon">Telefon:</label></td>
                    <td><input type="text" class="form-control" id="telefon" name="telefon" value="<?php echo $patient['Telefon']; ?>" required></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">
                        <button type="submit" name="update" class="btn btn-primary">Salvează Modificările</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <!-- Butoane de acțiune -->
    <div class="d-flex justify-content-between">
        <a href="pacienti.php" class="btn-back">Înapoi la lista pacienților</a>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Șterge Pacientul</button>
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
                    <form action="modificare_pacient.php?CNP=<?php echo $CNP; ?>" method="POST">
                        <label for="password">Introduceți parola pentru confirmare:</label>
                        <input type="password" name="password" class="form-control" required>
                        <button type="submit" name="delete" class="btn btn-danger mt-2">Confirmă Ștergerea</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery și Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
