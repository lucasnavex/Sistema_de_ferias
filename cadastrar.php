
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
        REGISTRO DE FÉRIAS
    </nav>

    <?php
    include './dao/formulario.php';
    $crud = new Crud($conn);

    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Se o formulário foi enviado, processar a ação
        $nome = $_POST['nome'];
        $matricula_servidor = $_POST['matricula_servidor'];
        $unidade_lotacao = $_POST['unidade_lotacao'];
        $categoria_funcional = $_POST['categoria_funcional'];
        $central = $_POST['central'];
        $gestor = $_POST['gestor'];
        $motivo_informacao = $_POST['motivo_informacao'];
        $qtd_periodos_ferias = $_POST['qtd_periodos_ferias'];

        // Se for uma operação de edição, atualize os dados
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
                $qtd_periodos_ferias
            );

            // Redirecionar para listar.php após a atualização
            header("Location: listar.php");
            exit();
        } else {
            // Se for uma operação de cadastro, chame o método de cadastro
            $crud->cadastrar(
                $nome,
                $matricula_servidor,
                $unidade_lotacao,
                $categoria_funcional,
                $central,
                $gestor,
                $motivo_informacao,
                $qtd_periodos_ferias
            );
        }
    }

    // Obter o registro para exibir no formulário se for uma operação de edição
    $registro = $id ? $crud->editar($id) : null;

    ?>
    <form action="?action=<?php echo $id ? 'atualizar&id=' . $id : 'cadastrar'; ?>" method="post">
        <h2><?php echo $id ? 'Formulário de Edição' : 'Formulário de Cadastro'; ?></h2>
        <?php if ($id) : ?>
            <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">
        <?php endif; ?>

        Nome: <input type="text" class="input-cadastro" name="nome" value="<?php echo $registro['nome'] ?? ''; ?>" required><br>
        Matrícula do Servidor: <input type="text" input-cadastro name="matricula_servidor" value="<?php echo $registro['matricula_servidor'] ?? ''; ?>" required><br>
        Unidade de Lotação: <input type="text" input-cadastro name="unidade_lotacao" value="<?php echo $registro['unidade_lotacao'] ?? ''; ?>" required><br>
        Categoria Funcional: <input type="text" input-cadastro name="categoria_funcional" value="<?php echo $registro['categoria_funcional'] ?? ''; ?>" required><br>
        Central: <input type="text" name="central" value="<?php echo $registro['central'] ?? ''; ?>" required><br>
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
            Quantidade de Períodos de Férias:
            <input type="number" name="qtd_periodos_ferias" value="<?php echo isset($registro['qtd_periodos_ferias']) ? $registro['qtd_periodos_ferias'] : ''; ?>" min="1" max="3" required>
            <br>
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