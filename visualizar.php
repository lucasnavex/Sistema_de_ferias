<?php
include './dao/formulario.php';
$crud = new Crud($conn);

$id = isset($_GET['id']) ? $_GET['id'] : null;

// Obter o registro para exibir
$registro = $id ? $crud->editar($id) : null;

// Obter as datas de férias
$datasFerias = $crud->listarDatasFerias($id);
?>

<!DOCTYPE html>
<html lang="en">

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

    <div class="container">
        <div class="detalhes-registro">
            <h2>Detalhes do Registro</h2>
            <p>Nome: <?php echo $registro['nome'] ?? ''; ?></p>
            <p>Matrícula do Servidor: <?php echo $registro['matricula_servidor'] ?? ''; ?></p>

            <h2>Períodos de Férias</h2>
            <?php if (is_array($datasFerias) && !empty($datasFerias)) : ?>
                <?php foreach ($datasFerias as $index => $data) : ?>
                    <p>Período <?php echo $index + 1; ?>: <?php echo $data['data_inicio']; ?> a <?php echo $data['data_fim']; ?></p>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Não há períodos de férias registrados.</p>
            <?php endif; ?>
            <a href="listar.php" class="button-voltar"><i class="fas fa-arrow-left"></i> Voltar para Listagem</a>

        </div>
    </div>
</body>

</html>