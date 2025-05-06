<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

include 'database.php';

$criminals_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM criminals"))['count'];
$crimes_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM crimes"))['count'];
$open_cases = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM crimes WHERE status='Open'"))['count'];
$closed_cases = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM crimes WHERE status='Closed'"))['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Criminal Management System</title>
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
        main h2 {
              color: #2c3e50;
              margin-bottom: 20px;
              border-bottom: 2px solid #f0f2f5;
              padding-bottom: 10px;
              padding-left:10px;
              padding-top:20px;
        

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
<body style="background-image: url('bg-image2.webp'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed;">

<div class="dashboard-container">

    <!-- Header -->
    <header>
        <h1>Criminal Management System</h1>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?> |
            <a href="index.php" style="color: orange;">Logout</a>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="add_criminal.php">Add Criminal</a></li>
            <li><a href="view_criminals.php">View Criminals</a></li>
            <li><a href="add_crime.php">Record Crime</a></li>
            <li><a href="view_cases.php">View Cases</a></li>
        </ul>
    </nav>

    <!-- Main Dashboard -->
    <main class="dashboard">
        <h2>Dashboard Overview</h2>

        <div class="stats-container">
            <div class="stat-box">
                <h3>Total Criminals</h3>
                <div class="stat-number"><?php echo $criminals_count; ?></div>
                <a href="view_criminals.php" class="btn">View All</a>
            </div>

            <div class="stat-box">
                <h3>Total Crimes</h3>
                <div class="stat-number"><?php echo $crimes_count; ?></div>
                <a href="view_cases.php" class="btn">View All</a>
            </div>

            <div class="stat-box">
                <h3>Open Cases</h3>
                <div class="stat-number status status-open"><?php echo $open_cases; ?></div>
            </div>

            <div class="stat-box">
                <h3>Closed Cases</h3>
                <div class="stat-number status status-closed"><?php echo $closed_cases; ?></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="add_criminal.php" class="btn">Add New Criminal</a>
                <a href="add_crime.php" class="btn">Record New Crime</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Criminal Management System</p>
    </footer>
</div>
</body>
</html>
