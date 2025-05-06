<?php 
session_start();
include 'database.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_sql = "DELETE FROM criminals WHERE id = $id";
    if (mysqli_query($conn, $delete_sql)) {
        $success_message = "Criminal record deleted successfully.";
    } else {
        $error_message = "Error deleting record: " . mysqli_error($conn);
    }
}

// Handle update submission
if (isset($_POST['update_criminal'])) {
    $id = $_POST['id'];
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $arrest_date = $_POST['arrest_date'];

    $update_sql = "UPDATE criminals SET 
        first_name = '$first_name',
        last_name = '$last_name',
        gender = '$gender',
        date_of_birth = '$dob',
        address = '$address',
        arrest_date = '$arrest_date'
        WHERE id = $id";

    if (mysqli_query($conn, $update_sql)) {
        $success_message = "Criminal record updated successfully.";
    } else {
        $error_message = "Error updating record: " . mysqli_error($conn);
    }
}

// If edit requested, get the criminal's data
$edit_criminal = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM criminals WHERE id = $edit_id");
    $edit_criminal = mysqli_fetch_assoc($edit_query);
}

// Get all criminals
$result = mysqli_query($conn, "SELECT * FROM criminals ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Criminals</title>
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
<body style="background-image: url('bg-image3.jpg'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed;">

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
                <li><a href="view_criminals.php" class="active">View Criminals</a></li>
                <li><a href="add_crime.php">Record Crime</a></li>
                <li><a href="view_cases.php">View Cases</a></li>
            </ul>
        </nav>

        <main>
            <h2>Criminal Records</h2>

            <?php if (isset($success_message)) echo "<div class='success-message'>$success_message</div>"; ?>
            <?php if (isset($error_message)) echo "<div class='error-message'>$error_message</div>"; ?>

            <!-- Edit Form -->
            <?php if ($edit_criminal): ?>
                <div class="form-container">
                    <h3>Edit Criminal: <?= $edit_criminal['first_name'] . " " . $edit_criminal['last_name'] ?></h3>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $edit_criminal['id'] ?>">
                        <label>First Name:</label>
                        <input type="text" name="first_name" value="<?= $edit_criminal['first_name'] ?>" required>

                        <label>Last Name:</label>
                        <input type="text" name="last_name" value="<?= $edit_criminal['last_name'] ?>" required>

                        <label>Gender:</label>
                        <select name="gender" required>
                            <option value="Male" <?= $edit_criminal['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $edit_criminal['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= $edit_criminal['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>

                        <label>Date of Birth:</label>
                        <input type="date" name="date_of_birth" value="<?= $edit_criminal['date_of_birth'] ?>" required>

                        <label>Address:</label>
                        <textarea name="address" required><?= $edit_criminal['address'] ?></textarea>

                        <label>Arrest Date:</label>
                        <input type="date" name="arrest_date" value="<?= $edit_criminal['arrest_date'] ?>" required>

                        <button type="submit" name="update_criminal" class="btn">Update</button>
                        <a href="view_criminals.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            <?php endif; ?>

            <div class="search-bar">
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search by name...">
            </div>

            <div class="table-container">
                <table id="criminalsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                            <th>Address</th>
                            <th>Arrest Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>{$row['id']}</td>";
                                echo "<td>{$row['first_name']} {$row['last_name']}</td>";
                                echo "<td>{$row['gender']}</td>";
                                echo "<td>{$row['date_of_birth']}</td>";
                                echo "<td>{$row['address']}</td>";
                                echo "<td>{$row['arrest_date']}</td>";
                                echo "<td class='actions'>
                                        <a href='view_criminal_details.php?id={$row['id']}' class='btn btn-small'>View</a>
                                        <a href='view_criminals.php?edit={$row['id']}' class='btn btn-small btn-secondary'>Edit</a>
                                        <a href='view_criminals.php?delete={$row['id']}' class='btn btn-small btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No criminal records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer>
            <p>&copy; 2025 Criminal Management System</p>
        </footer>
    </div>

    <script>
    function searchTable() {
        var input = document.getElementById("searchInput");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("criminalsTable");
        var tr = table.getElementsByTagName("tr");

        for (var i = 0; i < tr.length; i++) {
            var td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                var txtValue = td.textContent || td.innerText;
                tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }
    }
    </script>
</body>
</html>
