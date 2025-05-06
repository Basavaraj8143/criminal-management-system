<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

include 'database.php';

// Flash messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Handle crime deletion
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM crimes WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        $_SESSION['success_message'] = "Crime record deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting record.";
    }
    header("Location: view_cases.php");
    exit;
}

// Handle status update
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status_id'], $_POST['status'])) {
    $id = $_POST['update_status_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE crimes SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if($stmt->execute()) {
        $_SESSION['success_message'] = "Case status updated successfully.";
    } else {
        $_SESSION['error_message'] = "Error updating status.";
    }
    header("Location: view_cases.php");
    exit;
}

// Filter by status
$filter = $_GET['filter'] ?? 'all';
$where_clause = $filter != 'all' ? "WHERE crimes.status = ?" : "";

// Get all crimes with criminal names
$sql = "SELECT crimes.*, 
        CONCAT(criminals.first_name, ' ', criminals.last_name) as criminal_name
        FROM crimes 
        JOIN criminals ON crimes.criminal_id = criminals.id
        $where_clause
        ORDER BY crimes.date_committed DESC";

if ($filter != 'all') {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Cases - Criminal Management System</title>
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

<body style="background-image: url('bg-image2.webp'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed;">
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
            <li><a href="add_crime.php">Record Crime</a></li>
            <li><a href="view_cases.php" class="active">View Cases</a></li>
        </ul>
    </nav>

    <main>
        <h2>Case Records</h2>

        <?php if($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filters">
            <a href="view_cases.php?filter=all" class="filter-link <?php if($filter == 'all') echo 'active'; ?>">All</a>
            <a href="view_cases.php?filter=open" class="filter-link <?php if($filter == 'open') echo 'active'; ?>">Open</a>
            <a href="view_cases.php?filter=pending" class="filter-link <?php if($filter == 'pending') echo 'active'; ?>">Pending</a>
            <a href="view_cases.php?filter=closed" class="filter-link <?php if($filter == 'closed') echo 'active'; ?>">Closed</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Crime Title</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Criminal</th>
                        <th>Status</th>
                        <th>Severity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['crime_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo $row['date_committed']; ?></td>
                                <td><?php echo htmlspecialchars($row['criminal_name']); ?></td>
                                <td>
                                    <span class="status status-<?php echo strtolower($row['status']); ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="severity severity-<?php echo strtolower($row['severity']); ?>">
                                        <?php echo ucfirst($row['severity']); ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <!-- Status Update Form -->
                                    <form method="post" action="view_cases.php" style="display:inline-block;">
                                        <input type="hidden" name="update_status_id" value="<?php echo $row['id']; ?>">
                                        <select name="status" onchange="this.form.submit()">
                                            <option disabled selected>Update</option>
                                            <option value="open">Open</option>
                                            <option value="pending">Pending</option>
                                            <option value="closed">Closed</option>
                                        </select>
                                    </form>

                                    <!-- Delete Form -->
                                    <form method="post" action="view_cases.php" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this case?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8">No case records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        &copy; <?php echo date("Y"); ?> Criminal Management System
    </footer>
</div>
</body>
</html>
