<?php
include 'functions.php';

$message = '';
$email = '';
$password = '';
$repeat_password = '';
$registration_successful = false; // Flag to trigger modal

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    if (!empty($email) && !empty($password) && !empty($repeat_password)) {
        if ($password === $repeat_password) {
            try {
                register_user($email, $password);
                $message = "Registration successful. You can now login.";
                $registration_successful = true;
            } catch (Exception $e) {
                if ($e->getCode() === 1062) {
                    $message = "Email already exists.";
                } else {
                    $message = $e->getMessage();
                }
            }
        } else {
            $message = "Passwords do not match.";
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <form class="border p-4 bg-light" method="post" action="register.php">
        <h4>Create an Account</h4>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>
        </div>
        <div class="mb-3">
            <label for="repeat_password" class="form-label">Repeat Password</label>
            <input type="password" class="form-control" id="repeat_password" name="repeat_password" value="<?php echo htmlspecialchars($repeat_password); ?>" required>
        </div>
        <?php if ($message && !$registration_successful) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary w-100">Register</button>
        <div class="d-flex justify-content-between mt-3">
            <a href="login.php">Go to Login</a>
        </div>
    </form>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Registration Successful</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Registration successful. You can now login.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="redirectLoginButton">OK</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    $(document).ready(function() {
        <?php if ($registration_successful) : ?>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();

            $('#redirectLoginButton').click(function() {
                window.location.href = 'login.php';
            });
        <?php endif; ?>
    });
</script>