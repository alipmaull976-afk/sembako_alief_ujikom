<?php 
include 'config.php';
cek_akses('admin');

// Fitur Pencarian
$keyword = "";
$query_sql = "SELECT * FROM barang";
if (isset($_POST['cari'])) {
    $keyword = mysqli_real_escape_string($conn, $_POST['keyword']);
    $query_sql = "SELECT * FROM barang WHERE nama_barang LIKE '%$keyword%' OR jenis_barang LIKE '%$keyword%'";
}

$result = mysqli_query($conn, $query_sql);

include 'header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="text-dark m-0">Manajemen Stok</h4>
        <p class="text-muted small mb-0">Kelola ketersediaan produk sembako Anda.</p>
    </div>
    <a href="tambah_barang.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="fas fa-plus me-2"></i> Tambah Barang
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-5">
        <form action="" method="POST" class="input-group">
            <input type="text" name="keyword" class="form-control border-0 shadow-sm px-3" style="border-radius: 12px 0 0 12px;" placeholder="Cari nama barang..." value="<?= $keyword; ?>">
            <button class="btn btn-dark shadow-sm px-4" type="submit" name="cari" style="border-radius: 0 12px 12px 0;">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<div class="card stat-card p-4 border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="small text-uppercase text-muted">
                    <th class="ps-3">Foto</th>
                    <th>Detail Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result) > 0) :
                    while($row = mysqli_fetch_assoc($result)) : 
                ?>
                <tr>
                    <td class="ps-3">
                        <?php 
                        $foto = (!empty($row['gambar'])) ? 'assets/img/barang/'.$row['gambar'] : 'https://via.placeholder.com/50?text=No+Img';
                        ?>
                        <img src="<?= $foto; ?>" class="img-barang shadow-sm" style="width: 50px; height: 50px; object-fit: cover; border-radius: 12px;" alt="foto">
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= $row['nama_barang']; ?></div>
                        <small class="text-muted"><?= $row['deskripsi']; ?></small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border-0 shadow-sm py-2 px-3" style="border-radius: 10px;">
                            <?= $row['jenis_barang'] ?: '-'; ?>
                        </span>
                    </td>
                    <td class="fw-bold"><?= rupiah($row['harga']); ?></td>
                    <td>
                        <?php if($row['stok'] <= 5): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2" style="border-radius: 10px;">
                                <i class="fas fa-exclamation-triangle me-1"></i> Sisa <?= $row['stok']; ?> <?= $row['satuan']; ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2" style="border-radius: 10px;">
                                <?= $row['stok']; ?> <?= $row['satuan']; ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                            <a href="edit_barang.php?id=<?= $row['id_barang']; ?>" class="btn btn-light btn-sm px-3" title="Edit"><i class="fas fa-edit text-warning"></i></a>
                            
                            <a href="#" onclick="konfirmasiHapus(event, 'hapus_barang.php?id=<?= $row['id_barang']; ?>')" class="btn btn-light btn-sm px-3" title="Hapus"><i class="fas fa-trash text-danger"></i></a>
                        </div>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                else:
                ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-search fa-3x mb-3 opacity-20"></i>
                        <p>Barang yang kamu cari tidak ditemukan.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function konfirmasiHapus(event, url) {
    event.preventDefault(); // Mencegah browser langsung pindah halaman
    
    Swal.fire({
        title: 'Yakin mau hapus barang?',
        text: "Data barang yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33', // Merah
        cancelButtonColor: '#6c757d', // Abu-abu
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true // Tombol batal di kiri, hapus di kanan
    }).then((result) => {
        if (result.isConfirmed) {
            // Kalau kasir/admin klik "Ya", eksekusi link hapus_barang.php
            window.location.href = url;
        }
    })
}
</script>

<?php 
include 'footer.php'; 
?>