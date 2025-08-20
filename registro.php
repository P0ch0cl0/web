<?php
require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar(); 

$errors = [];

$sql = "SELECT id, codigoPostal FROM codigo_postal";
$resultado = $con->query($sql);
$cp = $resultado->fetchAll(PDO::FETCH_ASSOC);

if(!empty($_POST)){

    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $cp = trim($_POST['cp']);
    $direccion = trim($_POST['direccion']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if(esNulo([$nombres, $apellidos, $email, $telefono, $cp, $direccion, $usuario, $password, $repassword])){
        $errors[] = "Debe llenar todos los campos";
    }

    if(!esEmail($email)){
        $errors[] = "La direccion de correo no es valida";
    }

    if(!validaPassword($password, $repassword)){
        $errors[] = "Las contraseñas no coinciden";
    }

    if(usuarioExiste($usuario, $con)){
        $errors[] = "El nombre de usuario $usuario ya existe";
    }

    if(emailExiste($email, $con)){
        $errors[] = "El correo electronico $email ya existe";
    }

    if(count($errors) == 0){

    $id = registraCliente([$nombres, $apellidos, $email, $telefono, $cp, $direccion], $con);
    if($id > 0){
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $token = generarToken();
        if(!registraUsuario([$usuario, $pass_hash, $token, $id], $con)) {
            $errors[] = "Error al registrar usuario";
        }
    } else {
        $errors[] = "Error al registrar cliente";
    }
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
        <h2>Datos del cliente</h2>

        <?php mostrarMensajes($errors); ?>

        <form class="row g-3" action="registro.php" method="post" autocomplete="off">
            <div class="col-md-6">
                <label for="nombres"><span class="text-danger">*</span> Nombres</label>
                <input type="text" name="nombres" id="nombres" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="apellidos"><span class="text-danger">*</span> Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="email"><span class="text-danger">*</span> Correo</label>
                <input type="email" name="email" id="email" class="form-control" required>
                <span id="validaEmail" class="text-danger"></span>
            </div>
            <div class="col-md-6">
                <label for="telefono"><span class="text-danger">*</span> Telefono</label>
                <input type="tel" name="telefono" id="telefono" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="cp"><span class="text-danger">*</span> Codigo Postal</label>
                <div class="mb-3">
                        <select class="form-select" name="cp" id="cp" required>
                            <option values="">Seleccionar</option>
                            <?php foreach($cp as $coigoPostal){ ?>
                            <option value="<?php echo $coigoPostal['codigoPostal']; ?>">
                                <?php echo $coigoPostal['codigoPostal']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
            </div>
            <div class="col-md-6">
                <label for="direccion"><span class="text-danger">*</span> Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="usuario"><span class="text-danger">*</span> Usuario</label>
                <input type="text" name="usuario" id="usuario" class="form-control" required>
                <span id="validaUsuario" class="text-danger"></span>
            </div>
            <div class="col-md-6">
                <label for="password"><span class="text-danger">*</span> Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="repassword"><span class="text-danger">*</span> Repeteir Contraseña</label>
                <input type="password" name="repassword" id="repassword" class="form-control" required>
            </div>

            <i><b>Nota:</b> Una vez enviado al formulario vuelva a iniciar sesión</i>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
        </form>
    </div>
</main>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>  

    <script>
        let txtUsuario = document.getElementById('usuario')
        txtUsuario.addEventListener("blur", function(){
            existeUsuario(txtUsuario.value)
        }, false)

        let txtEmail = document.getElementById('email')
        txtEmail.addEventListener("blur", function(){
            existeEmail(txtEmail.value)
        }, false)

        function existeUsuario(usuario){
            let url = "clases/clienteAjax.php"
            let formData = new FormData()
            formData.append("action", "existeUsuario")
            formData.append("usuario", usuario)

            fetch(url, {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(data => {
                if(data.ok){
                    document.getElementById('usuario').value = '' 
                    document.getElementById('validaUsuario').innerHTML = 'Usuario no disponible'
                } else {
                    document.getElementById('validaUsuario').innerHTML = ''
                }
            })
        }

        function existeEmail(email){
            let url = "clases/clienteAjax.php"
            let formData = new FormData()
            formData.append("action", "existeEmail")
            formData.append("email", email)

            fetch(url, {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(data => {
                if(data.ok){
                    document.getElementById('email').value = '' 
                    document.getElementById('validaEmail').innerHTML = 'Email no disponible'
                } else {
                    document.getElementById('validaEmail').innerHTML = ''
                }
            })
        }
    </script>
</body>
</html>