<?php
require_once "database.php";
if (session_status() === PHP_SESSION_NONE)
    session_start();

// ====================
// eror validasi
// =====================

function displayErrorPopup($message)
{
    echo "
        <div class='error-popup-container'>
            <span class='error-icon'>⚠️⚠️<span>
            <p class='eror-akun'>{$message}</p>
        </div>";
    return false;
}

// ====================
// succes validasi
// =====================

function displaySuccesPopup($message)
{
    echo "
        <div class='error-popup-container'>
            <span class='error-icon'>✔️<span>
            <p class='eror-akun'>{$message}</p>
        </div>";
    return false;
}

// ================
// siswa registrasi
// ================

function addSiswa(array $data)
{
    global $connect;
    $required_fields = [
        'nama_lengkap_siswa',
        'nisn_siswa',
        'alamat_siswa',
        'tanggal_lahir_siswa',
        'jenis_kelamin_siswa',
        'no_telp_siswa',
        'password_siswa'
    ];
    $has_empty_field = false;

    foreach ($required_fields as $field) {
        if (empty(trim($data[$field]))) {
            $has_empty_field = true;
            break;
        }
    }

    if (empty($_FILES['foto_siswa']['name'])) {
        $has_empty_field = true;
    }

    if ($has_empty_field) {
        return displayErrorPopup("Harap isi semua field pendaftaran!");
    }

    $nisn_input = trim($data['nisn_siswa']);
    if (checkNisnDuplication($nisn_input)) {
        return displayErrorPopup("NISN ({$nisn_input}) sudah terdaftar. Silakan login atau gunakan NISN lain.");
    }

    $foto_siswa = uploadFileGambarSiswa();
    if ($foto_siswa === false) {
        return;
    }

    $stmnt = $connect->prepare("
        INSERT INTO siswa (NISN_SISWA, NAMA_LENGKAP_SISWA, ALAMAT_SISWA, TANGGAL_LAHIR_SISWA, JENIS_KELAMIN_SISWA, NO_TELPON_SISWA, FOTO_SISWA_SISWA, PASSWORD_SISWA)
        VALUES (:nisn_siswa, :nama_lengkap_siswa, :alamat_siswa, :tanggal_lahir_siswa, :jenis_kelamin_siswa, :no_telpon_siswa, :foto_siswa_siswa, :password_siswa)
    ");

    $result = $stmnt->execute([
        ":nisn_siswa" => htmlspecialchars($data['nisn_siswa']),
        ":nama_lengkap_siswa" => htmlspecialchars($data['nama_lengkap_siswa']),
        ":alamat_siswa" => htmlspecialchars($data['alamat_siswa']),
        ":tanggal_lahir_siswa" => htmlspecialchars($data['tanggal_lahir_siswa']),
        ":jenis_kelamin_siswa" => htmlspecialchars($data['jenis_kelamin_siswa']),
        ":no_telpon_siswa" => htmlspecialchars($data['no_telp_siswa']),
        ":foto_siswa_siswa" => $foto_siswa,
        ":password_siswa" => md5($data['password_siswa']),
    ]);

    if ($result && $stmnt->rowCount() > 0) {
        header("Location: ../index.php");
        exit;
    } else {
        return displayErrorPopup("Gagal insert data. Harap coba lagi.");
    }
}

// ===================
// cek duplikasi nisn
// ===================

function checkNisnDuplication(string $nisn_input)
{
    global $connect;

    $stmntDbl = $connect->prepare("SELECT COUNT(*) FROM siswa WHERE NISN_SISWA = :nisn");
    $stmntDbl->execute([
        ":nisn" => $nisn_input,
    ]);
    return $stmntDbl->fetchColumn() > 0;
}

// ===================
// upload gambar siswa
// ===================

function uploadFileGambarSiswa()
{
    $file = $_FILES['foto_siswa'];
    $ekstensiValid = ['jpg', 'jpeg', 'png'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return displayErrorPopup('Pilih gambar terlebih dahulu!');
        }
        return displayErrorPopup('Terjadi kesalahan saat mengunggah file.');
    }

    $ekstensiFile = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        return displayErrorPopup('Yang anda upload bukan file gambar (hanya JPG, JPEG, PNG)!');
    }

    if ($file['size'] > 1000000) {
        return displayErrorPopup('Ukuran gambar terlalu besar! Maksimal 1 MB');
    }

    $namaBaru = uniqid() . '.' . $ekstensiFile;
    $targetDir = "../source/upload/images/";

    if (move_uploaded_file($file['tmp_name'], $targetDir . $namaBaru)) {
        return $namaBaru;
    }

    return displayErrorPopup('Gagal memindahkan file ke server.');
}

// ===================
// fungsi login admin
// ===================

