<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    verify_otp($email, $otp);
}
?>

<?php include 'includes/header.php'; ?>

<form method="post" action="verify_otp.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="otp" placeholder="OTP" required>
        <button type="submit">Verify OTP</button>
    </form>

<?php include 'includes/footer.php'; ?>