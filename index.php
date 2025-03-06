<?php
// Afișează erorile PHP pentru debugging
session_start();

// Încarcă fișierul de conectare la baza de date
require_once 'db_connect.php';

// Verifică dacă utilizatorul a trimis datele de autentificare
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifică dacă email-ul și parola sunt setate în formular
    if (isset($_POST['email']) && isset($_POST['parola'])) {
        // Preia datele din formular
        $email = $_POST['email'];
        $parola = $_POST['parola'];

        // Pregătește și execută interogarea pentru a verifica dacă există utilizatorul cu email-ul respectiv
        $stmt = $dbh->prepare("SELECT * FROM doctori WHERE Email = :email"); // Email cu litere mari
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Verifică dacă există un rezultat
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifică dacă parola introdusă se potrivește cu parola din baza de date
            if ($parola == $row['Parola']) {  // 'Parola' cu litere mari
                // Autentificarea a fost reușită, redirecționează
                header("Location: index1.php");
                exit();
            } else {
                // Parola este incorectă
                $error_message = "Email sau parolă incorecte!";
            }
        } else {
            // Nu există utilizator cu acest email
            $error_message = "Email sau parolă incorecte!";
        }
    } else {
        // Dacă email-ul sau parola nu sunt setate
        $error_message = "Te rugăm să completezi toate câmpurile!";
    }
}
?>

<!-- Formularul de autentificare -->
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Asigură-te că html și body nu au margini suplimentare */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            box-sizing: border-box;
        }

        /* Fundalul paginii */
        body {
            background: url('images/8.jpg') no-repeat center center fixed;
            background-size: 100% 100%;
            font-family: 'Arial', sans-serif;
            color: #fff;
            overflow-x: hidden;
        }

        /* Container pentru centrul paginii */
        .container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 0 15px;
        }

        /* Stiluri pentru titluri */
        h2 {
            font-size: 2rem;
            color: #000;
            margin-bottom: 20px;
        }

        /* Stiluri pentru butoane */
        .btn-custom {
            padding: 20px 40px;
            font-size: 1.3rem;
            text-transform: uppercase;
            border-radius: 30px;
            margin: 15px;
            width: 250px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: scale(1.05);
        }

        .btn-primary-custom {
            background-color: #6c757d;
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: #5a6268;
        }

        /* Stiluri pentru footer */
        footer {
            color: black;
            padding: 0px;
            position: fixed;
            width: 100%;
            text-align: center;
            bottom: 0;
            margin: 0;
            box-shadow: none;
            border: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Autentificare</h2>

        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Introduceti email-ul" required>
            </div>
            <div class="form-group">
                <label for="parola">Parola:</label>
                <input type="password" class="form-control" id="parola" name="parola" placeholder="Introduceti parola" required>
            </div>
            <button type="submit" class="btn btn-custom btn-primary-custom">Autentificare</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 Clinică </p>
    </footer>

    <!-- jQuery și Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
