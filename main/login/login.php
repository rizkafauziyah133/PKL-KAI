<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loginAdminLink = document.getElementById('login-admin');
            const loginUserLink = document.getElementById('login-user');
            const loginAdminContainer = document.getElementById('container-login-admin');
            const loginUserContainer = document.getElementById('container-login-user');

            // Set default view
            loginAdminContainer.style.display = 'flex';
            loginUserContainer.style.display = 'none';

            loginUserLink.addEventListener('click', () => {
                loginAdminContainer.style.display = 'none';
                loginUserContainer.style.display = 'flex';
                loginAdminLink.classList.remove('active');
                loginUserLink.classList.add('active');
            });

            loginAdminLink.addEventListener('click', () => {
                loginUserContainer.style.display = 'none';
                loginAdminContainer.style.display = 'flex';
                loginUserLink.classList.remove('active');
                loginAdminLink.classList.add('active');
            });
        });
    </script>
    <style>
        body, html {
            padding: 0;
            margin: 0;
            overflow: hidden;
            height: 100%;
        }

        /* Background */
        .container-background {
            background-image: url(../../asset/login-image.jpg);
            height: 100vh;
            background-size: cover;
            background-attachment: fixed;
        }

        /* Logo */
        .container-logo {
            background-color: rgba(128, 128, 128, 0.8);
            width: 230px;
            height: 100px;
            position: absolute;
            top: -10px;
            left: 0;
            border-radius: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            scale: 0.5;
            border: 3px solid black;
        }
        .logo {
            width: 150px;
            height: 50px;
        }

        /* Form Login */
        #container-login-admin, #container-login-user {
            width: 100%;
            height: 100%;
            left: 0;
            position: absolute;
            justify-content: center;
            align-items: center;
        }
        .login {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: whitesmoke;
            border: 2px solid black;
            border-radius: 15px;
            box-shadow: 0px 0px 20px 5px grey;
            height: 380px;
            width: 420px;
            overflow: hidden;
        }
        .container-login-all {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
            width: 100%;
        }
        .login-title {
            padding: 20px 10px;
            cursor: pointer;
            font-weight: bold;
            font-size: 20px;
            margin-right: 10px;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }
        .login-title.active {
            border-bottom: 2px solid black;
        }
        .form-login {
            display: flex;
            flex-direction: column;
            width: 75%;
        }
        .login-label {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .login-input {
            margin-bottom: 12px;
            height: 27px;
        }
        .login-input:hover {
            background: linear-gradient(to right, rgba(0, 0, 255, 0.1), white);
        }
        .login-button {
            margin-top: 5px;
            height: 35px;
            border-radius: 20px;
            font-weight: bold;
            color: black;
            background-color: whitesmoke;
        }
        .login-button:hover {
            background-color: rgb(245, 245, 55);
            color: black;
            transition: all 0.1s ease-in-out;
        }
        .reset-password {
            display: flex;
            margin-top: 20px;
        }
        .reset-password div {
            font-size: 13px;
        }
        .reset-password a {
            font-size: 13px;
            font-weight: bold;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <!-- Container Login-->
    <div class="container-background">
        
        <!-- Logo -->
        <div class="container-logo">
            <img class="logo" src="../../asset/Logo_KAI.png" alt="image">
        </div>

        <!-- Login Admin -->
        <div id="container-login-admin">
            <div class="login">
                <div class="container-login-all">
                    <div class="login-title active" id="login-admin">Login Admin</div>
                    <div class="login-title" id="login-user">Login User</div>
                </div>
                <form action="../../config/admin-login.php" class="form-login" method="post">
                    <!-- Input Username -->
                    <label class="login-label" for="username">Username</label>
                    <input class="login-input" id="username-admin" type="text" name="username" required>
                    <!-- Input Password -->
                    <label class="login-label" for="password">Password</label>
                    <input class="login-input" id="password-admin" type="password" name="password" required>
                    <!-- Button Submit -->
                    <input class="login-button" id="login-button-admin" type="submit" value="Submit">
                    <!-- Link Reset Password -->
                    <div class="reset-password">
                        <div>Lupa kata sandi?</div>
                        <a href="">Reset Password</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Login User -->
        <div id="container-login-user">
            <div class="login">
                <div class="container-login-all">
                    <div class="login-title" id="login-admin">Login Admin</div>
                    <div class="login-title active" id="login-user">Login User</div>
                </div>
                <form action="../../config/user-login.php" class="form-login" method="post">
                    <!-- Input Username -->
                    <label class="login-label" for="username">Username</label>
                    <input class="login-input" id="username-user" type="text" name="username" required>
                    <!-- Input Password -->
                    <label class="login-label" for="password">Password</label>
                    <input class="login-input" id="password-user" type="password" name="password" required>
                    <!-- Button Submit -->
                    <input class="login-button" id="login-button-user" type="submit" value="Submit">
                    <!-- Link Reset Password -->
                    <div class="reset-password">
                        <div>Lupa kata sandi?</div>
                        <a href="">Reset Password</a>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</body>
</html>
