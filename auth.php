<?php
include 'functions.php';

session_start();

check_remember_token();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
