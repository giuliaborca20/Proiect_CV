<?php
// Setează datele de conexiune la baza de date
$host = 'localhost'; // sau IP-ul serverului de baze de date
$dbname = 'clinica'; // Numele bazei de date
$username = 'root'; // Numele de utilizator al bazei de date (implicit "root" pe localhost)
$password = ''; // Parola (implicită pe localhost este goală)

// Crează conexiunea
try {
    $dbh = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Setează opțiuni pentru conexiune (opțional)
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Conexiunea a fost realizată cu succes
} catch (PDOException $e) {
    // Dacă apare o eroare, o vom prinde și o vom afisa
    echo "Conexiunea a eșuat: " . $e->getMessage();
}
?>
