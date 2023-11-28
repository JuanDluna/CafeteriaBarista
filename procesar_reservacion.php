<?php

$conn = new mysqli("localhost", "root", "230403", "baristacafe");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $response = ["success" => false];

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
                $response["message"] = $row['Mensaje'];

                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                $response["error"] = "Error: " . $e->getMessage();
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
            $response["message"] = "Cliente no encontrado.";
        } else {
            $response["success"] = true;
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
                $response["message"] = $row['Mensaje'];

                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                $response["error"] = "Error: " . $e->getMessage();
            }
        }
    }
    echo json_encode($response);
}
?>