<?php
include 'functions.php';

$message = '';  // Initialize the message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user_id = authenticate_user($email, $password);
    if ($user_id) {
        send_otp($user_id);
        $message = "OTP sent to your email.";
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <form class="border p-4 bg-light" method="post" action="login.php">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        </div>
        <?php if ($message): ?>
            <div class="alert alert-danger small" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
