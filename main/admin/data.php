<?php
header('Content-Type: application/json');

// Koneksi ke database
$servername = "localhost"; 
$username = "root";       
$password = "";           
$dbname = "inventory";    

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data berdasarkan bulan
$sql = "SELECT MONTH(tanggal) AS bulan, COUNT(*) AS jumlah
        FROM tanggal
        GROUP BY MONTH(tanggal)
        ORDER BY MONTH(tanggal)";
$result = $conn->query($sql);

$labels = [];
$values = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bulan = (int)$row['bulan'];
        $labels[] = date('F', mktime(0, 0, 0, $bulan, 10)); // Nama bulan
        $values[] = (int)$row['jumlah'];
    }
}

// Jika ada bulan yang tidak ada datanya, tambahkan nilai 0
for ($i = 1; $i <= 12; $i++) {
    if (!in_array(date('F', mktime(0, 0, 0, $i, 10)), $labels)) {
        $labels[] = date('F', mktime(0, 0, 0, $i, 10));
        $values[] = 0;
    }
}

// Mengurutkan berdasarkan bulan
array_multisort($labels, SORT_ASC, $values);

echo json_encode([
    'labels' => $labels,
    'values' => $values
]);

$conn->close();
?>
