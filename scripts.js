// scripts.js

// Variables de referencia
const listaProductos = document.getElementById('listaProductos');
const listaCarrito = document.getElementById('listaCarrito');
const finalizarCompra = document.getElementById('finalizarCompra');
const modalPago = document.getElementById('modalPago');
const formPago = document.getElementById('formPago');
const cancelarPago = document.getElementById('cancelarPago');

// Almacén del carrito
const carrito = [];

// Calcular el total del carrito
function calcularTotal() {
    return carrito.reduce((acc, producto) => acc + parseFloat(producto.precio), 0);
}

// Renderizar productos (traídos desde el backend)
async function cargarProductos() {
    try {
        const response = await fetch('backend.php?action=getProductos');
        const productos = await response.json();
        renderProductos(productos);
    } catch (error) {
        console.error('Error cargando productos:', error);
    }
}

// Cargar clientes en el selector de pago
async function cargarClientes() {
    try {
        const response = await fetch('backend.php?action=getClientes');
        const clientes = await response.json();
        const selectorClientes = document.getElementById('clientePago');

        clientes.forEach(cliente => {
            const option = document.createElement('option');
            option.value = cliente.id_cliente;
            option.textContent = cliente.nombre;
            selectorClientes.appendChild(option);
        });
    } catch (error) {
        console.error('Error cargando clientes:', error);
    }
}

// Llamar a la carga de clientes
cargarClientes();

// Renderizar lista de productos
function renderProductos(productos) {
    listaProductos.innerHTML = '';
    productos.forEach(producto => {
        const div = document.createElement('div');
        div.classList.add('producto');
        div.innerHTML = `
            <h3>${producto.nombre}</h3>
            <p>${producto.descripcion}</p>
            <p>Precio: $${producto.precio}</p>
            <button onclick="agregarAlCarrito(${producto.id_producto})">Agregar al Carrito</button>
        `;
        listaProductos.appendChild(div);
    });
}

// Agregar producto al carrito
function agregarAlCarrito(idProducto) {
    fetch(`backend.php?action=getProducto&id=${idProducto}`)
        .then(response => response.json())
        .then(producto => {
            carrito.push(producto);
            renderCarrito();
        })
        .catch(error => console.error('Error agregando al carrito:', error));
}

// Renderizar carrito
function renderCarrito() {
    listaCarrito.innerHTML = '';
    if (carrito.length === 0) {
        listaCarrito.innerHTML = '<p>Tu carrito está vacío.</p>';
        return;
    }
    carrito.forEach((producto, index) => {
        const div = document.createElement('div');
        div.classList.add('item-carrito');
        div.innerHTML = `
            <p>${producto.nombre} - $${producto.precio}</p>
            <button onclick="eliminarDelCarrito(${index})">Eliminar</button>
        `;
        listaCarrito.appendChild(div);
    });
}

// Eliminar producto del carrito
function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    renderCarrito();
}

// Finalizar compra
finalizarCompra.addEventListener('click', () => {
    if (carrito.length === 0) {
        alert('El carrito está vacío.');
        return;
    }
    modalPago.classList.remove('oculto');
});

// Cancelar pago
cancelarPago.addEventListener('click', () => {
    modalPago.classList.add('oculto');
});

// Procesar pago
formPago.addEventListener('submit', (e) => {
    e.preventDefault();
    const clienteSeleccionado = document.getElementById('clientePago').value;

    const total = calcularTotal(); // Calcula el total del carrito aquí

    const datosPago = {
        cliente: clienteSeleccionado, // Asociar cliente
        nombre: document.getElementById('nombrePago').value,
        tarjeta: document.getElementById('tarjetaPago').value,
        fecha: document.getElementById('fechaPago').value,
        cvv: document.getElementById('cvvPago').value,
        total: total, // Total calculado
        productos: carrito
    };

    fetch('backend.php?action=procesarPago', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosPago)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pago realizado con éxito. ¡Gracias por tu compra!');
                carrito.length = 0; // Vaciar el carrito
                renderCarrito(); // Volver a renderizar
                modalPago.classList.add('oculto'); // Cerrar modal
            } else {
                alert('Error procesando el pago. Intenta de nuevo.');
            }
        })
        .catch(error => console.error('Error procesando el pago:', error));
});

// Cargar productos al iniciar
cargarProductos();
