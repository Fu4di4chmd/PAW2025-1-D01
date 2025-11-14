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
    <section class="dashboard-siswa">
        <div class="container-siswa">
            <h2>Selamat Datang di PPDB Online</h2>
            <p>Sekolah Berbasis Pesantren Modern yang Berkomitmen Mencetak Generasi Qur'ani, Unggul dalam Prestasi
                Akademik, Mandiri, Berjiwa Pemimpin, dan Berakhlakul Karimah untuk Membangun Peradaban Islam yang
                Gemilang.</p>
        </div>
        <div class="visi-misi">
            <h3>🏫 Visi</h3>
            <p>“Menjadi lembaga pendidikan berbasis pesantren modern yang unggul dalam pembentukan generasi Qur’ani,
                berakhlak mulia, berwawasan global, dan berjiwa kepemimpinan.”</p>
            <h3>🎯 Misi</h3>
            <p>
                Menanamkan nilai-nilai keislaman melalui kegiatan keagamaan yang terintegrasi dalam seluruh aspek
                pendidikan.
            </p>
        </div>
    </section>
</body>

</html>