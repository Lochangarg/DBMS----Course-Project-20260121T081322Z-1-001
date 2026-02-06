<?php
/**
 * E-Voting System - Common Functions
 * File: includes/functions.php
 */

if (!defined('EVOTING_SYSTEM')) {
    define('EVOTING_SYSTEM', true);
}

require_once 'config.php';

// ============================================
// AUTHENTICATION FUNCTIONS
// ============================================

/**
 * Hash password using PHP's password_hash
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Validate voter login
 */
function validateVoterLogin($epic_no, $password) {
    $db = getDB();
    $stmt = $db->prepare("SELECT voter_id, voter_name, voter_password, voter_status 
                          FROM voters WHERE voter_epic_no = ?");
    $stmt->bind_param("s", $epic_no);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $voter = $result->fetch_assoc();
        
        if ($voter['voter_status'] !== 'Active') {
            return ['success' => false, 'message' => 'Your voter account is not active.'];
        }
        
        if (verifyPassword($password, $voter['voter_password'])) {
            // Update last login
            $update_stmt = $db->prepare("UPDATE voters SET last_login = NOW() WHERE voter_id = ?");
            $update_stmt->bind_param("i", $voter['voter_id']);
            $update_stmt->execute();
            
            return [
                'success' => true,
                'voter_id' => $voter['voter_id'],
                'voter_name' => $voter['voter_name']
            ];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid EPIC number or password.'];
}

/**
 * Validate admin login
 */
function validateAdminLogin($email, $password) {
    $db = getDB();
    $stmt = $db->prepare("SELECT admin_id, admin_name, admin_password, admin_role, admin_status 
                          FROM admins WHERE admin_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        if ($admin['admin_status'] !== 'Active') {
            return ['success' => false, 'message' => 'Your admin account is not active.'];
        }
        
        if (verifyPassword($password, $admin['admin_password'])) {
            // Update last login
            $update_stmt = $db->prepare("UPDATE admins SET last_login = NOW() WHERE admin_id = ?");
            $update_stmt->bind_param("i", $admin['admin_id']);
            $update_stmt->execute();
            
            return [
                'success' => true,
                'admin_id' => $admin['admin_id'],
                'admin_name' => $admin['admin_name'],
                'admin_role' => $admin['admin_role']
            ];
        }
    }
    
    return ['success' => false, 'message' => 'Invalid email or password.'];
}

// ============================================
// VOTING FUNCTIONS
// ============================================

/**
 * Check if voter has already voted in an election
 */
function hasVoted($voter_id, $election_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT has_voted FROM vote_verification 
                          WHERE voter_id = ? AND election_id = ?");
    $stmt->bind_param("ii", $voter_id, $election_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['has_voted'] == 1;
    }
    
    return false;
}

/**
 * Cast a vote
 */
function castVote($voter_id, $candidate_id, $election_id, $constituency_id) {
    $db = getDB();
    
    // Check if already voted
    if (hasVoted($voter_id, $election_id)) {
        return ['success' => false, 'message' => 'You have already voted in this election.'];
    }
    
    // Begin transaction
    $conn = getConnection();
    $conn->begin_transaction();
    
    try {
        // Insert vote
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $vote_hash = hash('sha256', $voter_id . $candidate_id . time());
        
        $stmt1 = $db->prepare("INSERT INTO votes (voter_id, candidate_id, election_id, constituency_id, ip_address, vote_hash) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param("iiiiss", $voter_id, $candidate_id, $election_id, $constituency_id, $ip_address, $vote_hash);
        $stmt1->execute();
        
        // Update vote verification
        $stmt2 = $db->prepare("INSERT INTO vote_verification (voter_id, election_id, has_voted, voted_at) 
                               VALUES (?, ?, 1, NOW()) 
                               ON DUPLICATE KEY UPDATE has_voted = 1, voted_at = NOW()");
        $stmt2->bind_param("ii", $voter_id, $election_id);
        $stmt2->execute();
        
        // Log the action
        logAudit('Voter', $voter_id, 'VOTE_CAST', "Cast vote for candidate ID: $candidate_id");
        
        $conn->commit();
        return ['success' => true, 'message' => 'Your vote has been recorded successfully!'];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Error casting vote: ' . $e->getMessage()];
    }
}

/**
 * Get active elections for a constituency
 */
function getActiveElections($constituency_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT election_id, election_name, election_type, start_date, end_date 
                          FROM elections 
                          WHERE election_status = 'Ongoing' 
                          AND CURDATE() BETWEEN start_date AND end_date");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get candidates for an election and constituency
 */
function getCandidates($election_id, $constituency_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT c.candidate_id, c.candidate_name, c.age, c.gender, 
                          c.educational_qualification, p.party_name, p.party_symbol 
                          FROM candidates c 
                          JOIN parties p ON c.party_id = p.party_id 
                          WHERE c.election_id = ? AND c.constituency_id = ? 
                          AND c.candidate_status = 'Active' 
                          ORDER BY p.party_name");
    $stmt->bind_param("ii", $election_id, $constituency_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ============================================
// RESULT FUNCTIONS
// ============================================

/**
 * Calculate results for an election
 */
function calculateResults($election_id) {
    $db = getDB();
    
    // Delete existing results
    $stmt1 = $db->prepare("DELETE FROM results WHERE election_id = ?");
    $stmt1->bind_param("i", $election_id);
    $stmt1->execute();
    
    // Calculate and insert results
    $sql = "INSERT INTO results (election_id, constituency_id, candidate_id, total_votes, vote_percentage)
            SELECT 
                v.election_id,
                v.constituency_id,
                v.candidate_id,
                COUNT(*) as total_votes,
                ROUND((COUNT(*) * 100.0 / con_votes.total), 2) as vote_percentage
            FROM votes v
            JOIN (
                SELECT election_id, constituency_id, COUNT(*) as total
                FROM votes
                WHERE election_id = ?
                GROUP BY election_id, constituency_id
            ) con_votes ON v.election_id = con_votes.election_id 
                        AND v.constituency_id = con_votes.constituency_id
            WHERE v.election_id = ?
            GROUP BY v.election_id, v.constituency_id, v.candidate_id";
    
    $stmt2 = $db->prepare($sql);
    $stmt2->bind_param("ii", $election_id, $election_id);
    return $stmt2->execute();
}

/**
 * Get results for an election
 */
function getResults($election_id, $constituency_id = null) {
    $db = getDB();
    
    if ($constituency_id) {
        $stmt = $db->prepare("SELECT r.*, c.candidate_name, p.party_name, p.party_symbol, 
                              con.constituency_name 
                              FROM results r 
                              JOIN candidates c ON r.candidate_id = c.candidate_id 
                              JOIN parties p ON c.party_id = p.party_id 
                              JOIN constituencies con ON r.constituency_id = con.constituency_id 
                              WHERE r.election_id = ? AND r.constituency_id = ? 
                              ORDER BY r.total_votes DESC");
        $stmt->bind_param("ii", $election_id, $constituency_id);
    } else {
        $stmt = $db->prepare("SELECT r.*, c.candidate_name, p.party_name, p.party_symbol, 
                              con.constituency_name 
                              FROM results r 
                              JOIN candidates c ON r.candidate_id = c.candidate_id 
                              JOIN parties p ON c.party_id = p.party_id 
                              JOIN constituencies con ON r.constituency_id = con.constituency_id 
                              WHERE r.election_id = ? 
                              ORDER BY con.constituency_name, r.total_votes DESC");
        $stmt->bind_param("i", $election_id);
    }
    
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Log audit trail
 */
function logAudit($user_type, $user_id, $action_type, $description) {
    $db = getDB();
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    $stmt = $db->prepare("INSERT INTO audit_logs (user_type, user_id, action_type, action_description, ip_address, user_agent) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $user_type, $user_id, $action_type, $description, $ip_address, $user_agent);
    return $stmt->execute();
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate mobile number (Indian)
 */
function isValidMobile($mobile) {
    return preg_match('/^[6-9]\d{9}$/', $mobile);
}

/**
 * Calculate age from date of birth
 */
function calculateAge($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    return $birthDate->diff($today)->y;
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Check if election is ongoing
 */
function isElectionOngoing($election_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT election_status FROM elections WHERE election_id = ?");
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['election_status'] === 'Ongoing';
    }
    
    return false;
}

/**
 * Get voter details
 */
function getVoterDetails($voter_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT v.*, s.state_name, c.constituency_name 
                          FROM voters v 
                          JOIN states s ON v.state_id = s.state_id 
                          JOIN constituencies c ON v.constituency_id = c.constituency_id 
                          WHERE v.voter_id = ?");
    $stmt->bind_param("i", $voter_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get all states
 */
function getAllStates() {
    $db = getDB();
    $result = $db->query("SELECT * FROM states ORDER BY state_name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get constituencies by state
 */
function getConstituenciesByState($state_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM constituencies WHERE state_id = ? ORDER BY constituency_name");
    $stmt->bind_param("i", $state_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>