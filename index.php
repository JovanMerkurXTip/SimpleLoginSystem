<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="form border p-4 bg-light" method="post" action="login.php" onsubmit="showLoader()">
        <h4>Welcome</h4>
        <a href="login.php" class="btn btn-outline-secondary w-100 mb-3 mt-3">Sign in</a>
        <a href="register.php" class="btn btn-outline-primary w-100 mb-3">Create an Account</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>