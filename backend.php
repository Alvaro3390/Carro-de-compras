<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'TIENDA');

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Manejar acciones
$action = $_GET['action'] ?? null;


if ($action === 'getProductos') {
    // Obtener todos los productos
    $sql = "SELECT * FROM PRODUCTO";
    $result = $conn->query($sql);

    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }

    echo json_encode($productos);

} elseif ($action === 'getProducto') {
    // Obtener un producto por ID
    $id = $_GET['id'];
    $sql = "SELECT * FROM PRODUCTO WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($producto = $result->fetch_assoc()) {
        echo json_encode($producto);
    } else {
        echo json_encode(['error' => 'Producto no encontrado']);
    }

}elseif ($action === 'procesarPago') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_cliente = intval($data['cliente']); // Cliente seleccionado
    $productos = $data['productos'];
    $total = $data['total'];

    foreach ($productos as $producto) {
        $sql = "INSERT INTO COMPRA (cantidad, total, fecha, id_producto, id_cliente) VALUES (1, ?, NOW(), ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('dii', $producto['precio'], $producto['id_producto'], $id_cliente);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);
}

// obtener clientes (prueba)
elseif ($action === 'getClientes') {
    // Obtener todos los clientes
    $sql = "SELECT id_cliente, nombre FROM cliente";
    $result = $conn->query($sql);

    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }

    echo json_encode($clientes);
}elseif ($action === 'getCompras') {
    $id_cliente = intval($_GET['id_cliente']);
    $sql = "SELECT c.cantidad, c.total, p.nombre AS nombre_producto
            FROM COMPRA c
            JOIN PRODUCTO p ON c.id_producto = p.id_producto
            WHERE c.id_cliente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_cliente);
    $stmt->execute();
    $result = $stmt->get_result();

    $compras = [];
    while ($row = $result->fetch_assoc()) {
        $compras[] = $row;
    }

    echo json_encode($compras);
}


$conn->close();
?>

