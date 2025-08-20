<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$idCliente = $_SESSION['user_id'];
$sql = $con->prepare("SELECT id, direccion FROM clientes WHERE id = ? LIMIT 1");
$sql->execute([$idCliente]);
$direccion = $sql->fetch(PDO::FETCH_ASSOC);

$producto = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

// print_r($_SESSION);

$lista_carrito = array();

if($producto != null){
    foreach($producto as $clave => $cantidad){

        $sql = $con->prepare("SELECT id, nombre, precio, descuento, $cantidad AS cantidad FROM productos WHERE id=? AND activo=1");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
} else {
    header("Location: index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Escuelita</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
     <link href="css/estilos.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
<body>

<!-- Barra de Navegacion -->
<?php include 'menu.php'; ?>
<!-- Fin Barra -->




<!-- Contenedor -->
<main>
    <div class="container">

        <div class="row">
            <div class="col-12 col-md-4 mb-4">
                <h4>Detalles de pago</h4>
                <form action="clases/captura.php" method="POST" id="compraForm">
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección de Envío</label>
                        <input type="text" class="form-control" name="direccion" id="direccion"
                            value="<?php echo $direccion['direccion']; ?>" required autofocus />
                    </div>
                    <label for="metodoPago">Método de Pago</label>
                    <div class="mb-3">
                        <select class="form-select" name="metodoPago" id="metodoPago" required>
                            <option value="">Seleccionar</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta (Débito o Crédito)</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="col-12 col-md-8">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($lista_carrito == null) {
                                echo '<tr><td colspan="4" class="text-center"><b>Lista vacía</b></td></tr>';
                            } else {
                                $total = 0;
                                foreach($lista_carrito as $producto) {
                                    $_id = $producto['id'];
                                    $nombre = $producto['nombre'];
                                    $precio = $producto['precio'];
                                    $descuento = $producto['descuento'];
                                    $cantidad = $producto['cantidad'];
                                    $precio_desc = $precio - (($precio * $descuento) / 100);
                                    $subtotal = $cantidad * $precio_desc;
                                    $total += $subtotal;
                            ?>
                            <tr>
                                <td><?php echo $nombre; ?></td>
                                <td><?php echo $cantidad; ?></td>
                                <td>
                                    <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]"><?php echo 
                                    MONEDA . number_format($subtotal, 2, '.', ','); ?></div>
                                </td>
                            </tr>
                            <?php } ?>

                            <tr>
                                <td colspan="3" class="text-end">
                                    <p class="h4" id="total"><?php echo MONEDA . number_format($total, 2, '.', ','); ?></p>
                                </td>
                            </tr>
                        </tbody>
                        <?php } ?>
                    </table>
                </div>

                <button type="submit" form="compraForm" id="confirmar-compra" class="btn btn-primary w-100">Confirmar Compra</button>
            </div>

            <div class="col-12 mt-4">
                <span class="text-danger">* El costo de envío no se muestra en el total de la compra</span><br>
                <span class="text-danger">* El costo de envío puede variar desde $5 hasta $15 dependiendo de tu ubicación</span><br>
                <span class="text-danger">* Compras de más de 5 CAJAS: Envío GRATIS</span>
            </div>
        </div>

    </div>
</main>
<!-- Fin Contenedor -->




<!-- Boostrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    




</body>
</html>