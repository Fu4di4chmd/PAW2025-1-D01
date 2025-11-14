<?php
require_once "database.php";
if (session_status() === PHP_SESSION_NONE)
    session_start();

function addSiswa(array $data)
{
    // mengambil data yang ada di dalam koneksi
    global $connect;
    if (
        empty($data['nama_lengkap_siswa']) ||
        empty($data['nisn_siswa']) ||
        empty($data['alamat_siswa']) ||
        empty($data['tanggal_lahir_siswa']) ||
        empty($data['jenis_kelamin_siswa']) ||
        empty($data['no_telp_siswa']) ||
        empty($data['password_siswa']) ||
        empty($_FILES['foto_siswa']['name'])
    ) {
        echo "
            <div class='error-popup-container'>
                <p class='eror-akun'>Harap isi semuanya</p>
            </div>";
        return;
    }

    // proses memasukan data ke database
    $stmnt = $connect->prepare("
        INSERT INTO siswa (NISN_SISWA, NAMA_LENGKAP_SISWA, ALAMAT_SISWA, TANGGAL_LAHIR_SISWA, JENIS_KELAMIN_SISWA, NO_TELPON_SISWA, FOTO_SISWA_SISWA, PASSWORD_SISWA)
        VALUES (:nisn_siswa, :nama_lengkap_siswa, :alamat_siswa, :tanggal_lahir_siswa, :jenis_kelamin_siswa, :no_telpon_siswa, :foto_siswa_siswa, :password_siswa)
        ");
    $stmnt->execute([
        ":nisn_siswa" => htmlspecialchars($data['nisn_siswa']),
        ":nama_lengkap_siswa" => htmlspecialchars($data['nama_lengkap_siswa']),
        ":alamat_siswa" => htmlspecialchars($data['alamat_siswa']),
        ":tanggal_lahir_siswa" => htmlspecialchars($data['tanggal_lahir_siswa']),
        ":jenis_kelamin_siswa" => htmlspecialchars($data['jenis_kelamin_siswa']),
        ":no_telpon_siswa" => htmlspecialchars($data['no_telp_siswa']),
        ":foto_siswa_siswa" => uploadFileGambarSiswa(),
        ":password_siswa" => md5($data['password_siswa']),
    ]);
    if ($stmnt->rowCount() > 0) {
        header("Location: ../index.php");
    } else {
        echo "Gagal insert data.";
    }
}

function uploadFileGambarSiswa()
{
    $namaFile = $_FILES['foto_siswa']['name'];
    $ukuranFile = $_FILES['foto_siswa']['size'];
    $error = $_FILES['foto_siswa']['error'];
    $tmpName = $_FILES['foto_siswa']['tmp_name'];

    // cek apakah user tidak upload file
    if ($error === 4) {
        echo "<script>alert('Pilih gambar terlebih dahulu!');</script>";
        return false;
    }

    // ekstensi yang diperbolehkan
    $ekstensiValid = ['jpg', 'jpeg', 'png'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    // cek ekstensi
    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Yang anda upload bukan file gambar (hanya JPG, JPEG, PNG)!');</script>";
        return false;
    }

    // cek ukuran (maks 1 MB)
    if ($ukuranFile > 1000000) {
        echo "<script>alert('Ukuran gambar terlalu besar! Maksimal 1 MB');</script>";
        return false;
    }

    // generate nama unik biar tidak bentrok
    $namaBaru = uniqid() . '.' . $ekstensiFile;
    move_uploaded_file($tmpName, "../source/upload/images/" . $namaBaru);
    return $namaBaru;
}


function loginUser($username, $password)
{
    global $connect;

    // 1️⃣ Cek dulu apakah username cocok di tabel admin
    $stmntAdmin = $connect->prepare("SELECT * FROM admin WHERE USERNAME_ADMIN = :username");
    $stmntAdmin->execute([':username' => $username]);
    $admin = $stmntAdmin->fetch();

    if ($admin) {
        // Jika username ditemukan di tabel admin
        if ($admin['PASSWORD_ADMIN'] === $password) {
            $_SESSION['LOGIN_ROLE'] = 'admin';
            $_SESSION['ADMIN_ID'] = $admin['ID_ADMIN'];
            $_SESSION['NAMA_ADMIN'] = $admin['NAMA_ADMIN'];
            $_SESSION['USERNAME_ADMIN'] = $admin['USERNAME_ADMIN'];

            header("Location: ./admin/index.php");
            exit;
        } else {
            echo "<script>alert('Password admin salah!');</script>";
            return false;
        }
    }

    // 2️⃣ Jika bukan admin, cek di tabel siswa berdasarkan NISN
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
        } else {
            echo "
            <div class='error-popup-container'>
                <p class='eror-akun'>Password siswa salah</p>
            </div>";
            return false;
        }
    }

    // 3️⃣ Jika tidak ditemukan di kedua tabel
    echo "
            <div class='error-popup-container'>
                <p class='eror-akun'>Akun tidak ditemukan</p>
            </div>
        ";
    return false;
}


