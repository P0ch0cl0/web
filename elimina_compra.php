<?php
require 'config/config.php';
require 'config/database.php';

$db = new DataBase();
$con = $db->conectar();


$id =  $_POST['id'];
$sql = $con->prepare("UPDATE compra SET status = 'Cancelado' WHERE id = ?");
$sql->execute([$id]);

$sqlCompra = $con->prepare("SELECT id FROM compra WHERE id = ? LIMIT 1");
$sqlCompra->execute([$id]);
$rowCompra = $sqlCompra->fetch(PDO::FETCH_ASSOC);
$idCompra =$rowCompra['id'];

$sqlDetalle = $con->prepare("SELECT id, cantidad, id_producto FROM detalle_compra WHERE id_compra=?");
$sqlDetalle->execute([$idCompra]);


while($row = $sqlDetalle->fetch(PDO::FETCH_ASSOC)){
    $id = $row['id'];
    $cantidad = $row['cantidad'];
    $idProducto = $row['id_producto'];

    $sqlProducto = $con->prepare("SELECT id, stock FROM productos WHERE id = ?");
    $sqlProducto->execute([$idProducto]);
    $rowProducto = $sqlProducto->fetch(PDO::FETCH_ASSOC);
    $stock = $rowProducto['stock'];

    $sql = $con->prepare("UPDATE productos SET stock = $cantidad + $stock WHERE id = ?");
    $sql->execute([$idProducto]);
}


header('Location: compras.php');
?>

