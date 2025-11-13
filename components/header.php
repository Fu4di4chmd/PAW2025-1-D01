
<?php

?>
<link rel="stylesheet" href="../source/css/header.css">
 <header>
    <div class="container">
        <h1 class="logo">PPDB PESANTREN</h1>
        <nav>
            <ul>
                <li><a href="../dashboard/index.php">beranda</a></li>
                <li><a href="info.html">Informasi</a></li>
                <li><a href="register.html">Registrasi</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
                <li><a href="../siswa/edit_siswa.php"><img src="../source/upload/images/<?= $siswa['FOTO_SISWA_SISWA']; ?>" alt="Foto Siswa" width="100"
                    style="border-radius: 50%;">
                    </a>
                    <!-- <a href="../auth/logout.php">Logout</a></li> -->
                </li>
            </ul>
        </nav>
    </div>
</header>

