<?php

$conn = new mysqli("localhost", "root", "230403", "baristacafe");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    if (isset($_POST['nombre'])) {
        // Nuevo cliente
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $fechaHora = $_POST['datetime'];
        $personas = $_POST['num_personas'];
        $direccion = $_POST['direccion'];
        $telefono1 = $_POST['telefono1'];
        $telefono2 = $_POST['telefono2'];

        // Llamar al stored procedure para insertar el cliente
        $stmt = $conn->prepare("CALL InsertarCliente(?, ?, ?, @p_IdCliente)");
        $stmt->bind_param("sss", $nombre, $direccion, $correo);
        $stmt->execute();

        // Obtener el ID del cliente recién insertado
        $stmt->close(); // Cierra el statement antes de ejecutar la siguiente consulta
        $result = $conn->query("SELECT @p_IdCliente as IdCliente");
        $row = $result->fetch_assoc();
        $idCliente = $row['IdCliente'];

        // Continuar con el procesamiento
        if ($idCliente == -1) {
            echo "<script>alert('Ya existe un cliente con esos datos.');</script>";
        } else {
            echo "<script>alert('ID del nuevo cliente: " . $idCliente . "');</script>";
            // Insertar los teléfonos si están presentes
            $stmtPhone = $conn->prepare("INSERT INTO TelefonosCliente(IdCliente, Telefonos) VALUES (?, ?)");

            $stmtPhone->bind_param("is", $idCliente, $telefono1);
            $stmtPhone->execute();

            if (!empty($telefono2)) {
                $stmtPhone->bind_param("is", $idCliente, $telefono2);
                $stmtPhone->execute();
            }

            $stmtPhone->close();

            // Separar la fecha y la hora
            list($fechaReservacion, $horaReservacion) = explode('T', $fechaHora);

            // Convertir la fecha y hora al formato adecuado para MySQL
            $fechaHoraReservacion = date("Y-m-d H:i:s", strtotime("$fechaReservacion $horaReservacion"));

            try {
                // Llamar al procedimiento almacenado HacerReservacion
                $stmt = $conn->prepare("CALL HacerReservacion(?, ?, ?, ?, @Mensaje)");
                $stmt->bind_param("issi", $idCliente, $fechaReservacion, $fechaHoraReservacion, $personas);
                $stmt->execute();

                // Obtener el mensaje de la reserva
                $result = $conn->query("SELECT @Mensaje as Mensaje");
                $row = $result->fetch_assoc();
                $mensajeReserva = $row['Mensaje'];

                // Cerrar la conexión y liberar recursos
                $stmt->close();
                $conn->close();

                // Mostrar el mensaje de la reserva
                echo "<script>alert('" . $mensajeReserva . "');</script>";

                // ... (más código)        
            } catch (mysqli_sql_exception $e) {
                echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
            }
        }
    } elseif (isset($_POST['id'])) {
        // Cliente existente
        $idCliente = $_POST['id'];
        $correo = $_POST['email'];
        $fechaHora = $_POST['datetime'];
        $personas = $_POST['num_personas'];

        // Verificar que el cliente exista en la base de datos
        $stmtCheckCliente = $conn->prepare("SELECT IdCliente FROM cliente WHERE IdCliente = ? AND CorreoElectronico = ?");
        $stmtCheckCliente->bind_param("is", $idCliente, $correo);
        $stmtCheckCliente->execute();
        $stmtCheckCliente->store_result();
        $numRows = $stmtCheckCliente->num_rows;
        $stmtCheckCliente->close();

        if ($numRows == 0) {
            echo "<script>alert('Cliente no encontrado.');</script>";
        } else {
            // Continuar con el procesamiento
            // Separar la fecha y la hora
            list($fechaReservacion, $horaReservacion) = explode('T', $fechaHora);

            // Convertir la fecha y hora al formato adecuado para MySQL
            $fechaHoraReservacion = date("Y-m-d H:i", strtotime("$fechaReservacion $horaReservacion"));

            try {
                // Llamar al procedimiento almacenado HacerReservacion
                $stmt = $conn->prepare("CALL HacerReservacion(?, ?, ?, ?, @Mensaje)");
                $stmt->bind_param("issi", $idCliente, $fechaReservacion, $fechaHoraReservacion, $personas);
                $stmt->execute();

                // Obtener el mensaje de la reserva
                $result = $conn->query("SELECT @Mensaje as Mensaje");
                $row = $result->fetch_assoc();
                $mensajeReserva = $row['Mensaje'];

                // Cerrar la conexión y liberar recursos
                $stmt->close();
                $conn->close();

                // Mostrar el mensaje de la reserva
                echo "<script>alert('" . $mensajeReserva . "');</script>";

                // ... (más código)        
            } catch (mysqli_sql_exception $e) {
                echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
            }
        }
    }
}

