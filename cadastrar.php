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

            Nome: <input type="text" class="input-cadastro" name="nome" value="<?php echo $registro['nome'] ?? ''; ?>" required><br>
            Matrícula do Servidor:
            <input type="text" name="matricula_servidor" oninput="validarMatricula(this)" pattern="[0-9]{7}" title="Digite exatamente 7 números" value="<?php echo $registro['matricula_servidor'] ?? ''; ?>" required><br>
            Unidade de Lotação: <input type="text" input-cadastro name="unidade_lotacao" value="<?php echo $registro['unidade_lotacao'] ?? ''; ?>" required><br>
            Categoria Funcional: <input type="text" input-cadastro name="categoria_funcional" value="<?php echo $registro['categoria_funcional'] ?? ''; ?>" required><br>
            Central: <input type="text" name="central" value="<?php echo $registro['central'] ?? ''; ?>"><br>
            Gestor: <input type="radio" name="gestor" value="1" <?php if (isset($registro['gestor']) && $registro['gestor'] == '1') echo 'checked'; ?> required>Sim
            <input type="radio" name="gestor" value="0" <?php if (isset($registro['gestor']) && $registro['gestor'] == '0') echo 'checked'; ?> required>Não<br>
            Motivo da Informação: <textarea name="motivo_informacao" required><?php echo $registro['motivo_informacao'] ?? ''; ?></textarea><br>
            Períodos de férias: <input type="text" input-cadastro name="qtd_periodos_ferias" value="<?php echo $registro['qtd_periodos_ferias'] ?? ''; ?>" required><br>

            <!-- Campos de data para os períodos -->
            <?php for ($i = 1; $i <= 3; $i++) : ?>
                <label for="data_inicio_<?php echo $i; ?>">Data Início Período <?php echo $i; ?></label>
                <input type="date" name="data_inicio_<?php echo $i; ?>" id="data_inicio_<?php echo $i; ?>" value="<?php echo $registro ? date('Y-m-d', strtotime($registro['data_inicio_' . $i])) : ''; ?>">

                <label for="data_fim_<?php echo $i; ?>">Data Fim Período <?php echo $i; ?></label>
                <input type="date" name="data_fim_<?php echo $i; ?>" id="data_fim_<?php echo $i; ?>" value="<?php echo $registro ? date('Y-m-d', strtotime($registro['data_fim_' . $i])) : ''; ?>">
            <?php endfor; ?>


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