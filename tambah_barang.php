<?php 
include 'config.php';
cek_akses('admin'); 

// PANGGIL HEADER (Otomatis panggil Sidebar & CSS iPhone)
include 'header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="text-dark m-0">Input Produk Baru</h4>
        <p class="text-muted small mb-0">Pastikan data barang yang diinput sudah benar.</p>
    </div>
    <a href="daftar_barang.php" class="btn btn-light rounded-pill px-4 shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card stat-card border-0 p-4">
            <form action="proses_tambah_barang.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <div class="p-3 bg-light rounded-4 border-dashed mb-3" style="border: 2px dashed #dee2e6;">
                            <img src="https://via.placeholder.com/150?text=Pilih+Foto" id="load_gambar" class="img-fluid rounded-3 mb-3 shadow-sm" style="width: 100%; aspect-ratio: 1/1; object-fit: cover;">
                            <label for="foto" class="btn btn-primary btn-sm rounded-pill w-100">
                                <i class="fas fa-camera me-1"></i> Pilih Foto
                            </label>
                            <input type="file" name="gambar" id="foto" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <small class="text-muted d-block small">Maksimal 2MB (JPG, PNG, WebP)</small>
                    </div>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label small fw-bold text-secondary">Nama Lengkap Barang</label>
                                <input type="text" name="nama_barang" class="form-control border-0 bg-light px-3 py-2" style="border-radius: 12px;" placeholder="Contoh: Beras Rojo Lele 5kg" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label small fw-bold text-secondary">Kategori</label>
                                <select name="jenis_barang" class="form-select border-0 bg-light px-3 py-2" style="border-radius: 12px;">
                                    <option value="Sembako">Sembako</option>
                                    <option value="Minuman">Minuman</option>
                                    <option value="Bumbu Dapur">Bumbu Dapur</option>
                                    <option value="Sabun & Deterjen">Sabun & Deterjen</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold text-secondary">Stok Awal</label>
                                <input type="number" name="stok" class="form-control border-0 bg-light px-3 py-2" style="border-radius: 12px;" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold text-secondary">Satuan</label>
                                <input type="text" name="satuan" class="form-control border-0 bg-light px-3 py-2" style="border-radius: 12px;" placeholder="Kg / Pcs / Dus">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold text-secondary">Harga Jual (Rp)</label>
                                <input type="number" name="harga" class="form-control border-0 bg-light px-3 py-2" style="border-radius: 12px;" placeholder="0" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="form-control border-0 bg-light px-3 py-2" style="border-radius: 12px;" rows="3" placeholder="Tambahkan keterangan barang..."></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm py-3 fw-bold">
                                <i class="fas fa-save me-2"></i> Simpan Data Barang
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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

<?php 
include 'footer.php'; 
?>