function updateSiswa($nisn, $data)
{
    global $connect;

    // Ambil data siswa lama
    $stmnt = $connect->prepare("SELECT * FROM siswa WHERE NISN_SISWA = :nisn");
    $stmnt->execute([':nisn' => $nisn]);
    $siswaLama = $stmnt->fetch();

    if (!$siswaLama) {
        echo "Data siswa tidak ditemukan!";
        return false;
    }

    // Cek apakah ada upload foto baru
    if (isset($_FILES['foto_siswa']) && $_FILES['foto_siswa']['error'] === 0) {
        $fotoBaru = uploadFileGambarSiswa();
        if ($fotoBaru === false) {
            return; // upload gagal
        }

        // Hapus foto lama (kalau ada)
        if (!empty($siswaLama['FOTO_SISWA_SISWA'])) {
            $oldPath = "../source/upload/images/" . $siswaLama['FOTO_SISWA_SISWA'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
    } else {
        // Tidak upload foto baru → pakai foto lama
        $fotoBaru = $siswaLama['FOTO_SISWA_SISWA'];
    }

    // Proses update data ke database
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
        ':nama' => $data['nama_lengkap_siswa'],
        ':alamat' => $data['alamat_siswa'],
        ':tgl' => $data['tanggal_lahir_siswa'],
        ':jk' => $data['jenis_kelamin_siswa'],
        ':telp' => $data['no_telp_siswa'],
        ':foto' => $fotoBaru,
        ':nisn' => $nisn
    ]);

    if ($update) {
        header("Location: ../dashboard/index.php");
        exit;
    } else {
        echo "Gagal update data siswa.";
    }
}


// --- FUNGSI BARU UNTUK UPLOAD DOKUMEN ---
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
        echo "<script>alert('File " . $keterangan . " tidak valid (hanya PDF, JPG, JPEG, PNG)!');</script>";
        return false;
    }

    // Cek ukuran (maks 2 MB)
    if ($ukuranFile > 2000000) {
        echo "<script>alert('Ukuran file " . $keterangan . " terlalu besar! Maksimal 2 MB');</script>";
        return false;
    }

    $namaBaru = 'doc_' . uniqid() . '.' . $ekstensiFile;
    move_uploaded_file($tmpName, "../source/upload/documents/" . $namaBaru);
    return $namaBaru;
}

