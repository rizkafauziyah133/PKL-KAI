<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../config/login.php");
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

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Ambil parameter dari query string
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Ambil nama_unit dari user_account berdasarkan user_id
$sql_user = "SELECT nama_unit FROM user_account WHERE no = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user_data = $user_result->fetch_assoc();
$nama_unit = $user_data['nama_unit'];

// Ambil data dari database untuk user yang sedang login
$sql = "SELECT lokasi, nama_pemilik, nama_barang, sn, keterangan, tanggal, status FROM user_data WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Hitung total jumlah data sesuai dengan pencarian
$sql_count = "SELECT COUNT(*) as total FROM user_data 
              WHERE CONCAT(lokasi, ' ', nama_pemilik, ' ', nama_barang, ' ', tanggal, ' ', status) LIKE ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("s", $search_param);
$stmt_count->execute();
$total_result = $stmt_count->get_result();
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengisian Data</title>
    <link rel="stylesheet" type="text/css" href="../../css/page-user/troubleshooting-application.css">
    <link rel="stylesheet" type="text/css" href="../../css/background/background.css">
    <link rel="stylesheet" type="text/css" href="../../css/feature/feature.css">
    <script src="../script/feature.js" defer></script>
</head>
<body>
    <!--Feature-->
    <div class="container-background-left">
        <div class="container-feature-title">PT.KAI DIVRE IV TJ.KARANG</div>
        <div class="container-feature-option">
            <a href="dashboard-unit.php">
                <div class="feature">
                    <img src="../../asset/icon-dashboardadmin.png">
                    <div class="feature-label">Dashboard Unit</div>
                </div>
            </a>
            <div class="feature">
                <img src="../../asset/icon-akununit.png">
                <div class="feature-label">Pengajuan Troubleshooting</div>
            </div>
            <a href="submission-status.php  ">
                <div class="feature">
                    <img src="../../asset/icon-kelolatroubleshooting.png">
                    <div class="feature-label">Status Pengajuan</div>
                </div>
            </a>
            <span>
                <a id="logoutBtn" class="container-feature-icon-back">
                    <img src="../../asset/back.png">
                </a>
            </span>

            <div onclick="logoutModal()" class="modal">
                <div class="modal-content">
                    <p>Apakah Anda ingin logout?</p>
                    <div class="modal-buttons">
                        <button id="confirmLogout">Ya</button>
                        <button id="cancelLogout">Tidak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-background-right">
        <div class="background-top">Selamat Datang <?php echo htmlspecialchars($nama_unit); ?></div>  
        <div class="data-form-title">Formulir Pengajuan Data</div>
        <div class="container-data">
            <form class="data-form" action="save_data.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>" >
                    
                    <label for="lokasi">Lokasi:</label><br>
                    <input type="text" id="lokasi" name="lokasi" required><br><br>
                    
                    <label for="nama_pemilik">Nama Pemilik:</label><br>
                    <input type="text" id="nama_pemilik" name="nama_pemilik" required><br><br>
                    
                    <label for="nama_barang">Nama Barang:</label><br>
                    <input type="text" id="nama_barang" name="nama_barang" required><br><br>
                    
                    <label for="sn">Serial Number (SN):</label><br>
                    <input type="text" id="sn" name="sn" required><br><br>
                    
                    <label for="keterangan">Keterangan:</label><br>
                    <textarea id="keterangan" name="keterangan" rows="3" required></textarea><br><br>
                    
                    <input type="submit" value="Simpan Data">
            </form>
        </div>
    </div>
</body>
</html>
