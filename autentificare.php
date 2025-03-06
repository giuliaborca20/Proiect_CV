<?php
session_start();

// Conectarea la baza de date
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clinica";  // Asigură-te că numele bazei de date este corect

try {
    $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Verificăm dacă formularul a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obținem valorile introduse
    $email = $_POST['email'];
    $parola = $_POST['parola'];

    // Verificăm dacă emailul există în baza de date
    $sql = "SELECT * FROM doctori WHERE Email = :email";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    // Dacă doctorul este găsit
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificăm parola
        if (password_verify($parola, $user['Parola'])) {
            // Autentificare reușită, salvăm datele în sesiune
            $_SESSION['user_id'] = $user['Id_doctor'];
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['user_nume'] = $user['Nume'];
            $_SESSION['user_prenume'] = $user['Prenume'];
            $_SESSION['user_specialitate'] = $user['Nume_specialitate'];

            // Redirect către dashboard sau altă pagină
            header("Location: dashboard.php");
            exit();
        } else {
            // Parola incorectă
            echo "Email sau parolă incorecte!";
        }
    } else {
        // Emailul nu există
        echo "Email sau parolă incorecte!";
    }
}
?>
