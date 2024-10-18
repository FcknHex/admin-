<?php
session_start();
include '../conn.php'; // Include your connection file

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch the total number of users
$query = "SELECT COUNT(id) AS total_users FROM users";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$totalUsers = $row['total_users']; // Get the total number of users

// Fetch the total number of sellers
$querySellers = "SELECT COUNT(seller_id) AS total_sellers FROM sellers";
$resultSellers = mysqli_query($conn, $querySellers);
$rowSellers = mysqli_fetch_assoc($resultSellers);
$totalSellers = $rowSellers['total_sellers']; // Get the total number of sellers

// Fetch the total number of pending seller applicants
$queryPendingApplicants = "SELECT COUNT(applicantID) AS total_pending_applicants FROM seller_applicant WHERE status = 'pending'";
$resultPendingApplicants = mysqli_query($conn, $queryPendingApplicants);
$rowPendingApplicants = mysqli_fetch_assoc($resultPendingApplicants);
$totalPendingApplicants = $rowPendingApplicants['total_pending_applicants']; // Get the total number of pending applicants

// Fetch users for the table
$queryUsers = "SELECT id, firstname, lastname FROM users"; // Only fetch required fields
$resultUsers = mysqli_query($conn, $queryUsers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('https://www.transparenttextures.com/patterns/leaf.png'); /* Subtle leaf pattern */
            background-color: #e0f7fa; /* Light background color for a cozy feel */
            margin: 0;
            padding: 0;
            color: #333;
        }
        nav {
            background-color: #4C8C4A; /* Dark green color */
            color: white;
            padding: 10px;
            position: fixed;
            height: 100%;
            width: 200px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        }
        nav h2 {
            color: white;
            margin: 0;
            padding: 10px 0;
            font-family: 'Georgia', serif; /* A more elegant font */
        }
        nav ul {
            list-style-type: none;
            padding: 0;
        }
        nav li {
            margin: 10px 0;
        }
        nav li a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            border-radius: 5px;
            transition: background 0.3s;
        }
        nav li a:hover {
            background-color: limegreen;
        }
        .container {
            margin-left: 220px; /* Space for the sidebar */
            padding: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-family: 'Georgia', serif; /* A more elegant font */
        }
        .summary {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .summary-box {
            background-color: #ffffff; /* White background for summary boxes */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            text-align: center;
            margin: 0 10px; /* Horizontal margin for spacing */
            margin-bottom: 20px; /* Vertical margin for spacing */
        }
        .summary-box h2 {
            color: #4C8C4A; /* Dark green color */
        }
        /* Button styles */
        .report-buttons {
            display: flex;
            justify-content: flex-start; /* Align to the left */
            margin: 20px 0;
        }
        .report-button {
            background-color: #4C8C4A; /* Dark green color */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 15px 20px; /* Adjusted padding for a more compact look */
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            margin-right: 10px; /* Space between buttons */
        }
        .report-button:hover, .report-button.active {
            background-color: limegreen; /* Lighter green on hover and active state */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #4C8C4A; /* Dark green color */
            color: white;
        }
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px; /* Rounded corners for modal */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        // JavaScript to handle button click
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.report-button');

            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    // Remove 'active' class from all buttons
                    buttons.forEach(btn => btn.classList.remove('active'));
                    // Add 'active' class to the clicked button
                    this.classList.add('active');
                    // Optional: Add your reporting logic here
                    
                });
            });
        });
    </script>
</head>
<body>
    <nav>
        <h2>PlantBazaar</h2>
        <ul>
            <li><a class="active" href="admindashboard.php">Users</a></li>
            <li><a href="adminsellerinfo.php">Sellers</a></li>
            <li><a href="sellerapplicant.php">Seller Applicants</a></li>
            <li><a href="adminreports.php">Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header">
            <h1>Admin Dashboard</h1>
        </div>
        
        <div class="summary">
            <div class="summary-box">
                <h2>Total Users</h2>
                <p><strong><?php echo $totalUsers; ?></strong></p>
            </div>
            <div class="summary-box">
                <h2>Total Sellers</h2>
                <p><strong><?php echo $totalSellers; ?></strong></p>
            </div>
            <div class="summary-box">
                <h2>Total Applicants</h2>
                <p><strong><?php echo $totalPendingApplicants; ?></strong></p>
            </div>
            <div class="summary-box">
                <h2>Total Reports</h2>
                <p><strong></strong></p>
            </div>
        </div>

        <!-- Report Buttons -->
        <div class="report-buttons">
            <button class="report-button">Report Buyer</button>
            <button class="report-button">Report Seller</button>
        </div>
    </div>
</body>
</html>
