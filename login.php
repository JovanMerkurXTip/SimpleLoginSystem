<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user_id = authenticate_user($email, $password);
    if ($user_id) {
        send_otp($user_id);
        echo "OTP sent to your email.";
    } else {
        echo "Invalid email or password.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<form method="post" action="login.php">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>

<?php include 'includes/footer.php'; ?>