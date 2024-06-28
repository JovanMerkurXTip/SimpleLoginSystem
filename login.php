<?php
session_start();
include 'functions.php';

if (isset($_SESSION['email']) && isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'] === true) {
    header("Location: secure_page.php");
    exit();
} else if (isset($_SESSION['otp_email'])) {
    header("Location: verify_otp.php");
    exit();
}

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
            $_SESSION['otp_email'] = $email;
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
    <form class="form border p-4 bg-light" method="post" action="login.php" onsubmit="showLoader()">
        <h4>Sign In</h4>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>
        </div>
        <?php if ($message && !$otp_sent) : ?>
            <div class="alert alert-danger small" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary w-100" id="loginButton">Sign in</button>
        <div class="d-flex justify-content-between mt-3">
            <a href="register.php">Create an Account</a>
            <a href="forgot_password.php">Forgot password?</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Include FontAwesome or any other icon library for the loader -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script>
    function showLoader() {
        const loginButton = document.getElementById('loginButton');
        loginButton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';
        loginButton.disabled = true;
    }
</script>
