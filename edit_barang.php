<?php 
include 'config.php';
cek_akses('admin');

// Ambil ID dari URL (Sesuai link di daftar_barang.php)
$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id'");
$data = mysqli_fetch_assoc($query);

// Jika ID tidak ditemukan, tendang balik
if (!$data) {
    header("Location: daftar_barang.php");
    exit;
}

include 'header.php'; 
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0 text-dark">Edit Data Produk</h4>
            <p class="text-muted small mb-0">Update informasi untuk: <strong><?= $data['nama_barang']; ?></strong></p>
        </div>
        <a href="daftar_barang.php" class="btn btn-light rounded-pill px-4 shadow-sm border">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                
                <form action="proses_edit_barang.php" method="POST" enctype="multipart/form-data">
                    
                    <input type="hidden" name="id_barang" value="<?= $data['id_barang']; ?>">

                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="p-3 bg-light rounded-4 mb-3" style="border: 2px dashed #dee2e6;">
                                <?php 
                                    $foto = (!empty($data['gambar'])) ? 'assets/img/barang/'.$data['gambar'] : 'assets/img/barang/default.png';
                                ?>
                                <img src="<?= $foto ?>" id="load_gambar" class="img-fluid rounded-3 mb-3 shadow-sm" style="width: 100%; aspect-ratio: 1/1; object-fit: cover;">
                                
                                <label for="foto" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold">
                                    <i class="fas fa-camera me-1"></i> Ganti Foto
                                </label>
                                <input type="file" name="gambar" id="foto" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <small class="text-muted small">Abaikan jika tidak ingin mengganti foto.</small>
                        </div>

                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    <label class="form-label small fw-bold">Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control bg-light border-0 py-2" style="border-radius: 10px;" value="<?= $data['nama_barang']; ?>" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold">Kategori</label>
                                    <input type="text" name="jenis_barang" class="form-control bg-light border-0 py-2" style="border-radius: 10px;" value="<?= $data['jenis_barang']; ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Stok</label>
                                    <input type="number" name="stok" class="form-control bg-light border-0 py-2 fw-bold" style="border-radius: 10px;" value="<?= $data['stok']; ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Satuan</label>
                                    <input type="text" name="satuan" class="form-control bg-light border-0 py-2" style="border-radius: 10px;" value="<?= $data['satuan']; ?>" placeholder="Pcs/Kg">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control bg-light border-0 py-2 fw-bold" style="border-radius: 10px;" value="<?= $data['harga']; ?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold">Deskripsi Singkat</label>
                                <textarea name="deskripsi" class="form-control bg-light border-0 py-2" style="border-radius: 10px;" rows="3"><?= $data['deskripsi']; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm fw-bold py-3">
                                <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi preview gambar biar interaktif
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('load_gambar').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'footer.php'; ?>