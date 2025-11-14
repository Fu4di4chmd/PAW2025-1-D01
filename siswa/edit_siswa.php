<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once("../config/function.php");

// pastikan sudah login
if (!isset($_SESSION['NISN_SISWA'])) {
    header("Location: ../index.php");
    exit;
}

$nisn = $_SESSION['NISN_SISWA'];

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
require_once "../components/header.php"
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profil Siswa</title>
    <link rel="stylesheet" href="../source/css/style.css">
</head>

<body>
    <section class="edit-siswa">
        <h2>Edit Profil Siswa</h2>
        <div class="edit-form-siswa">
            <form action="" method="POST" enctype="multipart/form-data">
                <label>Nama Lengkap:</label>
                <input type="text" name="nama_lengkap_siswa" value="<?= $siswa['NAMA_LENGKAP_SISWA'] ?>">

                <label>Alamat:</label>
                <input type="text" name="alamat_siswa" value="<?= $siswa['ALAMAT_SISWA'] ?>">

                <label>Tanggal Lahir:</label>
                <input type="date" name="tanggal_lahir_siswa" value="<?= $siswa['TANGGAL_LAHIR_SISWA'] ?>">

                <label>Jenis Kelamin:</label>
                <select name="jenis_kelamin_siswa">
                    <option value="L" <?= $siswa['JENIS_KELAMIN_SISWA'] == 'L' ? 'selected' : '' ?>>Laki-Laki</option>
                    <option value="P" <?= $siswa['JENIS_KELAMIN_SISWA'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>

                <label>No Telp:</label>
                <input type="text" name="no_telp_siswa" value="<?= $siswa['NO_TELPON_SISWA'] ?>">

                <label>Foto Saat Ini:</label><br>
                <img src="../source/upload/images/<?= $siswa['FOTO_SISWA_SISWA'] ?>" width="120" alt="Foto Siswa"><br>

                <label>Ganti Foto Baru (opsional):</label>
                <input type="file" name="foto_siswa">

                <button type="submit" name="submit_edit">💾 Simpan Perubahan</button>
            </form>
        </div>
    </section>

</body>

</html>