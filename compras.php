<?php
require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar(); 

$token = generarToken();
$_SESSION['token'] = $token;

if(!isset($_SESSION['user_cliente'])){
    header("Location: login.php");
    exit;
}

$idCliente = $_SESSION['user_cliente'];

    $sql = $con->prepare("SELECT id, fecha, status, total, dir_entrega FROM compra WHERE id_cliente = ? ORDER BY(fecha) DESC");
    $sql->execute([$idCliente]);
    

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
        <h4>Mis Compras</h4>
        <hr>
        <?php while($row = $sql->fetch(PDO::FETCH_ASSOC)){ ?>
        <div class="card">
            <div class="card-header">
                <?php echo $row['fecha']; ?> - <?php echo $row['status']; ?>
            </div>
            <div class="card-body">
                <h5 class="card-title">Total de la Compra: $<?php echo $row['total']; ?></h5>
                <p class="card-text">Direccion de Entrega: <?php echo $row['dir_entrega']; ?></p>
                <a href="compra_detalle.php?orden=<?php echo $row['id']; ?>&token=<?php echo $token; ?>" class="btn btn-primary">Detalles</a>
                <?php
                if($row['status'] == 'En Espera'){ ?>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#modalElimina" data-bs-id="<?php echo $row['id']; ?>">
                                Cancelar
                            </button>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</main>

<div class="modal fade" id="modalElimina" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Cancelar Pedido
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">¿Deseas Cancelar el Pedido?</div>
            <div class="modal-footer">
                <form action="elimina_compra.php" method="post">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let eliminaModal = document.getElementById('modalElimina')
eliminaModal.addEventListener('show.bs.modal', function(event) {
    let button = event.relatedTarget
    let id = button.getAttribute('data-bs-id')

    let modalInput = eliminaModal.querySelector('.modal-footer input')
    modalInput.value = id
})
</script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>  

</body>
</html>