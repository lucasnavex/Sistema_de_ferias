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
    if (
        empty($_POST['nome']) ||
        empty($_POST['matricula_servidor']) ||
        empty($_POST['unidade_lotacao']) ||
        empty($_POST['categoria_funcional']) ||
        empty($_POST['qtd_periodos_ferias'])
    ) {
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
        // Preparar os dados para serem inseridos
        $dados = [
            ':nome' => $_POST['nome'],
            ':matricula_servidor' => $_POST['matricula_servidor'],
            ':email' => $_POST['email'],
            ':unidade_lotacao' => $_POST['unidade_lotacao'],
            ':categoria_funcional' => $_POST['categoria_funcional'],
            ':central' => $_POST['central'],
            ':gestor' => isset($_POST['gestor']) && $_POST['gestor'] == '1' ? 1 : 0,
            ':motivo_informacao' => $_POST['motivo_informacao'],
            ':qtd_periodos_ferias' => $_POST['qtd_periodos_ferias'],
            ':data_inicio_1' => $_POST['data_inicio_1'],
            ':data_fim_1' => $_POST['data_fim_1'],
            ':data_inicio_2' => $_POST['data_inicio_2'],
            ':data_fim_2' => $_POST['data_fim_2'],
            ':data_inicio_3' => $_POST['data_inicio_3'],
            ':data_fim_3' => $_POST['data_fim_3']
        ];

        if ($id && isset($_POST['motivo_informacao'])) {
            $dados[':motivo_informacao'] = $_POST['motivo_informacao'];
        }

        try {
            if ($id) {
                $crud->atualizar($id, ...array_values($dados));
                header("Location: listar.php");
                exit();
            } else {
                $crud->cadastrar($dados);
                $successMessage = "Registro cadastrado com sucesso.";
            }
        } catch (Exception $e) {
            $errorMessage = "Erro ao cadastrar: " . $e->getMessage();
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
        <div class="title">Registros</div>
        <img src="./img/inss-logo.0e1a042d.png" alt="Logo" class="logo">
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
                <input type="hidden" name="matricula_original" value="<?php echo $registro['matricula_servidor']; ?>">
            <?php endif; ?>

            Nome: <input type="text" class="input-cadastro" name="nome" value="<?php echo $registro['nome'] ?? ''; ?>" required>
            Matrícula do Servidor:
            <input type="text" name="matricula_servidor" class="input-cadastro" oninput="validarMatricula(this)" pattern="[0-9]{7}" title="Digite exatamente 7 números" value="<?php echo $registro['matricula_servidor'] ?? ''; ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="input-cadastro" required>
            <div id="email-error" class="error-message"></div>
            <label for="unidade_lotacao"> Unidade de Lotação:</label>
            <select name="unidade_lotacao" id="unidade_lotacao" required>
                <option value="">Selecione...</option>
                <option value="11025050 - APS RIACHUELO" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025050 - APS RIACHUELO') echo 'selected'; ?>>APS RIACHUELO</option>
                <option value="115251 - SERVICO DE GERENCIAMENTO DE BENEFICIOS" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '115251 - SERVICO DE GERENCIAMENTO DE BENEFICIOS') echo 'selected'; ?>>SERVIÇO DE GERENCIAMENTO DE BENEFÍCIOS</option>
                <option value="1152522 - SAREC" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '1152522 - SAREC') echo 'selected'; ?>>SAREC</option>
                <option value="11025110 - APS SAO JOAO NEPOMUCENO" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025110 - APS SAO JOAO NEPOMUCENO') echo 'selected'; ?>>APS SÃO JOÃO NEPOMUCENO</option>
                <option value="11025040 - APS SAO DIMAS" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025040 - APS SAO DIMAS') echo 'selected'; ?>>APS SÃO DIMAS</option>
                <option value="11025060 - APS LEOPOLDINA" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025060 - APS LEOPOLDINA') echo 'selected'; ?>>APS LEOPOLDINA</option>
                <option value="11025080 - APS ALEM PARAIBA" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025080 - APS ALEM PARAIBA') echo 'selected'; ?>>APS ALÉM PARAÍBA</option>
                <option value="11025070 - APS MURIAE" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025070 - APS MURIAE') echo 'selected'; ?>>APS MURIAÉ</option>
                <option value="11025010 - APS CARANGOLA" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025010 - APS CARANGOLA') echo 'selected'; ?>>APS CARANGOLA</option>
                <option value="11025090 - APS PALMA" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025090 - APS PALMA') echo 'selected'; ?>>APS PALMA</option>
                <option value="11025020 - CATAGUASES" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025020 - CATAGUASES') echo 'selected'; ?>>CATAGUASES</option>
                <option value="11025 - GERENCIA EXECUTIVA" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '11025 - GERENCIA EXECUTIVA') echo 'selected'; ?>>GERÊNCIA EXECUTIVA</option>
                <option value="115252 - SGREC" <?php if (isset($registro['unidade_lotacao']) && $registro['unidade_lotacao'] == '115252 - SGREC') echo 'selected'; ?>>SGREC</option>
            </select>
            <label for="categoria_funcional">Categoria Funcional:</label>
            <select name="categoria_funcional" id="categoria_funcional" required>
                <option value="">Selecione...</option>
                <option value="Analista" <?php if (isset($registro['categoria_funcional']) && $registro['categoria_funcional'] == "Analista") echo "selected"; ?>>Analista</option>
                <option value="Analista Assistencia Social" <?php if (isset($registro['categoria_funcional']) && $registro['categoria_funcional'] == "Analista Assistencia Social") echo "selected"; ?>>Analista Assistência Social</option>
                <option value="Analista Terapeuta Ocupacional" <?php if (isset($registro['categoria_funcional']) && $registro['categoria_funcional'] == "Analista Terapeuta Ocupacional") echo "selected"; ?>>Analista Terapeuta Ocupacional</option>
                <option value="Tecnico" <?php if (isset($registro['categoria_funcional']) && $registro['categoria_funcional'] == "Tecnico") echo "selected"; ?>>Técnico</option>
            </select>

            <label for="central">Central / APS:</label>
            <select name="central" id="central" required>
                <option value="">Selecione...</option>
                <option value="APS" <?php if (isset($registro['central']) && $registro['central'] == "APS") echo "selected"; ?>>APS</option>
                <option value="MAN(BI)" <?php if (isset($registro['central']) && $registro['central'] == "MAN(BI)") echo "selected"; ?>>MAN(BI)</option>
                <option value="RD" <?php if (isset($registro['central']) && $registro['central'] == "RD") echo "selected"; ?>>RD</option>
                <option value="GEX" <?php if (isset($registro['central']) && $registro['central'] == "GEX") echo "selected"; ?>>GEX</option>
                <option value="DJ" <?php if (isset($registro['central']) && $registro['central'] == "DJ") echo "selected"; ?>>DJ</option>
                <option value="DIRBEN" <?php if (isset($registro['central']) && $registro['central'] == "DIRBEN") echo "selected"; ?>>DIRBEN</option>
                <option value="CES" <?php if (isset($registro['central']) && $registro['central'] == "CES") echo "selected"; ?>>CES</option>
            </select>

            <label for="gestor">Gestor:</label>
            <select name="gestor" id="gestor" required>
                <option value="">Selecione...</option>
                <option value="1" <?php if (isset($registro['gestor']) && $registro['gestor'] == '1') echo 'selected'; ?>>Sim</option>
                <option value="0" <?php if (isset($registro['gestor']) && $registro['gestor'] == '0') echo 'selected'; ?>>Não</option>
            </select>

            Observação: <textarea name="motivo_informacao"><?php echo $registro['motivo_informacao'] ?? ''; ?></textarea>

            <label for="qtd_periodos_ferias">Período de Férias:</label>
            <select name="qtd_periodos_ferias" id="qtd_periodos_ferias" required>
                <option value="" <?php if (!isset($registro['qtd_periodos_ferias']) || $registro['qtd_periodos_ferias'] == '') echo 'selected'; ?>>Selecione</option>
                <option value="1" <?php if (isset($registro['qtd_periodos_ferias']) && $registro['qtd_periodos_ferias'] == '1') echo 'selected'; ?>>Período único</option>
                <option value="2" <?php if (isset($registro['qtd_periodos_ferias']) && $registro['qtd_periodos_ferias'] == '2') echo 'selected'; ?>>Dois períodos</option>
                <option value="3" <?php if (isset($registro['qtd_periodos_ferias']) && $registro['qtd_periodos_ferias'] == '3') echo 'selected'; ?>>Três períodos</option>
            </select>

            <div id="periodos_ferias">
                <?php for ($i = 1; $i <= 3; $i++) : ?>
                    <?php
                    // Verifica se há valores para os campos de data
                    $data_inicio = isset($registro['data_inicio_' . $i]) && $registro['data_inicio_' . $i] !== '1970-01-01' ? date('Y-m-d', strtotime($registro['data_inicio_' . $i])) : '';
                    $data_fim = isset($registro['data_fim_' . $i]) && $registro['data_fim_' . $i] !== '1970-01-01' ? date('Y-m-d', strtotime($registro['data_fim_' . $i])) : '';

                    // Verificar se a data é '01/01/1970' e, se for, definir como vazio
                    if ($data_inicio == '1970-01-01') {
                        $data_inicio = '';
                    }

                    if ($data_fim == '1970-01-01') {
                        $data_fim = '';
                    }
                    ?>
                    <!-- Campos de data para o período <?php echo $i; ?> -->
                    <label for="data_inicio_<?php echo $i; ?>" style="display: <?php echo ($data_inicio != '') ? 'block' : 'none'; ?>;">Data Início Período <?php echo $i; ?>:</label>
                    <input type="date" name="data_inicio_<?php echo $i; ?>" id="data_inicio_<?php echo $i; ?>" style="display: <?php echo ($data_inicio != '') ? 'block' : 'none'; ?>;" value="<?php echo $data_inicio ? $data_inicio : ''; ?>">
                    <label for="data_fim_<?php echo $i; ?>" style="display: <?php echo ($data_fim != '') ? 'block' : 'none'; ?>;">Data Fim Período <?php echo $i; ?>:</label>
                    <input type="date" name="data_fim_<?php echo $i; ?>" id="data_fim_<?php echo $i; ?>" style="display: <?php echo ($data_fim != '') ? 'block' : 'none'; ?>;" value="<?php echo $data_fim ? $data_fim : ''; ?>">
                <?php endfor; ?>
            </div>

            <script>
                document.getElementById('qtd_periodos_ferias').addEventListener('change', function() {
                    var qtdPeriodos = this.value;
                    var periodosDiv = document.getElementById('periodos_ferias');

                    // Guardar os valores atuais dos campos de data
                    var valoresAtuais = {};
                    for (var i = 1; i <= 3; i++) {
                        var dataInicio = document.getElementById('data_inicio_' + i);
                        var dataFim = document.getElementById('data_fim_' + i);
                        valoresAtuais['data_inicio_' + i] = dataInicio ? dataInicio.value : '';
                        valoresAtuais['data_fim_' + i] = dataFim ? dataFim.value : '';
                    }

                    // Remover todos os campos de data anteriores
                    periodosDiv.innerHTML = '';

                    // Adicionar os campos de data relevantes com base na seleção do usuário
                    for (var i = 1; i <= qtdPeriodos; i++) {
                        var labelInicio = document.createElement('label');
                        labelInicio.setAttribute('for', 'data_inicio_' + i);
                        labelInicio.textContent = 'Data Início Período ' + i + ':';
                        periodosDiv.appendChild(labelInicio);

                        var inputInicio = document.createElement('input');
                        inputInicio.setAttribute('type', 'date');
                        inputInicio.setAttribute('name', 'data_inicio_' + i);
                        inputInicio.setAttribute('id', 'data_inicio_' + i);
                        inputInicio.value = valoresAtuais['data_inicio_' + i] || '';
                        periodosDiv.appendChild(inputInicio);

                        var labelFim = document.createElement('label');
                        labelFim.setAttribute('for', 'data_fim_' + i);
                        labelFim.textContent = 'Data Fim Período ' + i + ':';
                        periodosDiv.appendChild(labelFim);

                        var inputFim = document.createElement('input');
                        inputFim.setAttribute('type', 'date');
                        inputFim.setAttribute('name', 'data_fim_' + i);
                        inputFim.setAttribute('id', 'data_fim_' + i);
                        inputFim.value = valoresAtuais['data_fim_' + i] || '';
                        periodosDiv.appendChild(inputFim);

                        periodosDiv.appendChild(document.createElement('br'));
                    }
                });

                //validar e-mail
                document.addEventListener("DOMContentLoaded", function() {
                    var emailInput = document.getElementById("email");
                    var emailError = document.getElementById("email-error");

                    emailInput.addEventListener("input", function(event) {
                        var email = event.target.value;
                        if (!isValidEmail(email)) {
                            emailError.textContent = "Por favor, insira um email válido.";
                            emailError.style.display = "block";
                        } else {
                            emailError.textContent = "";
                            emailError.style.display = "none";
                        }
                    });

                    function isValidEmail(email) {
                        // Expressão regular para validar email
                        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        return emailRegex.test(email);
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