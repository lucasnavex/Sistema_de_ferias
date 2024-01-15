
<?php
     require_once("templates/header.php");
?>

<div id="login-container">
    <form action="<?= $BASE_URL ?>auth_process.php" method="POST" class="card">
        <div class="card-header">
            <h2>GRF</h2>
        </div>
        <div class="card-content">
            <input type="hidden" name="type" value="login">
            <div class="card-content-area">
                <i class="fas fa-user"></i>
                <input type="text" id="email" name="email" placeholder="Email">
            </div>
            <div class="card-content-area">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Senha">
            </div>
        </div>
        <div class="card-footer">
            <input type="submit"  value="Entrar" class="submit">
        </div>
        <div class="card-footer">
            <a href="#" class="recuperar_senha">Esqueceu a senha?</a>
        </div>
    </form>
</div>

