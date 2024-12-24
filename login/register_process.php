<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validaciones
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Por favor complete todos los campos";
        include 'register.php';
        exit();
    }
    
    if ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
        include 'register.php';
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor ingrese un email válido";
        include 'register.php';
        exit();
    }
    
    try {
        // Verificar si el usuario ya existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error = "El usuario o email ya está registrado";
            include 'register.php';
            exit();
        }
        
        // Encriptar contraseña y guardar usuario
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        
        header("Location: index.php?registered=true");
        exit();
        
    } catch(PDOException $e) {
        $error = "Error en el sistema. Por favor intente más tarde";
        include 'register.php';
        exit();
    }
}