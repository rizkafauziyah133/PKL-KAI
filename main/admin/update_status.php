<?php
// Koneksi ke database
$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "inventory_database";   

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses update status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sn = $conn->real_escape_string($_POST['sn']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Update status
    $updateSql = "UPDATE user_data SET status='$status' WHERE sn='$sn'";
    if ($conn->query($updateSql) === TRUE) {
        echo "Status berhasil diperbarui";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
