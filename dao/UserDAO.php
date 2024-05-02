<?php

  include_once("models/User.php");
  include_once("models/Message.php");

  class UserDAO implements UserDAOInterface {

    private $conn;
    private $url;
    private $message;

    public function __construct(PDO $conn, $url) {
      $this->conn = $conn;
      $this->url = $url;
      $this->message = new Message($url);
    }

    public function buildUser($data) {

      $user = new User();

      $user->id = $data["id"];
      $user->name = $data["name"];
      $user->lastname = $data["lastname"];
      $user->email = $data["email"];
      $user->password = $data["password"];
      $user->token = $data["token"];
      $stmt->bindParam(":gestor", $user->gestor, PDO::PARAM_BOOL);

      return $user;

    }
    
    public function create(User $user, $authUser = false) {
      
      $stmt = $this->conn->prepare("INSERT INTO users (
        name, lastname, email, password, token, gestor
      ) VALUES (
        :name, :lastname, :email, :password, :token , :gestor
      )");
    
      $stmt->bindParam(":name", $user->name);
      $stmt->bindParam(":lastname", $user->lastname);
      $stmt->bindParam(":email", $user->email);
      $stmt->bindParam(":password", $user->password);
      $stmt->bindParam(":token", $user->token);
      $stmt->bindParam(":gestor", $user->gestor, PDO::PARAM_BOOL);
      
      
      $stmt->execute();
      
      // Autentica usuário caso venha da tela de registro
      if($authUser) {
        
        $this->setTokenToSession($user->token);
        
      }
      
    }
    
    public function listarPorGestor($isGestor) {
      $stmt = $this->conn->prepare("SELECT * FROM sua_tabela WHERE gestor = :is_gestor");
      $stmt->bindParam(":is_gestor", $isGestor, PDO::PARAM_BOOL);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

    public function isUserGestor($email) {
      $stmt = $this->conn->prepare("SELECT gestor FROM users WHERE email = :email");
      $stmt->bindParam(":email", $email);
      $stmt->execute();
  
      if($stmt->rowCount() > 0) {
          $data = $stmt->fetch();
          return $data['gestor'] == 1; // Retorna true se o usuário é gestor (gestor = 1)
      } else {
          // Usuário não encontrado
          return false;
      }
  }
    public function update(User $user) {

      $stmt = $this->conn->prepare("UPDATE users SET 
        name = :name,
        lastname = :lastname,
        email = :email,
        token = :token
        WHERE id = :id
      ");

      $stmt->bindParam(":name", $user->name);
      $stmt->bindParam(":lastname", $user->lastname);
      $stmt->bindParam(":email", $user->email);
      $stmt->bindParam(":token", $user->token);
      $stmt->bindParam(":id", $user->id);

      $stmt->execute();
        
      // Redireciona e apresenta mensagem de sucesso
      $this->message->setMessage("Dados atualizados com sucesso!", "success", "listar.php");
      
    }

    public function changePassword($user) {

      $stmt = $this->conn->prepare("UPDATE users SET 
        password = :password
        WHERE id = :id
      ");

      $stmt->bindParam(":password", $user->password);
      $stmt->bindParam(":id", $user->id);

      $stmt->execute();
        
      // Redireciona e apresenta mensagem de sucesso
      $this->message->setMessage("Senha atualizada!", "success", "listar.php");
      
    }

    public function findByToken($token) {

      $stmt = $this->conn->prepare("SELECT * FROM users WHERE token = :token");

      $stmt->bindParam(":token", $token);

      $stmt->execute();

      if($stmt->rowCount() > 0) {

        $data = $stmt->fetch();
        $user = $this->buildUser($data);

        return $user;

      } else {
        return false;
      }

    }

    public function findByEmail($email) {

      if($email != "") {

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        
        $stmt->bindParam(":email", $email);

        $stmt->execute();

        if($stmt->rowCount() > 0) {

          $data = $stmt->fetch();
          $user = $this->buildUser($data);
  
          return $user;

        } else {
          return false;
        }

      }

      return false;

    }

    public function setTokenToSession($token, $redirect = true) {

      // Save token to session
      $_SESSION["token"] = $token;

      if($redirect) {

        // Redireciona e apresenta mensagem de sucesso
        $this->message->setMessage("Seja bem-vindo!", "success", "listar.php");

      }

    }

    public function verifyToken($protected = true) {

      
      if(!empty($_SESSION["token"])) {

        // Pega o token da session
        $token = $_SESSION["token"];

        $user = $this->findByToken($token);

        if($user) {
          return $user;
        } else if($protected) {

          // Redireciona para home caso não haja usuário
          $this->message->setMessage("Faça a autenticação para acessar esta página.", "error", "index.php");

        }

      } else {
        return false;
      }

    }

    public function authenticateUser($email, $password) {
      $user = $this->findByEmail($email);
  
      // Checa se o usuário existe
      if ($user) {
          // Checa se a senha bate
          if (password_verify($password, $user->password)) {
              // Gera o token e coloca na session, sem redirecionar
              $token = $user->generateToken();
              $this->setTokenToSession($token, false);
  
              // Atualiza token do usuário
              $user->token = $token;
             
  
              // Armazena o nome do usuário na sessão
              $_SESSION['usuario_nome'] = $user->name;
  
              $this->update($user);
  
              return true;
          }
      }
  
      return false;
  }

    public function destroyToken() {

      // Remove o token
      $_SESSION["token"] = "";

      // Redireciona e apresenta mensagem de sucesso
      $this->message->setMessage("Você fez o logout com sucesso!", "success", "index.php");

    }

    public function findById($id) {

      if($id != "") {

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
        
        $stmt->bindParam(":id", $id);

        $stmt->execute();

        if($stmt->rowCount() > 0) {

          $data = $stmt->fetch();
          $user = $this->buildUser($data);
  
          return $user;

        } else {
          return false;
        }

      }

    }

  }