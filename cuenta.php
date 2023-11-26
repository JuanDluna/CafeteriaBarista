<?php
// Verificar si se recibió un método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn = new mysqli("localhost", "root", "CacadeVaca230403", "baristacafe");

    // Verificar si la conexión fue exitosa
    if ($conn->connect_error) {
        die("Error al conectar a la base de datos: " . $conn->connect_error);
    }

    // Verificar si se recibió un id_cliente
    if (isset($_POST['id_cliente'])) {
        $id_cliente = $_POST['id_cliente'];
        $correo = $_POST['correo'];

        // Verificar si el id_cliente existe en la base de datos
        $stmt = $conn->prepare("SELECT * FROM cliente WHERE IdCliente = ? AND CorreoElectronico = ?");
        $stmt->bind_param("ss", $id_cliente, $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Iniciar sesión
            session_start();
            $_SESSION['id_cliente'] = $id_cliente;
            $_SESSION['correo'] = $correo;
            header("Location: panel-cliente.php");
        } else {
            echo "<script>alert('El id_cliente o el correo son incorrectos.')</script>";
        }
    } else {
        $id_empleado = $_POST['id_empleado'];
        $nombre_empleado = $_POST['nombre_empleado'];

        // Verificar si el id_empleado existe en la base de datos
        $stmt = $conn->prepare("SELECT * FROM empleado WHERE IdEmpleado = ? AND NombreEmpleado = ?");
        $stmt->bind_param("ss", $id_empleado, $nombre_empleado);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Iniciar sesión
            session_start();
            $_SESSION['id_empleado'] = $id_empleado;
            $_SESSION['nombre_empleado'] = $nombre_empleado;
            header("Location: panel-empleado.php");
        } else {
            echo "<script>alert('El id_empleado o el nombre_empleado son incorrectos.')</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="PROYECTO ESCOLAR DE BASES DE DATOS">
    <meta name="author" content="Juan De Luna // Ronaldo Martinez // Uriel Acosta">

    <title>Cuenta</title>

    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,400;0,600;0,700;1,200;1,700&display=swap" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/vegas.min.css" rel="stylesheet">
    <link href="css/tooplate-barista.css" rel="stylesheet">
</head>

<body>

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
        <div id="btns" class="container min-vh-100 d-flex flex-column justify-content-center">
            <div class="row custom-form booking-form">
                <div class="col-lg-4 col-md-10 col-8 mx-auto mt-2 d-flex">
                    <button id="btn-cliente" type="button" class="btn custom-btn flex-grow-1 btn-lg">Soy cliente</button>
                </div>
            </div>
            <div class="row custom-form booking-form">
                <div class="col-lg-4 col-md-10 col-8 mx-auto mt-2 d-flex">
                    <button id="btn-empleado" type="button" class="btn custom-btn flex-grow-1 btn-lg">Soy empleado</button>
                </div>
            </div>
        </div>

        <div class="container" id="formulario-cliente">
            <div class="row">
                <div class="col-lg-10 col-12 mx-auto">
                    <div class="booking-form-wrap">
                        <form class="custom-form booking-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <div class="text-center mb-4 pb-lg-2">
                                <em class="text-white">Iniciar sesión como cliente</em>
                            </div>
                            <div class="booking-form-body">
                                <div class="row">
                                    <div class="col-lg-6 col-12 mb-3">
                                        <input type="text" name="id_cliente" class="form-control" placeholder="ID Cliente" required>
                                    </div>
                                    <div class="col-lg-6 col-12 mb-3">
                                        <input type="email" name="correo" class="form-control" placeholder="Correo Electrónico" required>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn custom-btn form-control">Continuar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container" id="formulario-empleado">
            <div class="row">
                <div class="col-lg-10 col-12 mx-auto">
                    <div class="booking-form-wrap">
                        <form class="custom-form booking-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <div class="text-center mb-4 pb-lg-2">
                                <em class="text-white">Iniciar sesión como empleado</em>
                            </div>
                            <div class="booking-form-body">
                                <div class="row">
                                    <div class="col-lg-6 col-12 mb-3">
                                        <input type="text" name="id_empleado" class="form-control" placeholder="ID Empleado" required>
                                    </div>
                                    <div class="col-lg-6 col-12 mb-3">
                                        <input type="text" name="nombre_empleado" class="form-control" placeholder="Nombre" required>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn custom-btn form-control">Continuar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
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
            // Ocultar los divs de los formularios al cargar la página
            $("#formulario-cliente").hide();
            $("#formulario-empleado").hide();

            $("#btn-cliente").click(function() {
                $("#btns").removeClass("min-vh-100");
                $("#btns").addClass("d-none");
                // Mostrar el formulario de cliente y ocultar el formulario de empleado
                $("#formulario-cliente").show();

            });

            $("#btn-empleado").click(function() {
                $("#btns").removeClass("min-vh-100");
                $("#btns").addClass("d-none");
                // Mostrar el formulario de empleado y ocultar el formulario de cliente
                $("#formulario-empleado").show();


            });
        });
    </script>

</body>

</html>