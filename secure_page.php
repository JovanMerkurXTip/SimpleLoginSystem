<?php
include 'auth.php';
?>

<!-- This is the page that's secured behind login. It will be first thing user can see after login, like a dashboard or something similar. -->

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-center align-items-center vh-100">

    <div class="form border p-4 bg-light">
        <h4>You're now logged in!</h4>

        <div class="d-flex justify-content-between mt-3">
            <a href="logout.php" class="btn btn-outline-primary w-100">Logout</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>