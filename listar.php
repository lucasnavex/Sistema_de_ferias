<?php
require_once('dao/formulario.php');
require_once("dao/UserDAO.php");
require_once("globals.php");
require_once("db.php");


$userDAO = new UserDAO($conn, $BASE_URL);

if (isset($_GET['unidade_lotacao'])) {
    $unidade_filtrada = $_GET['unidade_lotacao'];
    $registros = $crud->listarPorUnidade($unidade_filtrada);
} else {
    $registros = $crud->listar();
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;

if ($msg == "success") {
    echo "<div class='msg success'>Registro excluído com sucesso.</div>";
} elseif ($msg == "error") {
    echo "<div class='msg error'>Erro ao excluir o registro.</div>";
}


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
                    $user = $userDAO->findByToken($_SESSION['token']);

                    if ($user) {
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
                <button><a href="index.php" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> Sair</a></button>
            </div>
        </div>

        <div class="content">
            <div action="" method="GET" class="filter-container">
                <select class="filter-select" id="unidade_lotacao">
                    <option value="">Selecione a Unidade de Lotação</option>
                    <option value="11025050 - APS RIACHUELO">APS RIACHUELO</option>
                    <option value="115251 - SERVICO DE GERENCIAMENTO DE BENEFICIOS">SERVIÇO DE GERENCIAMENTO DE BENEFÍCIOS</option>
                    <option value="1152522 - SAREC">SAREC</option>
                    <option value="11025110 - APS SAO JOAO NEPOMUCENO">APS SÃO JOÃO NEPOMUCENO</option>
                    <option value="11025040 - APS SAO DIMAS">APS SÃO DIMAS</option>
                    <option value="11025060 - APS LEOPOLDINA">APS LEOPOLDINA</option>
                    <option value="11025080 - APS ALEM PARAIBA">APS ALÉM PARAÍBA</option>
                    <option value="11025070 - APS MURIAE">APS MURIAÉ</option>
                    <option value="11025010 - APS CARANGOLA">APS CARANGOLA</option>
                    <option value="11025090 - APS PALMA">APS PALMA</option>
                    <option value="11025020 - CATAGUASES">CATAGUASES</option>
                    <option value="11025 - GERENCIA EXECUTIVA">GERÊNCIA EXECUTIVA</option>
                    <option value="115252 - SGREC">SGREC</option>
                </select>
                <button class="filter-button" onclick="filterRecords()">Buscar</button>
            </div>

            <script>
                function filterRecords() {
                    const selectedValue = document.getElementById("unidade_lotacao").value;
                    const rows = document.querySelectorAll(".registro-table tbody tr");
                    let rowCount = 0;

                    rows.forEach(row => {
                        const cell = row.cells[2];
                        if (selectedValue === "" || cell.textContent.includes(selectedValue)) {
                            row.style.display = "";
                            rowCount++;
                        } else {
                            row.style.display = "none";
                        }
                    });

                    const noRecordsMessage = document.getElementById("no-records-message");
                    if (rowCount === 0) {
                        noRecordsMessage.style.display = "block";
                    } else {
                        noRecordsMessage.style.display = "none";
                    }

                }
            </script>

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
                            <a href='deletar.php?id={$registro['id']}'><i class='fas fa-trash-alt'></i> </a>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>Nenhum registro encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div id="no-records-message" class="no-records-message" style="display: none;">
                Nenhum registro encontrado para a unidade de lotação selecionada.
            </div>
        </div>
    </div>