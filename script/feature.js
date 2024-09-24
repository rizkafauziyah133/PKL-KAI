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


// Ambil elemen tombol dan modal
const logoutModal = document.getElementById("logoutModal");
const confirmLogout = document.getElementById('confirmLogout');
const cancelLogout = document.getElementById('cancelLogout');

// Ketika tombol logout ditekan, tampilkan modal
function logoutBtn() {
    logoutModal.style.position = "fixed";
}

// Ketika tombol "Ya" ditekan, lakukan logout
confirmLogout.onclick = function() {
    window.location.href = "../main/user/logout.php"; 
}

// Ketika tombol "Tidak" ditekan, tutup modal
cancelLogout.onclick = function() {
    logoutModal.style.display = 'none';
}

// Ketika pengguna mengklik di luar modal, modal akan ditutup
window.onclick = function(event) {
    if (event.target === logoutModal) {
        logoutModal.style.display = 'none';
    }
}

//Logout
function logout() {
    window.location.href = '../login/login.php';
}

// //Mengedit data status
// function updateStatus(sn, selectElement) {
//     if (confirm("Apakah anda yakin ingin merubah data?")) {
//         var statusBaru = selectElement.value;

//         // AJAX request untuk mengupdate status di database
//         var xhr = new XMLHttpRequest();
//         xhr.open("POST", "../main/admin/update_status.php", true);
//         xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
//         xhr.onreadystatechange = function () {
//             if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
//                 // Update warna kolom berdasarkan status baru
//                 selectElement.parentElement.className = "status-" + statusBaru.toLowerCase();
//             }
//         };
//         xhr.send("sn=" + sn + "&status=" + statusBaru);
//     } else {
//         // Jika user membatalkan, reset kembali ke status sebelumnya
//         selectElement.selectedIndex = selectElement.getAttribute("data-initial-status");
//     }
// }

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