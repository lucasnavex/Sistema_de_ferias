<?php
require_once('./dao/formulario.php');
require_once("templates/header.php");
$crud = new Crud($conn);
?>


<nav>
    REGISTROS
</nav>

<div class="container">
    <div class="header">
        <div class="left-menu">
            Olá, Administrador
        </div>
        <div class="right-menu">
            <button><a href="cadastro.php" class="nav-link">Cadastro</a></button>
            <button><a href="cadastrar.php" class="nav-link">Novo Registro</a></button>
        </div>
    </div>

    <div class="content">
        <?php
        // Seu código PHP para exibir a tabela
        $crud->listar();
        ?>
    </div>
</div>


