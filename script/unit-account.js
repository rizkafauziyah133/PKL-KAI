window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('msg');
    const msgType = urlParams.get('type');

    if (message && msgType) {
        const popup = document.getElementById('popup');
        const popupText = document.getElementById('popup-text');
        popupText.innerText = message;
        popup.classList.add(msgType);
        popup.style.opacity = '1';

        // Menghilangkan notifikasi setelah 3 detik
        setTimeout(() => {
            popup.style.opacity = '0';
            setTimeout(() => {
                popup.style.display = 'none';
                popup.style.opacity = '1';
            }, 500); // Waktu transisi
        }, 3000); // 3 detik
    }
};

