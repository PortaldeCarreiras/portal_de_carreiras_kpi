<script src="./js/jquery-3.7.1.slim.min.js"></script>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="index.php">Sistema Indicadores</a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control" type="date" placeholder="" name="txtbuscar" aria-label="Search for..." aria-describedby="btnNavbarSearch" />
            <button class="btn btn-primary" name="btnbuscar" id="btnNavbarSearch" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i>
                <?php echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuário'; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item"><?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'Usuário'; ?></a>
                </li>
                <li>
                    <a class="dropdown-item" href="#!">Configurações</a>
                </li>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <?php if (isset($_SESSION['tipo_adm']) && $_SESSION['tipo_adm'] == 1): ?>
                    <li><a type="hidden" class="dropdown-item" href="adm.php">Adm</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                <?php endif; ?>
                <?php // Verifica se o usuário é tipo_adm e se a página atual é adm.php
                if (isset($_SESSION['tipo_adm']) && $_SESSION['tipo_adm'] == 1):    // Verifica se o usuário é tipo_adm
                    $isAdmPage = basename($_SERVER['PHP_SELF']) === 'adm.php';  // Verifica se a página atual é adm.php
                    // Exibe o item do menu apenas se ambas as condições forem verdadeiras
                    if ($isAdmPage): ?>
                        <li><a class="dropdown-item" href="#?">Adm - acesso</a></li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li><a class="dropdown-item" href="index.php">Home</a></li>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload - Selecione Arquivo</a>
                                <a class="nav-link" href="index.php?tarefasf=1">Filtrar Informações</a>
                                <a class="nav-link" href="relatorio.php">Gerar Relatório</a>
                            </nav>
                        </div>
                <?php endif;
                endif; ?>
                <li><a class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#modalSair">Sair</a></li>
            </ul>
        </li>
    </ul>
</nav>

<!-- Janela Popup para confirmação da ação de Sair -->
<div class="modal fade" id="modalSair" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-draggable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Finalizar Tarefa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Deseja sair do sistema?
            </div>
            <div class="modal-footer">
                <!-- Chama a função Resetar() no evento de Click  - onClick -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="./login/sair.php">
                    <button type="button" class="btn btn-primary">Sim</button>
                </a>
            </div>
        </div>
    </div>
</div>