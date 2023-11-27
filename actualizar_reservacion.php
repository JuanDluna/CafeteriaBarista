<?php
// actualizar_reservacion.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = new mysqli("localhost", "root", "230403", "baristacafe");

    // Check connection
    if ($sql->connect_error) {
        die("Connection failed: " . $sql->connect_error);
    }

    $nuevaFecha = $_POST["nuevaFecha"];
    $nuevaHora = $_POST["nuevaHora"];

    // Preparar la consulta
    $stmt = $sql->prepare("UPDATE reservacion SET FechaReservacion = ?, HoraReservacion = ? WHERE NumeroMesa = ? AND IdCliente = ?");
    $stmt->bind_param("ssii", $nuevaFecha, $nuevaHora, $numeroMesa, $idCliente);

    // Ejecutar la consulta
    $stmt->execute();

    // Cerrar la conexión y liberar recursos
    $stmt->close();
    $sql->close();

    // Redirigir a la página principal o mostrar un mensaje según sea necesario
    header("Location: panel-cliente.php");
    exit();
}
