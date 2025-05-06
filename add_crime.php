<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

include 'database.php';

$success_message = "";
$error_message = "";

// Get all criminals for dropdown
$criminals_sql = "SELECT id, first_name, last_name FROM criminals ORDER BY first_name, last_name";
$criminals_result = mysqli_query($conn, $criminals_sql);

// Handle crime form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $criminal_id = mysqli_real_escape_string($conn, $_POST['criminal_id']);
    $crime_type = mysqli_real_escape_string($conn, $_POST['crime_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $date_committed = mysqli_real_escape_string($conn, $_POST['date_committed']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $severity = mysqli_real_escape_string($conn, $_POST['severity']);
    
    // Insert data into the database
    $sql = "INSERT INTO crimes (criminal_id, crime_type, description, date_committed, location, status, severity) 
            VALUES ('$criminal_id', '$crime_type', '$description', '$date_committed', '$location', '$status', '$severity')";
    
    if(mysqli_query($conn, $sql)) {
        $success_message = "Crime record added successfully.";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Crime - Criminal Management System</title>
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* Semi-transparent container for content */
        .dashboard-container {
            background-color: rgba(255, 255, 255, 0.85); /* Semi-transparent white */
            backdrop-filter: blur(4px); /* Optional: blur background behind container */
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 1200px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* Optional: shadow for depth */
        }

        .quick-actions {
            text-align: center;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .action-buttons a {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .action-buttons a:hover {
            background-color: #45a049;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            gap: 10px;
            background-color: #2c3e50;
            margin: 0;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            display: block;
        }

        nav ul li a:hover,
        nav ul li a.active {
            background-color: #1a252f;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            border-radius: 10px 10px 0 0;
        }

        footer {
            text-align: center;
            color: white;
            background-color: #2c3e50;
            padding: 10px;
            border-radius: 0 0 10px 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body style="background-image: url('bg-image.jpg'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed;">
    <div class="dashboard-container">
        <header>
            <h1>Criminal Management System</h1>
            <div class="user-info">
                Welcome, <?php echo $_SESSION['username']; ?> | 
                <a href="index.php?logout=true">Logout</a>
            </div>
        </header>
        
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="add_criminal.php">Add Criminal</a></li>
                <li><a href="view_criminals.php">View Criminals</a></li>
                <li><a href="add_crime.php" class="active">Record Crime</a></li>
                <li><a href="view_cases.php">View Cases</a></li>
            </ul>
        </nav>
        
        <main>
            <h2>Record New Crime</h2>
            
            <?php if(!empty($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="criminal_id">Select Criminal:</label>
                    <select id="criminal_id" name="criminal_id" required>
                        <option value="">Select Criminal</option>
                        <?php
                        if(mysqli_num_rows($criminals_result) > 0) {
                            while($row = mysqli_fetch_assoc($criminals_result)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['first_name'] . " " . $row['last_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="crime_type">Crime Type:</label>
                        <select id="crime_type" name="crime_type" required>
                            <option value="">Select Crime Type</option>
                            <option value="Theft">Theft</option>
                            <option value="Robbery">Robbery</option>
                            <option value="Assault">Assault</option>
                            <option value="Murder">Murder</option>
                            <option value="Fraud">Fraud</option>
                            <option value="Cybercrime">Cybercrime</option>
                            <option value="Drug Offense">Drug Offense</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_committed">Date Committed:</label>
                        <input type="date" id="date_committed" name="date_committed" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Case Status:</label>
                        <select id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="Open">Open</option>
                            <option value="Pending">Pending</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="severity">Severity:</label>
                        <select id="severity" name="severity" required>
                            <option value="">Select Severity</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Record Crime</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </main>
        
        <footer>
            <p>&copy; 2025 Criminal Management System</p>
        </footer>
    </div>
</body>
</html>