<?php
include 'config.php';

// Pastikan fungsi rupiah tersedia
if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}

// PROTEKSI: Cek apakah ID ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID Transaksi tidak ditemukan!'); window.location.href='admin_riwayat.php';</script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data transaksi, nama kasir, DAN NAMA MEMBER (jika pakai member)
$query_nota = "SELECT t.*, u.username, m.nama_member 
               FROM transaksi t 
               JOIN users u ON t.id_user = u.id_user 
               LEFT JOIN member m ON t.kode_member = m.kode_member 
               WHERE t.id_transaksi = '$id'";
$head = mysqli_query($conn, $query_nota);
$d = mysqli_fetch_assoc($head);

// PROTEKSI: Jika data transaksi tidak ada di database
if (!$d) {
    echo "<script>alert('Data transaksi tidak ditemukan di database!'); window.location.href='admin_riwayat.php';</script>";
    exit;
}

// Ambil rincian barang
$items = mysqli_query($conn, "SELECT dt.*, b.nama_barang, b.harga FROM detail_transaksi dt JOIN barang b ON dt.id_barang = b.id_barang WHERE dt.id_transaksi = '$id'");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nota #<?= $id; ?></title>
    <style>
    .no-print {
        margin-top: 20px;
        text-align: center;
    }
    .btn-nav {
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        font-family: sans-serif;
        font-size: 14px;
        display: inline-block;
        border: 1px solid #ddd;
        background: #f8f9fa;
        color: #333;
    }
    .btn-primary { background: #007bff; color: white; border: none; }
    
    /* Induk dari semua konten nota */
    body {
        width: 80mm; 
        margin: 0 auto;
        padding: 10px;
        font-family: 'Courier New', Courier, monospace; 
        font-size: 12px;
    }

    /* Pengaturan saat diprint */
    @media print {
        @page { size: 80mm auto; margin: 0; }
        body { width: 80mm; margin: 0; }
        .no-print { display: none !important; } 
    }

    .center { text-align: center; }
    .right { text-align: right; }
    .line { border-bottom: 1px dashed #000; margin: 5px 0; }
    table { width: 100%; }
    </style>
</head>
<body onload="window.print()">
    <div class="center">
        <strong style="font-size: 14px;">TOKO SEMBAKO ALIEF</strong><br>
        Jl. BTN Cinderaya No. 20<br>
        Karangsong, Indramayu<br>
    </div>
    
    <div class="line"></div>
    
    <div style="display: flex; justify-content: space-between; font-size: 12px;">
        <span>Nota: #<?= $id; ?></span>
        <span>Kasir: <?= strtoupper($d['username'] ?? 'ADMIN'); ?></span>
    </div>
    
    <div class="center" style="margin-bottom: 5px;">
        <small><?= date('d/m/Y H:i', strtotime($d['tanggal'])); ?></small>
    </div>
    
    <div class="line"></div>
    
   <table>
        <?php 
        $subtotal_asli = 0;
        // Reset pointer agar data bisa diambil lagi
        mysqli_data_seek($items, 0); 
        while($row = mysqli_fetch_assoc($items)): 
            // Hitung subtotal secara manual: Harga x Jumlah
            $sub_hitung = $row['harga'] * $row['jumlah'];
            $subtotal_asli += $sub_hitung;
        ?>
        <tr>
            <td colspan="2" style="padding-top: 5px;"><?= strtoupper($row['nama_barang']); ?></td>
        </tr>
        <tr>
            <td><?= $row['jumlah']; ?> x <?= number_format($row['harga'], 0, ',', '.'); ?></td>
            <td class="right"><?= number_format($sub_hitung, 0, ',', '.'); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <div class="line"></div>
    
    <?php
        $total_belanja = $d['total'];
        $diskon_member = isset($d['diskon_member']) ? (int)$d['diskon_member'] : 0;
        
        // Penanda apakah perlu menampilkan garis subtotal
        $ada_potongan = ($diskon_member > 0);

        // Mengambil uang dari URL (?bayar=...), jika tidak ada anggap uang pas
        $uang_tunai = isset($_GET['bayar']) ? (int)$_GET['bayar'] : $total_belanja;
        $kembalian = $uang_tunai - $total_belanja;
    ?>
    <table>
        <?php if ($ada_potongan): ?>
        <tr>
            <td style="padding-top: 5px;">Subtotal</td>
            <td class="right" style="padding-top: 5px;">
                Rp <?= number_format($subtotal_asli, 0, ',', '.'); ?>
            </td>
        </tr>
        <?php endif; ?>

        <?php if ($diskon_member > 0): ?>
        <tr>
            <td style="padding-bottom: 5px;">Diskon Member</td>
            <td class="right" style="padding-bottom: 5px;">
                -Rp <?= number_format($diskon_member, 0, ',', '.'); ?>
            </td>
        </tr>
        <?php endif; ?>

        <tr>
            <td style="font-weight: bold; font-size: 14px; padding-top: 5px; border-top: <?= ($ada_potongan) ? '1px dashed #ccc' : 'none' ?>;">TOTAL BAYAR</td>
            <td class="right" style="font-weight: bold; font-size: 14px; padding-top: 5px; border-top: <?= ($ada_potongan) ? '1px dashed #ccc' : 'none' ?>;">
                Rp <?= number_format($total_belanja, 0, ',', '.'); ?>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 5px; border-top: 1px dashed #ccc;">TUNAI</td>
            <td class="right" style="padding-top: 5px; border-top: 1px dashed #ccc;">
                Rp <?= number_format($uang_tunai, 0, ',', '.'); ?>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 2px;">KEMBALI</td>
            <td class="right" style="padding-top: 2px;">
                Rp <?= number_format($kembalian, 0, ',', '.'); ?>
            </td>
        </tr>
    </table>
    
    <?php if(!empty($d['kode_member'])): ?>
    <div class="line"></div>
    <div class="center" style="margin-top: 5px; font-size: 11px;">
        -- INFO MEMBER --<br>
        <strong><?= strtoupper($d['nama_member']); ?></strong> (<?= $d['kode_member']; ?>)<br>
        <?php $poin_didapat = floor($total_belanja / 10000) * 100; ?>
        Poin Didapat: <strong>+<?= $poin_didapat; ?> Poin</strong>
    </div>
    <?php endif; ?>

    <div class="line"></div>
    <div class="center" style="margin-top:10px">
        *** TERIMA KASIH ***<br>
        Barang yang sudah dibeli<br>
        tidak dapat ditukar/dikembalikan
    </div>

    <div class="no-print">
        <hr>
        <p style="font-family: sans-serif; font-size: 11px; color: #666;">Transaksi Selesai!</p>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="admin_riwayat.php" class="btn-nav btn-primary">
                <b>&laquo; Kembali ke Laporan</b>
            </a>
        <?php else: ?>
            <a href="kasir_transaksi.php" class="btn-nav btn-primary">
                <b>+ Transaksi Baru</b>
            </a>
            <div style="margin-top: 10px;">
                <a href="kasir_dashboard.php" style="font-family: sans-serif; font-size: 11px; color: #999;">Ke Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>