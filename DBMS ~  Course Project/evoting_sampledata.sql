-- ============================================
-- SAMPLE DATA FOR E-VOTING SYSTEM
-- ============================================

USE evoting_system;

-- ============================================
-- INSERT STATES
-- ============================================
INSERT INTO states (state_name, state_code, total_constituencies) VALUES
('Maharashtra', 'MH', 48),
('Uttar Pradesh', 'UP', 80),
('West Bengal', 'WB', 42),
('Madhya Pradesh', 'MP', 29),
('Tamil Nadu', 'TN', 39),
('Karnataka', 'KA', 28),
('Gujarat', 'GJ', 26),
('Rajasthan', 'RJ', 25),
('Delhi', 'DL', 7),
('Bihar', 'BR', 40);

-- ============================================
-- INSERT CONSTITUENCIES
-- ============================================
INSERT INTO constituencies (constituency_name, constituency_code, state_id, total_voters, constituency_type) VALUES
-- Maharashtra
('Mumbai North', 'MH-01', 1, 1450000, 'Lok Sabha'),
('Mumbai South', 'MH-02', 1, 1320000, 'Lok Sabha'),
('Pune', 'MH-03', 1, 1580000, 'Lok Sabha'),
('Nagpur', 'MH-04', 1, 1420000, 'Lok Sabha'),
('Thane', 'MH-05', 1, 1650000, 'Lok Sabha'),

-- Uttar Pradesh
('Lucknow', 'UP-01', 2, 1720000, 'Lok Sabha'),
('Varanasi', 'UP-02', 2, 1680000, 'Lok Sabha'),
('Kanpur', 'UP-03', 2, 1590000, 'Lok Sabha'),
('Agra', 'UP-04', 2, 1530000, 'Lok Sabha'),

-- West Bengal
('Kolkata North', 'WB-01', 3, 1380000, 'Lok Sabha'),
('Kolkata South', 'WB-02', 3, 1290000, 'Lok Sabha'),

-- Delhi
('New Delhi', 'DL-01', 9, 1420000, 'Lok Sabha'),
('East Delhi', 'DL-02', 9, 1560000, 'Lok Sabha'),
('South Delhi', 'DL-03', 9, 1480000, 'Lok Sabha');

-- ============================================
-- INSERT POLITICAL PARTIES
-- ============================================
INSERT INTO parties (party_name, party_symbol, party_abbreviation, founded_year, party_status) VALUES
('Bharatiya Janata Party', 'Lotus', 'BJP', 1980, 'National'),
('Indian National Congress', 'Hand', 'INC', 1885, 'National'),
('Aam Aadmi Party', 'Broom', 'AAP', 2012, 'State'),
('Shiv Sena', 'Bow and Arrow', 'SS', 1966, 'State'),
('Nationalist Congress Party', 'Clock', 'NCP', 1999, 'State'),
('Trinamool Congress', 'Flower', 'TMC', 1998, 'State'),
('Samajwadi Party', 'Bicycle', 'SP', 1992, 'State'),
('Bahujan Samaj Party', 'Elephant', 'BSP', 1984, 'National'),
('Communist Party of India (Marxist)', 'Hammer Sickle Star', 'CPI(M)', 1964, 'National'),
('Independent', 'Various', 'IND', 1947, 'Regional');

-- ============================================
-- INSERT ELECTIONS
-- ============================================
INSERT INTO elections (election_name, election_type, election_year, start_date, end_date, election_status) VALUES
('Lok Sabha General Elections 2024', 'Lok Sabha', 2024, '2024-04-19', '2024-06-01', 'Completed'),
('Lok Sabha General Elections 2029', 'Lok Sabha', 2029, '2029-04-15', '2029-05-25', 'Scheduled'),
('Delhi Vidhan Sabha Elections 2025', 'Vidhan Sabha', 2025, '2025-02-05', '2025-02-05', 'Ongoing');

