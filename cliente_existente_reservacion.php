<?php
// Obtener los datos enviados por POST
$id_cliente = $_POST['id_cliente'];
$correo = $_POST['correo'];
$horaReservacion = $_POST['horaReservacion'];
$num_personas = $_POST['num_personas'];


$conn = new mysqli("localhost", "root", "230403", "baristacafe");

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consultar las mesas disponibles
$sql = "SELECT * FROM mesas WHERE sillas >= $num_personas AND disponible = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Si hay mesas disponibles, asignar una reservación
    $row = $result->fetch_assoc();
    $mesa_id = $row['id'];
    $fecha_reservacion = date('Y-m-d H:i:s', strtotime($horaReservacion));

    // Insertar la reservación en la base de datos
    $sql = "INSERT INTO reservaciones (id_cliente, mesa_id, fecha_reservacion) VALUES ($id_cliente, $mesa_id, '$fecha_reservacion')";
    if ($conn->query($sql) === TRUE) {
        echo "Reservación realizada con éxito";
    } else {
        echo "Error al realizar la reservación: " . $conn->error;
    }
} else {
    echo "No hay mesas disponibles para el número de personas especificado";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
