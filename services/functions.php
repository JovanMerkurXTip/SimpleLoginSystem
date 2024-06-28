<?php

include __DIR__ . '\..\config\config.php';
include __DIR__ . '\..\services\email_service.php';

function echo_console_log($message)
{
    echo "<script>console.log('$message');</script>";
}

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
        $is_verified = 0;
        $stmt = $conn->prepare("INSERT INTO users (email, password, salt, is_verified) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("sssi", $email, $hashed_password, $salt, $is_verified);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();

        if (!generate_registration_verification_link($email)) {

            $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();

            throw new Exception("Failed to send verification email.");
        }
    } catch (Exception $e) {
        $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
        throw $e;
    }
}

function generate_registration_verification_link($email)
{
    global $conn;

    try {
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+1 day'));
        $verification_link = BASE_URL . "auth/verify_account.php?token=$token";

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        if (!$user_id) {
            echo_console_log("User not found.");
            return false;
        }

        $stmt = $conn->prepare("INSERT INTO registration_verification_tokens (user_id, token, expiration) VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE token = ?, expiration = ?");
        $stmt->bind_param("issss", $user_id, $token, $expiration, $token, $expiration);
        $stmt->execute();
        $stmt->close();

        if (!send_verify_account_link($email, $verification_link)) {
            echo_console_log("Failed to send verification email.");
            return false;
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function verify_account($token)
{
    global $conn;

    $stmt = $conn->prepare("SELECT user_id FROM registration_verification_tokens WHERE token = ? AND expiration > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        echo_console_log("Invalid or expired token.");
        return false;
    }

    $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM registration_verification_tokens WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    echo_console_log("Account verified successfully.");
    return true;
}




function authenticate_user($email, $password)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, password, salt, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $db_password, $salt, $is_verified);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        echo_console_log("User not found.");
        return false;
    }

    if (!$is_verified) {
        echo_console_log("Account not verified.");
        return false;
    }

    if (!$user_id || $db_password !== hash_password($password, $salt)) {
        echo_console_log("Invalid email or password.");
        return false;
    }

    return $user_id;
}

function send_otp($user_id)
{
    global $conn;

    try {
        $otp = rand(100000, 999999);
        $expiration = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $stmt = $conn->prepare("INSERT INTO login_otp_codes (user_id, otp, expiration) VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE otp = ?, expiration = ?");
        $stmt->bind_param("issss", $user_id, $otp, $expiration, $otp, $expiration);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();

        if (!send_otp_email($email, $otp)) {
            return false;
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

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

    $stmt = $conn->prepare("SELECT otp, expiration FROM login_otp_codes WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_otp, $expiration);
    $stmt->fetch();
    $stmt->close();

    if (!($db_otp === $otp && strtotime($expiration) > time())) {
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
    // Returns null if there is no errors - so its successful if it returns null
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
        echo_console_log("No remember token found.");
        return false;
    }

    $token = $_COOKIE['remember_token'];
    $stmt = $conn->prepare("SELECT email FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    if (!$email) {
        echo_console_log("Invalid remember token.");
        return false;
    }

    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['email'] = $email;
    $_SESSION['is_authenticated'] = true;
    return true;
}

function generate_reset_link($email)
{
    global $conn;

    try {
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $reset_link = BASE_URL . "auth/reset_password.php?token=$token";

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        if (!$user_id) {
            return false;
        }

        $stmt = $conn->prepare("INSERT INTO password_reset_tokens (user_id, token, expiration) VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE token = ?, expiration = ?");
        $stmt->bind_param("issss", $user_id, $token, $expiration, $token, $expiration);
        $stmt->execute();
        $stmt->close();

        if (!send_reset_password_link($email, $reset_link)) {
            return false;
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function reset_password($token, $new_password)
{
    global $conn;

    try {
        $stmt = $conn->prepare("SELECT user_id FROM password_reset_tokens WHERE token = ? AND expiration > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        if (!$user_id) {
            echo_console_log("Invalid or expired token.");
            return false;
        }

        $stmt = $conn->prepare("SELECT email, salt FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($email, $salt);
        $stmt->fetch();
        $stmt->close();

        if (!$email) {
            echo_console_log("User not found.");
            return false;
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

        echo_console_log("Password reset successfully.");
        return true;
    } catch (Exception $e) {
        return false;
    }
}
