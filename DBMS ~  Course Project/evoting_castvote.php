<?php
define('EVOTING_SYSTEM', true);
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

// Check if voter is logged in
requireVoterLogin();

$voter_id = $_SESSION['voter_id'];
$voter_details = getVoterDetails($voter_id);

// Get active elections for voter's constituency
$elections = getActiveElections($voter_details['constituency_id']);

$error = '';
$success = '';
$candidates = [];
$selected_election = null;

// Handle election selection
if (isset($_GET['election_id'])) {
    $election_id = intval($_GET['election_id']);
    
    // Check if already voted
    if (hasVoted($voter_id, $election_id)) {
        $error = 'You have already cast your vote in this election.';
    } else {
        $candidates = getCandidates($election_id, $voter_details['constituency_id']);
        $selected_election = $election_id;
    }
}

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_vote'])) {
    $candidate_id = intval($_POST['candidate_id']);
    $election_id = intval($_POST['election_id']);
    $confirmation = isset($_POST['confirmation']);
    
    if (!$confirmation) {
        $error = 'Please confirm that you want to cast your vote.';
    } else {
        $result = castVote($voter_id, $candidate_id, $election_id, $voter_details['constituency_id']);
        
        if ($result['success']) {
            $success = $result['message'];
            setFlashMessage('success', 'Your vote has been successfully recorded! Thank you for participating in democracy.');
            header('Location: voter-dashboard.php');
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cast Your Vote - E-Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .candidate-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .candidate-card.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }
        .party-symbol {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-vote-yea"></i> Indian E-Voting System
            </a>
            <div class="ms-auto">
                <span class="navbar-text text-white me-3">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($voter_details['voter_name']); ?>
                </span>
                <a href="voter-dashboard.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Voter Information -->
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-id-card"></i> Voter Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($voter_details['voter_name']); ?></p>
                        <p><strong>EPIC No:</strong> <?php echo htmlspecialchars($voter_details['voter_epic_no']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Constituency:</strong> <?php echo htmlspecialchars($voter_details['constituency_name']); ?></p>
                        <p><strong>State:</strong> <?php echo htmlspecialchars($voter_details['state_name']); ?></p>
                    </div>
                </div>
            </div>
        </div>

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

        <?php if (!$selected_election): ?>
            <!-- Election Selection -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-alt"></i> Select Election</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($elections)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No active elections available at the moment.
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($elections as $election): ?>
                                <a href="?election_id=<?php echo $election['election_id']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($election['election_name']); ?></h5>
                                        <span class="badge bg-success">Active</span>
                                    </div>
                                    <p class="mb-1">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo formatDate($election['start_date']); ?> to 
                                        <?php echo formatDate($election['end_date']); ?>
                                    </p>
                                    <small class="text-muted">
                                        Type: <?php echo htmlspecialchars($election['election_type']); ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Candidate Selection -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-vote-yea"></i> Cast Your Vote</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($candidates)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle"></i> No candidates available for this election.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-4">
                            <strong><i class="fas fa-exclamation-triangle"></i> Important:</strong>
                            Please review your choice carefully. Once submitted, your vote cannot be changed.
                        </div>

                        <form method="POST" action="" id="voteForm">
                            <input type="hidden" name="election_id" value="<?php echo $selected_election; ?>">
                            
                            <div class="row">
                                <?php foreach ($candidates as $candidate): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card candidate-card h-100" onclick="selectCandidate(<?php echo $candidate['candidate_id']; ?>)">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input candidate-radio" 
                                                           type="radio" 
                                                           name="candidate_id" 
                                                           id="candidate_<?php echo $candidate['candidate_id']; ?>"
                                                           value="<?php echo $candidate['candidate_id']; ?>" 
                                                           required>
                                                    <label class="form-check-label w-100" for="candidate_<?php echo $candidate['candidate_id']; ?>">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                <i class="fas fa-user-circle fa-3x text-primary"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h5 class="mb-1"><?php echo htmlspecialchars($candidate['candidate_name']); ?></h5>
                                                                <p class="mb-0 text-muted">
                                                                    <?php echo $candidate['age']; ?> years, 
                                                                    <?php echo $candidate['gender']; ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="party-info bg-light p-3 rounded">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    <i class="fas fa-flag fa-2x text-warning"></i>
                                                                </div>
                                                                <div>
                                                                    <strong><?php echo htmlspecialchars($candidate['party_name']); ?></strong><br>
                                                                    <small class="text-muted">Symbol: <?php echo htmlspecialchars($candidate['party_symbol']); ?></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <?php if ($candidate['educational_qualification']): ?>
                                                            <p class="mt-2 mb-0 small">
                                                                <i class="fas fa-graduation-cap"></i> 
                                                                <?php echo htmlspecialchars($candidate['educational_qualification']); ?>
                                                            </p>
                                                        <?php endif; ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <hr class="my-4">

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="confirmation" name="confirmation" required>
                                <label class="form-check-label" for="confirmation">
                                    <strong>I confirm that I have made my choice and I want to cast my vote. 
                                    I understand that this action cannot be undone.</strong>
                                </label>
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" name="submit_vote" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle"></i> Submit Vote
                                </button>
                                <a href="voter-dashboard.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectCandidate(candidateId) {
            // Remove all selected classes
            document.querySelectorAll('.candidate-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            const radio = document.getElementById('candidate_' + candidateId);
            radio.checked = true;
            radio.closest('.candidate-card').classList.add('selected');
        }

        // Confirm before submission
        document.getElementById('voteForm')?.addEventListener('submit', function(e) {
            if (!confirm('Are you absolutely sure you want to submit your vote? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>