function handleAdminLogin($username, $password)
{
    global $connect;
    $stmntAdmin = $connect->prepare("SELECT * FROM admin WHERE USERNAME_ADMIN = :username");
    $stmntAdmin->execute([':username' => $username]);
    $admin = $stmntAdmin->fetch();

    if ($admin) {
        if ($admin['PASSWORD_ADMIN'] === $password) {
            $_SESSION['LOGIN_ROLE'] = 'admin';
            $_SESSION['ADMIN_ID'] = $admin['ID_ADMIN'];
            $_SESSION['NAMA_ADMIN'] = $admin['NAMA_ADMIN'];
            $_SESSION['USERNAME_ADMIN'] = $admin['USERNAME_ADMIN'];
            header("Location: ./admin/index.php");
            exit;
        }
        return displayErrorPopup('Password admin salah');
    }
    return false;
}

// ===================
// fungsi login siswa
// ===================

function handleSiswaLogin($username, $password)
{
    global $connect;
    $stmntSiswa = $connect->prepare("SELECT * FROM siswa WHERE NISN_SISWA = :nisn");
    $stmntSiswa->execute([':nisn' => $username]);
    $siswa = $stmntSiswa->fetch();

    if ($siswa) {
        if ($siswa['PASSWORD_SISWA'] === $password) {
            $_SESSION['LOGIN_ROLE'] = 'siswa';
            $_SESSION['NISN_SISWA'] = $siswa['NISN_SISWA'];
            $_SESSION['NAMA_SISWA'] = $siswa['NAMA_LENGKAP_SISWA'];
            header("Location:./dashboard/index.php");
            exit;
        }
        return displayErrorPopup('Password siswa salah');
    }
    return false;
}

// ===================
// login admin / siswa
// ===================

function loginUser($username, $password)
{
    if (handleAdminLogin($username, $password)) {
        return true;
    }
    if (handleSiswaLogin($username, $password)) {
        return true;
    }
    return displayErrorPopup('Akun tidak ditemukan');
}

// ====================
// siswa update profile
// ====================

