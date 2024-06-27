<?php
session_start();
include 'functions.php';

$message = '';
$email = '';
$password = '';
$otp_sent = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $user_id = authenticate_user($email, $password);
        if (!$user_id) {
            $message = "Invalid email or password.";
            $password = '';
        } else {
            $_SESSION['email'] = $email;
            if (send_otp($user_id)) {
                header('Location: verify_otp.php');
                exit();
            }
        }
    } catch (Exception $e) {
        $message = "An error occurred: " . $e->getMessage();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <form class="border p-4 bg-light" method="post" action="login.php">
        <h4>Login</h4>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="<?php echo htmlspecialchars($password); ?>" required>
        </div>
        <?php if ($message && !$otp_sent) : ?>
            <div class="alert alert-danger small" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>