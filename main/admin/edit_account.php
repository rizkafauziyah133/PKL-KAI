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

// Ambil data dari form
$no = $_POST['no'];
$email = $_POST['email'];
$nama_unit = $_POST['nama_unit'];
$username = $_POST['username'];
$password = $_POST['password'];

if (!empty($password)) {
    // Jika password diisi, hash password tersebut
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE user_account SET email = ?, nama_unit = ?, username = ?, password = ? WHERE no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $email, $nama_unit, $username, $hashed_password, $no);
} else {
    // Jika password tidak diisi, update tanpa mengubah password
    $sql = "UPDATE user_account SET email = ?, nama_unit = ?, username = ? WHERE no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $email, $nama_unit, $username, $no);
}

if ($stmt->execute()) {
    // Berhasil diperbarui, redirect ke halaman manage_accounts
    header("Location: unit-account.php");
    exit();
} else {
    // Tampilkan pesan kesalahan
    echo "Terjadi kesalahan: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
