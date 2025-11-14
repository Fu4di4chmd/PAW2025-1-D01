<?php
// ... (Bagian include function.php dan cek login sudah ada) ...
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
$stmnt = $connect->prepare("SELECT * FROM siswa WHERE NISN_SISWA = :nisn");
$stmnt->execute([':nisn' => $nisn]);
$siswa = $stmnt->fetch();

require_once "../components/header.php"
    ?>
<div class="form-container">
    <h2>Riwayat Pendaftaran dan Status</h2>
    <img src="../source/upload/images/<?= $siswa['FOTO_SISWA_SISWA'] ?>" alt="">