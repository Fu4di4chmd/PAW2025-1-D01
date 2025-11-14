<link rel="stylesheet" href="../source/css/style.css">
<section class="header-admin-siswa">
    <h2 class="logo">PPDB Pesantern AL - AMIN</h2>
    <nav>
        <ul>
            <li><a href="../dashboard/index.php">Beranda</a></li>
            <li><a href="../siswa/browse_calon.php">Informasi</a></li>
            <li><a href="../siswa/pendaftaran.php">Pendaftaran</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
            <li><a href="../siswa/edit_siswa.php"><?= $siswa['NAMA_LENGKAP_SISWA']; ?></a></li>

            <!-- FOTO + NAMA -->
            <li class="user-info">
                <a href="../siswa/edit_siswa.php" class="user-link">
                    <img src="../source/upload/images/<?= $siswa['FOTO_SISWA_SISWA']; ?>" alt="Foto Siswa">
                </a>
            </li>
        </ul>
    </nav>
</section>