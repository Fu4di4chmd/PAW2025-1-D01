<?php

?>
<link rel="stylesheet" href="../source/css/header.css">
<header>
    <div class="container">
        <h1 class="logo">PPDB PESANTREN</h1>
        <nav>
            <ul>
                <li><a href="../dashboard/index.php">Beranda</a></li>
                <li><a href="../siswa/browse_calon.php">Informasi</a></li>
                <li><a href="../siswa/pendaftaran.php">Pendaftaran</a></li>
                <li><a href="../siswa/edit_siswa.php"><img
                            src="../source/upload/images/<?= $siswa['FOTO_SISWA_SISWA']; ?>" alt="Foto Siswa"
                            width="100" style="border-radius: 50%;">
                    </a>
                    <!-- <a href="../auth/logout.php">Logout</a></li> -->
                </li>
            </ul>
        </nav>
    </div>
</header>