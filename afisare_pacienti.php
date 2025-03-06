<?php
// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'Clinica');

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Inițializare variabilă de căutare
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Modificare interogare SQL pentru a include filtrarea
if ($search) {
    $sql = "SELECT * FROM Pacienti WHERE Nume LIKE '%$search%' OR CNP LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM Pacienti";
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
        /* Stiluri pentru body */
        body {
            background: url('images/8.jpg') no-repeat center center fixed;
            background-size: 100% 100%;
            font-family: 'Arial', sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }

        /* Container principal cu două coloane */
        .main-container {
            display: flex;
            flex: 1;
        }

        /* Bara laterală */
        .sidebar {
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
            padding-left: 15px;
        }

        .sidebar h3 {
            color: #FFD700;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background-color: #FFD700;
            color: #343a40;
        }

        /* Conținutul principal */
        .content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        h2 {
            background-color: #D6ED17FF;
            padding: 1%;
            border-radius: 35px;
            text-align: center;
        }

        /* Stil pentru bara de căutare */
        .search-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .search-container input {
            width: 80%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            color: black;
        }

        .search-container button {
            background-color: #606060FF;
            color: #D6ED17FF;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #D6ED17FF;
            color: #606060FF;
        }

        table {
            background-color: #D6ED17FF;
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 5px solid #606060FF;
            padding: 10px;
            color: black;
        }

        th {
            background-color: #606060FF;
            color: #D6ED17FF;
        }

        .edit-btn, .pdf-btn {
            background-color: #606060FF;
            color: #D6ED17FF;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .edit-btn:hover, .pdf-btn:hover {
            background-color: #D6ED17FF;
            color: #606060FF;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <!-- Bara laterală -->
        <div class="sidebar">
            <h3>Opțiuni</h3>
            <a href="index.php">Acasă</a>
            <a href="consultatii.php">Consultatii</a>
            <a href="generare_statistici2.php">Generare Statistici</a>
            <a href="pacienti.php">Pacienți</a>
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
            </div>

            <table border="1">
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
                <?php
                if ($result->num_rows > 0) {
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
                            <td>
                                <form action='modificare_pacient.php' method='get' style='display:inline-block;'>
                                    <input type='hidden' name='CNP' value='{$row['CNP']}'>
                                    <input type='submit' value='Editează' class='edit-btn'>
                                </form>
                                <form action='generare_PDF2.php' method='get' style='display:inline-block;'>
                                    <input type='hidden' name='CNP' value='{$row['CNP']}'>
                                    <input type='submit' value='Generare PDF' class='pdf-btn'>
                                </form>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Nu există pacienți care corespund criteriilor de căutare.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <!-- jQuery și Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
