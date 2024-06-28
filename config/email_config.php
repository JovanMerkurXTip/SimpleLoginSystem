<?php

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

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
    'admin_email' => 'j.dozic@merkurxtip.rs'
];