-- ============================================
-- INSERT CANDIDATES
-- ============================================
INSERT INTO candidates (candidate_name, party_id, constituency_id, election_id, age, gender, educational_qualification, criminal_cases, assets_value) VALUES
-- Mumbai North (Election 2024)
('Rajesh Kumar Sharma', 1, 1, 1, 52, 'Male', 'MBA, LLB', 0, 25000000.00),
('Priya Deshmukh', 2, 1, 1, 48, 'Female', 'MA Political Science', 0, 18000000.00),
('Amit Patil', 4, 1, 1, 55, 'Male', 'BCom', 1, 32000000.00),

-- Mumbai South (Election 2024)
('Sunita Rao', 1, 2, 1, 45, 'Female', 'PhD Economics', 0, 45000000.00),
('Mohammed Farooq', 2, 2, 1, 50, 'Male', 'LLM', 0, 28000000.00),

-- Pune (Election 2024)
('Vikram Singh Rathore', 1, 3, 1, 47, 'Male', 'BE, MBA', 0, 35000000.00),
('Anjali Kulkarni', 2, 3, 1, 42, 'Female', 'MA Sociology', 0, 22000000.00),

-- Varanasi (Election 2024)
('Narendra Modi', 1, 7, 1, 73, 'Male', 'MA Political Science', 0, 35000000.00),
('Ajay Rai', 2, 7, 1, 59, 'Male', 'BA', 2, 15000000.00),

-- New Delhi (Election 2025)
('Arvind Kejriwal', 3, 12, 3, 56, 'Male', 'B.Tech IIT', 3, 12000000.00),
('Ramesh Bidhuri', 1, 12, 3, 63, 'Male', 'BA', 1, 42000000.00),
('Sandeep Dikshit', 2, 12, 3, 58, 'Male', 'MBA', 0, 35000000.00),

-- East Delhi (Election 2025)
('Atishi Marlena', 3, 13, 3, 42, 'Female', 'BA Oxford', 0, 8000000.00),
('Gautam Gambhir', 1, 13, 3, 43, 'Male', 'BCom', 0, 750000000.00);

-- ============================================
-- INSERT ADMINS
-- ============================================
INSERT INTO admins (admin_name, admin_email, admin_password, admin_role, mobile_number, assigned_state_id) VALUES
('Chief Election Commissioner', 'cec@eci.gov.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', '9876543210', NULL),
('Rajesh Kumar - Maharashtra Officer', 'rajesh.mh@eci.gov.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Election Officer', '9876543211', 1),
('Priya Sharma - UP Officer', 'priya.up@eci.gov.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Election Officer', '9876543212', 2),
('Amit Patel - Delhi Officer', 'amit.dl@eci.gov.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Election Officer', '9876543213', 9),
('Data Entry Operator 1', 'data1@eci.gov.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Data Entry Operator', '9876543214', NULL);

-- Note: All admin passwords are hashed. Plain text password is: admin@123

