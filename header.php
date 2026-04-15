<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= ucfirst($_SESSION['role'] ?? 'App'); ?> - Sembako Alief</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            background-color: #f8f9fa; font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased; letter-spacing: -0.02em; color: #1d1d1f; overflow-y: scroll;
        }
        .sidebar { position: sticky; top: 0; height: 100vh; background: #212529; color: white; padding-top: 20px; overflow-y: auto; }
        .nav-link { color: rgba(255,255,255,0.7); margin-bottom: 5px; border-radius: 8px; padding: 10px 20px; font-weight: 500; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: <?= (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? '#007aff' : '#0d6efd'; ?>; color: white !important; }
        
        /* Pengaturan Dinamis Badge */
        .badge-admin { background-color: #198754; color: white; }
        .badge-kasir { background-color: #ffc107; color: #000; }
        .badge-mode { font-size: 0.7rem; padding: 3px 10px; border-radius: 20px; font-weight: bold; }
        
        .stat-card { border: none; border-radius: 18px; box-shadow: 0 5px 15px rgba(0,0,0,0.04); transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        h4, h5, .fw-bold { font-weight: 700 !important; letter-spacing: -0.03em !important; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 d-none d-md-block sidebar text-center shadow">
            <div class="mb-5">
                <i class="fas fa-store fa-3x mb-2 text-white"></i>
                <h5 class="fw-bold d-block mb-0 text-white">SEMBAKO ALIEF</h5>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <span class="badge-mode badge-admin">ADMIN MODE</span>
                <?php else: ?>
                    <span class="badge-mode badge-kasir">KASIR MODE</span>
                <?php endif; ?>
            </div>
            
            <nav class="nav flex-column text-start px-3">
                <?php $uri = basename($_SERVER['PHP_SELF']); ?>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <a class="nav-link <?= $uri == 'admin_dashboard.php' ? 'active' : '' ?>" href="admin_dashboard.php"><i class="fas fa-th-large me-2"></i> Dashboard</a>
                    <a class="nav-link <?= $uri == 'tambah_barang.php' ? 'active' : '' ?>" href="tambah_barang.php"><i class="fas fa-box-open me-2"></i> Tambah Barang</a>
                    <a class="nav-link <?= $uri == 'daftar_barang.php' ? 'active' : '' ?>" href="daftar_barang.php"><i class="fas fa-list me-2"></i> Daftar Barang</a>
                    <a class="nav-link <?= ($uri == 'daftar_user.php' || $uri == 'tambah_user.php' || $uri == 'edit_user.php') ? 'active' : '' ?>" href="daftar_user.php"><i class="fas fa-users me-2"></i> Kelola User</a>
                    
                    <a class="nav-link <?= $uri == 'admin_member.php' ? 'active' : '' ?>" href="admin_member.php"><i class="fas fa-id-card me-2"></i> Kelola Member</a>
                    
                    <a class="nav-link <?= $uri == 'admin_riwayat.php' ? 'active' : '' ?>" href="admin_riwayat.php"><i class="fas fa-file-invoice-dollar me-2"></i> Laporan</a>
                <?php else: ?>
                    <a class="nav-link <?= $uri == 'kasir_dashboard.php' ? 'active' : '' ?>" href="kasir_dashboard.php"><i class="fas fa-desktop me-2"></i> Dashboard</a>
                    <a class="nav-link <?= $uri == 'kasir_transaksi.php' ? 'active' : '' ?>" href="kasir_transaksi.php"><i class="fas fa-cash-register me-2"></i> Layar Kasir</a>
                <?php endif; ?>
                
                <hr class="bg-secondary">
                <a class="nav-link text-danger" href="logout.php" onclick="konfirmasiKeluar(event)"><i class="fas fa-sign-out-alt me-2"></i> Keluar</a>
            </nav>
        </div>
        
        <div class="col-md-10 p-4">

<script>
// FUNGSI EFEK SUARA NADA TURUN
function suaraKeluar() {
    let context = new (window.AudioContext || window.webkitAudioContext)();
    let osc = context.createOscillator();
    let gain = context.createGain();

    osc.type = 'sine'; 
    osc.frequency.setValueAtTime(400, context.currentTime); 
    osc.frequency.exponentialRampToValueAtTime(100, context.currentTime + 0.3);
    
    gain.gain.setValueAtTime(1.0, context.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.01, context.currentTime + 0.3);

    osc.connect(gain);
    gain.connect(context.destination);

    osc.start();
    osc.stop(context.currentTime + 0.3);
}

// FUNGSI KONFIRMASI KELUAR
function konfirmasiKeluar(event) {
    if (event) event.preventDefault(); // Cegah pindah halaman langsung
    
    // Panggil suara pas tombol diklik
    suaraKeluar();
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Selesai Bertugas?',
            text: "Sesi kerja Anda akan diakhiri.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    } else {
        if (confirm("Selesai Bertugas? Anda akan keluar dari sesi ini.")) {
            window.location.href = 'logout.php';
        }
    }
}
</script>