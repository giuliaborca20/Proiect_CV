<?php
// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'Clinica');

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Variabilele pentru formular
$nume = $prenume = $cnp = $adresa = $email = $telefon = "";
$data_nasterii = $varsta = "";

// Dacă formularul este trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nume = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $cnp = $_POST['cnp'];
    $adresa = $_POST['adresa'];
    $email = $_POST['email'];
    $telefon = $_POST['telefon'];

    // Extragem data nașterii și vârsta din CNP
    if (strlen($cnp) == 13) {
        $an = substr($cnp, 1, 2); // primele 2 caractere (anul)
        $luna = substr($cnp, 3, 2); // următoarele 2 caractere (luna)
        $zi = substr($cnp, 5, 2); // următoarele 2 caractere (ziua)

        // Dacă anul este mai mic decât 22, înseamnă că este 2000+
        if ($an < 22) {
            $an = '20' . $an;
        } else {
            $an = '19' . $an;
        }

        // Formăm data nașterii
        $data_nasterii = $an . '-' . $luna . '-' . $zi;

        // Calculăm vârsta
        $data_curenta = new DateTime();
        $data_nastere = new DateTime($data_nasterii);
        $interval = $data_curenta->diff($data_nastere);
        $varsta = $interval->y;
    }

    // Inserarea pacientului în baza de date
    $sql = "INSERT INTO Pacienti (Nume, Prenume, CNP, Adresa, Data_nasterii, Varsta, Email, Telefon) 
            VALUES ('$nume', '$prenume', '$cnp', '$adresa', '$data_nasterii', '$varsta', '$email', '$telefon')";

    if ($conn->query($sql) === TRUE) {
        // Redirecționare la index1.php după 3 secunde
        header("refresh:3;url=pacienti.php");
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
    <title>Adăugare Pacient</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: url('images/8.jpg') no-repeat center center fixed;
            background-size: cover; /* Imaginea va acoperi întreaga fereastră */
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
    <h2 class="text-center">Adăugare Pacient</h2>
    <form action="adaugare_pacient.php" method="POST">
        <div class="form-group">
            <label for="nume">Nume:</label>
            <input type="text" class="form-control" id="nume" name="nume" required>
        </div>
        <div class="form-group">
            <label for="prenume">Prenume:</label>
            <input type="text" class="form-control" id="prenume" name="prenume" required>
        </div>
        <div class="form-group">
            <label for="cnp">CNP:</label>
            <input type="text" class="form-control" id="cnp" name="cnp" required>
        </div>
        <div class="form-group">
            <label for="adresa">Adresă:</label>
            <input type="text" class="form-control" id="adresa" name="adresa" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="telefon">Telefon:</label>
            <input type="text" class="form-control" id="telefon" name="telefon" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Adaugă Pacient</button>
    </form>

    <!-- Buton de înapoi -->
    <a href="pacienti.php" class="btn btn-back btn-block">Înapoi</a>
</div>

<!-- jQuery și Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
