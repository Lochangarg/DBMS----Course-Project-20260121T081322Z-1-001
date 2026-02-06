<?php
/**
 * E-Voting System - Session Management
 * File: includes/session.php
 */

if (!defined('EVOTING_SYSTEM')) {
    define('EVOTING_SYSTEM', true);
}

// Start session with secure settings
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    session_start();
}

/**
 * Initialize voter session
 */
function initVoterSession($voter_id, $voter_name) {
    $_SESSION['voter_logged_in'] = true;
    $_SESSION['voter_id'] = $voter_id;
    $_SESSION['voter_name'] = $voter_name;
    $_SESSION['user_type'] = 'voter';
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    
    // Regenerate session ID for security
    session_regenerate_id(true);
}

/**
 * Initialize admin session
 */
function initAdminSession($admin_id, $admin_name, $admin_role) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $admin_id;
    $_SESSION['admin_name'] = $admin_name;
    $_SESSION['admin_role'] = $admin_role;
    $_SESSION['user_type'] = 'admin';
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    
    // Regenerate session ID for security
    session_regenerate_id(true);
}

/**
 * Check if voter is logged in
 */
function isVoterLoggedIn() {
    return isset($_SESSION['voter_logged_in']) && $_SESSION['voter_logged_in'] === true;
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Check session timeout
 */
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        
        if ($inactive > SESSION_TIMEOUT) {
            destroySession();
            return false;
        }
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Require voter login
 */
function requireVoterLogin() {
    if (!isVoterLoggedIn() || !checkSessionTimeout()) {
        header('Location: voter-login.php?expired=1');
        exit();
    }
}

/**
 * Require admin login
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn() || !checkSessionTimeout()) {
        header('Location: admin-login.php?expired=1');
        exit();
    }
}

/**
 * Destroy session
 */
function destroySession() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Logout user
 */
function logout($redirect = 'index.php') {
    destroySession();
    header('Location: ' . $redirect);
    exit();
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Display flash message HTML
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $alertClass = $flash['type'] == 'success' ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}
?>