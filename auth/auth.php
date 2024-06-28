<?php
require __DIR__ . '/../services/functions.php';

session_start();

check_remember_token();

if (!(isset($_SESSION['email']) && isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'] === true)) {
    header("Location: ../auth/login.php");
    exit();
}
