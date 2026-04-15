<?php
include 'config.php';

// Pastikan session sudah jalan
if(session_status() === PHP_SESSION_NONE) { session_start(); }

// Panggil header agar tampilannya sama dengan halaman lain
include 'header.php'; 

// LOGIKA KODE MEMBER OTOMATIS (MBR-001, dst)
$query = mysqli_query($conn, "SELECT max(kode_member) as kodeTerbesar FROM member");
$data = mysqli_fetch_array($query);
$kodeMember = $data['kodeTerbesar'];

// Mengambil angka dari kode terbesar, lalu ditambah 1
if ($kodeMember) {
    $urutan = (int) substr($kodeMember, 4, 3);
    $urutan++;
} else {
    $urutan = 1; // Jika tabel masih kosong, mulai dari 1
}

$huruf = "MBR-";
// sprintf("%03s", $urutan) berfungsi mengubah angka 1 menjadi "001"
$kodeMemberBaru = $huruf . sprintf("%03s", $urutan);



// LOGIKA SIMPAN DATA KE DATABASE
$sukses_simpan = false;
$kode_terdaftar = "";
$nama_terdaftar = "";

if(isset($_POST['simpan_member'])) {
    $kode = $_POST['kode_member'];
    $nama = $_POST['nama_member'];
    // Jika di database tidak ada kolom no_hp, nilai ini tidak perlu di-insert
    // $hp   = $_POST['no_hp'];

    // PERBAIKAN: Hanya memasukkan kode_member, nama_member, dan poin
    $insert = mysqli_query($conn, "INSERT INTO member (kode_member, nama_member, poin) VALUES ('$kode', '$nama', 0)");
    
    if($insert) {
        $sukses_simpan = true;
        $kode_terdaftar = $kode;
        $nama_terdaftar = $nama;
        
        // Update kode baru untuk tampilan form selanjutnya
        $urutan++;
        $kodeMemberBaru = $huruf . sprintf("%03s", $urutan);
    } else {
        echo "<script>alert('Gagal menyimpan data!');</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"> Manajemen Member</h4>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Daftarkan Member Baru</h6>
                <form action="" method="POST">
                    
                    <div class="mb-3">
                        <label class="small fw-bold">Kode Member (Otomatis)</label>
                        <input type="text" name="kode_member" class="form-control bg-light text-primary fw-bold" value="<?= $kodeMemberBaru ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_member" class="form-control" placeholder="Masukkan nama pelanggan" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold">Nomor HP / WhatsApp (Opsional)</label>
                        <input type="number" name="no_hp" class="form-control" placeholder="Contoh: 08123456789">
                        <small class="text-muted" style="font-size: 11px;">*Hanya sebagai catatan, tidak masuk ke database utama.</small>
                    </div>

                    <button type="submit" name="simpan_member" class="btn btn-primary w-100 fw-bold py-2">
                        <i class="fas fa-save me-2"></i> Simpan & Buat Kartu
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <?php if($sukses_simpan): ?>
        <div class="card border-0 shadow-sm bg-primary text-white text-center" style="border-radius: 15px; height: 100%;">
            <div class="card-body p-5">
                <h5 class="fw-bold mb-1">Pendaftaran Berhasil!</h5>
                <p class="small mb-4">Silakan minta pelanggan memfoto QR Code di bawah ini.</p>
                
                <div class="bg-white text-dark p-4 mx-auto shadow" style="border-radius: 15px; max-width: 250px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= $kode_terdaftar ?>" alt="QR Code Member" class="img-fluid mb-3">
                    
                    <h5 class="fw-bold m-0 text-primary"><?= strtoupper($nama_terdaftar) ?></h5>
                    <p class="text-muted small m-0 fw-bold"><?= $kode_terdaftar ?></p>
                </div>
                
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Member Terdaftar!',
                text: 'Kartu QR Code sudah siap difoto.',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
        <?php else: ?>
        
        <div class="card border-0 shadow-sm h-100 d-flex justify-content-center align-items-center bg-light" style="border-radius: 15px; border: 2px dashed #ccc !important;">
            <div class="text-center text-muted p-4">
                <i class="fas fa-qrcode fa-4x mb-3 text-secondary"></i>
                <h6 class="fw-bold">Kartu Member Digital</h6>
                <small>Isi formulir di samping untuk membuat kartu member dan QR Code secara otomatis.</small>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-2 shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-list-ul me-2"></i>Daftar Member Terdaftar</h6>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode Member</th>
                        <th>Nama Lengkap</th>
                        <th width="20%">Poin Terkumpul</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Ambil data member dari database urut dari yang terbaru
                    $query_member = mysqli_query($conn, "SELECT * FROM member ORDER BY kode_member DESC");
                    $no = 1;
                    
                    // Cek apakah ada data member
                    if(mysqli_num_rows($query_member) > 0) {
                        while($row = mysqli_fetch_assoc($query_member)): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3 py-2"><?= $row['kode_member'] ?></span></td>
                        <td class="fw-bold"><?= strtoupper($row['nama_member']) ?></td>
                        
                        <td class="text-success fw-bold">
                            <i class="fas fa-coins text-warning me-1"></i>
                            <?= number_format($row['poin'], 0, ',', '.') ?> Poin
                        </td>
                        <td>
                            <a href="admin_member_hapus.php?id=<?= $row['kode_member'] ?>" 
                               class="btn btn-sm btn-outline-danger rounded-3" 
                               onclick="return confirm('Yakin ingin menghapus member <?= strtoupper($row['nama_member']) ?>?\n\nPeringatan: Seluruh data poin akan hilang selamanya!')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    } else {
                        // Jika belum ada member sama sekali
                        echo "<tr><td colspan='5' class='text-center text-muted py-4'><i class='fas fa-folder-open mb-2 fa-2x opacity-50'></i><br>Belum ada data member terdaftar.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>