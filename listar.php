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
            <table class='registro-table'>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Matrícula</th>
                        <th>Unidade de lotação</th>
                        <th>Categoria Funcional</th>
                        <th>Central</th>
                        <th>Gestor</th>
                        <th>Motivo da Informação</th>
                        <th>Períodos de férias</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obter os registros da função listar do Crud
                    $registros = $crud->listar();

                    // Verificar se há registros
                    if ($registros) {
                        // Iterar sobre os registros
                        foreach ($registros as $registro) {
                            echo "<tr>";
                            echo "<td>{$registro['nome']}</td>";
                            echo "<td>{$registro['matricula_servidor']}</td>";
                            echo "<td>{$registro['unidade_lotacao']}</td>";
                            echo "<td>{$registro['categoria_funcional']}</td>";
                            echo "<td>{$registro['central']}</td>";
                            echo "<td>" . ($registro['gestor'] == 1 ? 'Sim' : 'Não') . "</td>";
                            echo "<td>{$registro['motivo_informacao']}</td>";
                            echo "<td>{$registro['qtd_periodos_ferias']}</td>";
                            echo "<td>
                            <a href='visualizar.php?id={$registro['id']}'><i class='fas fa-eye'></i> </a>
                            <a href='cadastrar.php?id={$registro['id']}'><i class='fas fa-edit'></i> </a>
                            <a href='./deletar.php?id={$registro['id']}'><i class='fas fa-trash-alt'></i> </a>
                        </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>Nenhum registro encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>