document.addEventListener('DOMContentLoaded', () => {
    const loginAdmin = document.getElementById('container-login-admin');
    const loginUser = document.getElementById('container-login-user');
    const loginUserLink = document.getElementById('login-user');
    const loginAdminLink = document.getElementById('login-admin');

    loginUserLink.addEventListener('click', () => {
        loginAdmin.style.display = 'none';
        loginUser.style.display = 'flex';
    });

    loginAdminLink.addEventListener('click', () => {
        loginUser.style.display = 'none';
        loginAdmin.style.display = 'flex';
    });
});
