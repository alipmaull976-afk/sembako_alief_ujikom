<?php 
include 'config.php'; 
cek_akses('kasir'); 
$id_u = $_SESSION['id_user'];
$nama_kasir = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Nota - Sembako Alief</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Inter', sans-serif; 
            overflow-y: scroll; 
            color: #1d1d1f; 
        }
        .sidebar { 
            position: sticky; 
            top: 0; 
            height: 100vh; 
            background: #212529; 
            color: white; 
            padding-top: 20px; 
        }
        .nav-link { 
            color: rgba(255,255,255,0.7); 
            margin-bottom: 5px; 
            border-radius: 8px; 
            padding: 10px 20px; 
            transition: 0.3s; 
        }
        .nav-link:hover, .nav-link.active { 
            background: #0d6efd; 
            color: white !important; 
        }
        .badge-mode { 
            background-color: #ffc107; 
            color: #000; 
            font-size: 0.7rem; 
            padding: 3px 10px; 
            border-radius: 20px; 
            font-weight: bold; 
        }
        .stat-card { 
            border: none; 
            border-radius: 18px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.04); 
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 d-none d-md-block sidebar text-center shadow">
            <div class="mb-5">
                <i class="fas fa-store fa-3x mb-2 text-white"></i>
                <h5 class="fw-bold text-white">SEMBAKO ALIEF</h5>
                <span class="badge-mode">KASIR MODE</span>
            </div>
            <nav class="nav flex-column text-start px-3">
                <a class="nav-link" href="kasir_dashboard.php"><i class="fas fa-desktop me-2"></i> Dashboard</a>
                <a class="nav-link" href="kasir_transaksi.php"><i class="fas fa-cash-register me-2"></i> Layar Kasir</a>
                <a class="nav-link active" href="riwayat_transaksi.php"><i class="fas fa-history me-2"></i> Riwayat Nota</a>
                <hr class="bg-secondary">
                <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Keluar</a>
            </nav>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold m-0">Riwayat Penjualan Saya</h4>
                <span class="text-muted small">Petugas: <strong><?= ucfirst($nama_kasir) ?></strong></span>
            </div>

            <div class="card stat-card p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No. Nota</th>
                                <th>Tanggal</th>
                                <th>Total Belanja</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $riwayat = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_user = '$id_u' ORDER BY tanggal DESC");
                            if(mysqli_num_rows($riwayat) > 0) {
                                while($d = mysqli_fetch_assoc($riwayat)): ?>
                                <tr>
                                    <td class="fw-bold text-dark">#<?= $d['id_transaksi'] ?></td>
                                    <td class="text-muted small"><?= date('d M Y, H:i', strtotime($d['tanggal'])) ?></td>
                                    <td class="fw-bold text-primary"><?= rupiah($d['total']) ?></td>
                                    <td>
                                        <a href="cetak_nota.php?id=<?= $d['id_transaksi'] ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-print me-1"></i> Cetak Nota
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; 
                            } else {
                                echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Belum ada riwayat transaksi hari ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>