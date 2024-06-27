<?php
session_start();
include 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'];
    $email = $_SESSION['email'];
    if (verify_otp($email, $otp)) {
        $message = '';
        header('Location: secure_page.php');
        exit();
    } else {
        $message = 'Invalid OTP';
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <form class="border p-4 bg-light" method="post" action="verify_otp.php">
        <h4>Verify One-time Passcode</h4>
        <div class="mb-3 mt-3">
            <label for="otp" class="form-label">Code is sent to your email. Please enter it below.</label>
            <input type="text" class="form-control" id="otp" name="otp" placeholder="Passcode" required>
        </div>
        <?php if ($message) : ?>
            <div class="alert alert-danger small" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary w-100">Verify</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>