<?php

define('BASE_URL', 'http://localhost/');

$server_name = "localhost";
$username = "root";
$password = "";
$db_name = "simplelogin";

$conn = new mysqli($server_name, $username, $password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
