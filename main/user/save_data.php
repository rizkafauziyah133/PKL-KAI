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

// Mengambil data dari formulir
$user_id = $_POST['user_id'];
$lokasi = $_POST['lokasi'];
$nama_pemilik = $_POST['nama_pemilik'];
$nama_barang = $_POST['nama_barang'];
$sn = $_POST['sn'];
$keterangan = $_POST['keterangan'];

// Query untuk menyimpan data ke database
$sql = "INSERT INTO user_data (user_id, lokasi, nama_pemilik, nama_barang, sn, keterangan, tanggal, status) 
        VALUES (?, ?, ?, ?, ?, ?, CURRENT_DATE, 'Diproses')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssss", $user_id, $lokasi, $nama_pemilik, $nama_barang, $sn, $keterangan);

//Kondisi True dan False
if ($stmt->execute()) {
    header("Location: dashboard-unit.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
