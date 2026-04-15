<?php
include 'config.php';

// BAGIAN HEADER SWEETALERT
echo "<!DOCTYPE html>
<html>
<head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body>";

// Ambil data dari POST
$id     = $_POST['id_barang'];
$nama   = mysqli_real_escape_string($conn, $_POST['nama_barang']);
$jenis  = mysqli_real_escape_string($conn, $_POST['jenis_barang']);
$stok   = $_POST['stok'];
$satuan = mysqli_real_escape_string($conn, $_POST['satuan']);
$harga  = $_POST['harga'];
$desc   = mysqli_real_escape_string($conn, $_POST['deskripsi']);

// Cek apakah ada foto baru yang diupload
if (!empty($_FILES['gambar']['name'])) {
    
    // PROSES PEMBERSIHAN FOTO LAMA (AGAR TIDAK DOUBLE)
    // Ambil nama file lama dari database
    $query_foto_lama = mysqli_query($conn, "SELECT gambar FROM barang WHERE id_barang = '$id'");
    $data_foto_lama  = mysqli_fetch_assoc($query_foto_lama);
    $foto_lama       = $data_foto_lama['gambar'];

    // Jika ada foto lama dan filenya beneran ada di folder, maka hapus!
    if ($foto_lama != "" && file_exists("assets/img/barang/" . $foto_lama)) {
        unlink("assets/img/barang/" . $foto_lama);
    }
    // AKHIR PEMBERSIHAN 

    $nama_foto = time() . "_" . $_FILES['gambar']['name'];
    $path = "assets/img/barang/" . $nama_foto;
    
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $path)) {
        $query = "UPDATE barang SET nama_barang='$nama', jenis_barang='$jenis', stok='$stok', satuan='$satuan', harga='$harga', deskripsi='$desc', gambar='$nama_foto' WHERE id_barang='$id'";
    }
} else {
    // Kalau nggak ganti foto
    $query = "UPDATE barang SET nama_barang='$nama', jenis_barang='$jenis', stok='$stok', satuan='$satuan', harga='$harga', deskripsi='$desc' WHERE id_barang='$id'";
}

// 3. EKSEKUSI KE DATABASE
if (mysqli_query($conn, $query)) {
    echo "<script>
        // Bunyi Ting!
        new Audio('assets/audio/ta-da_yrvBrlS.mp3').play().catch(e => console.log('Audio off'));

        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data barang & foto lama sudah dibersihkan.',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'daftar_barang.php';
        });
    </script>";
} else {
    $pesan_error = mysqli_error($conn);
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Update!',
            text: 'Error: $pesan_error',
        }).then(() => {
            window.history.back();
        });
    </script>";
}
echo "</body></html>";
?>