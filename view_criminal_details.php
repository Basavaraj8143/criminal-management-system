<?php  
session_start();
include 'database.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Get criminal ID from the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $criminal_id = $_GET['id'];
    $query = "SELECT * FROM criminals WHERE id = $criminal_id";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $criminal = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Criminal not found.";
    }
} else {
    $error_message = "Invalid criminal ID.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Criminal Details</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 1200px;
            max-height:800px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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
            margin-top: 250px;
        }

        .criminal-details {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 10px 20px;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .criminal-details img {
            grid-column: 1 / -1;
            justify-self: center;
            max-width: 250px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .criminal-details p {
            margin: 0;
        }

        .criminal-details a.btn {
            grid-column: 1 / -1;
            justify-self: center;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
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
                <a href="index.php?logout=true" style="color: #ecf0f1;">Logout</a>
            </div>
        </header>

        <main>
            <h2 style="text-align: center;">Criminal Details</h2>

            <?php if (isset($error_message)) echo "<div class='error-message'>$error_message</div>"; ?>

            <?php if (isset($criminal)): ?>
                <div class="criminal-details">
                    <!-- Display Image -->
                    <?php if (!empty($criminal['image_path'])): ?>
                        <img src="assets/<?php echo $criminal['image_path']; ?>" alt="Criminal Image">
                    <?php else: ?>
                        <p style="grid-column: 1 / -1; text-align: center;"><strong>No image available</strong></p>
                    <?php endif; ?>

                    <p><strong>Name:</strong></p>
                    <p><?php echo $criminal['first_name'] . " " . $criminal['last_name']; ?></p>

                    <p><strong>Gender:</strong></p>
                    <p><?php echo $criminal['gender']; ?></p>

                    <p><strong>Date of Birth:</strong></p>
                    <p><?php echo $criminal['date_of_birth']; ?></p>

                    <p><strong>Address:</strong></p>
                    <p><?php echo $criminal['address']; ?></p>

                    <p><strong>Arrest Date:</strong></p>
                    <p><?php echo $criminal['arrest_date']; ?></p>

                    <a href="view_criminals.php" class="btn">Back to List</a>
                </div>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; 2025 Criminal Management System</p>
        </footer>
    </div>
</body>
</html>
