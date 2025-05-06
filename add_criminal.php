<?php
session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

include 'database.php';

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $identifying_marks = mysqli_real_escape_string($conn, $_POST['identifying_marks']);
    $arrest_date = mysqli_real_escape_string($conn, $_POST['arrest_date']);

    $photo = "";
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "assets/";
        if(!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if($check !== false) {
            $photo = $target_dir . uniqid() . "." . $imageFileType;

            if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $photo)) {
                $error_message = "Error uploading file.";
            }
        } else {
            $error_message = "File is not an image.";
        }
    }

    if(empty($error_message)) {
        $sql = "INSERT INTO criminals (first_name, last_name, gender, date_of_birth, address, identifying_marks, arrest_date, photo) 
                VALUES ('$first_name', '$last_name', '$gender', '$date_of_birth', '$address', '$identifying_marks', '$arrest_date', '$photo')";

        if(mysqli_query($conn, $sql)) {
            $success_message = "Criminal record added successfully.";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Criminal - Criminal Management System</title>
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
<body style="background-image: url('bg-image2.webp'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed;">
<body>
<div class="dashboard-container">
    <header>
        <h1>Criminal Management System</h1>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['username']; ?> |
            <a href="index.php?logout=true" style="color: orange;">Logout</a>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add_criminal.php" class="active">Add Criminal</a></li>
            <li><a href="view_criminals.php">View Criminals</a></li>
            <li><a href="add_crime.php">Record Crime</a></li>
            <li><a href="view_cases.php">View Cases</a></li>
        </ul>
    </nav>

    <main>
        <h2>Add New Criminal</h2>

        <?php if(!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if(!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="identifying_marks">Identifying Marks:</label>
                <textarea id="identifying_marks" name="identifying_marks" rows="3"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="arrest_date">Arrest Date:</label>
                    <input type="date" id="arrest_date" name="arrest_date" required>
                </div>

                <div class="form-group">
                    <label for="photo">Photo:</label>
                    <input type="file" id="photo" name="photo" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Add Criminal</button>
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
