<!-- Update profil siswa setelah login. -->
<?php
session_start();
require_once("../config/function.php");

// pastikan sudah login
if (!isset($_SESSION['NISN_SISWA'])) {
    header("Location: ../auth/login.php");
    exit;
}

$nisn = $_SESSION['NISN_SISWA'];

// Ambil data siswa
global $connect;
$stmnt = $connect->prepare("SELECT * FROM siswa WHERE NISN_SISWA = :nisn");
$stmnt->execute([':nisn' => $nisn]);
$siswa = $stmnt->fetch();

if (!$siswa) {
    echo "Data siswa tidak ditemukan!";
    exit;
}

if (isset($_POST['submit_edit'])) {
    updateSiswa($nisn, $_POST);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profil Siswa</title>
</head>

<body>

    <h2>Edit Profil</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama_lengkap_siswa" value="<?= $siswa['NAMA_LENGKAP_SISWA'] ?>"><br><br>

        <label>Alamat:</label><br>
        <input type="text" name="alamat_siswa" value="<?= $siswa['ALAMAT_SISWA'] ?>"><br><br>

        <label>Tanggal Lahir:</label><br>
        <input type="date" name="tanggal_lahir_siswa" value="<?= $siswa['TANGGAL_LAHIR_SISWA'] ?>"><br><br>

        <label>Jenis Kelamin:</label><br>
        <select name="jenis_kelamin_siswa">
            <option value="L" <?= $siswa['JENIS_KELAMIN_SISWA'] == 'L' ? 'selected' : '' ?>>Laki-Laki</option>
            <option value="P" <?= $siswa['JENIS_KELAMIN_SISWA'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
        </select><br><br>

        <label>No Telp:</label><br>
        <input type="text" name="no_telp_siswa" value="<?= $siswa['NO_TELPON_SISWA'] ?>"><br><br>

        <label>Foto Saat Ini:</label><br>
        <img src="../source/upload/images/<?= $siswa['FOTO_SISWA_SISWA'] ?>" width="100"><br><br>

        <label>Ganti Foto Baru (opsional):</label><br>
        <input type="file" name="foto_siswa"><br><br>

        <button type="submit" name="submit_edit">Simpan Perubahan</button>
    </form>

</body>

</html>