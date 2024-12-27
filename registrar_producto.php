<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    if (strlen($nombre) < 3) {
        die("El nombre debe tener al menos 3 caracteres.");
    }
    if ($precio <= 0) {
        die("El precio debe ser mayor a 0.");
    }
    if ($stock < 0) {
        die("El stock no puede ser negativo.");
    }

    // Conexión a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'tienda');
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO producto (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $stock);

    if ($stmt->execute()) {
        echo "Producto registrado exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
