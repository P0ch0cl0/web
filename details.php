<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($id == '' || $token == '') {
  echo 'Error al procesar la peticion';
  exit;
} else {
  $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

  if ($token == $token_tmp){

    $sql = $con->prepare("SELECT count(id) FROM productos WHERE id=? AND activo=1");
    $sql->execute([$id]);
    if($sql->fetchColumn() > 0) {

    $sql = $con->prepare("SELECT nombre, descripcion, precio, descuento FROM productos WHERE id=? AND activo=1 LIMIT 1");
    $sql->execute([$id]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $nombre = $row['nombre'];
    $descripcion = $row['descripcion'];
    $precio = $row['precio'];
    $descuento = $row['descuento'];
    $precio_desc = $precio - (($precio * $descuento) / 100);
    $dir_images = 'images/productos/' . $id . '/';

    $rutaImg = $dir_images . 'principal.jpg';

    if(!file_exists($rutaImg)) {
      $imagen = "images/no-photo.jpg";
    }

    $imagenes = array();
    if(file_exists($dir_images)){
    $dir = dir($dir_images);

    while(($archivo = $dir->read()) !=false){
        if($archivo != 'principal.jpg' && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg'))){
          $imagenes[] = $dir_images . $archivo;
      }
    }
    $dir->close();
  }
    }
  } else {
    echo 'Error al procesar la peticion';
  exit;
  }
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

<?php include 'menu.php'; ?>

<main>
    <div class="container">
      <div class="row">
        <div class="col-md-6 order-md-1">
        
        <div id="carouselImages" class="carousel slide">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src= <?php echo $rutaImg; ?> class="d-block w-100">
    </div>

    <?php foreach($imagenes as $img) { ?>
    <div class="carousel-item">
    <img src= <?php echo $img; ?> class="d-block w-100">
    </div>
    <?php } ?>

  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

        </div>
        <div class="col-md-6 order-md-2">
        <h2><?php echo $nombre; ?></h2>

        <?php if($descuento > 0) {?>
          <p><del><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></del></p>
          <h2><?php echo MONEDA . number_format($precio_desc, 2, '.', ','); ?>
        <small class="text-success"><?php echo $descuento; ?>% de Descuento!</small>
          </h2>
          <?php }else {?>

          <h2><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></h2>
          <?php } ?>
          <p class="lead">
            <?php echo $descripcion; ?>
          </p>

          <div class="col-4 my-3 ms-4">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" id="button-decrement">-</button>
                            <input class="form-control text-center" id="cantidad" name="cantidad" type="number" min="1"
                                max="100" value="1">
                            <button class="btn btn-outline-secondary" type="button" id="button-increment">+</button>
                        </div>
                    </div>

          <div class="d-grid gap-3 col-10 mx-auto">
            <button class="btn btn-primary" type="button" onclick="shopProducto(<?php echo $id; ?>, cantidad.value, '<?php echo $token_tmp; ?>') ">Comprar ahora</button>
            <button class="btn btn-outline-primary" type="button" onclick="addProducto(<?php echo $id; ?>, cantidad.value, '<?php echo $token_tmp; ?>') ">Agregar al carrito</button>
          </div>
        </div>
      </div>
        
    </div>
</main>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    
    <script>
      function addProducto(id, cantidad, token) {
        let url='clases/carrito.php'
        let formData = new FormData()
        formData.append('id', id)
        formData.append('cantidad', cantidad)
        formData.append('token', token)

        fetch(url, {
          method: 'POST',
          body : formData,
          mode: 'cors'
        }).then(response => response.json())
        .then(data => {
          if(data.ok){
            let elemento = document.getElementById("num_cart")
            elemento.innerHTML = data.numero
          }else{
            alert("No hay suficientes productos")
          }
        })
      }

      function shopProducto(id, cantidad, token) {
    let url = 'clases/carrito.php';
    let formData = new FormData();
    formData.append('id', id);
    formData.append('cantidad', cantidad);
    formData.append('token', token);

    fetch(url, {
        method: 'POST',
        body: formData,
        mode: 'cors'
    }).then(response => response.json())
    .then(data => {
        if (data.ok) {
            // Redirecciona a checkout.php después de agregar el producto
            window.location.href = 'checkout.php';
        } else {
            alert("No hay suficientes productos");
        }
    })
    .catch(error => {
        console.error("Error:", error);
    });
}
    </script>
    <script>
    const decrementButton = document.getElementById('button-decrement');
    const incrementButton = document.getElementById('button-increment');
    const quantityInput = document.getElementById('cantidad');

    decrementButton.addEventListener('click', () => {
        if (quantityInput.value > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    });

    incrementButton.addEventListener('click', () => {
        if (quantityInput.value < 100) {
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }
    });
</script>

</body>
</html>