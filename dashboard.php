<?php
session_start();

// Verifică dacă medicul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Afișează informațiile medicului
echo "Bine ai venit, " . $_SESSION['user_name'] . "!<br>";
echo "Specialitatea: " . $_SESSION['user_specialty'] . "<br>";
echo "<a href='logout.php'>Ieși din cont</a>";
?>
