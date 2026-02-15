<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "sqlroot"; // Updated password
$dbname = "voting_system";

// Enable detailed error output
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   try {
       // Step 1: Connect without DB
       $conn = new mysqli($servername, $username, $password);
       
       // Step 2: Create DB
       $conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
       
       // Step 3: Select DB
       $conn->select_db($dbname);
       
       // Step 4: Import SQL
       $sqlFile = 'database.sql';
       if (!file_exists($sqlFile)) {
           throw new Exception("File '$sqlFile' not found in current directory!");
       }
       
       $queries = file_get_contents($sqlFile);
       // Split multi-query string by semicolons (simple approach)
       // Note: complex SQL with triggers/procedures might need better parsing, but simple CREATE TABLE is fine
       $conn->multi_query($queries);
       
       // Flush multi-query results to free connection
       while ($conn->next_result()) {;}
       
       $message = "Database '$dbname' created and initialized successfully!";
       // Redirect or link to login
   } catch (mysqli_sql_exception $e) {
       $error = "Detailed Error: " . $e->getMessage();
   } catch (Exception $e) {
       $error = "Error: " . $e->getMessage();
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <title>Setup Voting Database</title>
   <style>
       body { font-family: sans-serif; max-width: 600px; margin: 2rem auto; padding: 2rem; background: #f4f4f9; color: #333; }
       .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
       button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
       button:hover { background: #0056b3; }
       .success { color: green; background: #e8f5e9; padding: 1rem; border-radius: 4px; border: 1px solid green; }
       .error { color: red; background: #ffebee; padding: 1rem; border-radius: 4px; border: 1px solid red; }
   </style>
</head>
<body>
   <div class="card">
       <h1>Initial Setup</h1>
       <p>Click below to create the database <code>voting_system</code> and import tables.</p>
       
       <?php if ($message): ?>
           <div class="success">
               <h3>Success!</h3>
               <p><?php echo $message; ?></p>
               <p><a href="Login/login.php" style="background:#28a745; color:white; padding:10px 15px; text-decoration:none; border-radius:4px;">Go to Login Page</a></p>
           </div>
       <?php elseif ($error): ?>
           <div class="error">
               <h3>Setup Failed</h3>
               <p><?php echo $error; ?></p>
               <p>Check your user/password in <code>setup_database.php</code> and try again.</p>
           </div>
           <form method="POST" style="margin-top:1rem;">
               <button type="submit">Retry Installation</button>
           </form>
       <?php else: ?>
           <form method="POST">
               <button type="submit">Run Installation</button>
           </form>
       <?php endif; ?>
   </div>
</body>
</html>
