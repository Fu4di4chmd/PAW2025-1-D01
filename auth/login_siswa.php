<!-- Form login siswa/admin
Proses cek username & password
Session set -->
<?php
require_once(__DIR__ . "/../config/function.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nisn_siswa_login = $_POST['nisn_login_siswa'];
    $password_siswa_login = md5($_POST['password_login_siswa']);
    loginSiswa($nisn_siswa_login, $password_siswa_login);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa / Admin</title>
    <link rel="stylesheet" href="./source/css/style.css">
</head>

<body>
    <div class="login-container">
    <form action="" method="POST">
        <div class="login-input">
        <label for="nisn_login_siswa">Masukkan NISN</label>
        <input type="text" name="nisn_login_siswa" id="nisn_login_siswa">
        </div>

        <div class="login-input">
        <label for="password_login_siswa">Masukkan Password</label>
        <input type="text" name="password_login_siswa" id="password_login_siswa">
        </div>

        <button type="submit" name="submit_login_siswa" class="btn-submit">login</button>
    </form>
    </div>
</body>

</html>