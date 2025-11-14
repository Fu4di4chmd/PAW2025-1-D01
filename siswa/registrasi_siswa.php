<?php
require_once(__DIR__ . "/../config/function.php");
function getStickyValue($fieldName)
{
    return isset($_POST[$fieldName]) ? htmlspecialchars(trim($_POST[$fieldName])) : '';
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    addSiswa($_POST);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Siswa</title>
    <link rel="stylesheet" href="../source/css/style.css">
</head>

<body>
    <section class="registrasi-siswa">
        <div class="login-container">
            <form action="" method="POST" enctype="multipart/form-data">
                <h2>Registrasi Siswa</h2>

                <div class="login-input">
                    <label for="nama_lengkap_siswa">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap_siswa" id="nama_lengkap_siswa"
                        placeholder="Masukkan nama lengkap" value="<?= getStickyValue('nama_lengkap_siswa') ?>">
                </div>

                <div class="login-input">
                    <label for="nisn_siswa">NISN</label>
                    <input type="text" name="nisn_siswa" id="nisn_siswa" value="<?= getStickyValue('nisn_siswa') ?>"
                        placeholder="Masukkan NISN">
                </div>

                <div class="login-input">
                    <label for="alamat_siswa">Alamat</label>
                    <input type="text" name="alamat_siswa" id="alamat_siswa"
                        value="<?= getStickyValue('alamat_siswa') ?>" placeholder="Masukkan alamat">
                </div>

                <div class="login-input">
                    <label for="tanggal_lahir_siswa">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir_siswa" <?= getStickyValue('tanggal_lahir_siswa') ?>id="tanggal_lahir_siswa" placeholder="Masukkan tangal-lahir">
                </div>

                <div class="login-input">
                    <label for="jenis_kelamin_siswa">Jenis Kelamin</label>
                    <select name="jenis_kelamin_siswa" id="jenis_kelamin_siswa">
                        <option value="laki_laki">Laki-Laki</option>
                        <option value="perempuan">Perempuan</option>
                    </select>
                </div>

                <div class="login-input">
                    <label for="no_telp_siswa">Nomor Telepon</label>
                    <input type="text" name="no_telp_siswa" id="no_telp_siswa"
                        value="<?= getStickyValue('no_telp_siswa') ?>" placeholder="Masukkan no telpon">
                </div>

                <div class="login-input">
                    <label for="foto_siswa">Foto Siswa</label>
                    <input type="file" name="foto_siswa" id="foto_siswa" accept="image/*">
                </div>

                <div class="login-input">
                    <label for="password_siswa">Password</label>
                    <input type="password" name="password_siswa" id="password_siswa" <?= getStickyValue('password_siswa') ?> placeholder="Masukkan password">
                </div>
                <p>Sudah punya akun?<a href="../index.php"> Login</a></p>
                <button type="submit" name="submit_siswa_register" class="btn-submit">Daftar Sekarang</button>
            </form>
        </div>
    </section>

</body>

</html>