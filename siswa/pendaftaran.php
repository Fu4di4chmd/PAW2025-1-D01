<?php
require_once(__DIR__ . "/../config/function.php");
if (session_status() === PHP_SESSION_NONE)
    session_start();

// pastikan sudah login
if (!isset($_SESSION['NISN_SISWA'])) {
    header("Location: ../index.php");
    exit;
}

$nisn = $_SESSION['NISN_SISWA'];
global $connect;

// Ambil data siswa
$stmnt = $connect->prepare("SELECT * FROM siswa WHERE NISN_SISWA = :nisn");
$stmnt->execute([':nisn' => $nisn]);
$siswa = $stmnt->fetch();

// Ambil data jurusan dari database
$jurusan_stmnt = $connect->prepare("SELECT * FROM jurusan");
$jurusan_stmnt->execute();
$jurusans = $jurusan_stmnt->fetchAll();

// --- TANGANI SUBMISSION FORM ---
if (isset($_POST['submit_pendaftaran'])) {
    // Panggil fungsi pendaftaran yang ada di config/function.php
    addPendaftaran($_POST, $nisn);
}
// -------------------------------------

require_once '../components/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Siswa</title>
    <link rel="stylesheet" href="../source/css/style.css">
</head>

<body>
    <div class="form-container">
        <h2>Formulir Pendaftaran Siswa</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="nama_wali">Nama Wali</label>
            <input type="text" name="nama_wali" id="nama_wali" placeholder="Masukkan nama wali">

            <label for="no_hp">No HP</label>
            <input type="text" name="no_hp" id="no_hp" placeholder="Masukkan nomor HP wali">

            <label for="program_pondok">Pilih Program Pondok</label>
            <select name="program_pondok" id="program_pondok">
                <option value="">-- Program Pondok --</option>
                <option value="Tahfidz Alquran">Tahfidz Alquran</option>
                <option value="Diniyah">Diniyah</option>
                <option value="Qiroati">Qiroati</option>
            </select>

            <label for="jurusan">Jurusan</label>
            <select name="jurusan" id="jurusan">
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($jurusans as $jurusan): ?>
                    <option value="<?= trim($jurusan['NAMA_JURUSAN']) ?>"><?= $jurusan['NAMA_JURUSAN'] ?></option>
                <?php endforeach; ?>
            </select>

            <div class="document-upload">
                <label for="file_akte">Upload Akte Kelahiran *</label>
                <input type="file" name="file_akte" id="file_akte" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <div class="document-upload">
                <label for="file_kk">Upload Kartu Keluarga (KK) *</label>
                <input type="file" name="file_kk" id="file_kk" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <button type="submit" name="submit_pendaftaran">Daftar</button>
        </form>
    </div>
</body>

</html>