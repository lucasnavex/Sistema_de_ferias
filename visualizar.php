<?php
require_once("dao/UserDAO.php");
require_once("globals.php");
require_once("db.php");
include './dao/formulario.php';
$userDAO = new UserDAO($conn, $BASE_URL);
$crud = new Crud($conn);
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Obter o registro para exibir no formulário se for uma operação de visualização
$registro = $id ? $crud->editar($id) : null;
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
                <button><a href="cadastrar.php" class="nav-link">Novo Registro</a></button>
            </div>
        </div>

        <div class="content">
            <div class="visualizar-container">

                <?php if ($registro) : ?>
                    <table class='registro-table'>
                        <tr>
                            <th>Período</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>Última Atualização</th> <!-- Nova coluna -->
                        </tr>

                        <?php for ($i = 1; $i <= 3; $i++) : ?>
                            <tr>
                                <td>Período <?php echo $i; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($registro['data_inicio_' . $i])) ?? ''; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($registro['data_fim_' . $i])) ?? ''; ?></td>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($registro['timestamp'])) ?? ''; ?></td>
                            </tr>
                        <?php endfor; ?>
                    </table>
                <?php else : ?>
                    <p>Registro não encontrado.</p>
                <?php endif; ?>

                <button><a href="listar.php" class="button-voltar"><i class="fas fa-arrow-left"></i> Voltar para Listagem</a></button>
            </div>
        </div>
    </div>
</body>

</html>