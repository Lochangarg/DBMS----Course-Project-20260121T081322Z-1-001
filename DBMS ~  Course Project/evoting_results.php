<?php
define('EVOTING_SYSTEM', true);
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

$db = getDB();

// Get all completed elections
$completed_elections = $db->query("SELECT * FROM elections 
                                   WHERE election_status = 'Completed' 
                                   ORDER BY election_year DESC");

// Get selected election
$selected_election = isset($_GET['election_id']) ? intval($_GET['election_id']) : null;
$selected_constituency = isset($_GET['constituency_id']) ? intval($_GET['constituency_id']) : null;

$results = [];
$election_details = null;
$constituencies = [];

if ($selected_election) {
    // Get election details
    $stmt = $db->prepare("SELECT * FROM elections WHERE election_id = ?");
    $stmt->bind_param("i", $selected_election);
    $stmt->execute();
    $election_details = $stmt->get_result()->fetch_assoc();
    
    // Get constituencies for this election
    $constituencies_query = "SELECT DISTINCT c.constituency_id, c.constituency_name 
                             FROM constituencies c 
                             JOIN candidates ca ON c.constituency_id = ca.constituency_id 
                             WHERE ca.election_id = ? 
                             ORDER BY c.constituency_name";
    $stmt = $db->prepare($constituencies_query);
    $stmt->bind_param("i", $selected_election);
    $stmt->execute();
    $constituencies = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get results
    if ($selected_constituency) {
        $results = getResults($selected_election, $selected_constituency);
    } else {
        $results = getResults($selected_election);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results - E-Voting System</title>
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="candidate-list.php">Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="results.php">Results</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-4 fw-bold text-center mb-2">
                    <i class="fas fa-chart-bar text-primary"></i> Election Results
                </h1>
                <p class="text-center text-muted">View detailed results of completed elections</p>
            </div>
        </div>

        <!-- Election Selector -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-6">
                        <label for="election_id" class="form-label fw-bold">
                            <i class="fas fa-calendar-alt"></i> Select Election
                        </label>
                        <select name="election_id" id="election_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">-- Choose Election --</option>
                            <?php while ($election = $completed_elections->fetch_assoc()): ?>
                                <option value="<?php echo $election['election_id']; ?>" 
                                        <?php echo ($selected_election == $election['election_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($election['election_name']); ?> 
                                    (<?php echo $election['election_year']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <?php if ($selected_election && !empty($constituencies)): ?>
                        <div class="col-md-6">
                            <label for="constituency_id" class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt"></i> Filter by Constituency
                            </label>
                            <select name="constituency_id" id="constituency_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Constituencies</option>
                                <?php foreach ($constituencies as $constituency): ?>
                                    <option value="<?php echo $constituency['constituency_id']; ?>"
                                            <?php echo ($selected_constituency == $constituency['constituency_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($constituency['constituency_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if ($election_details): ?>
            <!-- Election Details -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> 
                        <?php echo htmlspecialchars($election_details['election_name']); ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p class="mb-1"><strong>Election Type:</strong></p>
                            <p><?php echo htmlspecialchars($election_details['election_type']); ?></p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1"><strong>Year:</strong></p>
                            <p><?php echo $election_details['election_year']; ?></p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1"><strong>Period:</strong></p>
                            <p><?php echo formatDate($election_details['start_date']); ?> to 
                               <?php echo formatDate($election_details['end_date']); ?></p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1"><strong>Status:</strong></p>
                            <p><span class="badge bg-success"><?php echo $election_details['election_status']; ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($results)): ?>
                <!-- Results Display -->
                <?php
                $current_constituency = '';
                foreach ($results as $result):
                    if ($result['constituency_name'] !== $current_constituency):
                        if ($current_constituency !== '') {
                            echo '</div></div>'; // Close previous constituency card
                        }
                        $current_constituency = $result['constituency_name'];
                ?>
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo htmlspecialchars($current_constituency); ?>
                                </h5>
                            </div>
                            <div class="card-body">
                <?php 
                    endif; 
                    
                    // Determine if winner
                    $is_winner = ($result['result_status'] === 'Won');
                ?>
                                <div class="result-card <?php echo $is_winner ? 'winner' : ''; ?> card mb-3">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <?php if ($is_winner): ?>
                                                        <i class="fas fa-trophy fa-2x text-warning me-3"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-user-circle fa-2x text-secondary me-3"></i>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h5 class="mb-1">
                                                            <?php echo htmlspecialchars($result['candidate_name']); ?>
                                                            <?php if ($is_winner): ?>
                                                                <span class="badge bg-success ms-2">WINNER</span>
                                                            <?php endif; ?>
                                                        </h5>
                                                        <p class="mb-0 text-muted">
                                                            <i class="fas fa-flag"></i> 
                                                            <?php echo htmlspecialchars($result['party_name']); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="progress" style="height: 30px;">
                                                    <div class="progress-bar <?php echo $is_winner ? 'bg-success' : 'bg-primary'; ?>" 
                                                         role="progressbar" 
                                                         style="width: <?php echo $result['vote_percentage']; ?>%"
                                                         aria-valuenow="<?php echo $result['vote_percentage']; ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <?php echo number_format($result['vote_percentage'], 2); ?>%
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <h3 class="mb-0 <?php echo $is_winner ? 'text-success' : 'text-primary'; ?>">
                                                    <i class="fas fa-vote-yea"></i> 
                                                    <?php echo number_format($result['total_votes']); ?>
                                                </h3>
                                                <p class="mb-0 text-muted">Total Votes</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                <?php endforeach; ?>
                            </div>
                        </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>No Results Available</h5>
                    <p class="mb-0">Results have not been calculated for this election yet.</p>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- No Election Selected -->
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-5x text-muted mb-4"></i>
                <h3>Select an Election</h3>
                <p class="text-muted">Choose an election from the dropdown above to view results</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Indian E-Voting System. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>