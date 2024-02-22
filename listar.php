<?php
require_once('dao/formulario.php');
require_once("dao/UserDAO.php");
require_once("globals.php");
require_once("db.php");


$userDAO = new UserDAO($conn, $BASE_URL);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald&display=swap" rel="stylesheet">

</head>

<body>
    <nav>
        <img src="./img/inss-logo.0e1a042d.png" alt="Logo" class="logo">
        Registros
    </nav>

    </div>
    <?php if (!empty($flassMessage["msg"])) : ?>
        <div class="msg-container">
            <p class="msg <?= $flassMessage["type"] ?>"><?= $flassMessage["msg"] ?></p>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="header">
            <div class="left-menu">
                <?php
                if (isset($_SESSION['token'])) {
                    // Pega o usuário autenticado
                    $user = $userDAO->findByToken($_SESSION['token']);

                    if ($user) {
                        // Exibe o nome do usuário
                        echo "Olá, " . $_SESSION['usuario_nome'];
                    } else {
                        echo "Olá, Usuário";
                    }
                } else {
                    echo "Olá, Convidado";
                }
                ?>
            </div>
            <div class="right-menu">                
                <button><a href=" cadastro.php" class="nav-link">Cadastro</a></button>
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