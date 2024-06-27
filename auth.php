<?php
include 'functions.php';

session_start();

check_remember_token();

if (!(isset($_SESSION['email']) && isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'] === true)) {
    header("Location: login.php");
    exit();
}
