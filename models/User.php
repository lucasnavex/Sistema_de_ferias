<?php

    class User {

    public $id;
    public $nome;
    public $lastname;
    public $password;
    public $email;
    public $token;

    public function getFullName(User $user) {
        return $user->name . " " . $user->lastname;
    }

    public function generateToken() {
        return bin2hex(random_bytes(50));
      }
    }


    interface UserDAOInterface {

        public function buildUser($data);
        public function create(User $user, $authUser = false);
        public function update(User $user);
        public function findByToken($token);
        public function verifyToken($protected = true);
        public function setTokenToSession($token, $redirect = true);
        public function authenticateUser($email, $password);
        public function findByEmail($email);
        public function findById($id);
      
    }
