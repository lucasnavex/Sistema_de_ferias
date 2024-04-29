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
        <div class="title">Registros</div>
        <img src="./img/inss-logo.0e1a042d.png" alt="Logo" class="logo">
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
        </div>

        <div class="content">
            <div class="visualizar-container">

                <?php if ($registro) : ?>
                    <table class='registro-table'>
                        <tr>
                            <th>Período</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                        </tr>

                        <?php for ($i = 1; $i <= 3; $i++) : ?>
                            <?php
                            $dataInicio = isset($registro['data_inicio_' . $i]) && $registro['data_inicio_' . $i] != '0000-00-00' ? date('d/m/Y', strtotime($registro['data_inicio_' . $i])) : '';
                            $dataFim = isset($registro['data_fim_' . $i]) && $registro['data_fim_' . $i] != '0000-00-00' ? date('d/m/Y', strtotime($registro['data_fim_' . $i])) : '';
                            ?>
                            <?php if (!empty($dataInicio) && !empty($dataFim)) : ?>
                                <tr>
                                    <td>Período <?php echo $i; ?></td>
                                    <td><?php echo $dataInicio; ?></td>
                                    <td><?php echo $dataFim; ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </table>
                <?php else : ?>
                    <p>Registro não encontrado.</p>
                <?php endif; ?>

                <?php
                $historico = $crud->listarHistoricoParaRegistro($id);

                if ($historico) :
                ?>
                    <br>
                    <h2>Histórico de Edições</h2>
                    <table class='historico-table'>
                        <thead>
                            <tr>
                                <th>Campo Editado</th>
                                <th>Data Antiga</th>
                                <th>Data Nova</th>
                                <th>Data de Modificação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historico as $item) : ?>
                                <tr>
                                    <td><?php
                                        $campoEditado = str_replace('_', ' ', $item['campo_editado']);
                                        echo ucwords($campoEditado);
                                        ?></td>

                                    <td>
                                        <?php echo $item['data_antiga'] ? date('d/m/Y', strtotime($item['data_antiga'])) : '';
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo $item['data_nova'] ? date('d/m/Y', strtotime($item['data_nova'])) : '';
                                        ?>
                                    </td>

                                    <td>
                                        <?php echo date('d/m/Y', strtotime($item['data_modificacao'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?><br>
                    <p>Nenhuma edição encontrada para este registro.</p>
                <?php endif; ?>
                <button><a href="listar.php" class="button-voltar"><i class="fas fa-arrow-left"></i> Voltar para Listagem</a></button>
            </div>
        </div>
    </div>
</body>

</html>