?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="PROYECTO ESCOLAR DE BASES DE DATOS">
    <meta name="author" content="Juan De Luna // Ronaldo Martinez // Uriel Acosta">

    <title>Reservacion</title>

    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,400;0,600;0,700;1,200;1,700&display=swap" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/vegas.min.css" rel="stylesheet">
    <link href="css/tooplate-barista.css" rel="stylesheet">
</head>

<body class="reservation-page">
    <main>
        <header>
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand d-flex align-items-center" href="index.html">
                        <img src="images/coffee-beans.png" class="navbar-brand-image img-fluid" alt="">
                        Barista Cafe
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-lg-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php#section_1">Inicio</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php#section_2">Acerca de</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php#section_3">Nuestro menu</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php#section_4">Reseñas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php#section_5">Contacto</a>
                            </li>
                        </ul>
                        <div class="ms-lg-3">
                            <a class="btn custom-btn custom-border-btn" href="reservation.php">
                                Reserva aquí
                                <i class="bi-arrow-up-right ms-2"></i>
                            </a>
                        </div>
                        <div class="ms-lg-3">
                            <a class="btn custom-btn custom-border-btn" href="cuenta.php">
                                Cuenta
                                <i class="bi-person-fill ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </header>


        <section class="booking-section section-padding">
            <div id="IsClient" class="container min-vh-100 d-flex flex-column justify-content-center">
                <div class="row custom-form booking-form">
                    <div class="col-lg-4 col-md-10 col-8 mx-auto mt-2 d-flex">
                        <button id="buttonNotClient" type="button" class="btn custom-btn flex-grow-1 btn-lg">¡Soy
                            cliente nuevo!</button>
                    </div>
                </div>
                <div class="row custom-form booking-form">
                    <div class="col-lg-4 col-md-10 col-8 mx-auto mt-2 d-flex">
                        <button id="buttonIsClient" type="button" class="btn custom-btn flex-grow-1 btn-lg">¿Ya eres
                            cliente?</button>
                    </div>
                </div>
            </div>
            <div class="container" id="formNewClient">
                <div class="row">
                    <div class="col-lg-10 col-12 mx-auto">
                        <div class="booking-form-wrap">
                            <div class="row">
                                <div class="col-lg-7 col-12 p-0">
                                    <form class="custom-form booking-form" id="newClientForm" action="" method="post">
                                        <div class="text-center mb-4 pb-lg-2">
                                            <em class="text-white">Reservación para clientes nuevos</em>
                                            <h2 class="text-white">Haz tu reservación</h2>
                                        </div>
                                        <div class="booking-form-body">
                                            <div class="row">
                                                <div class="col-lg-6 col-12">
                                                    <input type="text" name="nombre" id="booking-form-name" class="form-control" placeholder="Nombre completo..." required>
                                                </div>
                                                <div class="col-lg-6 col-12">
                                                    <input type="email" class="form-control" name="correo" id="booking-form-email" placeholder="Correo Electrónico" required>
                                                </div>
                                                <div class="col-lg-12 col-12">
                                                    <input type="datetime-local" name="datetime" id="booking-form-datetime" class="form-control" required>
                                                </div>
                                                <div class="col-lg-12 col-12">
                                                    <input type="number" name="num_personas" id="booking-form-number" class="form-control" placeholder="Número de personas" required>
                                                </div>
                                                <div class="col-lg-12 col-12">
                                                    <input type="text" name="direccion" id="booking-form-direccion" class="form-control" placeholder="Dirección personal" required="">
                                                </div>
                                                <div class="col-lg-12 col-12">
                                                    <input type="tel" class="form-control" name="telefono1" id="booking-form-tel1" placeholder="Teléfono 1" pattern="[0-9]+" required>
                                                </div>
                                                <div class="col-lg-12 col-12">
                                                    <input type="tel" class="form-control" name="telefono2" id="booking-form-tel2" placeholder="Teléfono 2" pattern="[0-9]+">
                                                </div>
                                                <div class="col-lg-4 col-md-10 col-8 mx-auto mt-2">
                                                    <button class="form-control" id="submitNewClient">Continuar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-lg-5 col-12 p-0">
                                    <div class="booking-form-image-wrap">
                                        <img src="images/barman-with-fruits.jpg" class="booking-form-image img-fluid" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container" id="existing-client-form">
                <div class="row">
                    <div class="col-lg-10 col-12 mx-auto">
                        <div class="booking-form-wrap">
                            <div class="row">
                                <div class="col-lg-7 col-12 p-0">
                                    <form class="custom-form booking-form" id="existingClientForm" action="" method="post">
                                        <div class="text-center mb-4 pb-lg-2">
                                            <em class="text-white">Reservación para clientes existentes</em>
                                            <h2 class="text-white">Haz tu reservación</h2>
                                        </div>
                                        <div class="booking-form-body">
                                            <div class="row">
                                                <div class="col-lg-6 col-12">
                                                    <input type="text" name="id" id="id" class="form-control" placeholder="IdCliente" required>
                                                </div>
                                                <div class="col-lg-6 col-12">
                                                    <input type="email" name="email" id="email" class="form-control" placeholder="Correo Electrónico" required>
                                                </div>
                                                <div class="col-lg-6 col-12">
                                                    <input type="datetime-local" name="datetime" id="datetime" class="form-control" required>
                                                </div>
                                                <div class="col-lg-6 col-12">
                                                    <input type="number" name="num_personas" id="number" class="form-control" placeholder="Número de personas" required>
                                                </div>
                                                <div class="col-lg-12 col-12">
                                                    <button id="submitExistingClient" class="form-control">Continuar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-lg-5 col-12 p-0">
                                    <div class="booking-form-image-wrap">
                                        <img src="images/barman-with-fruits.jpg" class="booking-form-image img-fluid" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="site-footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-12 me-auto">
                        <em class="text-white d-block mb-4">¿Dónde puedes encontrarnos?</em>
                        <strong class="text-white">
                            <i class="bi-geo-alt me-2"></i>
                            Aguascalientes, Paseos de Aguascalientes
                        </strong>
                        <ul class="social-icon mt-4">
                            <li class="social-icon-item">
                                <a href="#" class="social-icon-link bi-facebook">
                                </a>
                            </li>
                            <li class="social-icon-item">
                                <a href="" target="_new" class="social-icon-link bi-twitter">
                                </a>
                            </li>
                            <li class="social-icon-item">
                                <a href="#" class="social-icon-link bi-whatsapp">
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-12 mt-4 mb-3 mt-lg-0 mb-lg-0">
                        <em class="text-white d-block mb-4">Contacto</em>
                        <p class="d-flex mb-1">
                            <strong class="me-2">Teléfono:</strong>
                            <a href="tel:305-240-9671" class="site-footer-link">
                                (33) 2050 5290
                            </a>
                        </p>
                        <p class="d-flex">
                            <strong class="me-2">Email:</strong>
                            <a href="mailto:info@yourgmail.com" class="site-footer-link">
                                Baristacafe@gmail.com
                            </a>
                        </p>
                    </div>
                    <div class="col-lg-5 col-12">
                        <em class="text-white d-block mb-4">Nuestro horario.</em>
                        <ul class="opening-hours-list">
                            <li class="d-flex">
                                Lunes - Viernes
                                <span class="underline"></span>
                                <strong>16:00 - 22:30</strong>
                            </li>
                            <li class="d-flex">
                                Sábados y Domingos
                                <span class="underline"></span>
                                <strong>11:00 - 16:30</strong>
                            </li>
                            <li class="d-flex">
                                Martes
                                <span class="underline"></span>
                                <strong>Cerrado</strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-8 col-12 mt-4">
                        <p class="copyright-text mb-0">
                            Copyright © Barista Cafe - Diseño: Uriel, Ronaldo, Juan
                            <a rel="sponsored" href="" target="_blank">Todos los derechos reservados</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.sticky.js"></script>
        <script src="js/vegas.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#formNewClient").hide();
                $("#existing-client-form").hide();


                $("#buttonNotClient").click(function() {
                    $("#IsClient").removeClass("min-vh-100");
                    $("#IsClient").addClass("d-none");
                    $("#formNewClient").show();
                });

                $("#buttonIsClient").click(function() {
                    $("#IsClient").removeClass("min-vh-100");
                    $("#IsClient").addClass("d-none");
                    $("#existing-client-form").show();
                });

            });
        </script>

</body>

</html>