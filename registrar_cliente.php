<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $direccion = trim($_POST['direccion']);

    if (strlen($nombre) < 3) {
        die("El nombre debe tener al menos 3 caracteres.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("El email no es válido.");
    }

    // Conexión a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'tienda');
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO cliente (nombre, email, direccion) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $email, $direccion);

    if ($stmt->execute()) {
        echo "Cliente registrado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
