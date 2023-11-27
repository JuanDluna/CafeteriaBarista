<?php
// Verificar la sesión del cliente
session_start();

if (!isset($_SESSION['id_cliente'])) {
    header("Location: cuenta.php"); // Redirigir a la página de inicio de sesión si no hay sesión activa
    exit();
}

$sql = new mysqli("localhost", "root", "", "baristacafe");

// Check connection
if ($sql->connect_error) {
    die("Connection failed: " . $sql->connect_error);
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="PROYECTO ESCOLAR DE BASES DE DATOS">
    <meta name="author" content="Juan De Luna // Ronaldo Martinez // Uriel Acosta">

    <title>Panel de cliente</title>

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

    <section class="booking-section section-padding bg-light text-dark">
        <div class="container">
            <h2 class="mt-5 text-light p-3" style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(10px);">Bienvenido, <?php echo $_SESSION['nombre']; ?>!</h2>
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover table-striped bg-white">
                    <thead class="thead-dark">
                        <tr>
                            <th>Fecha de reservación</th>
                            <th>Hora de reservación</th>
                            <th>Mesa asignada</th>
                            <th>Acciones</th> <!-- Nueva columna para acciones -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Obtener las reservaciones del usuario
                        $userId = $_SESSION['id_cliente'];
                        $stmt = $sql->prepare("SELECT * FROM reservacion WHERE IdCliente = ?");
                        $stmt->bind_param("s", $userId);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . date('d/m/Y', strtotime($row['FechaReservacion'])) . "</td>";
                                $horaReservacion = date('H:i', strtotime($row['HoraReservacion']));
                                echo "<td>" . $horaReservacion . "</td>";
                                echo "<td>" . $row['NumeroMesa'] . "</td>";
                                echo "<td>";
                                echo "<button class='btn btn-danger btn-sm eliminar-reservacion' data-numero-mesa='" . $row['NumeroMesa'] . "' data-id-cliente='" . $row['IdCliente'] . "' data-fecha-reservacion='" . $row['FechaReservacion'] . "' data-hora-reservacion='" . $row['HoraReservacion'] . "'>Eliminar</button>";
                                echo "<br>";
                                echo "<br>";
                                echo "<button class='btn btn-warning btn-sm modificar-reservacion' data-numero-mesa='" . $row['NumeroMesa'] . "' data-id-cliente='" . $row['IdCliente'] . "' data-fecha-reservacion='" . $row['FechaReservacion'] . "' data-hora-reservacion='" . $row['HoraReservacion'] . "'>Modificar</button>";
                                echo "</td>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No se encontraron reservaciones.</td></tr>";
                        }
                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>



    <!-- Pie de Página -->
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
            $('.eliminar-reservacion').click(function() {
                var numeroMesa = $(this).data('numero-mesa');
                var idCliente = $(this).data('id-cliente');
                var fechaReservacion = $(this).data('fecha-reservacion');
                var horaReservacion = $(this).data('hora-reservacion');

                // Confirmar la eliminación con el usuario (puedes usar un modal u otra confirmación)
                if (confirm("¿Estás seguro de que deseas eliminar esta reservación?")) {
                    // Enviar la solicitud AJAX para eliminar la reservación
                    $.ajax({
                        type: 'POST',
                        url: 'eliminar_reservacion_ajax.php',
                        data: {
                            numeroMesa: numeroMesa,
                            idCliente: idCliente,
                            fechaReservacion: fechaReservacion,
                            horaReservacion: horaReservacion
                        },
                        success: function(response) {
                            // Manejar la respuesta del servidor
                            if (response.success) {
                                // Recargar la página o actualizar la tabla
                                location.reload();
                            } else {
                                // Mostrar un mensaje de error
                                alert('Error al eliminar la reservación: ' + response.message);
                            }
                        },
                        error: function() {
                            alert('Error en la solicitud AJAX');
                        }
                    });
                }
            });
            $(document).on('click', '.modificar-reservacion', function() {
                console.log('click modificar reservacion');
                var button = $(this);
                var row = button.closest('tr');

                // Obtener los valores de los campos de entrada
                var primeraFecha = row.find('td:eq(0)').text();
                var segundaHora = row.find('td:eq(1)').text();

                // Cambiar el primer campo por un input:time con el valor recuperado
                var fechaParts = primeraFecha.split('/');
                var inputFecha = $('<input type="date" class="form-control">').val(fechaParts[2] + '-' + fechaParts[1] + '-' + fechaParts[0]);
                row.find('td:eq(0)').html(inputFecha);

                // Cambiar el segundo campo por un input:time con el valor recuperado
                var inputHora = $('<input type="time" class="form-control">').val(segundaHora);
                row.find('td:eq(1)').html(inputHora);

                // Cambiar el botón a "Continuar" y cambiar la clase
                button.text('Continuar');
                button.removeClass('modificar-reservacion btn-warning').addClass('continuar-reservacion btn-primary');
            });


            $(document).on('click', '.continuar-reservacion', function() {
                var button = $(this);
                var row = button.closest('tr');

                // Obtener los valores de los campos de entrada
                var nuevaFecha = row.find('td:eq(0) input').val();
                var nuevaHora = row.find('td:eq(1) input').val();

                // Formatear la nueva fecha a "dd/mm/aaaa"
                var fechaParts = nuevaFecha.split('-');
                nuevaFecha = fechaParts[2] + '/' + fechaParts[1] + '/' + fechaParts[0];

                // Reemplazar los inputs con los valores formateados
                row.find('td:eq(0)').text(nuevaFecha);
                row.find('td:eq(1)').text(nuevaHora);

                // Cambiar el botón a "Modificar" y cambiar la clase
                button.text('Modificar');
                button.removeClass('continuar-reservacion btn-primary').addClass('modificar-reservacion btn-warning');

                // Aquí se agrega un formulario oculto para enviar los datos al servidor
                var form = $("<form>")
                    .attr("method", "post")
                    .attr("action", "actualizar_reservacion.php") // Cambia esto al archivo que manejará la actualización
                    .css("display", "none")
                    .append($("<input>").attr("type", "hidden").attr("name", "nuevaFecha").val(nuevaFecha))
                    .append($("<input>").attr("type", "hidden").attr("name", "nuevaHora").val(nuevaHora));

                // Agregar el formulario al cuerpo del documento y enviarlo
                $("body").append(form);
                form.submit();
            });


        });
    </script>


</body>

</html>