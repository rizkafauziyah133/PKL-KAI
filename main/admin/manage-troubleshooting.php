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

// Update status jika ada permintaan POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $sn = $conn->real_escape_string($_POST['sn']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Update status
    $updateSql = "UPDATE user_data SET status='$status' WHERE sn='$sn'";
    if ($conn->query($updateSql) === TRUE) {
        echo "<script>alert('Status berhasil diperbarui');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
    
    // Redirect untuk mencegah pengiriman ulang data
    header("Location: " . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
    exit;
}

// Ambil jumlah baris yang ditampilkan
$rowsPerPage = isset($_GET['rows']) ? (int)$_GET['rows'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rowsPerPage;

// Ambil kata kunci pencarian
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Ambil data dari tabel user_data
$sql = "SELECT tanggal, lokasi, nama_pemilik, nama_barang, sn, keterangan, status 
        FROM user_data 
        WHERE tanggal LIKE '%$search%' 
           OR lokasi LIKE '%$search%' 
           OR nama_pemilik LIKE '%$search%' 
           OR nama_barang LIKE '%$search%' 
           OR sn LIKE '%$search%' 
           OR keterangan LIKE '%$search%' 
           OR status LIKE '%$search%' 
        LIMIT $offset, $rowsPerPage";
$result = $conn->query($sql);

// Hitung total baris untuk pencarian
$totalSql = "SELECT COUNT(*) AS total 
             FROM user_data 
             WHERE tanggal LIKE '%$search%' 
                OR lokasi LIKE '%$search%' 
                OR nama_pemilik LIKE '%$search%' 
                OR nama_barang LIKE '%$search%' 
                OR sn LIKE '%$search%' 
                OR keterangan LIKE '%$search%' 
                OR status LIKE '%$search%'";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalRows = $totalRow['total'];
$totalPages = ceil($totalRows / $rowsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../../css/page-admin/manage-troubleshooting.css">
    <link rel="stylesheet" type="text/css" href="../../css/background/background.css">
    <link rel="stylesheet" type="text/css" href="../../css/feature/feature.css">
    <script src="../../script/feature.js" defer></script>
    <style>
        /* Data Pengajuan Troubleshooting */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .status-diproses {
            background-color: orange;
        }
        .status-gagal {
            background-color: red;
            color: white;
        }
        .status-selesai {
            background-color: green;
            color: white;
        }
        .container-pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination-button {
            cursor: pointer;
            padding: 10px;
            margin: 5px;
            border: 1px solid black;
            background-color: #f2f2f2;
        }
        .pagination-button.disabled {
            cursor: not-allowed;
            background-color: #ddd;
        }
        /* Styling untuk overlay */
.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

/* Styling untuk dialog konfirmasi logout */
.logout-form {
    display: flex;
    justify-content: center;
}

.logout-confirmation {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 40px 50px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    z-index: 1000;
}

.logout-confirmation h3 {
    margin: 0 0 20px;
}

.confirm-logout-button, .cancel-logout-button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 10px;
}

.confirm-logout-button {
    background-color: #f44336;
    color: #fff;
}

.cancel-logout-button {
    background-color: #ccc;
    color: #000;
}

    </style>
    <script>
        function updateRows(rows) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('rows', rows);
            urlParams.set('page', 1); // Reset ke halaman pertama
            window.location.search = urlParams.toString();
        }
        
        function goToPage(page) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', page);
            window.location.search = urlParams.toString();
        }

        function search() {
            const searchInput = document.querySelector('.input-search').value;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('search', searchInput);
            urlParams.set('page', 1); // Reset ke halaman pertama
            window.location.search = urlParams.toString();
        }
        function confirmUpdate(form) {
        if (confirm("Apakah Anda yakin ingin mengubah data?")) {
            form.submit();
        }
    }
    fetch('count_rows.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('rowCount').innerText = 'Jumlah baris: ' + data;
            })
            .catch(error => console.error('Error:', error));    
    </script>
