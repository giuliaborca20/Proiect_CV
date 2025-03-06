<?php
// Conectare la baza de date
$conn = new mysqli('localhost', 'root', '', 'Clinica');

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Preluarea consultațiilor viitoare din tabela programariviitoare
$sql = "SELECT 
            CNP, 
            NumePacient, 
            Data_consult, 
            Ora_consult, 
            Specialitate, 
            Id_doctor
        FROM programariviitoare
        WHERE Data_consult > CURDATE()"; // Filtrare doar programările viitoare

$result = $conn->query($sql);

// Verificăm dacă interogarea a returnat rezultate
if (!$result) {
    die("Eroare la interogarea SQL: " . $conn->error);
}

// Creăm un array pentru a stoca programările pe zile
$programari_viitoare = [];
while ($row = $result->fetch_assoc()) {
    $data_consult = date("Y-m-d", strtotime($row['Data_consult'])); // Extragem doar data
    $programari_viitoare[$data_consult][] = $row;
}

// Închidem conexiunea la baza de date
$conn->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programări Viitoare</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet">

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

        .fc-event {
            font-size: 10px; /* Mărimea fontului mai mică */
            padding: 3px; /* Spațiu mai mic între text și bordură */
        }

        .fc-day {
            height: 30px; /* Reducerea înălțimii fiecărei zile */
        }

        #calendar {
            width: 100%; /* Lățimea se ajustează automat */
            max-width: 800px; /* Lățimea maximă de 800px */
            height: 400px; /* O înălțime fixă de 400px */
            margin: 0 auto; /* Centrează calendarul pe pagină */
        }

    </style>
</head>
<body>

<div class="main-container">
    <div class="sidebar">
        <h3>Opțiuni</h3>
        <a href="index.php">Acasă</a>
        <a href="pacienti.php">Pacienți</a>
	<a href="consultatii.php">Consultatii</a>
        <a href="generare_statistici2.php">Generare Statistici</a>
          </div>

    <div class="content">
        <h2>Programări Viitoare</h2>
        <a href="programare_consultatie.php" class="btn btn-primary mb-3">Adaugă Programare</a>
    <div id="calendar"></div>
    </div>
</div>

<script>
// Construim un calendar utilizând FullCalendar
$(document).ready(function() {
    $('#calendar').fullCalendar({
        events: function(start, end, timezone, callback) {
            var events = [];
            
            // Programările viitoare din baza de date
            <?php foreach ($programari_viitoare as $data => $programari): ?>
                <?php foreach ($programari as $programare): ?>
                    events.push({
                        title: '<?php echo $programare['NumePacient']; ?>',
                        start: '<?php echo $data . "T" . $programare['Ora_consult']; ?>', // Data și Ora_consult
                        description: 'Specialitate: <?php echo $programare['Specialitate']; ?>', // Optional, dacă vrei să adaugi specialitatea
                        url: 'detalii_consultatie.php?CNP=<?php echo $programare['CNP']; ?>&Id_doctor=<?php echo $programare['Id_doctor']; ?>'
                    });
                <?php endforeach; ?>
            <?php endforeach; ?>
            
            callback(events);
        },
        dayClick: function(date, jsEvent, view) {
            alert('Click pe ziua: ' + date.format());
        },
        editable: true,
        droppable: true,
        eventLimit: true, // Permite afișarea unui număr limitat de evenimente pe zi
        height: 500, // Poți ajusta înălțimea totală a calendarului
    });
});
</script>

</body>
</html>
