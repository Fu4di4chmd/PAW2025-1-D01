<!-- Form login siswa/admin
Proses cek username & password
Session set -->
<?php
require_once("../config/function.php");
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
</head>

<body>
    <form action="" method="POST">
        <table border="0">
            <tr>
                <td>
                    <label for="nisn_login_siswa">Masukkan NISN</label>
                </td>
                <td>
                    <input type="text" name="nisn_login_siswa" id="nisn_login_siswa">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="password_login_siswa">Masukkan Password</label>
                </td>
                <td>
                    <input type="text" name="password_login_siswa" id="password_login_siswa">
                </td>
            </tr>
            <tr>
                <td>
                    <button type="submit" name="submit_login_siswa">Kirim</button>
                </td>
            </tr>
        </table>
    </form>
</body>

</html>