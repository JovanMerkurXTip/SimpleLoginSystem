<?php include 'includes/header.php'; ?>

<form method="post" action="reset_password.php">
    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
    <input type="password" name="new_password" placeholder="New Password" required>
    <button type="submit">Reset Password</button>
</form>

<?php include 'includes/footer.php'; ?>
