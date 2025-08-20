<?php
require 'config/config.php';
require 'config/database.php';

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
    <div class="container text-center" style="margin-top: 50px;">
        <h1 class="display-4">Si ha olvidado su contrseña, le sugerimos contactarnos via telefonica para proporcionarle una nueva ó bien puede acudir a nuestras instalaciones</h1>
        <p class="lead">Recuerde tener a la mano su nombre de usuario ó correo electronico</p>
        <a href="index.php" class="btn btn-primary btn-lg mt-4">Volver a la Página Principal</a>
    </div>
</main>

</body>
</html>