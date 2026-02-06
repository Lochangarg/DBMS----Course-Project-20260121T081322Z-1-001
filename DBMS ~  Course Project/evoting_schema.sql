-- ============================================
-- E-VOTING SYSTEM DATABASE SCHEMA
-- For Indian Elections - DBMS Project
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS evoting_system;
USE evoting_system;

-- ============================================
-- Table 1: STATES
-- ============================================
CREATE TABLE states (
    state_id INT PRIMARY KEY AUTO_INCREMENT,
    state_name VARCHAR(100) NOT NULL UNIQUE,
    state_code VARCHAR(10) NOT NULL UNIQUE,
    total_constituencies INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Table 2: CONSTITUENCIES
-- ============================================
CREATE TABLE constituencies (
    constituency_id INT PRIMARY KEY AUTO_INCREMENT,
    constituency_name VARCHAR(150) NOT NULL,
    constituency_code VARCHAR(20) NOT NULL UNIQUE,
    state_id INT NOT NULL,
    total_voters INT DEFAULT 0,
    constituency_type ENUM('Lok Sabha', 'Vidhan Sabha') DEFAULT 'Lok Sabha',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (state_id) REFERENCES states(state_id) ON DELETE CASCADE
);

-- ============================================
-- Table 3: POLITICAL PARTIES
-- ============================================
CREATE TABLE parties (
    party_id INT PRIMARY KEY AUTO_INCREMENT,
    party_name VARCHAR(150) NOT NULL UNIQUE,
    party_symbol VARCHAR(100) NOT NULL,
    party_abbreviation VARCHAR(20),
    founded_year INT,
    party_status ENUM('National', 'State', 'Regional') DEFAULT 'Regional',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Table 4: ELECTIONS
-- ============================================
CREATE TABLE elections (
    election_id INT PRIMARY KEY AUTO_INCREMENT,
    election_name VARCHAR(200) NOT NULL,
    election_type ENUM('Lok Sabha', 'Vidhan Sabha', 'Local') DEFAULT 'Lok Sabha',
    election_year INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    election_status ENUM('Scheduled', 'Ongoing', 'Completed') DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- Table 5: CANDIDATES
-- ============================================
CREATE TABLE candidates (
    candidate_id INT PRIMARY KEY AUTO_INCREMENT,
    candidate_name VARCHAR(150) NOT NULL,
    party_id INT NOT NULL,
    constituency_id INT NOT NULL,
    election_id INT NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    educational_qualification VARCHAR(200),
    criminal_cases INT DEFAULT 0,
    assets_value DECIMAL(15, 2) DEFAULT 0,
    candidate_photo VARCHAR(255),
    manifesto TEXT,
    candidate_status ENUM('Active', 'Withdrawn', 'Disqualified') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (party_id) REFERENCES parties(party_id) ON DELETE CASCADE,
    FOREIGN KEY (constituency_id) REFERENCES constituencies(constituency_id) ON DELETE CASCADE,
    FOREIGN KEY (election_id) REFERENCES elections(election_id) ON DELETE CASCADE,
    UNIQUE KEY unique_candidate (candidate_name, constituency_id, election_id)
);

-- ============================================
-- Table 6: VOTERS
-- ============================================
CREATE TABLE voters (
    voter_id INT PRIMARY KEY AUTO_INCREMENT,
    voter_epic_no VARCHAR(20) NOT NULL UNIQUE COMMENT 'Electoral Photo Identity Card Number',
    voter_name VARCHAR(150) NOT NULL,
    father_name VARCHAR(150) NOT NULL,
    date_of_birth DATE NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    mobile_number VARCHAR(15) UNIQUE,
    email VARCHAR(100) UNIQUE,
    address TEXT NOT NULL,
    state_id INT NOT NULL,
    constituency_id INT NOT NULL,
    voter_password VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    aadhaar_number VARCHAR(12) UNIQUE COMMENT 'Masked/Encrypted',
    voter_status ENUM('Active', 'Inactive', 'Deceased', 'Migrated') DEFAULT 'Active',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    FOREIGN KEY (state_id) REFERENCES states(state_id) ON DELETE RESTRICT,
    FOREIGN KEY (constituency_id) REFERENCES constituencies(constituency_id) ON DELETE RESTRICT
);

-- ============================================
-- Table 7: ADMINS
-- ============================================
CREATE TABLE admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    admin_name VARCHAR(150) NOT NULL,
    admin_email VARCHAR(100) NOT NULL UNIQUE,
    admin_password VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    admin_role ENUM('Super Admin', 'Election Officer', 'Data Entry Operator') DEFAULT 'Election Officer',
    mobile_number VARCHAR(15),
    assigned_state_id INT,
    admin_status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    FOREIGN KEY (assigned_state_id) REFERENCES states(state_id) ON DELETE SET NULL
);

-- ============================================
-- Table 8: VOTES
-- ============================================
CREATE TABLE votes (
    vote_id INT PRIMARY KEY AUTO_INCREMENT,
    voter_id INT NOT NULL,
    candidate_id INT NOT NULL,
    election_id INT NOT NULL,
    constituency_id INT NOT NULL,
    vote_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    vote_hash VARCHAR(255) COMMENT 'For verification integrity',
    FOREIGN KEY (voter_id) REFERENCES voters(voter_id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id) ON DELETE CASCADE,
    FOREIGN KEY (election_id) REFERENCES elections(election_id) ON DELETE CASCADE,
    FOREIGN KEY (constituency_id) REFERENCES constituencies(constituency_id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (voter_id, election_id) COMMENT 'One vote per election per voter'
);

-- ============================================
-- Table 9: VOTE VERIFICATION
-- ============================================
CREATE TABLE vote_verification (
    verification_id INT PRIMARY KEY AUTO_INCREMENT,
    voter_id INT NOT NULL,
    election_id INT NOT NULL,
    has_voted BOOLEAN DEFAULT FALSE,
    voted_at TIMESTAMP NULL,
    verification_token VARCHAR(100) UNIQUE,
    FOREIGN KEY (voter_id) REFERENCES voters(voter_id) ON DELETE CASCADE,
    FOREIGN KEY (election_id) REFERENCES elections(election_id) ON DELETE CASCADE,
    UNIQUE KEY unique_verification (voter_id, election_id)
);

-- ============================================
-- Table 10: RESULTS
-- ============================================
CREATE TABLE results (
    result_id INT PRIMARY KEY AUTO_INCREMENT,
    election_id INT NOT NULL,
    constituency_id INT NOT NULL,
    candidate_id INT NOT NULL,
    total_votes INT DEFAULT 0,
    vote_percentage DECIMAL(5, 2) DEFAULT 0,
    result_status ENUM('Leading', 'Won', 'Lost') DEFAULT 'Leading',
    margin_votes INT DEFAULT 0,
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (election_id) REFERENCES elections(election_id) ON DELETE CASCADE,
    FOREIGN KEY (constituency_id) REFERENCES constituencies(constituency_id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id) ON DELETE CASCADE,
    UNIQUE KEY unique_result (election_id, constituency_id, candidate_id)
);

-- ============================================
-- Table 11: AUDIT LOGS
-- ============================================
CREATE TABLE audit_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_type ENUM('Voter', 'Admin') NOT NULL,
    user_id INT NOT NULL,
    action_type VARCHAR(100) NOT NULL,
    action_description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Table 12: SYSTEM SETTINGS
-- ============================================
CREATE TABLE system_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================
CREATE INDEX idx_voter_epic ON voters(voter_epic_no);
CREATE INDEX idx_voter_constituency ON voters(constituency_id);
CREATE INDEX idx_candidate_election ON candidates(election_id);
CREATE INDEX idx_votes_election ON votes(election_id);
CREATE INDEX idx_votes_candidate ON votes(candidate_id);
CREATE INDEX idx_results_election ON results(election_id);
CREATE INDEX idx_audit_timestamp ON audit_logs(action_timestamp);

-- ============================================
-- VIEWS FOR REPORTING
-- ============================================

-- View: Candidate Details with Party and Constituency
CREATE VIEW vw_candidate_details AS
SELECT 
    c.candidate_id,
    c.candidate_name,
    c.age,
    c.gender,
    p.party_name,
    p.party_symbol,
    con.constituency_name,
    s.state_name,
    e.election_name,
    c.candidate_status
FROM candidates c
JOIN parties p ON c.party_id = p.party_id
JOIN constituencies con ON c.constituency_id = con.constituency_id
JOIN states s ON con.state_id = s.state_id
JOIN elections e ON c.election_id = e.election_id;

-- View: Election Results Summary
CREATE VIEW vw_election_results AS
SELECT 
    r.election_id,
    e.election_name,
    con.constituency_name,
    c.candidate_name,
    p.party_name,
    r.total_votes,
    r.vote_percentage,
    r.result_status
FROM results r
JOIN elections e ON r.election_id = e.election_id
JOIN constituencies con ON r.constituency_id = con.constituency_id
JOIN candidates c ON r.candidate_id = c.candidate_id
JOIN parties p ON c.party_id = p.party_id
ORDER BY r.election_id, con.constituency_name, r.total_votes DESC;

-- View: Voter Turnout Statistics
CREATE VIEW vw_voter_turnout AS
SELECT 
    e.election_id,
    e.election_name,
    con.constituency_id,
    con.constituency_name,
    con.total_voters,
    COUNT(DISTINCT v.voter_id) as votes_cast,
    ROUND((COUNT(DISTINCT v.voter_id) / con.total_voters * 100), 2) as turnout_percentage
FROM elections e
JOIN constituencies con
LEFT JOIN votes v ON e.election_id = v.election_id AND con.constituency_id = v.constituency_id
GROUP BY e.election_id, con.constituency_id;

-- ============================================
-- END OF SCHEMA
-- ============================================