// --- FUNGSI BARU UNTUK PROSES PENDAFTARAN (MENGGANTIKAN FUNGSI pendaftaran() LAMA) ---
function addPendaftaran(array $data, $nisn)
{
    global $connect;

    // 1. VALIDASI MASUKAN WAJIB
    if (
        empty($data['nama_wali']) ||
        empty($data['no_hp']) ||
        empty($data['program_pondok']) ||
        empty($data['jurusan']) ||
        empty($_FILES['file_akte']['name']) ||
        empty($_FILES['file_kk']['name'])
    ) {
        echo "<div class='error-popup-container'><p class='eror-akun'>Harap isi semua field pendaftaran dan upload dokumen wajib!</p></div>";
        return false;
    }

    // 2. Cek duplikasi (agar siswa tidak double daftar jika status masih 0)
    $stmnt_check = $connect->prepare("SELECT COUNT(*) FROM pendaftaran WHERE NISN_SISWA = :nisn AND STATUS = '0'");
    $stmnt_check->execute([':nisn' => $nisn]);
    if ($stmnt_check->fetchColumn() > 0) {
        echo "<div class='error-popup-container'><p class='eror-akun'>Anda sudah memiliki pendaftaran dengan status 'Masih Proses'.</p></div>";
        return false;
    }

    // 3. Ambil ID_JURUSAN
    $nama_jurusan_input = trim($data['jurusan']);
    $stmnt_jurusan = $connect->prepare("SELECT ID_JURUSAN FROM jurusan WHERE NAMA_JURUSAN = :nama_jurusan");
    $stmnt_jurusan->execute([':nama_jurusan' => $nama_jurusan_input]);
    $jurusan_data = $stmnt_jurusan->fetch();
    $id_jurusan = $jurusan_data ? $jurusan_data['ID_JURUSAN'] : NULL;

    // 4. Upload Dokumen (Akte dan KK)
    $path_akte = uploadDocumentFile('file_akte', 'Akte Kelahiran');
    $path_kk = uploadDocumentFile('file_kk', 'Kartu Keluarga');

    if ($path_akte === false || $path_kk === false) {
        return false;
    }

    // 5. Simpan ke tabel PENDATARAN (Tabel 1)
    $status_awal = "0"; // 0 = Masih Proses
    $stmnt_pendaftaran = $connect->prepare("
        INSERT INTO pendaftaran (NISN_SISWA, ID_JURUSAN, TANGGAL_PENDAFTARAN, NAMA_WALI, NO_HP_WALI, STATUS, JURUSAN, JENJANG)
        VALUES (:nisn, :id_jurusan, NOW(), :wali, :hp, :status, :jurusan, :jenjang)
        /* TANGGAL DIISI OTOMATIS OLEH MySQL NOW() */
    ");

    $result_pendaftaran = $stmnt_pendaftaran->execute([
        ":nisn" => $nisn,
        ":id_jurusan" => $id_jurusan,
        ":wali" => htmlspecialchars($data['nama_wali']),
        ":hp" => htmlspecialchars($data['no_hp']),
        ":status" => $status_awal,
        ":jurusan" => $nama_jurusan_input,
        ":jenjang" => htmlspecialchars($data['program_pondok']),
    ]);

    $id_pendaftaran = $connect->lastInsertId();

    if ($result_pendaftaran) {
        // 6. Simpan ke tabel DOKUMEN (Tabel 2 - Akte)
        $stmnt_dok_akte = $connect->prepare("
            INSERT INTO dokumen (ID_PENDAFTARAN, KETERANGAN, JENIS_DOKUMEM, PATH_FILE)
            VALUES (:id_pendaftaran, 'Akte Kelahiran Siswa', 'Akte Kelahiran', :path_file)
        ");
        $stmnt_dok_akte->execute([
            ":id_pendaftaran" => $id_pendaftaran,
            ":path_file" => $path_akte
        ]);

        // 7. Simpan ke tabel DOKUMEN (Tabel 3 - Kartu Keluarga)
        $stmnt_dok_kk = $connect->prepare("
            INSERT INTO dokumen (ID_PENDAFTARAN, KETERANGAN, JENIS_DOKUMEM, PATH_FILE)
            VALUES (:id_pendaftaran, 'Kartu Keluarga Siswa', 'Kartu Keluarga', :path_file)
        ");
        $stmnt_dok_kk->execute([
            ":id_pendaftaran" => $id_pendaftaran,
            ":path_file" => $path_kk
        ]);

        header("Location: browse_calon.php");
        exit;
    } else {
        echo "Gagal menyimpan data pendaftaran.";
        return false;
    }
}
?>