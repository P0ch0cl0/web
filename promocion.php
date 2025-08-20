<?php
require 'config/config.php';
require_once 'config/database.php';

$db = new Database();
$con = $db->conectar();

$idCategoria = $_GET['cat'] ?? '';
$orden = $_GET['orden'] ?? '';

$orders = [
    'asc' => 'nombre ASC',
    'desc' => 'nombre DESC',
    'precio_alto' => 'precio DESC',
    'precio_bajo' => 'precio ASC',
];

$order = $orders[$orden] ?? '';

if(!empty($order)){
    $order = " ORDER BY $order";
}

if(!empty($idCategoria)){
    $sql = $con->prepare("SELECT id, nombre, precio, descuento FROM productos WHERE activo=1 AND descuento > 0 AND id_categoria = ? $order");
    $sql->execute([$idCategoria]);
} else {
    $sql = $con->prepare("SELECT id, nombre, precio, descuento FROM productos WHERE activo=1 AND descuento > 0 $order");
    $sql->execute();
}
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

$sqlCategorias = $con->prepare("SELECT id, nombre FROM categorias WHERE activo = 1");
$sqlCategorias->execute();
$categorias = $sqlCategorias->fetchAll(PDO::FETCH_ASSOC);

// Obtener el nombre de la categoría seleccionada
$categoriaSeleccionada = '';
if ($idCategoria) {
    foreach ($categorias as $categoria) {
        if ($categoria['id'] == $idCategoria) {
            $categoriaSeleccionada = $categoria['nombre'];
            break;
        }
    }
}

// Incluir el menu.php pasando las categorías y la categoría seleccionada
include 'menu.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Escuelita</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
</head>

<body>

    <!-- <?php include 'menu.php'; ?> -->

    <main>
        <div class="container">
            <div class="row">

                <div class="col-12 col-md-12">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 justify-content-end g-4">
                        <div class="col mb-2">
                            <form action="promocion.php" id="ordenForm" method="get">
                                <input type="hidden" name="cat" id="cat" value="<?php echo $idCategoria; ?>">

                            <select name="orden" id="orden" class="form-select form-select-sm" onchange="submitForm()"> 
                            <option value="">Ordenar por...</option>
                                <option value="precio_alto" <?php echo ($orden === 'precio_alto') ? 'selected' : ''; ?>>Precio mas alto</option>
                                <option value="precio_bajo" <?php echo ($orden === 'precio_bajo') ? 'selected' : ''; ?>>Precio mas bajo</option>
                                <option value="asc" <?php echo ($orden === 'asc') ? 'selected' : ''; ?>>Nombre A-Z</option>
                                <option value="desc" <?php echo ($orden === 'desc') ? 'selected' : ''; ?>>Nombre Z-A</option>
                            </select>
                            </form>
                        </div>
                    </div>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                        <?php foreach($resultado as $row) { ?>
                        <div class="col">
                            <div class="card shadow-sm">
                                <?php 
                                    $id = $row ['id'];
                                    $precio_desc = $row['precio'] - (($row['precio'] * $row['descuento']) / 100);
                                    $imagen = "images/productos/" . $id . "/principal.jpg";
                                    $precio = $row['precio'];
                                    if(!file_exists($imagen)) {
                                    $imagen = "images/no-image.jpg";
                                    }
                                ?>
                                <img src=<?php echo $imagen; ?> class="d-block w-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $row['nombre']; ?></h5>

                                    <?php if($row['descuento'] > 0) {?>
                                    <p><del><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></del>
                                        <?php echo MONEDA . number_format($precio_desc, 2, '.', ','); ?></p>
                                    <?php }else {?>
                                    <p class="card-text">
                                        <?php echo MONEDA . number_format($row['precio'], 2, '.', ','); ?></p>
                                    <?php } ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="btn-group">
                                            <a href="details.php?id=<?php echo $row['id']; ?>&token=<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN);?>"
                                                class="btn btn-primary">Detalles</a>
                                        </div>
                                        <a class="btn btn-success"
                                            onClick="addProducto(<?php echo $row['id']; ?>, '<?php echo hash_hmac('sha1', $row['id'], KEY_TOKEN);?>') ">Agregar
                                            al carrito</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>


    <script>
    function addProducto(id, token) {
        let url = 'clases/carrito.php';
        let formData = new FormData();
        formData.append('id', id);
        formData.append('token', token);

        fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    let elemento = document.getElementById("num_cart")
                    elemento.innerHTML = data.numero
                } else {
                    alert("No hay suficientes productos")
                }
            })
    }

    function submitForm(){
        document.getElementById('ordenForm').submit();
    }
    </script>

</body>

</html>