<?php
require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';

$token_session = $_SESSION['token'];
$orden = $_GET['orden'] ?? null;
$token = $_GET['token'] ?? null;

if($orden == null || $token == null || $token != $token_session){
    header("Location: compras.php");
    exit;
}

$db = new Database();
$con = $db->conectar(); 

    $sqlCompra = $con->prepare("SELECT id, fecha, total FROM compra WHERE id = ? LIMIT 1");
    $sqlCompra->execute([$orden]);
    $rowCompra = $sqlCompra->fetch(PDO::FETCH_ASSOC);
    $idCompra =$rowCompra['id'];

    $fecha = new DateTime($rowCompra['fecha']);
    $fecha = $fecha->format('d/m/Y');

    $sqlDetalle = $con->prepare("SELECT id, nombre, precio, cantidad FROM detalle_compra WHERE id_compra=?");
    $sqlDetalle->execute([$idCompra]);

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

<?php include 'menu.php'; ?>

<main>
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Detalle de la Compra</strong>
                    </div>
                    <div class="card-body">
                        <p><strong>Fecha: </strong><?php echo $fecha; ?></p>
                        <p><strong>Total: </strong><?php echo MONEDA . number_format($rowCompra['total'], 2, '.', ','); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-8">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while($row = $sqlDetalle->fetch(PDO::FETCH_ASSOC)){
                                $nombre = $row['nombre'];
                                $precio = $row['precio'];
                                $cantidad = $row['cantidad'];
                                $subtotal = $precio * $cantidad;
                                ?>
                                <tr>
                                    <td><?php echo $nombre; ?></td>
                                    <td><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></td>
                                    <td><?php echo $cantidad; ?></td>
                                    <td><?php echo MONEDA . number_format($subtotal, 2, '.', ','); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>  

</body>
</html>