-- ============================================
-- INSERT VOTERS
-- ============================================
INSERT INTO voters (voter_epic_no, voter_name, father_name, date_of_birth, age, gender, mobile_number, email, address, state_id, constituency_id, voter_password, aadhaar_number, voter_status) VALUES
-- Mumbai North Voters
('MH0120240001', 'Rahul Verma', 'Suresh Verma', '1990-05-15', 34, 'Male', '9812345601', 'rahul.verma@email.com', 'Flat 301, Shivaji Nagar, Mumbai-400001', 1, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780001', 'Active'),
('MH0120240002', 'Sneha Kapoor', 'Rajesh Kapoor', '1985-08-22', 39, 'Female', '9812345602', 'sneha.k@email.com', 'A-45, Andheri West, Mumbai-400053', 1, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780002', 'Active'),
('MH0120240003', 'Arjun Reddy', 'Venkat Reddy', '1995-12-10', 29, 'Male', '9812345603', 'arjun.r@email.com', 'B-12, Borivali East, Mumbai-400066', 1, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780003', 'Active'),
('MH0120240004', 'Meera Joshi', 'Prakash Joshi', '1992-03-18', 32, 'Female', '9812345604', 'meera.joshi@email.com', 'C-78, Malad West, Mumbai-400064', 1, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780004', 'Active'),
('MH0120240005', 'Vikram Singh', 'Amarjeet Singh', '1988-07-25', 36, 'Male', '9812345605', 'vikram.singh@email.com', 'D-99, Kandivali West, Mumbai-400067', 1, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780005', 'Active'),

-- Mumbai South Voters
('MH0220240006', 'Kavita Sharma', 'Mohan Sharma', '1991-11-30', 33, 'Female', '9812345606', 'kavita.s@email.com', '5th Floor, Nariman Point, Mumbai-400021', 1, 2, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780006', 'Active'),
('MH0220240007', 'Ravi Kumar', 'Dinesh Kumar', '1987-04-12', 37, 'Male', '9812345607', 'ravi.kumar@email.com', 'A-Block, Worli, Mumbai-400018', 1, 2, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780007', 'Active'),

-- Varanasi Voters
('UP0220240008', 'Sanjay Mishra', 'Ram Mishra', '1989-09-08', 35, 'Male', '9812345608', 'sanjay.m@email.com', 'Lanka Road, Varanasi-221005', 2, 7, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780008', 'Active'),
('UP0220240009', 'Anita Pandey', 'Shiv Pandey', '1993-06-20', 31, 'Female', '9812345609', 'anita.p@email.com', 'Assi Ghat, Varanasi-221001', 2, 7, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780009', 'Active'),
('UP0220240010', 'Deepak Yadav', 'Ramesh Yadav', '1990-01-15', 34, 'Male', '9812345610', 'deepak.y@email.com', 'Godowlia, Varanasi-221001', 2, 7, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780010', 'Active'),

-- New Delhi Voters
('DL0120250011', 'Amit Gupta', 'Vinod Gupta', '1986-10-05', 38, 'Male', '9812345611', 'amit.gupta@email.com', 'Connaught Place, New Delhi-110001', 9, 12, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780011', 'Active'),
('DL0120250012', 'Pooja Malhotra', 'Sunil Malhotra', '1994-02-28', 30, 'Female', '9812345612', 'pooja.m@email.com', 'Kasturba Gandhi Marg, Delhi-110001', 9, 12, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780012', 'Active'),
('DL0120250013', 'Rohit Sharma', 'Anil Sharma', '1991-07-14', 33, 'Male', '9812345613', 'rohit.sharma@email.com', 'Janpath, New Delhi-110001', 9, 12, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780013', 'Active'),
('DL0120250014', 'Neha Kapoor', 'Manoj Kapoor', '1996-12-22', 28, 'Female', '9812345614', 'neha.kapoor@email.com', 'Rajpath Area, Delhi-110004', 9, 12, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780014', 'Active'),
('DL0120250015', 'Karan Mehta', 'Rajesh Mehta', '1989-04-30', 35, 'Male', '9812345615', 'karan.mehta@email.com', 'Barakhamba Road, Delhi-110001', 9, 12, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123456780015', 'Active');

-- Note: All voter passwords are hashed. Plain text password is: voter@123

-- ============================================
-- INSERT SAMPLE VOTES (Election 2024 - Completed)
-- ============================================
INSERT INTO votes (voter_id, candidate_id, election_id, constituency_id, ip_address) VALUES
-- Mumbai North votes
(1, 1, 1, 1, '192.168.1.101'),
(2, 2, 1, 1, '192.168.1.102'),
(3, 1, 1, 1, '192.168.1.103'),
(4, 1, 1, 1, '192.168.1.104'),
(5, 3, 1, 1, '192.168.1.105'),

-- Mumbai South votes
(6, 4, 1, 2, '192.168.1.106'),
(7, 5, 1, 2, '192.168.1.107'),

-- Varanasi votes
(8, 8, 1, 7, '192.168.1.108'),
(9, 8, 1, 7, '192.168.1.109'),
(10, 9, 1, 7, '192.168.1.110');

-- ============================================
-- INSERT VOTE VERIFICATION RECORDS
-- ============================================
INSERT INTO vote_verification (voter_id, election_id, has_voted, voted_at) VALUES
(1, 1, TRUE, '2024-05-15 10:30:00'),
(2, 1, TRUE, '2024-05-15 11:45:00'),
(3, 1, TRUE, '2024-05-15 14:20:00'),
(4, 1, TRUE, '2024-05-15 15:10:00'),
(5, 1, TRUE, '2024-05-15 16:30:00'),
(6, 1, TRUE, '2024-05-16 09:15:00'),
(7, 1, TRUE, '2024-05-16 12:00:00'),
(8, 1, TRUE, '2024-05-20 10:00:00'),
(9, 1, TRUE, '2024-05-20 13:30:00'),
(10, 1, TRUE, '2024-05-20 16:45:00'),
-- New Delhi voters for ongoing election
(11, 3, FALSE, NULL),
(12, 3, FALSE, NULL),
(13, 3, FALSE, NULL),
(14, 3, FALSE, NULL),
(15, 3, FALSE, NULL);

-- ============================================
-- INSERT RESULTS (for completed election)
-- ============================================
INSERT INTO results (election_id, constituency_id, candidate_id, total_votes, vote_percentage, result_status, margin_votes) VALUES
-- Mumbai North Results
(1, 1, 1, 450000, 45.50, 'Won', 50000),
(1, 1, 2, 400000, 40.40, 'Lost', 0),
(1, 1, 3, 140000, 14.10, 'Lost', 0),

-- Mumbai South Results
(1, 2, 4, 520000, 52.00, 'Won', 120000),
(1, 2, 5, 400000, 40.00, 'Lost', 0),

-- Varanasi Results
(1, 7, 8, 680000, 68.00, 'Won', 280000),
(1, 7, 9, 320000, 32.00, 'Lost', 0);

-- ============================================
-- INSERT AUDIT LOGS
-- ============================================
INSERT INTO audit_logs (user_type, user_id, action_type, action_description, ip_address) VALUES
('Admin', 1, 'LOGIN', 'Chief Election Commissioner logged in', '192.168.0.1'),
('Admin', 1, 'ELECTION_CREATE', 'Created Lok Sabha 2024 election', '192.168.0.1'),
('Voter', 1, 'LOGIN', 'Rahul Verma logged in', '192.168.1.101'),
('Voter', 1, 'VOTE_CAST', 'Cast vote in Mumbai North constituency', '192.168.1.101'),
('Admin', 2, 'CANDIDATE_ADD', 'Added candidate Rajesh Kumar Sharma', '192.168.0.2'),
('Voter', 8, 'LOGIN', 'Sanjay Mishra logged in', '192.168.1.108'),
('Voter', 8, 'VOTE_CAST', 'Cast vote in Varanasi constituency', '192.168.1.108'),
('Admin', 1, 'RESULTS_CALCULATE', 'Calculated results for Mumbai North', '192.168.0.1');

-- ============================================
-- INSERT SYSTEM SETTINGS
-- ============================================
INSERT INTO system_settings (setting_key, setting_value, setting_description) VALUES
('voting_enabled', 'true', 'Global voting enable/disable flag'),
('minimum_age', '18', 'Minimum age for voter registration'),
('session_timeout', '1800', 'Session timeout in seconds (30 minutes)'),
('max_login_attempts', '3', 'Maximum failed login attempts before lockout'),
('system_maintenance', 'false', 'System maintenance mode'),
('result_display_enabled', 'true', 'Enable public result display'),
('voter_registration_enabled', 'true', 'Enable new voter registration');

-- ============================================
-- END OF SAMPLE DATA
-- ============================================