<?php
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
$email = $_POST['email'];
$username = $_POST['username'];
$nama_unit = $_POST['unit_name'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Query untuk menyimpan data
$sql = "INSERT INTO user_account (username, password, nama_unit, email) 
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $username, $password, $nama_unit, $email);

if ($stmt->execute()) {
    header("Location: unit-account.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>