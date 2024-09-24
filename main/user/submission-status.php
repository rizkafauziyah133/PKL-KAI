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
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../../css/page-user/submission-status.css">
    <link rel="stylesheet" type="text/css" href="../../css/background/background.css">
    <link rel="stylesheet" type="text/css" href="../../css/feature/feature.css">
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
            <a href="troubleshooting-application.php"">
                <div class="feature">
                    <img src="../../asset/icon-akununit.png">
                    <div class="feature-label">Pengajuan Troubleshooting</div>
                </div>
            </a>
            <div class="feature">
                <img src="../../asset/icon-kelolatroubleshooting.png">
                <div class="feature-label">Status Pengajuan</div>
            </div>
            <span>
                <a href="logout.php" class="container-feature-icon-back">
                    <img src="../../asset/back.png">
                </a>
            </span>
        </div>
    </div>

    <div class="container-background-right">
        <div class="background-top">Selamat Datang <?php echo htmlspecialchars($nama_unit); ?></div>
        <div class="container-box-data">
            <div class="container-table">
                <div class="table-title">Riwayat Pengiriman Data </div>
                
                <!-- Form untuk memilih jumlah baris per halaman dan pencarian -->
                <form class="data-filter" method="GET" action="submission-status.php">
                    <label for="limit">Tampilkan </label>
                    <select name="limit" id="limit" onchange="this.form.submit()">
                        <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                    </select>
                    <label for="limit">Baris</label>

                    <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="submit" value="Cari">

                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                </form>
                
                <table class="table-data" border="1">
                    <tr>
                        <th>No</th>
                        <th>Lokasi</th>
                        <th>Nama Pemilik</th>
                        <th>Nama Barang</th>
                        <th>SN</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    // Inisialisasi nomor urut berdasarkan halaman
                    $no = $offset + 1; 
                    while ($row = $result->fetch_assoc()) {
                        // Tentukan kelas CSS berdasarkan status
                        $status_class = '';
                        switch ($row['status']) {
                            case 'Diproses':
                                $status_class = 'status-diproses';
                                break;
                            case 'Selesai':
                                $status_class = 'status-selesai';
                                break;
                            case 'Gagal':
                                $status_class = 'status-gagal';
                                break;
                            default:
                                $status_class = '';
                                break;
                        }

                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['lokasi']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_pemilik']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['sn']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                        echo "<td class='$status_class'>" . htmlspecialchars($row['status']) . "</td>";
                        echo "</tr>";
                    }

                    // Tambahkan baris kosong jika kurang dari 10
                    $remaining_rows = $limit - $result->num_rows;
                    for ($i = 0; $i < $remaining_rows; $i++) {
                        echo "<tr>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>

                <!-- Navigasi halaman -->
                <div style="margin-top: 20px;">
                <?php if ($page > 1): ?>
                    <a href="?limit=<?php echo $limit; ?>&page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">← Sebelumnya</a>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?limit=<?php echo $limit; ?>&page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" style="margin-left: 20px;">Berikutnya →</a>
                <?php endif; ?>
            </div>
            </div>
        </div>
    <div>
</body>
</html>