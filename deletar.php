<?php
include './dao/formulario.php';

$crud = new Crud($conn);

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    $crud->excluir($id);
} else {
    echo "ID não fornecido para exclusão.";
}
?>