<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../config/admin-login.php");
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Ambil ID akun yang akan dihapus
$no = isset($_POST['no']) ? (int)$_POST['no'] : 0;

if ($no > 0) {
    // Mulai transaksi
    $conn->begin_transaction();
    
    try {
        // Hapus data terkait di tabel user_data
        $sql = "DELETE FROM user_data WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $no);
        $stmt->execute();

        // Hapus akun dari tabel user_account
        $sql = "DELETE FROM user_account WHERE no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $no);
        $stmt->execute();
        
        // Commit transaksi
        $conn->commit();

        // Redirect atau tampilkan pesan sukses
        header("Location: unit-account.php?success=1");
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        
        // Tampilkan pesan kesalahan
        echo "Terjadi kesalahan: " . $e->getMessage();
    }

    $stmt->close();
}

$conn->close();
?>
