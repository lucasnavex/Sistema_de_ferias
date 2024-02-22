<?php
require_once("templates/header.php");
?>

<div id="cadastro">
    <form class="card" action="<?= $BASE_URL ?>auth_process.php" method="POST">
        <div class="card-header">
            <h3>GRF</h3>
            <h5>Cadastro 1 </h5>
        </div>
        <div class="card-content">
            <input type="hidden" name="type" value="register">
            <div class="card-content-area">
                <i class="fas fa-user"></i>
                <input type="text" name="name" id="name" placeholder="Nome" autocomplete="off" required>
            </div>
            <div class="card-content-area">
                <i class="fas fa-user"></i>
                <input type="text" name="lastname" id="lastname" placeholder="Sobrenome" autocomplete="off" required>
            </div>
            <div class="card-content-area">
                <i class="fas fa-envelope"></i>
                <input type="text" name="email" id="email" placeholder="Email" autocomplete="off" required>
            </div>
            <div class="card-content-area">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Senha" autocomplete="off" required>
            </div>
            <div class="card-content-area">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirmpassword" id="confirmpassword" placeholder="Confirme a Senha" autocomplete="off" required>
            </div> 
            Gestor: <input type="radio" name="gestor"  id="gestor" value="1" required>Sim
            <input type="radio" name="gestor"  id="gestor" value="0" required>Não
        </div>
        <div class="card-footer">
            <input type="submit" value="Cadastrar" class="submit">
        </div>
    </form>
</div>
</body>

</html>