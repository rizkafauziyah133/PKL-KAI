<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Ambil data dari formulir
$username = $_POST['username'];
$password = $_POST['password'];

// Query untuk memeriksa pengguna
$sql = "SELECT no, password FROM user_account WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($user_id, $hashed_password);
$stmt->fetch();

if ($stmt->num_rows == 1) {
    if (password_verify($password, $hashed_password)) {
        // Set session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        session_regenerate_id(true); // Mengganti session ID dengan yang baru
        header("Location: ../main/user/dashboard-unit.php?user_id=" . $user_id);
        exit();
    } else {
        $_SESSION['login_error'] = "Password anda salah";
    }
} else {
    $_SESSION['login_error'] = "Username tidak terdaftar";
}

$stmt->close();
$conn->close();

// Redirect kembali ke halaman login dengan error
header("Location: ../main/login/login.php");
exit();
?>
