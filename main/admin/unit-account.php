<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../config/admin-login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$totalDataSql = "SELECT COUNT(*) AS total FROM user_account WHERE email LIKE '%$search%' OR nama_unit LIKE '%$search%' OR username LIKE '%$search%'";
$totalDataResult = $conn->query($totalDataSql);
$totalData = $totalDataResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $perPage);

$sql = "SELECT no, email, nama_unit, username FROM user_account WHERE email LIKE '%$search%' OR nama_unit LIKE '%$search%' OR username LIKE '%$search%' LIMIT $perPage OFFSET $offset";
$result = $conn->query($sql);

// Query untuk menghitung jumlah baris
$sql2 = "SELECT COUNT(*) AS total FROM user_account ";
$result2 = $conn->query($sql2);

// Ambil jumlah baris
$rowCount = "0";  // Default jika tidak ada data
if ($result2 && $result2->num_rows > 0) {
    $row = $result2->fetch_assoc();
    $rowCount = $row["total"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../../css/page-admin/unit-account.css">
    <link rel="stylesheet" type="text/css" href="../../css/background/background.css">
    <link rel="stylesheet" type="text/css" href="../../css/feature/feature.css">
    <script src="../../script/unit-account.js" defer></script>
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
            <div class="feature">
                <img src="../../asset/icon-akununit.png">
                <div class="feature-label">Akun Unit</div>
            </div>
            <a href="manage-troubleshooting.php">
                <div class="feature">
                    <img src="../../asset/icon-kelolatroubleshooting.png">
                    <div class="feature-label">Kelola Troubleshooting</div>
                </div>
            </a>
            <span id="container-icon-logout" class="container-feature-icon-back">
                <img id="icon-logout" src="../../asset/back.png">
            </span>
        </div>
    </div>

    <!--Data-->
    <div class="container-background-right">
        <div class="background-top">Selamat Datang di Halaman Admin</div>
        <!--Unit Account-->
        <div class="container-unit-account">
            <div class="unit-account-title">Buat Akun Unit</div>
            <form action="user_account_database.php" class="container-unit-account-data" method="post">
                <div class="unit-account-data" id="unit-account-data">
                    <!--Input Email-->
                    <label class="unit-account-data-label" for="email">Email</label>
                    <input class="unit-account-data-input" id="email" name="email" type="email" required>
                    <!--Input Name-->
                    <label class="unit-account-data-label" for="unit_name">Nama Unit</label>
                    <input class="unit-account-data-input" id="unit_name" name="unit_name" type="text" required>
                    <!--Input Username-->
                    <label class="unit-account-data-label" for="username">Username</label>
                    <input class="unit-account-data-input" id="username" name="username" type="text" required>
                    <!--Input Password-->
                    <label class="unit-account-data-label" for="password">Password</label>
                    <input class="unit-account-data-input" id="password" name="password" type="password" required>
                    <!--Submit Data-->
                    <input class="unit-account-data-button" type="submit" value="submit" onclick="submit()">
                </div>
                <div class="unit-account-total-data">
                    <div id="rowCount"><?php echo htmlspecialchars($rowCount); ?></div>
                    <div class=rowCountText>Unit</div>
                </div>
            </form>
        </div>

        <!--List Unit-->
        <div class="container-unit-list">
            <div class="unit-list-title">List Unit</div>
            <form class="container-search" method="GET" action="unit-account.php">
                <label for="per_page">Tampilkan</label>
                <select class="per_page" name="per_page" id="per_page" onchange="this.form.submit()">
                    <option value="10" <?php if ($perPage == 10) echo 'selected'; ?>>10</option>
                    <option value="25" <?php if ($perPage == 25) echo 'selected'; ?>>25</option>
                    <option value="50" <?php if ($perPage == 50) echo 'selected'; ?>>50</option>
                    <option value="100" <?php if ($perPage == 100) echo 'selected'; ?>>100</option>
                </select>
                <div>baris</div>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Cari</button>
            </form>
            
            <table>
                <tr>
                    <th>No</th>
                    <th>Email</th>
                    <th>Nama Unit</th>
                    <th>Username</th>
                    <th>Status</th>
                </tr>
                <?php
                $rowNum = $offset + 1;
            
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $rowNum . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_unit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>
                            <img src='../../asset/edit.png' id='icon-edit' data-id='{$row['no']}' alt='Edit' style='cursor:pointer;'>
                            <img src='../../asset/delete.png' id='icon-delete' data-id='{$row['no']}' alt='Delete' style='cursor:pointer;'>
                        </td>";
                        echo "</tr>";
                        $rowNum++;
                    }
                }
            
                // Isi baris kosong jika kurang dari $perPage
                for ($i = $rowNum; $i <= $perPage + $offset; $i++) {
                    echo "<tr>";
                    echo "<td>" . $i . "</td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";
                }
                ?>
            </table>
            
            <!-- <div class="pagination">
                <div>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?per_page=<?php echo $perPage; ?>&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            </div> -->
            
            <div class="overlay" id="overlay"></div>
            
            <div class="centered-form" id="editForm">
                <h3 class="edit-account-title">Edit Akun Unit</h3>
                <form class="edit-account" id="editAccountForm" action="edit_account.php" method="post">
                    <input type="hidden" name="no" id="editNo">
                    <label for="email">Email</label>
                    <input type="email" id="editEmail" name="email" required>
                    <label for="nama_unit">Nama Unit</label>
                    <input type="text" id="editNamaUnit" name="nama_unit" required>
                    <label for="username">Username</label>
                    <input type="text" id="editUsername" name="username" required>
                    <label for="password">Password</label>
                    <input type="password" id="editPassword" name="password">
                    <button type="submit">Simpan</button>
                    <button type="button" id="cancelEdit">Batal</button>
                </form>
            </div>
            
            <div class="confirm-delete" id="confirmDelete">
                <h3 class="confirm-delete-title">Hapus Akun Unit</h3>
                <p>Apakah Anda yakin ingin menghapus akun unit?</p>
                <form id="deleteAccountForm" action="delete_account.php" method="post">
                    <input type="hidden" name="no" id="deleteNo">
                    <button class="confirm-delete-button" type="submit" class="button">Hapus</button>
                    <button class="confirm-cancel-button" type="button" id="cancelDelete" class="button">Batalkan</button>
                </form>
            </div>

            <!-- Logout confirmation -->
            <div class="overlayy" id="logoutOverlay"></div>
            <div class="logout-confirmation" id="logoutConfirmation">
                <h3>Apakah Anda yakin ingin keluar?</h3>
                <form class="logout-form" id="logoutForm" action="logout.php" method="post">
                    <button type="submit" class="confirm-logout-button">Ya</button>
                    <button type="button" class="cancel-logout-button" id="cancelLogout">Tidak</button>
                </form>
            </div>

            <script>
                // Event untuk edit
                document.querySelectorAll('#icon-edit').forEach(item => {
                    item.addEventListener('click', function() {
                        const no = this.getAttribute('data-id');
                        const email = this.parentNode.parentNode.children[1].textContent;
                        const nama_unit = this.parentNode.parentNode.children[2].textContent;
                        const username = this.parentNode.parentNode.children[3].textContent;
                        
                        document.getElementById('editNo').value = no;
                        document.getElementById('editEmail').value = email;
                        document.getElementById('editNamaUnit').value = nama_unit;
                        document.getElementById('editUsername').value = username;
                        document.getElementById('editPassword').value = '';
            
                        document.getElementById('overlay').style.display = 'block';
                        document.getElementById('editForm').style.display = 'block';
                    });
                });
            
                // Event untuk batal edit
                document.getElementById('cancelEdit').addEventListener('click', function() {
                    document.getElementById('editForm').style.display = 'none';
                    document.getElementById('overlay').style.display = 'none';
                });
            
                // Event untuk hapus
                document.querySelectorAll('#icon-delete').forEach(item => {
                    item.addEventListener('click', function() {
                        const no = this.getAttribute('data-id');
                        document.getElementById('deleteNo').value = no;
                        document.getElementById('overlay').style.display = 'block';
                        document.getElementById('confirmDelete').style.display = 'block';
                    });
                });
            
                // Event untuk batal hapus
                document.getElementById('cancelDelete').addEventListener('click', function() {
                    document.getElementById('confirmDelete').style.display = 'none';
                    document.getElementById('overlay').style.display = 'none';
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
        </div>
    </div>
</body>
</html>