<?php
require_once("templates/header.php");


// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os dados do formulário
    $name = $_POST["name"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Gera o hash da senha
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insere os dados no banco de dados
    $stmt = $conn->prepare("INSERT INTO users (name, lastname, email, password) VALUES (:name, :lastname, :email, :password)");
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":lastname", $lastname);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $hashedPassword);

    if ($stmt->execute()) {
        echo "Usuário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar o usuário.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
</head>
<body>

<h2>Cadastro de Usuário</h2>

<form action="" method="POST">
    <label for="name">Nome:</label>
    <input type="text" id="name" name="name" required><br>

    <label for="lastname">Sobrenome:</label>
    <input type="text" id="lastname" name="lastname" required><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="password">Senha:</label>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="Cadastrar">
</form>

</body>
</html>
