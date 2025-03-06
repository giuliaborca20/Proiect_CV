

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem de Management al Clinicii</title>

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
            box-sizing: border-box; /* Evită marginile suplimentare care pot apărea din cauza box modelului */
        }

        /* Fundalul paginii */
        body {
            background: url('images/8.jpg') no-repeat center center fixed;
            background-size: 100% 100%; /* Ajustează imaginea pentru a se potrivi exact */
            font-family: 'Arial', sans-serif;
            color: #fff;
            overflow-x: hidden; /* Evită overflow-ul orizontal */
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
        h1 {
            font-size: 3.5rem;
            color: #000;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 1.8rem;
            color: #001;
            margin-bottom: 40px;
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

        /* Efecte la hover pentru butoane */
        .btn-custom:hover {
            transform: scale(1.05);
        }

        /* Culori pentru butoane */
        .btn-primary-custom {
            background-color: #6c757d;
            color: white;
        }
        .btn-primary-custom:hover {
            background-color: #5a6268;
        }

        .btn-success-custom {
            background-color: #28a745;
            color: white;
        }
        .btn-success-custom:hover {
            background-color: #218838;
        }

        .btn-info-custom {
            background-color: #17a2b8;
            color: white;
        }
        .btn-info-custom:hover {
            background-color: #117a8b;
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
            box-shadow: none; /* Elimină orice border suplimentar */
            border: none; /* Elimină orice border suplimentar */
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Sistem de Management al Clinicii</h1>
        <h2>Bine ai venit! Alege o opțiune:</h2>

        <div class="d-flex flex-column align-items-center">
            <button class="btn btn-custom btn-primary-custom" onclick="window.location.href='pacienti.php'">
                <i class="fas fa-users"></i> Pacienți
            </button>
            <button class="btn btn-custom btn-success-custom" onclick="window.location.href='consultatii.php'">
                <i class="fas fa-notes-medical"></i> Consultatii
            </button>
            <button class="btn btn-custom btn-info-custom" onclick="window.location.href='generare_statistici2.php'">
                <i class="fas fa-chart-bar"></i> Generare Statistici
            </button>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Clinică </p>
    </footer>

    <!-- jQuery și Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
