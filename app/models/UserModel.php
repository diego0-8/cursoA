<?php
// app/models/UserModel.php

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtiene un usuario por su correo electrónico.
    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Almacena el token de reseteo de contraseña en la base de datos.
    public function storePasswordResetToken($userId, $token, $expires) {
        // Primero, elimina cualquier token existente para ese usuario.
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        // Inserta el nuevo token.
        $stmt = $this->pdo->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $token, $expires]);
    }

    // Obtiene los datos asociados a un token de reseteo.
    public function getResetTokenData($token) {
        $stmt = $this->pdo->prepare("SELECT * FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    // Actualiza la contraseña de un usuario.
    public function updatePassword($userId, $password) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->execute([$password, $userId]);
    }
    
    // Elimina un token de la base de datos.
    public function deleteResetToken($token) {
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
    }
}
