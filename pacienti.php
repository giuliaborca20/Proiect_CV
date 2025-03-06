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

// Interogare pentru a selecta pacienții pe baza căutării (filtrare după nume sau CNP)
if ($search) {
    $sql = "SELECT * FROM Pacienti WHERE Nume LIKE '%$search%' OR CNP LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM Pacienti"; // Dacă nu există căutare, aducem toți pacienții
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizare Pacienți</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background: url('images/8.jpg') no-repeat center center fixed;
            background-size: cover; /* Imaginea va acoperi întreaga fereastră */
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Containerul principal */
        .main-container {
            display: flex;
            height: 100vh;
            padding: 20px;
        }

        /* Bara laterală */
        .sidebar {
            width: 250px;
            background-color: rgba(0, 0, 0, 0.7); /* Fundal transparent pentru sidebar */
            color: white;
            padding-top: 20px;
            padding-left: 15px;
            border-radius: 10px;
        }

        .sidebar h3 {
            color: #fff;
            font-size: 20px;
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

        /* Conținutul principal */
        .content {
            flex: 1;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8); /* Fundal semi-transparent */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }

        /* Stiluri pentru formularul de căutare */
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

        .search-container button:hover {
            background-color: #0056b3;
        }

        /* Stiluri pentru tabel */
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
            font-size: 16px;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        td {
            color: #555;
        }

        .edit-btn, .pdf-btn {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-transform: uppercase;
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

        footer {
            background-color: #343a40;
            color: white;
            padding: 15px;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

    </style>
</head>
<body>

    <div class="main-container">
        <!-- Bara laterală -->
        <div class="sidebar">
            <h3>Opțiuni</h3>
            <a href="index1.php">Acasă</a>
            <a href="consultatii.php">Consultatii</a>
            <a href="generare_statistici2.php">Generare Statistici</a>
           
        </div>

        <!-- Conținutul principal -->
        <div class="content">
            <h2>Vizualizare Pacienți</h2>

            <!-- Formular de căutare -->
            <div class="search-container">
                <form action="pacienti.php" method="get" style="width: 100%;">
                    <input type="text" name="search" placeholder="Căutați pacienți după nume sau CNP" value="<?php echo $search; ?>">
                    <button type="submit">Căutare</button>
                </form>
<a href="adaugare_pacient.php" class="btn btn-success" style="margin-left: 20px;">Adăugare Pacient</a>
            
            </div>

            <table>
                <thead>
                    <tr>
                        <th>CNP</th>
                        <th>Nume</th>
                        <th>Prenume</th>
                        <th>Adresa</th>
                        <th>Data Nașterii</th>
                        <th>Vârsta</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Iterare prin fiecare rând din rezultate
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['CNP']}</td>
                                <td>{$row['Nume']}</td>
                                <td>{$row['Prenume']}</td>
                                <td>{$row['Adresa']}</td>
                                <td>{$row['Data_nasterii']}</td>
                                <td>{$row['Varsta']}</td>
                                <td>{$row['Email']}</td>
                                <td>{$row['Telefon']}</td>
                                <td class='action-btns'>
                                    <!-- Butonul de editare care trimite CNP-ul pacientului pentru modificare -->
                                    <form action='modificare_pacient.php' method='get' style='display:inline-block;'>
                                        <input type='hidden' name='CNP' value='{$row['CNP']}'>
                                        <input type='submit' value='Editează' class='edit-btn'>
                                    </form>
                                    <!-- Butonul pentru generare PDF care trimite către generare_PDF2.php -->
                                    <form action='generare_PDF2.php' method='get' style='display:inline-block;'>
                                        <input type='hidden' name='CNP' value='{$row['CNP']}'>
                                        <input type='submit' value='Generare PDF' class='pdf-btn'>
                                    </form>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>Nu există pacienți care să corespundă criteriilor de căutare.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery și Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
