<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama         = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $jenis_barang = mysqli_real_escape_string($conn, $_POST['jenis_barang']);
    $stok         = $_POST['stok'];
    $satuan       = mysqli_real_escape_string($conn, $_POST['satuan']);
    $harga        = $_POST['harga'];
    $deskripsi    = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $nama_gambar_baru = "default.png"; 

    if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != "") {
        $fitur_gambar = $_FILES['gambar']['name'];
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg', 'webp');
        $x = explode('.', $fitur_gambar);
        $ekstensi = strtolower(end($x));
        $file_tmp = $_FILES['gambar']['tmp_name'];
        
        $nama_gambar_baru = time() . '_' . $fitur_gambar;
        $path_upload = __DIR__ . '/assets/img/barang/' . $nama_gambar_baru;

        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if (!move_uploaded_file($file_tmp, $path_upload)) {
                echo "<script>alert('ERROR! Foto gagal masuk folder.'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Ekstensi foto tidak diperbolehkan!'); window.history.back();</script>";
            exit;
        }
    }

    $query = "INSERT INTO barang (nama_barang, jenis_barang, stok, satuan, harga, deskripsi, gambar) VALUES ('$nama', '$jenis_barang', '$stok', '$satuan', '$harga', '$deskripsi', '$nama_gambar_baru')";
    
    // BAGIAN NOTIFIKASI MEWAH
    echo "<!DOCTYPE html>
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>
            body { font-family: 'Inter', sans-serif; background: #f8f9fa; }
        </style>
    </head>
    <body>";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            // Mainkan suara sukses mpeg dari folder kamu
            new Audio('assets/audio/ta-da_yrvBrlS.mp3').play();
            
            Swal.fire({
                icon: 'success',
                title: 'Mantap!',
                text: 'Barang Berhasil Ditambah!',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = 'daftar_barang.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Waduh!',
                text: 'Gagal menyimpan ke database!',
                confirmButtonColor: '#007aff'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }
    
    echo "</body></html>";
}
?>