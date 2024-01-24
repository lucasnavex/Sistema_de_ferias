<?php
include './dao/formulario.php';

$crud = new Crud($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Registros</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>


<body>
    <nav>
        REGISTROS
    </nav>

    <div class="container">
        <div class="header">
            <div class="left-menu">
                Olá, Administrador
            </div>
            <div class="right-menu">
                <button>Cadastro</button>
                <button><a href="editar_cadastrar.php" class="nav-link">Novo Registro</a></button>
            </div>
        </div>

        <div class="content">
            <?php
            // Seu código PHP para exibir a tabela
            $crud->listar();
            ?>
        </div>
    </div>
</body>

</html>