function updateSiswa($nisn, $data)
{
    global $connect;

    $required_fields = [
        'nama_lengkap_siswa',
        'alamat_siswa',
        'tanggal_lahir_siswa',
        'jenis_kelamin_siswa',
        'no_telp_siswa'
    ];
    foreach ($required_fields as $field) {
        if (empty(trim($data[$field]))) {
            return displayErrorPopup("Semua field wajib diisi untuk update profile!");
        }
    }
    $stmnt = $connect->prepare("SELECT * FROM siswa WHERE NISN_SISWA = :nisn");
    $stmnt->execute([':nisn' => $nisn]);
    $siswaLama = $stmnt->fetch();

    if (!$siswaLama) {
        return displayErrorPopup("Data siswa tidak ditemukan untuk NISN ini!");
    }

    $fotoBaru = $siswaLama['FOTO_SISWA_SISWA'];

    if (isset($_FILES['foto_siswa']) && $_FILES['foto_siswa']['error'] === 0) {

        $uploadedFileName = uploadFileGambarSiswa();

        if ($uploadedFileName === false) {
            return;
        }

        if (!empty($siswaLama['FOTO_SISWA_SISWA'])) {
            $oldPath = "../source/upload/images/" . $siswaLama['FOTO_SISWA_SISWA'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $fotoBaru = $uploadedFileName;
    }

    $stmnt = $connect->prepare("
        UPDATE siswa SET
            NAMA_LENGKAP_SISWA = :nama,
            ALAMAT_SISWA = :alamat,
            TANGGAL_LAHIR_SISWA = :tgl,
            JENIS_KELAMIN_SISWA = :jk,
            NO_TELPON_SISWA = :telp,
            FOTO_SISWA_SISWA = :foto
        WHERE NISN_SISWA = :nisn
    ");

    $update = $stmnt->execute([
        ':nama' => htmlspecialchars($data['nama_lengkap_siswa']),
        ':alamat' => htmlspecialchars($data['alamat_siswa']),
        ':tgl' => $data['tanggal_lahir_siswa'],
        ':jk' => $data['jenis_kelamin_siswa'],
        ':telp' => htmlspecialchars($data['no_telp_siswa']),
        ':foto' => $fotoBaru,
        ':nisn' => $nisn
    ]);

    if ($update) {
        header("Location: ../dashboard/index.php");
        exit;
    } else {
        return displayErrorPopup("Gagal memperbarui data siswa Silakan coba lagi.");
    }
}

// =====================
// Siswa upload document
// =====================

function uploadDocumentFile($fileKey, $keterangan)
{
    $namaFile = $_FILES[$fileKey]['name'];
    $ukuranFile = $_FILES[$fileKey]['size'];
    $error = $_FILES[$fileKey]['error'];
    $tmpName = $_FILES[$fileKey]['tmp_name'];

    if ($error === 4) {
        return false;
    }

    // Ekstensi yang diperbolehkan (pdf, jpg, jpeg, png)
    $ekstensiValid = ['pdf', 'jpg', 'jpeg', 'png'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        displayErrorPopup("File tidak valid (hanya PDF, JPG, JPEG, PNG");
        return false;
    }

    if ($ukuranFile > 2000000) {
        echo displayErrorPopup("Ukuran file terlalu besar! Maksimal 2 MB");
        return false;
    }

    $namaBaru = 'doc_' . uniqid() . '.' . $ekstensiFile;
    move_uploaded_file($tmpName, "../source/upload/documents/" . $namaBaru);
    return $namaBaru;
}

// ===========================
// siswa melakukan pendaftaran
// ===========================

function insertDocument($id_pendaftaran, $keterangan, $jenis, $path)
{
    global $connect;
    $stmnt = $connect->prepare("
        INSERT INTO dokumen (ID_PENDAFTARAN, KETERANGAN, JENIS_DOKUMEM, PATH_FILE)
        VALUES (:id_pendaftaran, :keterangan, :jenis, :path_file)
    ");
    return $stmnt->execute([
        ":id_pendaftaran" => $id_pendaftaran,
        ":keterangan" => $keterangan,
        ":jenis" => $jenis,
        ":path_file" => $path
    ]);
}

function getJurusanIdByName(string $nama_jurusan_input)
{
    global $connect;
    $stmnt = $connect->prepare("SELECT ID_JURUSAN FROM jurusan WHERE NAMA_JURUSAN = :nama");
    $stmnt->execute([':nama' => trim($nama_jurusan_input)]);
    $jurusan_data = $stmnt->fetch();
    return $jurusan_data ? $jurusan_data['ID_JURUSAN'] : NULL;
}

function processDocumentUploads($id_pendaftaran, $fileKey, $keterangan, $jenis)
{
    $path = uploadDocumentFile($fileKey, $keterangan);
    if ($path === false) {
        return false;
    }

    $success = insertDocument($id_pendaftaran, $keterangan, $jenis, $path);

    if (!$success) {
        unlink("../source/upload/documents/" . $path);
        return false;
    }
    return true;
}

function addPendaftaran(array $data, $nisn)
{
    global $connect;

    $required_fields = ['nama_wali', 'no_hp', 'program_pondok', 'jurusan'];
    foreach ($required_fields as $field) {
        if (empty(trim($data[$field]))) {
            return displayErrorPopup("Harap isi semua field pendaftaran!");
        }
    }

    if (empty($_FILES['file_akte']['name']) || empty($_FILES['file_kk']['name'])) {
        return displayErrorPopup("Harap upload semua dokumen wajib (Akte & Kartu Keluarga)!");
    }

    $stmnt_check = $connect->prepare("SELECT COUNT(*) FROM pendaftaran WHERE NISN_SISWA = :nisn AND STATUS = '0'");
    $stmnt_check->execute([':nisn' => $nisn]);
    if ($stmnt_check->fetchColumn() > 0) {
        return displayErrorPopup("Anda sudah memiliki pendaftaran dengan status 'Masih Proses'.");
    }

    $nama_jurusan_input = trim($data['jurusan']);
    $id_jurusan = getJurusanIdByName($nama_jurusan_input);

    $status_penerimaan = "0";
    $stmnt_pendaftaran = $connect->prepare("
        INSERT INTO pendaftaran (NISN_SISWA, ID_JURUSAN, TANGGAL_PENDAFTARAN, NAMA_WALI, NO_HP_WALI, STATUS, JURUSAN, JENJANG)
        VALUES (:nisn, :id_jurusan, NOW(), :wali, :hp, :status, :jurusan, :jenjang)
    ");

    $result_pendaftaran = $stmnt_pendaftaran->execute([
        ":nisn" => $nisn,
        ":id_jurusan" => $id_jurusan,
        ":wali" => htmlspecialchars($data['nama_wali']),
        ":hp" => htmlspecialchars($data['no_hp']),
        ":status" => $status_penerimaan,
        ":jurusan" => $nama_jurusan_input,
        ":jenjang" => htmlspecialchars($data['program_pondok']),
    ]);

    $id_pendaftaran = $connect->lastInsertId();

    if ($result_pendaftaran) {
        $akte_success = processDocumentUploads($id_pendaftaran, 'file_akte', 'Akte Kelahiran Siswa', 'Akte Kelahiran');
        $kk_success = processDocumentUploads($id_pendaftaran, 'file_kk', 'Kartu Keluarga Siswa', 'Kartu Keluarga');

        if ($akte_success && $kk_success) {
            header("Location: browse_calon.php");
            exit;
        } else {
            return displayErrorPopup("Pendaftaran gagal di tingkat dokumen.");
        }
    } else {
        return displayErrorPopup("Gagal menyimpan data pendaftaran utama.");
    }
}