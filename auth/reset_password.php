<?php
require __DIR__ . '/../services/functions.php';

$message = '';
$password = '';
$repeat_password = '';
$reset_successful = false; // Flag to trigger modal

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_GET['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $repeat_password = $_POST['repeat_password'] ?? '';

    if (empty($token)) {
        $message = "Invalid or missing token.";
    } elseif (empty($password) || empty($repeat_password)) {
        $message = "Please fill in all fields.";
    } elseif ($password !== $repeat_password) {
        $message = "Passwords do not match.";
    } else {
        try {
            if (reset_password($token, $password)) {
                $reset_successful = true;
            } else {
                $message = "Invalid or expired link. Please try again.";
            }
        } catch (Exception $e) {
            $message = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <form class="form border p-4 bg-light" method="post" action="reset_password.php?token=<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
        <h4>Reset Password</h4>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>
        </div>
        <div class="mb-3">
            <label for="repeat_password" class="form-label">Repeat Password</label>
            <input type="password" class="form-control" id="repeat_password" name="repeat_password" value="<?php echo htmlspecialchars($repeat_password); ?>" required>
        </div>
        <?php if ($message && !$reset_successful) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary w-100">Reset</button>
    </form>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Password Reset Successful</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                You can now sign in.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="redirectLoginButton">Okay</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
    $(document).ready(function() {
        <?php if ($reset_successful) : ?>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();

            $('#redirectLoginButton').click(function() {
                window.location.href = '../auth/login.php';
            });
        <?php endif; ?>
    });
</script>