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

$errors = [];

$idUser = $_POST['id'];
$nombres = $_POST['nombres'];
$apellidos = $_POST['apellidos'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$cp = $_POST['cp'];
$direccion = $_POST['direccion'];

if (esNulo([$nombres, $apellidos, $email, $telefono, $cp, $direccion])) {
    $errors[] = "Debe llenar todos los campos";
}

if (!esEmail($email)) {
    $errors[] = "El correo no es válido";
}

if (count($errors) == 0) {
    $sql = $con->prepare("UPDATE clientes SET nombres = ?, apellidos = ?, email = ?, telefono = ?, cp = ?, direccion = ? WHERE id = ?");
    $sql->execute([$nombres, $apellidos, $email, $telefono, $cp, $direccion, $idUser]);

    header("Location: configuracion.php");
} else {
    // Redirigir de nuevo al formulario con errores
    $_SESSION['errors'] = $errors;
    header("Location: configuracion.php");
}
?>
