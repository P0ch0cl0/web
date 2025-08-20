<?php
require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar(); 

$proceso = isset($_GET['pago']) ? 'pago' : 'login';

$errors = [];

if(!empty($_POST)){

    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $proceso = $_POST['proceso'] ?? 'login';

    if(esNulo([$usuario, $password])){
        $errors[] = "Debe llenar todos los campos";
    }

    if(count($errors) == 0){
        $errors[] = login($usuario, $password, $con, $proceso);
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

<main class="form-login m-auto pt-4">
    <h2>Iniciar Sesión</h2>
    <?php mostrarMensajes($errors); ?>

    <form action="login.php" method="post" autocomplete="off">

        <input type="hidden" name="proceso" value="<?php echo $proceso; ?>">


        <div class="form-floating">
            <input class="form-control" type="text" name="usuario" id="usuario" placeholder="Usuario" >
            <label for="usuario">Usuario</label><br>
        </div>

        <div class="form-floating">
            <input class="form-control" type="password" name="password" id="password" placeholder="Contraseña" >
            <label for="password">Contraseña</label>
        </div>
        <div class="col-12">
            <a href="recupera.php">¿Olvidaste tu contraseña?</a>
        </div><br>

        <div class="d-grid gap-3 col-12">
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </div>
        <hr>
        <div class="col-12">
            ¿No tiene una cuenta? <a href="registro.php">Registrate aqui</a>
        </div>
    </form>
</main>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>  

    
</body>
</html>