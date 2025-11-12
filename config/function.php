<!-- semua function CRUD
upload file
login
register
update status
get jurusan
get siswa
get dokumen
dll -->

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
        echo "Semua field wajib diisi!";
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


function loginSiswa($nisn, $password)
{
    global $connect;
    $stmnt = $connect->prepare("SELECT * FROM siswa WHERE NISN_SISWA = :nisn_login_siswa");
    $stmnt->execute([
        ":nisn_login_siswa" => $nisn,
    ]);
    $log = $stmnt->fetch();
    if (!$log) {
        echo "Username tidak valid!";
        return;
    }
    if ($log['PASSWORD_SISWA'] !== $password) {
        echo "Password salah!";
        return;
    }
    $_SESSION['NISN_SISWA'] = $log['NISN_SISWA'];
    header("Location: ./dashboard/index.php");
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
    if ($_FILES['foto_siswa']['error'] === 0) {
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

function showName($nisn)
{
    global $connect;
    $stmnt = $connect->prepare("SELECT NAMA_LENGKAP_SISWA FROM siswa WHERE NISN_SISWA = :nisn_siswa");
    $stmnt->execute([
        ":nisn_siswa" => $nisn,
    ]);
    $st = $stmnt->fetch();
    return $st['NAMA_LENGKAP_SISWA'];
}
?>