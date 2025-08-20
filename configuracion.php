<?php
require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar(); 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$idUser = $_SESSION['user_id'];

$sql = $con->prepare("SELECT id, nombres, apellidos, email, telefono, cp, direccion FROM clientes WHERE id = ?");
$sql->execute([$idUser]);
$usuario = $sql->fetch(PDO::FETCH_ASSOC);

$errors = [];

$sql = "SELECT id, codigoPostal FROM codigo_postal";
$resultado = $con->query($sql);
$cp = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Escuelita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php include 'menu.php'; ?>

<main>
    <div class="container">
        <h2>Datos del cliente</h2>

        <?php mostrarMensajes($errors); ?>

        <form class="row g-3" action="actualizaUsuario.php" method="post" autocomplete="off">
            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
            <div class="col-md-6">
                <label for="nombres"><span class="text-danger">*</span> Nombres</label>
                <input type="text" name="nombres" id="nombres" class="form-control" value="<?php echo $usuario['nombres']; ?>" required>
            </div>
            <div class="col-md-6">
                <label for="apellidos"><span class="text-danger">*</span> Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" class="form-control" value="<?php echo $usuario['apellidos']; ?>" required>
            </div>
            <div class="col-md-6">
                <label for="email"><span class="text-danger">*</span> Correo</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo $usuario['email']; ?>" required>
                <span id="validaEmail" class="text-danger"></span>
            </div>
            <div class="col-md-6">
                <label for="telefono"><span class="text-danger">*</span> Teléfono</label>
                <input type="tel" name="telefono" id="telefono" class="form-control" value="<?php echo $usuario['telefono']; ?>" required>
            </div>
            <div class="col-md-6">
                <label for="cp"><span class="text-danger">*</span> Código Postal</label>
                <div class="mb-3">
                    <select class="form-select" name="cp" id="cp" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($cp as $codigoPostal) { ?>
                        <option value="<?php echo $codigoPostal['codigoPostal']; ?>" <?php if ($usuario['cp'] == $codigoPostal['id']) echo 'selected'; ?>>
                            <?php echo $codigoPostal['codigoPostal']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <label for="direccion"><span class="text-danger">*</span> Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control" value="<?php echo $usuario['direccion']; ?>" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>  

</body>
</html>