</head>
<body>
    <!--Feature-->
    <div class="container-background-left">
        <div class="container-feature-title">PT.KAI DIVRE IV TJ.KARANG</div>
        <div class="container-feature-option">
            <a href="dashboard-admin.php">
                <div class="feature">
                    <img src="../../asset/icon-dashboardadmin.png">
                    <div class="feature-label">Dashboard Admin</div>
                </div>
            </a>
            <a href="unit-account.php">
                <div class="feature">
                    <img src="../../asset/icon-akununit.png">
                    <div class="feature-label">Akun Unit</div>
                </div>
            </a>
            <div class="feature">
                <img src="../../asset/icon-kelolatroubleshooting.png">
                <div class="feature-label">Kelola Troubleshooting</div>
            </div>
            <span id="container-icon-logout" class="container-feature-icon-back">
                <img src="../../asset/back.png">
            </span>
        </div>
    </div>

    <!-- Konfirmasi Logout -->
    <div class="overlay" id="logoutOverlay"></div>
    <div class="logout-confirmation" id="logoutConfirmation">
        <h3>Apakah Anda yakin ingin keluar?</h3>
        <form class="logout-form" id="logoutForm" action="logout.php" method="post">
            <button type="submit" class="confirm-logout-button">Ya</button>
            <button type="button" class="cancel-logout-button" id="cancelLogout">Tidak</button>
        </form>
    </div>

    <!--Data-->
    <div class="container-background-right">
        <div class="background-top">Selamat Datang di Halaman Admin</div>
        <!--Submission Data-->
        <div class="container-submission-data">
            <div class="container-submission-data-title">Data Pengajuan Troubleshooting</div>
            <div class="container-filter-data">
                <div class="container-data-filter">Tampilkan</div>
                <select class="list-data-filter" onchange="updateRows(this.value)">
                    <option value="10" <?= $rowsPerPage == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= $rowsPerPage == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $rowsPerPage == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $rowsPerPage == 100 ? 'selected' : '' ?>>100</option>
                </select>
                <div>baris</div>
                <input class="input-search" placeholder="Pencarian" value="<?= htmlspecialchars($search) ?>">
                <button onclick="search()">Cari</button>
            </div>
            
            <table>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Nama Pemilik</th>
                    <th>Nama Barang</th>
                    <th>SN</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    $no = $offset + 1; // Inisialisasi nomor baris
                    $data = $result->fetch_all(MYSQLI_ASSOC);
                    $dataCount = count($data);

                    // Menampilkan data
                    foreach ($data as $row) {
                        $statusClass = "";
                        switch ($row["status"]) {
                            case "Diproses":
                                $statusClass = "status-diproses";
                                break;
                            case "Gagal":
                                $statusClass = "status-gagal";
                                break;
                            case "Selesai":
                                $statusClass = "status-selesai";
                                break;
                        }
                        echo "<tr>
                                <form method='post'>
                                    <td>{$no}</td>
                                    <td>{$row['tanggal']}</td>
                                    <td>{$row['lokasi']}</td>
                                    <td>{$row['nama_pemilik']}</td>
                                    <td>{$row['nama_barang']}</td>
                                    <td>{$row['sn']}</td>
                                    <td>{$row['keterangan']}</td>
                                    <td class='{$statusClass}'>
                                        <form method='post' style='display: inline;' onsubmit='confirmUpdate(this); return false;'>
                                            <select name='status' onchange='confirmUpdate(this.form)'>
                                                <option value='Diproses' " . ($row['status'] == 'Diproses' ? 'selected' : '') . ">Diproses</option>
                                                <option value='Selesai' " . ($row['status'] == 'Selesai' ? 'selected' : '') . ">Selesai</option>
                                                <option value='Gagal' " . ($row['status'] == 'Gagal' ? 'selected' : '') . ">Gagal</option>
                                            </select>
                                            <input type='hidden' name='sn' value='{$row['sn']}'>
                                            <input type='hidden' name='update_status' value='1'>
                                        </form>
                                    </td>
                                </form>
                              </tr>";
                        $no++;
                    }

                    // Menambahkan baris kosong jika data kurang dari jumlah yang dipilih
                    for ($i = $dataCount; $i < $rowsPerPage; $i++) {
                        echo "<tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>";
                    }
                } else {
                    // Jika tidak ada data
                    for ($i = 0; $i < $rowsPerPage; $i++) {
                        echo "<tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>";
                    }
                }
                ?>
            </table>

            <div class="container-pagination">
                <button class="pagination-button <?= $page <= 1 ? 'disabled' : '' ?>" onclick="<?= $page > 1 ? 'goToPage(' . ($page - 1) . ')' : '' ?>">&#9664; Prev</button>
                <button class="pagination-button <?= $page >= $totalPages ? 'disabled' : '' ?>" onclick="<?= $page < $totalPages ? 'goToPage(' . ($page + 1) . ')' : '' ?>">Next &#9654;</button>
            </div>
        </div>
    </div>
    <script>
        // Event untuk logout
document.getElementById('container-icon-logout').addEventListener('click', function() {
    document.getElementById('logoutOverlay').style.display = 'block';
    document.getElementById('logoutConfirmation').style.display = 'block';
});

// Event untuk batal logout
document.getElementById('cancelLogout').addEventListener('click', function() {
    document.getElementById('logoutConfirmation').style.display = 'none';
    document.getElementById('logoutOverlay').style.display = 'none';
});

    </script>
</body>
</html>

<?php
$conn->close();
?>
