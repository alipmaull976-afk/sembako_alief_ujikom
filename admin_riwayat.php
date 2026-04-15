<?php 
include 'config.php'; 

// Tangkap filter tanggal (Default hari ini)
$tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-d');
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d');

$query_transaksi = "SELECT t.*, u.username FROM transaksi t JOIN users u ON t.id_user = u.id_user WHERE DATE(t.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai' ORDER BY t.id_transaksi DESC";

$q = mysqli_query($conn, $query_transaksi);

// Hitung total pemasukan periode ini
$total_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) as sm FROM transaksi WHERE DATE(tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'"))['sm'] ?? 0;

include 'header.php'; 
?>

<style>
    /* Tampilan Normal di Layar */
    .stat-card { border-radius: 18px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .print-header { display: none; }

    @media print {
        /* KUNCI RAHASIA: Paksa warna background dan font tercetak */
        * {
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important;
        }
        
        body, .print-header h3, .print-header p, table, th, td {
            font-family: 'Inter', sans-serif !important;
            color: #1d1d1f !important;
        }

        /* Sembunyikan elemen web yang tidak perlu */
        .sidebar, .navbar, header, footer, .btn, .badge-mode, .nav-link, .d-flex.justify-content-between.mb-4, .d-print-none {
            display: none !important;
        }

        /* Layout Kertas */
        body, .container-fluid, .row, .col-md-10, .p-4 {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
            background: white !important;
        }

        /* KOP SURAT / HEADER LAPORAN MODERN */
        .print-header {
            display: block !important;
            text-align: center !important;
            border-bottom: 2px solid #1d1d1f !important; /* Garis bawah tunggal yg tegas */
            margin-bottom: 30px !important;
            padding-bottom: 20px !important;
            margin-top: 10px !important;
        }
        
        .print-header h3 {
            font-size: 22px !important;
            font-weight: 800 !important;
            letter-spacing: 1px !important;
            margin-bottom: 5px !important;
            text-transform: uppercase !important;
        }

        .print-header p {
            font-size: 14px !important;
            color: #6c757d !important;
            margin: 0 !important;
        }

        /* KOTAK TOTAL PEMASUKAN ELEGAN */
        .card.bg-success {
            background-color: #f8f9fa !important; /* Abu-abu super terang */
            border: none !important;
            border-left: 6px solid #198754 !important; /* Aksen hijau di kiri */
            border-radius: 8px !important;
            padding: 20px !important;
            margin-bottom: 30px !important;
            display: block !important;
        }
        
        .card.bg-success h6 {
            color: #6c757d !important;
            font-size: 12px !important;
            letter-spacing: 1.5px !important;
            margin-bottom: 5px !important;
        }

        .card.bg-success h2 {
            color: #1d1d1f !important; 
            font-size: 26px !important;
            font-weight: 800 !important;
        }

        .card.bg-success .d-flex { display: block !important; }
        .fa-money-bill-wave { display: none !important; } 

        /* TABEL ALA INVOICE PROFESIONAL */
        .table-responsive { overflow: visible !important; }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 10px !important;
            border: none !important; /* Hilangkan kotak luar */
        }
        
        th, td {
            border: none !important; /* Hilangkan garis vertikal */
            border-bottom: 1px solid #dee2e6 !important; /* Garis pembatas antar baris (halus) */
            padding: 14px 10px !important;
            text-align: left !important;
            vertical-align: middle !important;
        }
        
        th {
            background-color: #f8f9fa !important; /* Background abu-abu di header tabel */
            color: #495057 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 12px !important;
            letter-spacing: 0.5px !important;
            border-top: 2px solid #1d1d1f !important; /* Garis atas tabel tegas */
            border-bottom: 2px solid #1d1d1f !important; /* Garis bawah header tegas */
        }

        /* Cegah baris tabel terpotong di tengah saat ganti halaman kertas */
        tr { page-break-inside: avoid !important; }

        /* Sembunyikan kolom Aksi */
        th:last-child, td:last-child { display: none !important; }

        /* Rapikan teks di dalam tabel */
        .badge { color: #1d1d1f !important; background: transparent !important; border: none !important; padding: 0 !important; font-weight: 600 !important; }
        .text-primary { color: #1d1d1f !important; }
        .text-muted { color: #6c757d !important; }
    }
</style>

<div class="print-header">
    <h3>LAPORAN PENJUALAN SEMBAKO ALIEF</h3>
    <p style="font-size: 16px; margin: 0;">Periode Transaksi: <b><?= date('d/m/Y', strtotime($tgl_mulai)) ?></b> s/d <b><?= date('d/m/Y', strtotime($tgl_selesai)) ?></b></p>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="text-dark m-0 fw-bold">Laporan Penjualan</h4>
        <p class="text-muted small mb-0">Pantau arus kas masuk periode ini.</p>
    </div>
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
        <i class="fas fa-print me-2"></i> Cetak Laporan
    </button>
</div>

<div class="card stat-card border-0 p-4 mb-4 bg-white d-print-none">
    <form method="GET" action="" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary">Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" class="form-control border-0 bg-light px-3" style="border-radius: 12px;" value="<?= $tgl_mulai ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary">Tanggal Selesai</label>
            <input type="date" name="tgl_selesai" class="form-control border-0 bg-light px-3" style="border-radius: 12px;" value="<?= $tgl_selesai ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold shadow-sm">
                <i class="fas fa-filter me-2"></i> Tampilkan Data
            </button>
        </div>
    </form>
</div>

<div class="card stat-card border-0 p-4 mb-4 bg-success bg-opacity-10" style="border-left: 5px solid #198754 !important;">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="text-success fw-bold mb-1">TOTAL PEMASUKAN PERIODE INI</h6>
            <h2 class="text-success mb-0 fw-bold">Rp <?= number_format($total_masuk, 0, ',', '.') ?></h2>
        </div>
        <i class="fas fa-money-bill-wave fa-3x text-success opacity-25"></i>
    </div>
</div>

<div class="card stat-card border-0 p-4 bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="small text-uppercase text-muted">
                    <th class="ps-3">ID Nota</th>
                    <th>Waktu Transaksi</th>
                    <th>Nama Kasir</th>
                    <th>Total Bayar</th>
                    <th class="text-center d-print-none">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if($q && mysqli_num_rows($q) > 0): ?>
                    <?php while($r = mysqli_fetch_assoc($q)): ?>
                    <tr>
                        <td class="ps-3 fw-bold text-primary">#TRX-<?= $r['id_transaksi'] ?></td>
                        <td>
                            <div class="small fw-bold text-dark"><?= date('d M Y', strtotime($r['tanggal'])) ?></div>
                            <small class="text-muted"><?= date('H:i', strtotime($r['tanggal'])) ?> WIB</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border py-2 px-3" style="border-radius: 10px;">
                                <i class="fas fa-user me-1 text-muted"></i> <?= $r['username'] ?>
                            </span>
                        </td>
                        <td class="fw-bold text-dark">Rp <?= number_format($r['total'], 0, ',', '.') ?></td>
                        <td class="text-center d-print-none">
                            <a href="cetak_nota.php?id=<?= $r['id_transaksi'] ?>" 
                               onclick="window.open(this.href, 'strukWindow', 'width=450,height=700,top=50,left=50'); return false;" 
                               class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                <i class="fas fa-receipt me-1"></i> Lihat Struk
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Tidak ada transaksi pada tanggal tersebut.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>