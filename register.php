<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        register_user($email, $password);
        echo "Registration successful. Please check your email for the OTP.";
    } else {
        echo "Please fill in all fields.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<form method="post" action="register.php">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>

<?php include 'includes/footer.php'; ?>