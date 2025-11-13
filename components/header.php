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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="../source/css/header.css">
</head>

<body>
    <header class="navbar">
        <div class="navbar-overlay"></div>

        <div class="navbar-content">
            <div class="brand">NAMA PESANTREEEEEEN</div>

            <nav class="menu">
                <a href="../index.php">Beranda</a>
                <a href="../informasi.php">Informasi</a>
                <a href="../registrasi.php">Registrasi</a>
                <a href="../auth/login.php">Login Siswa</a>
            </nav>

            <div class="profile">
                <img src="../source/upload/images/<?= htmlspecialchars($siswa['FOTO_SISWA_SISWA']); ?>" alt="Foto">
                <span><?= htmlspecialchars($siswa['NAMA_LENGKAP_SISWA']); ?></span>
            </div>
        </div>
    </header>
</body>

</html>