<?php
require __DIR__ . '/../services/functions.php';

$message = '';
$verify_successful = false; // Flag to trigger modal

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $message = "Invalid or missing token.";
} else {
    try {
        if (verify_account($token)) {
            $verify_successful = true;
        } else {
            $message = "Invalid or expired link. Please try again.";
        }
    } catch (Exception $e) {
        $message = "An error occurred: " . $e->getMessage();
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <form class="form border p-4 bg-light" method="post" action="verify_account.php?token=<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
        <h4>Verify Account</h4>
        <?php if ($message && !$verify_successful) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($verify_successful) : ?>
            <div class="alert alert-success" role="alert">
                Account verified successfully. You can now sign in.
            </div>
            <a href="../auth/login.php" class="btn btn-outline-success w-100">Go to Login</a>
        <?php endif; ?>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>