<?php
// Verificar la sesión del cliente
session_start();

if (!isset($_SESSION['id_empleado']) || !isset($_SESSION['nombre_empleado'])) {
    header("Location: cuenta.php"); // Redirigir a la página de inicio de sesión si no hay sesión activa
    exit();
}

// Aquí puedes incluir cualquier lógica específica para la página del cliente
// ...}
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

    <!-- Contenido de la página del cliente -->
    <section class="booking-section section-padding bg-light text-dark">
        <div class="container">
        <h2 class="mt-5 text-light p-3" style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(10px);">Bienvenido, <?php echo $_SESSION['nombre_empleado']; ?>!</h2>
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover table-striped bg-white" id="data-table">
                <thead class="thead-dark">
                    <tr>
                        <th>Id Producto</th>
                        <th>Nombre Producto</th>
                        <th>Existencia</th>
                        <th>Precio</th> <!-- Nueva columna para acciones -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtener las reservaciones del usuario
                    $userId = $_SESSION['id_empleado'];
                    $stmt = $sql->prepare("SELECT * FROM producto");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["IdProducto"] . "</td>";
                            echo "<td>" . $row['NombreProducto'] . "</td>";
                            echo "<td>" . $row['Existencia'] . "</td>";
                            echo "<td>" . $row['Precio'] . "</td>";
                            
                            echo "<td>";
                            echo "<button class='btn btn-danger btn-sm eliminar-producto' data-id-producto='" . $row['IdProducto'] . "' data-id-producto='" . $row['NombreProducto'] . "' data-existencia='" . $row['Existencia'] . "' data-precio='" . $row['Precio'] . "'>Eliminar</button>";
                            echo "</td>";
                        
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No se encontraron productos.</td></tr>";
                    }
                    $stmt->close();
                    ?>
                    </tbody>
                </table>
                <button id="agregar-producto" class="btn btn-primary">Agregar producto</button>            </div>
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
        $('#agregar-producto').click(function () {
            var tabla = $('#data-table'); // Reemplaza 'id-de-tu-tabla' con el ID de tu tabla
            var nuevaFila = '<tr><td></td><td><input type="text" name="nombreProducto" /></td><td><input type="number" name="existencia" /></td><td><input type="number" step="0.01" name="precio" /></td><td><button class="guardar-producto btn btn-primary">Guardar</button></td></tr>';

            tabla.append(nuevaFila);
        });
        $(document).on('click', '.guardar-producto', function () {
            var fila = $(this).closest('tr');
            var nombreProducto = fila.find('input[name="nombreProducto"]').val();
            var existencia = fila.find('input[name="existencia"]').val();
            var precio = fila.find('input[name="precio"]').val();

            // Enviar la solicitud AJAX para agregar el producto
            $.ajax({
                type: 'POST',
                url: 'agregar_producto_ajax.php',
                data: {
                    nombreProducto: nombreProducto,
                    existencia: existencia,
                    precio: precio
                },
                success: function (response) {
                    // Manejar la respuesta del servidor
                    if (response.success) {
                        // Recargar la página o actualizar la tabla
                        location.reload();
                    } else {
                        // Mostrar un mensaje de error
                        alert('Error al agregar el producto: ' + response.message);
                    }
                },
                error: function () {
                    alert('Error en la solicitud AJAX');
                }
            });
        });
        $(document).ready(function () {
            $('.eliminar-producto').click(function () {
                var idProducto = $(this).data('id-producto');

                // Confirm the deletion with the user (you can use a modal or other confirmation)
                if (confirm("¿Estás seguro de que deseas eliminar este producto?")) {
                    // Send the AJAX request to delete the product
                    $.ajax({
                        type: 'POST',
                        url: 'eliminar_producto_ajax.php',
                        data: {
                            idProducto: idProducto
                        },
                        success: function (response) {
                            // Handle the server response
                            if (response.success) {
                                // Reload the page or update the table
                                location.reload();
                            } else {
                                // Show an error message
                                alert('Error al eliminar el producto: ' + response.message);
                            }
                        },
                        error: function () {
                            alert('Error en la solicitud AJAX');
                        }
                    });
                }
            });
        });
    </script>

</body>

</html>
