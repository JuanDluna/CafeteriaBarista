<?php
// actualizar_reservacion.php



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    session_start();

    $sql = new mysqli("localhost", "root", "230403", "baristacafe");

    // Check connection
    if ($sql->connect_error) {
        die("Connection failed: " . $sql->connect_error);
    }

    $idCliente = $_SESSION["id_cliente"];
    $numeroMesa = $_POST["numeroMesa"];
    $nuevaHora = $_POST["nuevaHora"];
    $nuevaFecha = $_POST["nuevaFecha"];
    $nuevaFecha = date("Y-m-d", strtotime($nuevaFecha));

    $horaReservacion = $nuevaFecha . " " . $nuevaHora;


    // Preparar la consulta
    $stmt = $sql->prepare("UPDATE reservacion SET FechaReservacion = ?, HoraReservacion = ? WHERE NumeroMesa = ? AND IdCliente = ?");
    $stmt->bind_param("ssii", $nuevaFecha, $horaReservacion, $numeroMesa, $idCliente);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Éxito
        $response['success'] = true;
        $response['message'] = 'Reservación modificada exitosamente';
    } else {
        // Error
        $response['success'] = false;
        $response['message'] = 'Error al modificar la reservación: ' . $stmt->error;
    }

    // Cerrar la conexión y liberar recursos
    $stmt->close();
    $sql->close();


    // Redirigir a la página principal o mostrar un mensaje según sea necesario
    header("Location: panel-cliente.php");
    exit();
}
