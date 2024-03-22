<?php
require_once './dao/formulario.php';

$crud = new Crud($conn);

$id = isset($_GET['id']) ? $_GET['id'] : null;
$registro = $id ? $crud->editar($id) : null;

$successMessage = $errorMessage = '';

// Processar o formulário se enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validationErrors = [];

    // Função para validar datas
    function validarData($data, $formato = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($formato, $data);
        return $d && $d->format($formato) === $data;
    }

    // Realize a validação aqui
    if (empty($_POST['nome']) || empty($_POST['matricula_servidor']) || empty($_POST['unidade_lotacao']) || empty($_POST['categoria_funcional']) || empty($_POST['motivo_informacao']) || empty($_POST['qtd_periodos_ferias'])) {
        $validationErrors[] = "Todos os campos são obrigatórios.";
    }

    if (!is_numeric($_POST['qtd_periodos_ferias']) || $_POST['qtd_periodos_ferias'] <= 0) {
        $validationErrors[] = "A quantidade de períodos de férias deve ser um número inteiro positivo.";
    }

    for ($i = 1; $i <= $_POST['qtd_periodos_ferias']; $i++) {
        $dataInicio = $_POST['data_inicio_' . $i];
        $dataFim = $_POST['data_fim_' . $i];

        if (!validarData($dataInicio) || !validarData($dataFim)) {
            $validationErrors[] = "Datas inválidas para o período $i.";
        }
    }

    if (empty($validationErrors)) {
        // Se a validação passar, proceda com o processamento do formulário
        $nome = $_POST['nome'];

        // Adicionar um campo oculto para armazenar a matrícula original
        $matricula_servidor = $_POST['matricula_servidor'];
        $matricula_original = $_POST['matricula_original'];


        $unidade_lotacao = $_POST['unidade_lotacao'];
        $categoria_funcional = $_POST['categoria_funcional'];
        $central = $_POST['central'];
        $gestor = isset($_POST['gestor']) && $_POST['gestor'] == '1' ? 1 : 0;
        $motivo_informacao = $_POST['motivo_informacao'];
        $qtd_periodos_ferias = $_POST['qtd_periodos_ferias'];

        $datas = [];

        for ($i = 1; $i <= 3; $i++) {
            $dataInicioKey = 'data_inicio_' . $i;
            $dataFimKey = 'data_fim_' . $i;

            $dataInicioValue = isset($_POST[$dataInicioKey]) ? $_POST[$dataInicioKey] : '';
            $dataFimValue = isset($_POST[$dataFimKey]) ? $_POST[$dataFimKey] : '';

            $datas[] = [
                'data_inicio' => $dataInicioValue,
                'data_fim' => $dataFimValue,
            ];
        }


        if ($id) {
            $crud->atualizar(
                $id,
                $nome,
                $matricula_servidor,
                $unidade_lotacao,
                $categoria_funcional,
                $central,
                $gestor,
                $motivo_informacao,
                $qtd_periodos_ferias,
                $datas[0]['data_inicio'],
                $datas[0]['data_fim'],
                $datas[1]['data_inicio'],
                $datas[1]['data_fim'],
                $datas[2]['data_inicio'],
                $datas[2]['data_fim']
            );

            // Redirecionar imediatamente para listar.php
            header("Location: listar.php");
            exit(); // Certifique-se de encerrar o script após o redirecionamento
        } else {
            $crud->cadastrar(
                $nome,
                $matricula_servidor,
                $unidade_lotacao,
                $categoria_funcional,
                $central,
                $gestor,
                $motivo_informacao,
                $qtd_periodos_ferias,
                $datas[0]['data_inicio'],
                $datas[0]['data_fim'],
                $datas[1]['data_inicio'],
                $datas[1]['data_fim'],
                $datas[2]['data_inicio'],
                $datas[2]['data_fim']
            );

            $successMessage = "Registro cadastrado com sucesso.";
        }
    } else {
        $errorMessage = "Por favor, corrija os seguintes erros:";
    }
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

    <script>
        function validarMatricula(input) {
            input.value = input.value.replace(/\D/g, '');

            if (input.value.length > 7) {
                input.value = input.value.slice(0, 7);
            }
        }
    </script>
</head>

