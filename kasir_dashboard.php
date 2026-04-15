<?php 
include 'config.php'; 

// Cek keamanan ganda: Harus login & harus sebagai Kasir
if(!isset($_SESSION['username'])){ header("Location: login.php"); exit; }
if($_SESSION['role'] != 'kasir'){ header("Location: index.php"); exit; }

$id_user = $_SESSION['id_user'];
$hari_ini = date('Y-m-d');
$nama_kasir = $_SESSION['username'];

// Query Statistik
$q_t = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE id_user = '$id_user' AND DATE(tanggal) = '$hari_ini'");
$transaksi_today = mysqli_fetch_assoc($q_t)['total'];

$q_u = mysqli_query($conn, "SELECT SUM(total) as total_uang FROM transaksi WHERE id_user = '$id_user' AND DATE(tanggal) = '$hari_ini'");
$omzet_today = mysqli_fetch_assoc($q_u)['total_uang'] ?? 0;

$res_stok_low = mysqli_query($conn, "SELECT nama_barang, jenis_barang, stok, satuan, harga FROM barang WHERE stok <= 10 ORDER BY stok ASC LIMIT 5");

// PANGGIL HEADER GABUNGAN DI SINI (Sidebar otomatis muncul)

include 'header.php'; 
?>

<style>
    .welcome-banner { 
        background: linear-gradient(135deg, #0d6efd, #00b4db); 
        color: white; 
        border-radius: 20px; 
        padding: 40px; 
        position: relative; 
        overflow: hidden; 
        box-shadow: 0 10px 30px rgba(13,110,253,0.2); 
        margin-bottom: 40px; 
    }
    .banner-icon { 
        position: absolute; 
        opacity: 0.15; 
        font-size: 15rem; 
        bottom: -40px; 
        right: -20px; 
        transform: rotate(-15deg); 
        }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0">Ringkasan Kerja</h4>
    <div class="text-end">
        <small class="text-muted d-block"><?= date('l, d F Y') ?></small>
        <span class="fw-bold text-primary">Petugas: <?= ucfirst($nama_kasir) ?></span>
    </div>
</div>

<div class="welcome-banner">
    <h1 class="fw-bold">Selamat Bekerja, <?= ucfirst($nama_kasir) ?>!</h1>
    <p class="fs-5 mb-0">Berikan pelayanan terbaik untuk pelanggan kita hari ini.</p>
    <i class="fas fa-cash-register banner-icon"></i>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-4 h-100 border-0">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white p-3 rounded-circle me-3"><i class="fas fa-receipt fa-lg"></i></div>
                <div>
                    <small class="text-muted d-block fw-bold">Transaksi Hari Ini</small>
                    <h2 class="mb-0 fw-bold"><?= $transaksi_today ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card p-4 h-100 border-0">
            <div class="d-flex align-items-center">
                <div class="bg-success text-white p-3 rounded-circle me-3"><i class="fas fa-wallet fa-lg"></i></div>
                <div>
                    <small class="text-muted d-block fw-bold">Omzet Saya</small>
                    <h4 class="mb-0 fw-bold text-success">Rp <?= number_format($omzet_today, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card p-4 h-100 border-0 bg-warning shadow-sm" style="cursor: pointer;" onclick="window.location.href='kasir_transaksi.php'">
            <div class="d-flex justify-content-between align-items-center h-100">
                <div>
                    <h4 class="mb-1 fw-bold">Layar Kasir</h4>
                    <small class="fw-bold">Klik untuk melayani</small>
                </div>
                <div class="bg-dark text-white p-3 rounded-circle shadow"><i class="fas fa-arrow-right fa-lg"></i></div>
            </div>
        </div>
    </div>
</div>

<?php 
// Panggil footer untuk menutup div container dari header
include 'footer.php'; 
?>