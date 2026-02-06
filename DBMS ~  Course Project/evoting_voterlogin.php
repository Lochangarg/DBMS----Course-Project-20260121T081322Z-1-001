<?php
define('EVOTING_SYSTEM', true);
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isVoterLoggedIn()) {
    header('Location: voter-dashboard.php');
    exit();
}

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $epic_no = sanitizeInput($_POST['epic_no']);
    $password = $_POST['password'];
    
    if (empty($epic_no) || empty($password)) {
        $error = 'Please enter both EPIC number and password.';
    } else {
        $result = validateVoterLogin($epic_no, $password);
        
        if ($result['success']) {
            initVoterSession($result['voter_id'], $result['voter_name']);
            logAudit('Voter', $result['voter_id'], 'LOGIN', 'Voter logged in successfully');
            header('Location: voter-dashboard.php');
            exit();
        } else {
            $error = $result['message'];
            logAudit('Voter', 0, 'LOGIN_FAILED', 'Failed login attempt for EPIC: ' . $epic_no);
        }
    }
}

// Check for session expired
if (isset($_GET['expired'])) {
    $error = 'Your session has expired. Please login again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Login - E-Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-vote-yea"></i> Indian E-Voting System
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-light">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <i class="fas fa-user-circle fa-3x mb-2"></i>
                        <h3 class="mb-0">Voter Login</h3>
                    </div>
                    <div class="card-body p-5">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="epic_no" class="form-label fw-bold">
                                    <i class="fas fa-id-card"></i> EPIC Number
                                </label>
                                <input type="text" class="form-control form-control-lg" 
                                       id="epic_no" name="epic_no" 
                                       placeholder="Enter your EPIC number" 
                                       required maxlength="20">
                                <small class="form-text text-muted">
                                    Electoral Photo Identity Card Number (e.g., MH0120240001)
                                </small>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <input type="password" class="form-control form-control-lg" 
                                       id="password" name="password" 
                                       placeholder="Enter your password" 
                                       required>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>

                            <div class="text-center">
                                <a href="#" class="text-decoration-none">Forgot Password?</a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center bg-light">
                        <p class="mb-0">
                            Don't have an account? 
                            <a href="voter-registration.php" class="fw-bold">Register Now</a>
                        </p>
                    </div>
                </div>

                <!-- Information Box -->
                <div class="alert alert-info mt-4" role="alert">
                    <h5 class="alert-heading">
                        <i class="fas fa-info-circle"></i> Demo Credentials
                    </h5>
                    <hr>
                    <p class="mb-0">
                        <strong>EPIC Number:</strong> MH0120240001<br>
                        <strong>Password:</strong> voter@123
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>