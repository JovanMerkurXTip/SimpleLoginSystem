<?php

include 'config.php';

function generate_salt($length = 16)
{
    return bin2hex(random_bytes($length));
}

function hash_password($password, $salt)
{
    return hash('sha256', $password . $salt);
}

function register_user($email, $password)
{
    global $conn;

    $salt = generate_salt();
    $hashed_password = hash_password($password, $salt);

    try {
        $stmt = $conn->prepare("INSERT INTO users (email, password, salt) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("sss", $email, $hashed_password, $salt);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        throw $e;
    }
}


function authenticate_user($email, $password)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, password, salt FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $db_password, $salt);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id || $db_password !== hash_password($password, $salt)) {
        // echo "Invalid email or password\n";
        return false; // TODO: Handle this case
    }

    return $user_id;
}

function send_otp($user_id)
{
    global $conn;

    try {
        $otp = rand(100000, 999999);
        $otp_expiration = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $stmt = $conn->prepare("INSERT INTO login_otp_codes (user_id, otp, otp_expiration) VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE otp = ?, otp_expiration = ?");
        $stmt->bind_param("issss", $user_id, $otp, $otp_expiration, $otp, $otp_expiration);
        $stmt->execute();
        $stmt->close();

        // TODO: Send OTP to user's email
        return true;
    } catch (Exception $e) {
        // throw $e;
        return false;
    }
}

// Returns null if there is no errors - so its successful if it returns null
function verify_otp($email, $otp)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        return "User not found.";
    }

    $stmt = $conn->prepare("SELECT otp, otp_expiration FROM login_otp_codes WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_otp, $otp_expiration);
    $stmt->fetch();
    $stmt->close();

    if (!($db_otp === $otp && strtotime($otp_expiration) > time())) {
        return "Invalid or expired passcode.";
    }

    session_start();
    $_SESSION['email'] = $email;
    $_SESSION['otp_email'] = null;
    $_SESSION['is_authenticated'] = true;

    $stmt = $conn->prepare("DELETE FROM login_otp_codes WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    return null;
}

function set_remember_token($email)
{
    global $conn;

    $token = bin2hex(random_bytes(32));

    $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE email = ?");
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();
    $stmt->close();

    setcookie("remember_token", $token, time() + 60 * 60 * 24 * 30, "/"); // 30 days
}

function check_remember_token()
{
    global $conn;

    if (!isset($_COOKIE['remember_token'])) {
        echo "<script>console.log('No remember token found.');</script>";
        return false; // TODO: Handle this case
    }

    $token = $_COOKIE['remember_token'];
    $stmt = $conn->prepare("SELECT email FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    if (!$email) {
        echo "<script>console.log('Invalid remember token.');</script>";
        return false; // TODO: Handle this case
    }

    session_start();
    $_SESSION['email'] = $email;
}

function generate_reset_link($email)
{
    global $conn;

    $token = bin2hex(random_bytes(32));
    $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $reset_link = BASE_URL . "reset_password.php?token=$token";

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        echo "User not found\n";
        return false; // TODO: Handle this case
    }

    $stmt = $conn->prepare("INSERT INTO password_reset_tokens (user_id, token, expiration) VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE token = ?, expiration = ?");
    $stmt->bind_param("issss", $user_id, $token, $expiration, $token, $expiration);
    $stmt->execute();
    $stmt->close();

    // TODO: Send reset link to user's email
    echo "RESET LINK: $reset_link\n";
}

function reset_password($token, $new_password)
{
    global $conn;

    $stmt = $conn->prepare("SELECT user_id FROM password_reset_tokens WHERE token = ? AND expiration > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        echo "Invalid or expired token\n";
        return false; // TODO: Handle this case
    }

    $stmt = $conn->prepare("SELECT email, salt FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($email, $salt);
    $stmt->fetch();
    $stmt->close();

    if (!$email) {
        echo "User not found\n";
        return false; // TODO: Handle this case
    }

    $hashed_password = hash_password($new_password, $salt);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM password_reset_tokens WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    echo "Password reset successfully\n";
    // TODO: Redirect to login page
}
