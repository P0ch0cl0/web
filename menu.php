<header><br><br>
    <div class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader"
                aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a href="index.php" class="navbar-brand d-flex align-items-center">
                <strong>Mi Escuelita</strong>
            </a>
            <a href="checkout.php" class="btn btn-secondary btn-sm me-2">
                        <i class="fas fa-shopping-cart"></i> Carrito <span id="num_cart"
                            class="badge bg-danger"><?php echo isset($num_cart) ? htmlspecialchars($num_cart, ENT_QUOTES, 'UTF-8') : 0; ?></span>
                    </a>
            <div class="collapse navbar-collapse" id="navbarHeader">
                <form action="index.php" method="get" class="d-flex ms-1 my-2 my-lg-0">
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" id="q" class="form-control form-control-sm" placeholder="Buscar..."
                            aria-describedby="icon-buscar">
                        <button type="submit" id="icon-buscar" class="btn btn-info btn-sm">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link active">Catálogo</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="index.php" id="categoriasDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo isset($categoriaSeleccionada) && $categoriaSeleccionada ? $categoriaSeleccionada : 'Categorías'; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="categoriasDropdown">
                            <li><a class="dropdown-item" href="index.php">Todo</a></li>
                            <?php if (isset($categorias)) { ?>
                            <?php foreach($categorias as $categoria) { ?>
                            <li><a class="dropdown-item"
                                    href="index.php?cat=<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8'); ?></a>
                            </li>
                            <?php } ?>
                            <?php } ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="promocion.php" class="nav-link">Descuentos</a>
                    </li>

                    <li class="nav-item">
                        <a href="informacion.php" class="nav-link">Sobre Nosotros</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center ms-2">
                    <?php if(isset($_SESSION['user_id'])) { ?>
                    <div class="dropdown">
                        <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="btn_session"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i>
                            <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : 'Usuario'; ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="btn_session">
                            <li><a class="dropdown-item" href="compras.php"><i class="fa-solid fa-bag-shopping"></i>
                                    Mis Compras</a></li>
                            <li><a class="dropdown-item" href="configuracion.php"><i class="fa-solid fa-gear"></i>
                                    Configuración</a></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fa-solid fa-user"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                    <?php } else { ?>
                    <a href="login.php" class="btn btn-success btn-sm ms-2">
                        <i class="fas fa-user"></i> Iniciar Sesión
                    </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</header>
