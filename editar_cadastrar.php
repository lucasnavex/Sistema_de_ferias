<?php
include '../dao/formulario.php';

$crud = new Crud($conn);

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se o formulário foi enviado, processar a ação
    $nome = $_POST['nome'];
    $matricula_servidor = $_POST['matricula_servidor'];
    $unidade_lotacao = $_POST['unidade_lotacao'];
    $categoria_funcional = $_POST['categoria_funcional'];
    $gestor = $_POST['gestor'];
    $motivo_informacao = $_POST['motivo_informacao'];
    $qtd_periodos_ferias = $_POST['qtd_periodos_ferias'];

    // Se for uma operação de edição, atualize os dados
    if ($id) {
        $crud->atualizar($id, $nome, $matricula_servidor, $unidade_lotacao, $categoria_funcional, $gestor, $motivo_informacao, $qtd_periodos_ferias);

        // Redirecionar para listar.php após a atualização
        header("Location: ../pages/listar.php");
        exit();
    } else {
        // Se for uma operação de cadastro, chame o método de cadastro
        $crud->cadastrar($nome, $matricula_servidor, $unidade_lotacao, $categoria_funcional, $gestor, $motivo_informacao, $qtd_periodos_ferias);
    }
}

// Obter o registro para exibir no formulário se for uma operação de edição
$registro = $id ? $crud->editar($id) : null;

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Formulário de Edição' : 'Formulário de Cadastro'; ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <nav>
        GERENCIADOR DE REGISTRO DE FÉRIAS
    </nav>

    <form action="?action=<?php echo $id ? 'atualizar&id=' . $id : 'cadastrar'; ?>" method="post">
        <h2><?php echo $id ? 'Formulário de Edição' : 'Formulário de Cadastro'; ?></h2>
        <?php if ($id) : ?>
            <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">
        <?php endif; ?>

        Nome: <input type="text" name="nome" value="<?php echo $registro['nome'] ?? ''; ?>" required><br>
        Matrícula do Servidor: <input type="text" name="matricula_servidor" value="<?php echo $registro['matricula_servidor'] ?? ''; ?>" required><br>
        Unidade de Lotação: <input type="text" name="unidade_lotacao" value="<?php echo $registro['unidade_lotacao'] ?? ''; ?>" required><br>
        Categoria Funcional: <input type="text" name="categoria_funcional" value="<?php echo $registro['categoria_funcional'] ?? ''; ?>" required><br>
        Gestor: <input type="text" name="gestor" value="<?php echo $registro['gestor'] ?? ''; ?>" required><br>
        Motivo da Informação: <textarea name="motivo_informacao" required><?php echo $registro['motivo_informacao'] ?? ''; ?></textarea><br>

        <?php if (!$id) : ?>
            Quantidade de Períodos de Férias:
            <select name="qtd_periodos_ferias" onchange="mostrarCamposData()">
                <option value="0">Selecione</option>
                <option value="1">1 Período</option>
                <option value="2">2 Períodos</option>
                <option value="3">3 Períodos</option>
            </select>
            <br>
            <div id="container_datas"></div>
        <?php else : ?>
            Quantidade de Períodos de Férias: <input type="number" name="qtd_periodos_ferias" value="<?php echo $registro['qtd_periodos_ferias'] ?? ''; ?>" min="1" max="3" required><br>
        <?php endif; ?>

        <input type="submit" value="<?php echo $id ? 'Atualizar' : 'Cadastrar'; ?>">
    </form>

    <script>
        function mostrarCamposData() {
            var qtdPeriodos = document.querySelector('select[name="qtd_periodos_ferias"]').value;
            var containerDatas = document.getElementById('container_datas');

            // Limpa o conteúdo existente
            containerDatas.innerHTML = '';

            // Cria os campos de data correspondentes ao número de períodos escolhidos
            for (var i = 0; i < qtdPeriodos; i++) {
                var labelInicio = document.createElement('label');
                labelInicio.innerHTML = 'Início do Período ' + (i + 1) + ': ';
                var inputInicio = document.createElement('input');
                inputInicio.type = 'date';
                inputInicio.name = 'inicio_periodo[]'; // Use um array para coletar várias datas

                var labelFim = document.createElement('label');
                labelFim.innerHTML = 'Fim do Período ' + (i + 1) + ': ';
                var inputFim = document.createElement('input');
                inputFim.type = 'date';
                inputFim.name = 'fim_periodo[]'; // Use um array para coletar várias datas

                containerDatas.appendChild(labelInicio);
                containerDatas.appendChild(inputInicio);
                containerDatas.appendChild(document.createElement('br'));
                containerDatas.appendChild(labelFim);
                containerDatas.appendChild(inputFim);
                containerDatas.appendChild(document.createElement('br'));
            }
        }
    </script>
</body>

</html>