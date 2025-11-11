<!-- Form register + proses masuk ke database. -->
<?php
require_once("../config/function.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    addSiswa($_POST);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <section class="create_registrasi">
        <div class="registrasi_siswa">
            <form action="" method="POST" enctype="multipart/form-data">
                <table border="0" cellpading="10" cellspacing="4">
                    <tbody>
                        <tr>
                            <td>
                                <label for="nama_lengkap_siswa">Masukkan nama lengkap</label>
                            </td>
                            <td>
                                <input type="text" name="nama_lengkap_siswa" id="nama_lengkap_siswa">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="nisn_siswa">Masukkan nisn</label>
                            </td>
                            <td>

                                <input type="text" name="nisn_siswa" id="nisn_siswa">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="alamat_siswa">Masukkan alamt</label>
                            </td>
                            <td>

                                <input type="text" name="alamat_siswa" id="alamat_siswa">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="tanggal_lahir_siswa">Masukkan tanggal lahir</label>
                            </td>
                            <td>

                                <input type="date" name="tanggal_lahir_siswa" id="tanggal_lahir_siswa">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="jenis_kelamin_siswa">Jenis kelamin</label>
                            </td>
                            <td>
                                <select name="jenis_kelamin_siswa" id="jenis_kelamin_siswa">
                                    <option value="laki_laki">Laki Laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <label for="no_telp_siswa">Masukkan no telp</label>
                            </td>
                            <td>

                                <input type="text" name="no_telp_siswa" id="no_telp_siswa">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="foto_siswa">Masukkan foto siswa</label>
                            </td>
                            <td>

                                <input type="file" name="foto_siswa" id="foto_siswa">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="password_siswa">Masukkan password</label>
                            </td>
                            <td>

                                <input type="text" name="password_siswa" id="password_siswa">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="submit" name="submit_siswa_register">Kirim</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </section>
</body>

</html>