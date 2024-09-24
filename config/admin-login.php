<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: ../main/admin/dashboard-admin.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin_account WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['user_id'] = $user; // Set the session variable
        header("Location: ../main/admin/dashboard-admin.php");
        exit();
    } else {
        echo "Invalid username or password.";
    }
} else {
    header("Location: ../main/login/login.php");
}

$conn->close();
?>
