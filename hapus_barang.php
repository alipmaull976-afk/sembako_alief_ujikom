<?php
include 'config.php';
cek_akses('admin');

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus data dari tabel barang
    $query = "DELETE FROM barang WHERE id_barang = '$id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Barang telah dihapus!'); window.location='daftar_barang.php';</script>";
    } else {
        echo "Gagal menghapus: " . mysqli_error($conn);
    }
} else {
    header("Location: daftar_barang.php");
}
?>