<?php
include 'config.php';
$id_user = $_SESSION['id_user'];
$id_brg  = $_POST['id_barang'];
$jml     = $_POST['jumlah'];

$res  = mysqli_query($conn, "SELECT harga FROM barang WHERE id_barang = '$id_brg'");
$data = mysqli_fetch_assoc($res);
$sub  = $data['harga'] * $jml;

// Cek jika barang sudah ada di keranjang, tinggal update jumlahnya
$cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_barang = '$id_brg' AND id_user = '$id_user'");
if(mysqli_num_rows($cek) > 0) {
    mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + $jml, subtotal = subtotal + $sub WHERE id_barang = '$id_brg' AND id_user = '$id_user'");
} else {
    mysqli_query($conn, "INSERT INTO keranjang (id_barang, id_user, jumlah, subtotal) VALUES ('$id_brg', '$id_user', '$jml', '$sub')");
}
header("Location: kasir_transaksi.php");
?>