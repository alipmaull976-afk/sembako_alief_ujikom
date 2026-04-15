<?php
include 'config.php';
date_default_timezone_set('Asia/Jakarta');
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$id_user = $_SESSION['id_user'];
$total   = $_POST['total']; // Ini Subtotal awal dari form
$bayar   = $_POST['uang_bayar'];
$kode_input = isset($_POST['kode_member']) ? $_POST['kode_member'] : '';

echo "<!DOCTYPE html>
<html>
<head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script>
        function playWrongSound() {
            let context = new (window.AudioContext || window.webkitAudioContext)();
            let osc = context.createOscillator();
            let gain = context.createGain();
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(150, context.currentTime);
            gain.gain.setValueAtTime(0.5, context.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, context.currentTime + 0.5);
            osc.connect(gain);
            gain.connect(context.destination);
            osc.start();
            osc.stop(context.currentTime + 0.5);
        }
    </script>
</head>
<body>";

// ==========================================
// LOGIKA MEMBER & DISKON (DENGAN PEMBULATAN)
// ==========================================
$diskon_member = 0;
$potongan_poin = 0;
$poin_didapat = 0;
$kode_member_db = "NULL"; 
$nama_member_teks = "";

if ($kode_input != "") {
    $cek_member = mysqli_query($conn, "SELECT * FROM member WHERE kode_member = '$kode_input'");
    if (mysqli_num_rows($cek_member) > 0) {
        $data_member = mysqli_fetch_assoc($cek_member);
        $kode_member_db = "'" . $data_member['kode_member'] . "'"; 
        $nama_member_teks = $data_member['nama_member'];
        
        // 1. Hitung diskon murni 5%
        $diskon_murni = $total * 0.05;
        
        // 2. Bulatkan ke kelipatan 500 terdekat
        // Contoh: 1.725 jadi 1.500 agar kembalian tidak receh
        $diskon_member = round($diskon_murni / 500) * 500;
        
        // 3. Update total setelah diskon bulat
        $total = $total - $diskon_member;
        
        // Poin: Setiap 10rb dapat 100 poin
        $poin_didapat = floor($total / 10000) * 100;
        mysqli_query($conn, "UPDATE member SET poin = poin + $poin_didapat WHERE kode_member = {$kode_member_db}");
    }
}

// VALIDASI UANG KURANG
if ($bayar < $total) {
    echo "<script>
        playWrongSound();
        Swal.fire({
            icon: 'warning', title: 'Uang Kurang!', text: 'Bayar Rp " . number_format($bayar, 0, ',', '.') . ", tapi totalnya Rp " . number_format($total, 0, ',', '.') . ".', confirmButtonColor: '#ffc107'
        }).then(() => { window.history.back(); });
    </script>";
    exit;
}

// VALIDASI STOK
$cart_check = mysqli_query($conn, "SELECT c.*, b.nama_barang, b.stok FROM keranjang c JOIN barang b ON c.id_barang = b.id_barang WHERE c.id_user = '$id_user'");
while($row = mysqli_fetch_assoc($cart_check)) {
    if ($row['jumlah'] > $row['stok']) {
        $nama = $row['nama_barang'];
        echo "<script>
            playWrongSound();
            Swal.fire({ icon: 'error', title: 'Stok Tidak Cukup!', text: 'Barang $nama sisa " . $row['stok'] . " unit.', confirmButtonColor: '#d33'
            }).then(() => { window.location.href = 'kasir_transaksi.php'; });
        </script>";
        exit;
    }
}

// PROSES SIMPAN KE DATABASE
$tgl = date('Y-m-d H:i:s');
$query_transaksi = "INSERT INTO transaksi (id_user, tanggal, total, status, kode_member, diskon_member, potongan_poin) 
                    VALUES ('$id_user', '$tgl', '$total', 'Selesai', $kode_member_db, '$diskon_member', '$potongan_poin')";
$save = mysqli_query($conn, $query_transaksi);

if ($save) {
    $id_trx = mysqli_insert_id($conn);
    $items = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_user = '$id_user'");
    while($i = mysqli_fetch_assoc($items)){
        $id_b = $i['id_barang']; $qty  = $i['jumlah'];
        mysqli_query($conn, "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah) VALUES ('$id_trx', '$id_b', '$qty')");
        mysqli_query($conn, "UPDATE barang SET stok = stok - $qty WHERE id_barang = '$id_b'");
    }
    mysqli_query($conn, "DELETE FROM keranjang WHERE id_user = '$id_user'");

    $kembalian = $bayar - $total;
    
    // Popup Info
    $teks_member = "";
    if ($kode_member_db != "NULL") {
        $teks_member = "<br><hr><small class='text-primary'>Member: <b>$nama_member_teks</b><br>Hemat (Bulat): Rp " . number_format($diskon_member, 0, ',', '.') . "<br>Poin Didapat: +$poin_didapat Poin</small>";
    }
    
    echo "<script>
        Swal.fire({
            icon: 'success', title: 'Transaksi Sukses!', html: 'Kembalian: <b>Rp " . number_format($kembalian, 0, ',', '.') . "</b>' + \"$teks_member\", confirmButtonText: 'Cetak Struk', confirmButtonColor: '#198754'
        }).then(() => { window.open('cetak_nota.php?id=$id_trx&bayar=$bayar', '_blank'); window.location.href = 'kasir_transaksi.php'; });
    </script>";
} else {
    echo "<script>alert('Gagal Simpan ke Database!'); window.history.back();</script>";
}
echo "</body></html>";
?>