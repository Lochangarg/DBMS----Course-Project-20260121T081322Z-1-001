<?php
define('EVOTING_SYSTEM', true);
require_once 'includes/config.php';
require_once 'includes/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indian E-Voting System - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-vote-yea"></i> Indian E-Voting System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="candidate-list.php">Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="results.php">Results</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-primary ms-2" href="voter-login.php">Voter Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light ms-2" href="admin-login.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section bg-gradient text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="fas fa-landmark"></i> Democracy at Your Fingertips
                    </h1>
                    <p class="lead mb-4">
                        Participate in the world's largest democracy through our secure, transparent, 
                        and accessible electronic voting system.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="voter-registration.php" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus"></i> Register as Voter
                        </a>
                        <a href="voter-login.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Cast Your Vote
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-vote-yea fa-10x opacity-75"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="stat-card p-4 bg-white rounded shadow-sm">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold">
                            <?php
                            $db = getDB();
                            $result = $db->query("SELECT COUNT(*) as count FROM voters WHERE voter_status = 'Active'");
                            $count = $result->fetch_assoc()['count'];
                            echo number_format($count);
                            ?>
                        </h3>
                        <p class="text-muted">Registered Voters</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card p-4 bg-white rounded shadow-sm">
                        <i class="fas fa-map-marker-alt fa-3x text-success mb-3"></i>
                        <h3 class="fw-bold">
                            <?php
                            $result = $db->query("SELECT COUNT(*) as count FROM constituencies");
                            $count = $result->fetch_assoc()['count'];
                            echo number_format($count);
                            ?>
                        </h3>
                        <p class="text-muted">Constituencies</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card p-4 bg-white rounded shadow-sm">
                        <i class="fas fa-user-tie fa-3x text-warning mb-3"></i>
                        <h3 class="fw-bold">
                            <?php
                            $result = $db->query("SELECT COUNT(*) as count FROM candidates WHERE candidate_status = 'Active'");
                            $count = $result->fetch_assoc()['count'];
                            echo number_format($count);
                            ?>
                        </h3>
                        <p class="text-muted">Candidates</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card p-4 bg-white rounded shadow-sm">
                        <i class="fas fa-calendar-check fa-3x text-danger mb-3"></i>
                        <h3 class="fw-bold">
                            <?php
                            $result = $db->query("SELECT COUNT(*) as count FROM elections");
                            $count = $result->fetch_assoc()['count'];
                            echo number_format($count);
                            ?>
                        </h3>
                        <p class="text-muted">Elections</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5">
        <div class="container">
            <h2 class="text-center mb-5 fw-bold">Why Choose E-Voting?</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center p-4">
                        <i class="fas fa-shield-alt fa-4x text-primary mb-3"></i>
                        <h4>Secure & Private</h4>
                        <p class="text-muted">
                            Advanced encryption and security measures ensure your vote remains confidential 
                            and tamper-proof.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center p-4">
                        <i class="fas fa-mobile-alt fa-4x text-success mb-3"></i>
                        <h4>Accessible</h4>
                        <p class="text-muted">
                            Vote from anywhere, anytime during the election period. No need to visit polling 
                            booths.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center p-4">
                        <i class="fas fa-tachometer-alt fa-4x text-warning mb-3"></i>
                        <h4>Fast Results</h4>
                        <p class="text-muted">
                            Electronic counting ensures quick and accurate results, reducing wait time 
                            significantly.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center p-4">
                        <i class="fas fa-check-circle fa-4x text-info mb-3"></i>
                        <h4>Transparent</h4>
                        <p class="text-muted">
                            Complete audit trail and verification mechanisms ensure transparency in the 
                            electoral process.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center p-4">
                        <i class="fas fa-leaf fa-4x text-success mb-3"></i>
                        <h4>Eco-Friendly</h4>
                        <p class="text-muted">
                            Paperless voting reduces environmental impact and promotes sustainable democracy.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center p-4">
                        <i class="fas fa-clock fa-4x text-danger mb-3"></i>
                        <h4>Time-Saving</h4>
                        <p class="text-muted">
                            No long queues or waiting times. Cast your vote in minutes from your home.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5 fw-bold">How It Works</h2>
            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <h2 class="mb-0">1</h2>
                    </div>
                    <h5>Register</h5>
                    <p class="text-muted">Create your voter account with valid documents</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <h2 class="mb-0">2</h2>
                    </div>
                    <h5>Verify</h5>
                    <p class="text-muted">Get your EPIC number verified by election officials</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <h2 class="mb-0">3</h2>
                    </div>
                    <h5>Login</h5>
                    <p class="text-muted">Login with your EPIC number and password</p>
                </div>
                <div class="col-md-3 text-center mb-4">
                    <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <h2 class="mb-0">4</h2>
                    </div>
                    <h5>Vote</h5>
                    <p class="text-muted">Cast your vote for your preferred candidate</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Indian E-Voting System</h5>
                    <p class="text-muted">
                        Empowering democracy through technology. Secure, transparent, and accessible 
                        elections for all.
                    </p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-muted">About Us</a></li>
                        <li><a href="candidate-list.php" class="text-muted">Candidates</a></li>
                        <li><a href="results.php" class="text-muted">Results</a></li>
                        <li><a href="#" class="text-muted">FAQs</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact</h5>
                    <p class="text-muted">
                        <i class="fas fa-envelope"></i> support@evoting.gov.in<br>
                        <i class="fas fa-phone"></i> 1800-XXX-XXXX
                    </p>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center text-muted">
                <p>&copy; 2025 Indian E-Voting System. All Rights Reserved. | A DBMS Course Project</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>