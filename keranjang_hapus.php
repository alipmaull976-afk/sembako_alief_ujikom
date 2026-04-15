<?php
include 'config.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM keranjang WHERE id_keranjang = '$id'");
header("Location: kasir_transaksi.php");
?>