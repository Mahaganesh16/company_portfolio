<?php
session_start();

// In a real application, use a database and password_hash
$valid_username = 'admin';
$valid_password = 'password123';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user === $valid_username && $pass === $valid_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: ../admin');
        exit;
    } else {
        header('Location: login?error=1');
        exit;
    }
} else {
    header('Location: login');
    exit;
}
