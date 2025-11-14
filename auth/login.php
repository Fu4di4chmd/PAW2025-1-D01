<!-- Form login siswa/admin
Proses cek username & password
Session set -->
<?php
require_once(__DIR__ . "/../config/function.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil input dan bersihkan dari karakter berbahaya
    $username = htmlspecialchars(trim($_POST['nisn_login_siswa']));
    $password = htmlspecialchars(trim($_POST['password_login_siswa']));

    // Enkripsi password (pastikan di database juga tersimpan dalam md5)
    $password = md5($password);

    // Panggil fungsi login gabungan (admin + siswa)
    loginUser($username, $password);
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
    <section class="login-admin-siswa">
        <div class="login">
            <form action="" method="POST">
                <div class="login-admin-siswa-input">
                    <label for="nisn_login_siswa">Masukkan NISN</label>
                    <input type="text" name="nisn_login_siswa" id="nisn_login_siswa">
                </div>

                <div class="login-admin-siswa-input">
                    <label for="password_login_siswa">Masukkan Password</label>
                    <input type="text" name="password_login_siswa" id="password_login_siswa">
                </div>
                belum punya akun? <a href="./siswa/registrasi_siswa.php">Register</a>

                <button type="submit" name="submit_login_siswa" class="btn-submit">login</button>
            </form>
        </div>
    </section>
</body>

</html>