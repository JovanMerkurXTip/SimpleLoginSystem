<?php
session_start();
include 'functions.php';

if (check_remember_token()) {
    header("Location: secure_page.php");
    exit();
} else if (isset($_SESSION['email']) && isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'] === true) {
    header("Location: secure_page.php");
    exit();
} else if (isset($_SESSION['otp_email'])) {
    header("Location: verify_otp.php");
    exit();
}

$message = '';
$email = '';
$link_sent = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    try {
        if (generate_reset_link($email)) {
            $link_sent = true;
        }
    } catch (Exception $e) {
        $message = "An error occurred: " . $e->getMessage();
    }
}

?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <form class="form border p-4 bg-light" method="post" action="forgot_password.php" onsubmit="showLoader()">
        <h4>Forgot Password</h4>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <?php if ($message && !$otp_sent) : ?>
            <div class="alert alert-danger small" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary w-100" id="sendLinkButton">Send Link</button>
        <div class="d-flex justify-content-between mt-3">
            <a href="login.php">Back to Sign in</a>
        </div>
    </form>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Reset link sent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Password reset link sent. Please check your email.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="redirectHomeButton">Okay</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    $(document).ready(function() {
        <?php if ($link_sent) : ?>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();

            $('#redirectHomeButton').click(function() {
                window.location.href = 'index.php';
            });
        <?php endif; ?>
    });

    function showLoader() {
        const sendLinkButton = document.getElementById('sendLinkButton');
        sendLinkButton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';
        sendLinkButton.disabled = true;
    }
</script>