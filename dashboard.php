<?php
include 'auth/auth.php';
?>

<!-- This is the page that's secured behind login. It will be first thing user can see after login, like a dashboard or something similar. -->

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">

    <div class="form border p-4 bg-light">
        <h4>Dashboard</h4>
        <p>You're logged in! Otherwise, you wouldn't be able to see this page.</p>

        <div class="d-flex justify-content-between mt-3">
            <a href="auth/logout.php" class="btn btn-outline-primary w-100">Logout</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>