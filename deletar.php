<?php
include './dao/formulario.php';

$crud = new Crud($conn);

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    if ($crud->excluir($id)) {
        header("Location: listar.php?msg=success");
        exit();
    } else {
        header("Location: listar.php?msg=error");
        exit();
    }
} else {
    echo "ID não fornecido para exclusão.";
}
?>
