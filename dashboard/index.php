<?php
require_once(__DIR__ . "/../config/function.php");
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
</head>

<body>
    <h2>Selamat datang di web kami</h2>

    <div>
        <img src="../source/upload/images/<?= $siswa['FOTO_SISWA_SISWA']; ?>" alt="Foto Siswa" width="100"
            style="border-radius: 50%;">
        <p><?= htmlspecialchars($siswa['NAMA_LENGKAP_SISWA']); ?></p>
    </div>

    <a href="../siswa/edit_siswa.php">Edit Profil</a> |
    <a href="../auth/logout.php">Logout</a>
</body>

</html>