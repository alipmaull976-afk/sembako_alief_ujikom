<?php 
include 'config.php';
cek_akses('admin'); 

// QUERY DATA STATISTIK
$count_barang    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang"))['total'];
$transaksi_today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal) = CURDATE()"))['total'];
$omzet_today     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) as total FROM transaksi WHERE DATE(tanggal) = CURDATE()"))['total'];

// DATA GRAFIK TREN
$res_grafik = mysqli_query($conn, "SELECT DATE_FORMAT(tanggal, '%d %b') as tgl, SUM(total) as omzet FROM transaksi WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(tanggal) ORDER BY tanggal ASC");
$labels = []; $data_omzet = [];
while($r = mysqli_fetch_assoc($res_grafik)){ 
    $labels[] = $r['tgl']; 
    $data_omzet[] = $r['omzet']; 
}

// DATA STOK KRITIS
$res_stok_low = mysqli_query($conn, "SELECT nama_barang, stok FROM barang WHERE stok <= 10 ORDER BY stok ASC LIMIT 5");

include 'header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0 text-dark">Ringkasan Bisnis</h4>
    <div class="text-end">
        <small class="text-muted d-block"><?= date('l, d F Y') ?></small>
        <span class="fw-bold text-primary">Administrator: <?= ucfirst($_SESSION['username']) ?></span>
    </div>
</div>

<div class="welcome-banner shadow-sm">
    <h1 class="fw-bold">Selamat Bekerja, <?= ucfirst($_SESSION['username']) ?>!</h1>
    <style>
    .banner-biru {
        background: linear-gradient(135deg, #007aff 0%, #0056b3 100%);
        color: white;
        border-radius: 20px;
        padding: 35px 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,122,255,0.25);
        margin-bottom: 30px;
        margin-top: 10px;
    }
    .banner-biru h1 { 
        font-weight: 800; 
        margin-bottom: 5px; 
        position: relative; 
        z-index: 2; 
    }
    .banner-biru p { 
        position: relative; 
        z-index: 2; 
        font-size: 1.1rem; 
        opacity: 0.9; 
        margin-bottom: 0; 
    }
    .icon-belakang { 
        position: absolute; 
        right: -10px; 
        bottom: -40px; 
        font-size: 14rem; 
        opacity: 0.1; 
        transform: rotate(-15deg); 
        z-index: 1; 
    }

    .card { 
        border-radius: 15px !important; 
        border: none !important; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important; 
    }
</style>

<div class="banner-biru">
    <h1>Selamat Bekerja, <?= ucfirst($_SESSION['username']) ?>!</h1>
    <p>Pantau pergerakan stok dan omzet toko hari ini dalam satu layar.</p>
    <i class="fas fa-shield-alt icon-belakang"></i>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-4 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white p-3 rounded-circle me-3">
                    <i class="fas fa-boxes fa-lg"></i>
                </div>
                <div>
                    <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Total Produk</small>
                    <h2 class="mb-0 fw-bold"><?= $count_barang ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-4 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="bg-success text-white p-3 rounded-circle me-3">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                </div>
                <div>
                    <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Transaksi Hari Ini</small>
                    <h2 class="mb-0 fw-bold"><?= $transaksi_today ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-4 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="bg-warning text-dark p-3 rounded-circle me-3">
                    <i class="fas fa-wallet fa-lg"></i>
                </div>
                <div>
                    <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Omzet Hari Ini</small>
                    <h4 class="mb-0 fw-bold text-dark">Rp <?= number_format($omzet_today ?? 0, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card stat-card p-4 border-0 shadow-sm h-100">
            <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-chart-line me-2"></i>Tren Penjualan (7 Hari Terakhir)</h5>
            <canvas id="salesChart" height="150"></canvas>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card p-4 border-0 shadow-sm h-100">
            <h5 class="fw-bold mb-4 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Stok Kritis</h5>
            <div class="list-group list-group-flush mb-3">
                <?php while($s = mysqli_fetch_assoc($res_stok_low)): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 bg-transparent">
                    <span class="text-dark fw-medium small"><?= $s['nama_barang'] ?></span>
                    <span class="badge bg-danger rounded-pill px-3"><?= $s['stok'] ?></span>
                </div>
                <?php endwhile; if(mysqli_num_rows($res_stok_low) == 0) echo "<div class='text-center py-4'><i class='fas fa-check-circle text-success fa-3x mb-2'></i><p class='text-muted small'>Semua stok aman.</p></div>"; ?>
            </div>
            <a href="daftar_barang.php" class="btn btn-outline-primary fw-bold w-100 rounded-pill btn-sm">Cek Detail Stok</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels); ?>,
            datasets: [{
                label: 'Omzet (Rp)',
                data: <?= json_encode($data_omzet); ?>,
                borderColor: '#007aff',
                backgroundColor: 'rgba(0, 122, 255, 0.1)',
                fill: true, 
                tension: 0.4, 
                borderWidth: 4, 
                pointRadius: 4,
                pointBackgroundColor: '#007aff'
            }]
        },
        options: { 
            responsive: true, 
            plugins: { legend: { display: false } }, 
            scales: { 
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#f1f3f5' } 
                }, 
                x: { 
                    grid: { display: false } 
                } 
            } 
        }
    });
</script>

<?php include 'footer.php'; ?>