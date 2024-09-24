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

// Query untuk menghitung jumlah status 'Diproses'
$sqlTrouble = "SELECT COUNT(*) AS total_diproses FROM user_data WHERE status = 'Diproses'";
$resultTrouble = $conn->query($sqlTrouble);
$totalDiproses = $resultTrouble->fetch_assoc()['total_diproses'];

// Query untuk menghitung jumlah status 'Selesai'
$sqlDone = "SELECT COUNT(*) AS total_selesai FROM user_data WHERE status = 'Selesai'";
$resultDone = $conn->query($sqlDone);
$totalSelesai = $resultDone->fetch_assoc()['total_selesai'];

// Hitung persentase selesai dari total data yang 'Diproses' dan 'Selesai'
$totalProcessed = $totalDiproses + $totalSelesai;
$percentageDone = $totalProcessed > 0 ? round(($totalSelesai / $totalProcessed) * 100, 2) : 0;

// Query untuk jumlah data per bulan
$sqlMonthly = "SELECT MONTH(tanggal) AS bulan, COUNT(*) AS jumlah 
                FROM user_data 
                GROUP BY MONTH(tanggal)
                ORDER BY MONTH(tanggal)";
$resultMonthly = $conn->query($sqlMonthly);

// Siapkan data untuk grafik
$bulanData = array_fill(1, 12, 0); // Array untuk bulan Januari sampai Desember
while ($row = $resultMonthly->fetch_assoc()) {
    $bulanData[$row['bulan']] = $row['jumlah'];
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../../css/page-admin/dashboard-admin.css">
    <link rel="stylesheet" type="text/css" href="../../css/background/background.css">
    <link rel="stylesheet" type="text/css" href="../../css/feature/feature.css">
    <script src="../../script/feature.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        function formatDate(date) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        function displayDate() {
            const today = new Date();
            const formattedDate = formatDate(today);
            
            // Menampilkan tanggal di semua elemen div
            document.getElementById('date1').textContent = formattedDate;
            document.getElementById('date2').textContent = formattedDate;
            document.getElementById('date3').textContent = formattedDate;
        }

        // Memanggil fungsi untuk menampilkan tanggal saat halaman dimuat
        window.onload = displayDate;

        
    </script>
</head>
<body>
    <!--Feature-->
    <div class="container-background-left">
        <div class="container-feature-title">PT.KAI DIVRE IV TJ.KARANG</div>
        <div class="container-feature-option">
            <div class="feature">
                <img src="../../asset/icon-dashboardadmin.png">
                <div class="feature-label">Dashboard Admin</div>
            </div>
            <a href="unit-account.php">
                <div class="feature">
                    <img src="../../asset/icon-akununit.png">
                    <div class="feature-label">Akun Unit</div>
                </div>
            </a>
            <a href="manage-troubleshooting.php">
                <div class="feature">
                    <img src="../../asset/icon-kelolatroubleshooting.png">
                    <div class="feature-label">Kelola Troubleshooting</div>
                </div>
            </a>
            <span id="container-icon-logout" class="container-feature-icon-back">
                <img src="../../asset/back.png">
            </span>
        </div>
    </div>

    <!-- Logout -->
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
        <!--Troubleshooting Data Box-->
        <div class="container-box-data">
            <!--Data Trouble-->
            <div class="box-data">
                <div class="box-data-number">
                    <img class="box-data-number-icon" src="../../asset/icon-user.png">
                    <div class="container-data-box">
                        <div id="data-trouble"><?= $totalDiproses ?></div>
                        <div id="text-data-trouble">Total Trouble</div>
                    </div>
                </div>
                <div class="box-data-detail">
                    <div class="data-detail-text">view detail</div>
                    <div id="date1" class="date-container"></div>    
                </div>
            </div>
            <!--Data Selesai-->
            <div class="box-data">
                <div class="box-data-number">
                    <img class="box-data-number-icon" src="../../asset/icon-user.png">
                    <div class="container-data-box">
                        <div id="data-done"><?= $totalSelesai ?></div>
                        <div id="text-data-done">Total Selesai</div>
                    </div>
                </div>
                <div class="box-data-detail">
                    <div class="data-detail-text">view detail</div>
                    <div id="date2" class="date-container"></div>
                </div>
            </div>
            <!--Data Persentase Total-->
            <div class="box-data">
                <div class="box-data-number">
                    <img class="box-data-number-icon" src="../../asset/icon-user.png">
                    <div class="container-data-box">
                        <div id="data-percent"><?= $percentageDone ?>%</div>
                        <div id="text-data-percent">Persentase Selesai</div>
                    </div>
                </div>
                <div class="box-data-detail">
                    <div class="data-detail-text">view detail</div>
                    <div id="date3" class="date-container"></div>
                </div>
            </div>
        </div>

        <!--Troubleshooting Chart-->
        <div class="container-grafik">
            <div class="chart-title">Grafik Troubleshooting</div>
            <div class="chart-data">
                <canvas id="monthlyDataChart"></canvas>
            </div>
        </div>
        
        <!--Troubleshooting List-->
        <div class="container-list">
            <div class="container-submission-data">
            <div class="list-title">List Troubleshooting</div>
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
                                <td>{$no}</td>
                                <td>{$row['tanggal']}</td>
                                <td>{$row['lokasi']}</td>
                                <td>{$row['nama_pemilik']}</td>
                                <td>{$row['nama_barang']}</td>
                                <td>{$row['sn']}</td>
                                <td>{$row['keterangan']}</td>
                                <td class='{$statusClass}'>{$row['status']}</td>
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
    </div>
    <script>
    function updateRows(value) {
        window.location.href = `?page=1&rows=${value}&search=<?= htmlspecialchars($search) ?>`;
    }

    function search() {
        const searchValue = document.querySelector('.input-search').value;
        window.location.href = `?page=1&rows=${<?= $rowsPerPage ?>}&search=${encodeURIComponent(searchValue)}`;
    }

    function updateStatus(select, sn) {
        const status = select.value;
        fetch('update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `sn=${encodeURIComponent(sn)}&status=${encodeURIComponent(status)}`
        }).then(response => response.text())
          .then(data => {
              if (data === 'success') {
                  alert('Status updated successfully');
              } else {
                  alert('Error updating status');
              }
          });
    }

    // Grafik
    const ctx = document.getElementById('monthlyDataChart').getContext('2d');
    const monthlyDataChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Jumlah Data per Bulan',
                data: <?= json_encode(array_values($bulanData)) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });
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