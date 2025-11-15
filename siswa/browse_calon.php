<?php
require_once(__DIR__ . "/../config/function.php");
if (session_status() === PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['NISN_SISWA'])) {
    header("Location: ../index.php");
    exit;
}

$nisn = $_SESSION['NISN_SISWA'];

global $connect;
$stmnt = $connect->prepare("SELECT * FROM siswa WHERE NISN_SISWA = :nisn");
$stmnt->execute([':nisn' => $nisn]);
$siswa = $stmnt->fetch();

global $connect;
$siswas = $connect->prepare("SELECT * FROM pendaftaran WHERE NISN_SISWA = :nisn");
$siswas->execute([':nisn' => $nisn]);
$siswas = $siswas->fetchAll();

global $connect;
$id_pendaftaran = $connect->prepare("SELECT ID_PENDAFTARAN FROM pendaftaran WHERE NISN_SISWA = :nisn");
$id_pendaftaran->execute([':nisn' => $nisn]);
$id_pendaftarans = $id_pendaftaran->fetch();

if (isset($id_pendaftarans['ID_PENDAFTARAN'])) {
    global $connect;
    $showdoc = $connect->prepare("SELECT * FROM dokumen WHERE ID_PENDAFTARAN = :id_pendaftaran");
    $showdoc->execute([':id_pendaftaran' => $id_pendaftarans['ID_PENDAFTARAN']]);
    $showdocs = $showdoc->fetchAll();
} else {
    displayErrorPopup("Tolong daftar terlebih dahulu");
}
require_once "../components/header.php"
    ?>
<div class="form-container">
    <h2 class="judul-riwayat">Riwayat Pendaftaran dan Status</h2>
    <?php foreach ($siswas as $sis): ?>
        <div class="riwayat-item">

            <img src="../source/upload/images/<?= $siswa['FOTO_SISWA_SISWA'] ?>" alt="Foto">

            <p><b>Tanggal:</b> <?= $sis['TANGGAL_PENDAFTARAN'] ?></p>
            <p><b>Nama Wali:</b> <?= $sis['NAMA_WALI'] ?></p>
            <p><b>No HP Wali:</b> <?= $sis['NO_HP_WALI'] ?></p>

            <p><b>Status:</b>
                <?php if ($sis['STATUS'] === "0"): ?>
                    <span class="status-badge status-proses">Proses Verifikasi</span>

                <?php elseif ($sis['STATUS'] === "1"): ?>
                    <span class="status-badge status-pending">Pending</span>

                <?php else: ?>
                    <span class="status-badge status-diterima">Diterima</span>
                <?php endif; ?>
            </p>

            <p><b>Jurusan:</b> <?= $sis['JURUSAN'] ?></p>
            <p><b>Program:</b> <?= $sis['JENJANG'] ?></p>
        </div>
    <?php endforeach ?>
    <?php if (isset($showdocs)): ?>
        <div class="dokumen-list">
            <?php foreach ($showdocs as $showdocument): ?>
                <?php if ($showdocument['JENIS_DOKUMEM'] === "Akte Kelahiran"): ?>
                    <a class="link-doc" href="../source/upload/documents/<?= $showdocument['PATH_FILE'] ?>" target="_blank">
                        Lihat Akte
                    </a>
                <?php elseif ($showdocument['JENIS_DOKUMEM'] === "Kartu Keluarga"): ?>
                    <a class="link-doc" href="../source/upload/documents/<?= $showdocument['PATH_FILE'] ?>" target="_blank">
                        Lihat KK
                    </a>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>
    </div>