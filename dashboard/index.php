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
<?php include '../components/header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="../source/css/style.css">
</head>

<body>
    <section class="header">
        <div class="container">
            <h2>Selamat Datang di PPDB Online</h2>
            <p>Sekolah Berbasis Pesantren Modern, Mencetak Generasi Qur'ani dan Berprestasi.</p>
        </div>
    </section>


</body>

</html>