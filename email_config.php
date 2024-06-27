<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

return [
    'smtp' => [
        'host' => 'smtp.office365.com',
        'username' => 'alertwebapp@outlook.com',
        'password' => 'Jejtag-togmi2-pirwoz',
        'port' => 587,
        'encryption' => PHPMailer::ENCRYPTION_STARTTLS,
        'from_email' => 'alertwebapp@outlook.com',
        'from_name' => 'MerkurXtip EURO 2024',
        'charset' => 'UTF-8',
    ],
    'reset_link_base_url' => 'http://localhost/reset_password.php',
    'admin_email' => 'j.dozic@merkurxtip.rs'
];