<body>
    <nav>
        <img src="./img/inss-logo.0e1a042d.png" alt="Logo" class="logo">
        REGISTRO DE FÉRIAS
    </nav>

    <form action="?action=<?php echo $id ? 'atualizar&id=' . $id : 'cadastrar'; ?>" method="post">
        <h2><?php echo $id ? 'Formulário de Edição' : 'Formulário de Cadastro'; ?></h2>

        <?php if (!empty($successMessage)) : ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
            <a href="listar.php">Voltar para a lista</a>
        <?php else : ?>
            <?php if (!empty($errorMessage)) : ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>

            <?php if ($id) : ?>
                <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">
                <!-- Adicionar campo oculto para armazenar matrícula original -->
                <input type="hidden" name="matricula_original" value="<?php echo $registro['matricula_servidor']; ?>">
            <?php endif; ?>

            Nome: <input type="text" class="input-cadastro" name="nome" value="<?php echo $registro['nome'] ?? ''; ?>" required>
            Matrícula do Servidor:
            <input type="text" name="matricula_servidor" oninput="validarMatricula(this)" pattern="[0-9]{7}" title="Digite exatamente 7 números" value="<?php echo $registro['matricula_servidor'] ?? ''; ?>" required>
            Unidade de Lotação:
            <select name="unidade_lotacao" required>
                <option value="">Selecione...</option>
                <option value="11025050 - APS RIACHUELO" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025050 - APS RIACHUELO') echo 'selected'; ?>>APS RIACHUELO</option>
                <option value="115251 - SERVIÇO DE GERENCIAMENTO DE BENEFÍCIOS" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '115251 - SERVIÇO DE GERENCIAMENTO DE BENEFÍCIOS') echo 'selected'; ?>>SERVIÇO DE GERENCIAMENTO DE BENEFÍCIOS</option>
                <option value="1152522 - SAREC" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '1152522 - SAREC') echo 'selected'; ?>>SAREC</option>
                <option value="11025110 - APS SÃO JOÃO NEPOMUCENO" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025110 - APS SÃO JOÃO NEPOMUCENO') echo 'selected'; ?>>APS SÃO JOÃO NEPOMUCENO</option>
                <option value="11025040 - APS SÃO DIMAS" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025040 - APS SÃO DIMAS') echo 'selected'; ?>>APS SÃO DIMAS</option>
                <option value="11025060 - APS LEOPOLDINA" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025060 - APS LEOPOLDINA') echo 'selected'; ?>>APS LEOPOLDINA</option>
                <option value="11025080 - APS ALÉM PARAÍBA" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025080 - APS ALÉM PARAÍBA') echo 'selected'; ?>>APS ALÉM PARAÍBA</option>
                <option value="11025070 - APS MURIAÉ" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025070 - APS MURIAÉ') echo 'selected'; ?>>APS MURIAÉ</option>
                <option value="11025010 - APS CARANGOLA" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025010 - APS CARANGOLA') echo 'selected'; ?>>APS CARANGOLA</option>
                <option value="11025090 - APS PALMA" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025090 - APS PALMA') echo 'selected'; ?>>APS PALMA</option>
                <option value="11025020 - CATAGUASES" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025020 - CATAGUASES') echo 'selected'; ?>>CATAGUASES</option>
                <option value="11025 - GERÊNCIA EXECUTIVA" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025 - GERÊNCIA EXECUTIVA') echo 'selected'; ?>>GERÊNCIA EXECUTIVA</option>
                <option value="115252 - SGREC" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '115252 - SGREC') echo 'selected'; ?>>SGREC</option>
            </select>
            <label for="categoria_funcional">Categoria Funcional:</label>
            <select name="categoria_funcional" id="categoria_funcional" required>
                <option value="">Selecione...</option>
                <option value="Analista" <?php if (isset($registro['categoria_funcional']) && $registro['categoria_funcional'] == "Analista") echo "selected"; ?>>Analista</option>
                <option value="Analista Assistência Social" <?php if (isset($registro['categoria_funcional']) && $registro['categoria_funcional'] == "Analista Assistência Social") echo "selected"; ?>>Analista Assistência Social</option>
                <option value="Analista Terapeuta Ocupacional" <?php if (isset($registro['categoria_funcional']) && $registro['categoria_funcional'] == "Analista Terapeuta Ocupacional") echo "selected"; ?>>Analista Terapeuta Ocupacional</option>
                <option value="Técnico" <?php if (isset($registro['categoria_funcional']) && $registro['categoria_funcional'] == "Técnico") echo "selected"; ?>>Técnico</option>
            </select>
            <label for="central">Central:</label>
            <select name="central" id="central" required>
                <option value="">Selecione...</option>
                <option value="MAN" <?php if (isset($registro['central']) && $registro['central'] == "MAN") echo "selected"; ?>>MAN</option>
                <option value="MAN(BI)" <?php if (isset($registro['central']) && $registro['central'] == "MAN(BI)") echo "selected"; ?>>MAN(BI)</option>
                <option value="RD" <?php if (isset($registro['central']) && $registro['central'] == "RD") echo "selected"; ?>>RD</option>
                <option value="GEX" <?php if (isset($registro['central']) && $registro['central'] == "GEX") echo "selected"; ?>>GEX</option>
                <option value="DJ" <?php if (isset($registro['central']) && $registro['central'] == "DJ") echo "selected"; ?>>DJ</option>
                <option value="DIRBEN" <?php if (isset($registro['central']) && $registro['central'] == "DIRBEN") echo "selected"; ?>>DIRBEN</option>
                <option value="CES" <?php if (isset($registro['central']) && $registro['central'] == "CES") echo "selected"; ?>>CES</option>
            </select>
            Gestor:
            <select name="gestor" required>
                <option value="">Selecione...</option>
                <option value="1" <?php if (isset($registro['gestor']) && $registro['gestor'] == '1') echo 'selected'; ?>>Sim</option>
                <option value="0" <?php if (isset($registro['gestor']) && $registro['gestor'] == '0') echo 'selected'; ?>>Não</option>
            </select>
            Observação: <textarea name="motivo_informacao" required><?php echo $registro['motivo_informacao'] ?? ''; ?></textarea>
            <label for="qtd_periodos_ferias">Período de Férias:</label>
            <select name="qtd_periodos_ferias" id="qtd_periodos_ferias" required>
                <option value="" <?php if (!isset($registro['qtd_periodos_ferias']) || $registro['qtd_periodos_ferias'] == '') echo 'selected'; ?>>Selecione</option>
                <option value="1" <?php if (isset($registro['qtd_periodos_ferias']) && $registro['qtd_periodos_ferias'] == '1') echo 'selected'; ?>>Período único</option>
                <option value="2" <?php if (isset($registro['qtd_periodos_ferias']) && $registro['qtd_periodos_ferias'] == '2') echo 'selected'; ?>>Dois períodos</option>
                <option value="3" <?php if (isset($registro['qtd_periodos_ferias']) && $registro['qtd_periodos_ferias'] == '3') echo 'selected'; ?>>Três períodos</option>
            </select>
            <br>

            <div id="periodos_ferias">
                <!-- Campos de data para o primeiro período -->
                <label for="data_inicio_1" style="display: none;">Data Início Período 1:</label>
                <input type="date" name="data_inicio_1" id="data_inicio_1" style="display: none;" value="<?php echo $registro ? date('Y-m-d', strtotime($registro['data_inicio_1'])) : ''; ?>">
                <label for="data_fim_1" style="display: none;">Data Fim Período 1:</label>
                <input type="date" name="data_fim_1" id="data_fim_1" style="display: none;" value="<?php echo $registro ? date('Y-m-d', strtotime($registro['data_fim_1'])) : ''; ?>">

                <!-- Campos de data para o segundo período -->
                <label for="data_inicio_2" style="display: none;">Data Início Período 2:</label>
                <input type="date" name="data_inicio_2" id="data_inicio_2" style="display: none;" value="<?php echo $registro ? date('Y-m-d', strtotime($registro['data_inicio_2'])) : ''; ?>">
                <label for="data_fim_2" style="display: none;">Data Fim Período 2:</label>
                <input type="date" name="data_fim_2" id="data_fim_2" style="display: none;" value="<?php echo $registro ? date('Y-m-d', strtotime($registro['data_fim_2'])) : ''; ?>">

                <!-- Campos de data para o terceiro período -->
                <label for="data_inicio_3" style="display: none;">Data Início Período 3:</label>
                <input type="date" name="data_inicio_3" id="data_inicio_3" style="display: none;" value="<?php echo $registro ? date('Y-m-d', strtotime($registro['data_inicio_3'])) : ''; ?>">
                <label for="data_fim_3" style="display: none;">Data Fim Período 3:</label>
                <input type="date" name="data_fim_3" id="data_fim_3" style="display: none;" value="<?php echo $registro ? date('Y-m-d', strtotime($registro['data_fim_3'])) : ''; ?>">
            </div>

            <script>
                document.getElementById('qtd_periodos_ferias').addEventListener('change', function() {
                    var qtdPeriodos = this.value;
                    var periodosDiv = document.getElementById('periodos_ferias');

                    // Esconder todos os campos de data
                    for (var i = 1; i <= 3; i++) {
                        document.querySelector('label[for="data_inicio_' + i + '"]').style.display = 'none';
                        document.getElementById('data_inicio_' + i).style.display = 'none';
                        document.querySelector('label[for="data_fim_' + i + '"]').style.display = 'none';
                        document.getElementById('data_fim_' + i).style.display = 'none';
                    }

                    // Exibir os campos de data relevantes com base na seleção do usuário
                    for (var i = 1; i <= qtdPeriodos; i++) {
                        document.querySelector('label[for="data_inicio_' + i + '"]').style.display = 'block';
                        document.getElementById('data_inicio_' + i).style.display = 'block';
                        document.querySelector('label[for="data_fim_' + i + '"]').style.display = 'block';
                        document.getElementById('data_fim_' + i).style.display = 'block';
                    }
                });
            </script>
            <input type="submit" value="<?php echo $id ? 'Atualizar' : 'Cadastrar'; ?>">
            <?php if (!empty($validationErrors)) : ?>
                <ul class="error-list">
                    <?php foreach ($validationErrors as $error) : ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endif; ?>
    </form>
</body>

</html>