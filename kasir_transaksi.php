<?php 
include 'config.php'; 

// Pastikan session sudah jalan dan yang login benar-benar kasir
if(session_status() === PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'kasir'){ header("Location: index.php"); exit; }

$id_u = $_SESSION['id_user'];
$nama_kasir = $_SESSION['username'];

// PANGGIL HEADER GABUNGAN
include 'header.php'; 
?>

<style>
    .stat-card { border: none; border-radius: 18px; box-shadow: 0 5px 15px rgba(0,0,0,0.04); background: white; }
    .total-box { background: #1e1e1e; color: #2ecc71; padding: 20px; border-radius: 12px; text-align: right; margin-bottom: 20px; }
    .stok-badge { font-size: 0.85rem; padding: 6px 12px; border-radius: 8px; background-color: #e9ecef; border: 1px dashed #adb5bd; color: #495057; margin-top: 8px; display: inline-block; transition: all 0.3s ease;}
    
    /* CSS BARU UNTUK MERAPIKAN TAMPILAN SCANNER */
    #reader { border-radius: 12px; overflow: hidden; border: 2px dashed #007aff !important; }
    #reader__dashboard_section_csr span { color: #007aff !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0">Layar Transaksi</h4>
    <span class="fw-bold text-primary"><i class="fas fa-user-circle me-1"></i> Kasir: <?= ucfirst($nama_kasir) ?></span>
</div>

<div class="row">
    <div class="col-md-4">
        
        <div class="card stat-card p-4 mb-3">
            <h6 class="fw-bold mb-3">Input Barang</h6>
            <form id="formTambah" action="keranjang_tambah.php" method="POST">
                <div class="mb-3">
                    <label class="small fw-bold">Pilih Produk</label>
                    <select name="id_barang" id="pilih_barang" class="form-select bg-light border-0" required onchange="tampilkanStok()">
                        <option value="" data-stok="">-- Pilih Barang --</option>
                        <?php 
                        $brg = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0");
                        while($b = mysqli_fetch_assoc($brg)) {
                            echo "<option value='{$b['id_barang']}' data-stok='{$b['stok']}'>{$b['nama_barang']} (Rp ".number_format($b['harga'], 0, ',', '.').")</option>";
                        }
                        ?>
                    </select>
                    <div id="info_stok" class="stok-badge" style="display: none;"></div>
                </div>
                
                <div class="mb-3">
                    <label class="small fw-bold">Jumlah</label>
                    <input type="number" name="jumlah" id="input_jumlah" class="form-control bg-light border-0" value="1" min="1" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2"><i class="fas fa-cart-plus me-2"></i> Tambah ke Daftar</button>
            </form>
        </div>

        <div class="card stat-card p-4 mb-3">
            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-qrcode me-2"></i>Scan Kartu Member</h6>
            <div id="reader" width="100%"></div>
            <small class="text-muted mt-2 text-center d-block">Arahkan layar HP pelanggan ke kamera</small>
        </div>

    </div>

    <div class="col-md-8">
        <div class="card stat-card p-4">
            
            <div class="table-responsive mb-3">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        $q = mysqli_query($conn, "SELECT k.*, b.nama_barang, b.harga FROM keranjang k JOIN barang b ON k.id_barang = b.id_barang WHERE k.id_user = '$id_u'");
                        while($c = mysqli_fetch_assoc($q)): $total += $c['subtotal']; ?>
                        <tr>
                            <td><?= $c['nama_barang'] ?></td>
                            <td><?= number_format($c['harga']) ?></td>
                            <td><?= $c['jumlah'] ?></td>
                            <td class="fw-bold text-primary"><?= number_format($c['subtotal']) ?></td>
                            <td><a href="keranjang_hapus.php?id=<?= $c['id_keranjang'] ?>" class="text-danger"><i class="fas fa-trash"></i></a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="total-box shadow-sm">
                <small class="d-block">TOTAL BELANJA</small>
                <h2 class="fw-bold mb-0">Rp <?= number_format($total, 0, ',', '.') ?></h2>
            </div>

            <form id="formBayar" action="simpan_transaksi.php" method="POST">
                <input type="hidden" name="total" id="total_belanja" value="<?= $total ?>">
                
                <div class="mb-3">
                    <label class="small fw-bold text-primary">Kode Member (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-primary"><i class="fas fa-id-card"></i></span>
                        <input type="text" name="kode_member" id="input_member" class="form-control fw-bold" placeholder="Menunggu hasil scan kamera..." readonly>
                        <button type="button" class="btn btn-outline-danger" onclick="hapusMember()" title="Hapus Member"><i class="fas fa-times"></i></button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small fw-bold">Uang Bayar</label>
                        <input type="number" name="uang_bayar" id="uang_bayar" class="form-control form-control-lg border-primary" oninput="hitungKembali()" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small fw-bold">Kembalian</label>
                        <input type="text" id="kembalian" class="form-control form-control-lg bg-light text-danger fw-bold" readonly placeholder="Rp 0">
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-lg w-100 fw-bold" <?= $total == 0 ? 'disabled' : '' ?>><i class="fas fa-check-circle me-2"></i> PROSES PEMBAYARAN</button>
            </form>

        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
// ==========================================
// 1. FITUR MEMORI MEMBER (SESSION STORAGE)
// ==========================================
// Munculkan kembali kode member jika sebelumnya sudah di-scan
document.addEventListener("DOMContentLoaded", function() {
    let memberLama = sessionStorage.getItem('member_tersimpan');
    if (memberLama) {
        document.getElementById('input_member').value = memberLama;
    }
});


// ==========================================
// 2. LOGIKA SCANNER KAMERA
// ==========================================
function onScanSuccess(decodedText, decodedResult) {
    let inputMember = document.getElementById('input_member');
    
    // Cek apakah hasil scan sama dengan yang sudah ada
    if (inputMember.value !== decodedText) {
        inputMember.value = decodedText;
        
        // SIMPAN KE MEMORI BROWSER AGAR TIDAK HILANG SAAT REFRESH
        sessionStorage.setItem('member_tersimpan', decodedText); 
        
        // Suara & Notif
        mainkanSuara(800, 0.1, 'sine');
        setTimeout(() => { mainkanSuara(1200, 0.2, 'sine'); }, 100);
        Swal.fire({
            icon: 'success', title: 'Member Terdeteksi!', text: 'Kode: ' + decodedText,
            timer: 1500, showConfirmButton: false, toast: true, position: 'top-end'
        });
    }
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", { fps: 10, qrbox: {width: 200, height: 200} }, false
);
html5QrcodeScanner.render(onScanSuccess);

function hapusMember() {
    document.getElementById('input_member').value = '';
    // Hapus juga dari memori browser saat tombol silang (X) ditekan
    sessionStorage.removeItem('member_tersimpan');
}


// ==========================================
// 3. FUNGSI TAMPILKAN STOK OTOMATIS
// ==========================================
function tampilkanStok() {
    var select = document.getElementById("pilih_barang");
    var selectedOption = select.options[select.selectedIndex];
    var stok = selectedOption.getAttribute("data-stok");
    var infoStok = document.getElementById("info_stok");
    var inputJumlah = document.getElementById("input_jumlah");

    if (stok && stok !== "") {
        infoStok.style.display = "inline-block";
        infoStok.innerHTML = "<i class='fas fa-box-open text-primary me-1'></i> Sisa Stok: <b>" + stok + "</b> unit";
        inputJumlah.setAttribute("max", stok);
        if (parseInt(inputJumlah.value) > parseInt(stok)) {
            inputJumlah.value = stok;
        }
    } else {
        infoStok.style.display = "none";
        inputJumlah.removeAttribute("max");
    }
}


// ==========================================
// 4. FUNGSI SUARA & KEMBALIAN
// ==========================================
function mainkanSuara(frekuensi, durasi, tipe) {
    let context = new (window.AudioContext || window.webkitAudioContext)();
    let osc = context.createOscillator();
    let gain = context.createGain();
    osc.type = tipe; 
    osc.frequency.setValueAtTime(frekuensi, context.currentTime);
    gain.gain.setValueAtTime(1.0, context.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.0001, context.currentTime + durasi);
    osc.connect(gain);
    gain.connect(context.destination);
    osc.start();
    osc.stop(context.currentTime + durasi);
}

function hitungKembali() {
    let total = <?= $total ?>;
    let bayar = document.getElementById('uang_bayar').value;
    let kembali = bayar - total;
    let kotakKembalian = document.getElementById('kembalian');

    if (bayar === "") {
        kotakKembalian.value = "";
        kotakKembalian.classList.remove('text-success');
        kotakKembalian.classList.add('text-danger');
        return;
    }
    if (kembali >= 0) {
        kotakKembalian.value = "Rp " + kembali.toLocaleString('id-ID');
        kotakKembalian.classList.remove('text-danger'); 
        kotakKembalian.classList.add('text-success');   
    } else {
        kotakKembalian.value = "Uang Kurang";
        kotakKembalian.classList.remove('text-success'); 
        kotakKembalian.classList.add('text-danger');     
    }
}

document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();
    mainkanSuara(600, 0.1, 'sine');
    setTimeout(() => { this.submit(); }, 200);
});

// ==========================================
// 5. PROSES PEMBAYARAN FINAL
// ==========================================
document.getElementById('formBayar').addEventListener('submit', function(e) {
    let total = <?= $total ?>;
    let bayar = document.getElementById('uang_bayar').value;
    
    if (parseInt(bayar) < total || bayar === "") {
        mainkanSuara(150, 0.3, 'sawtooth'); 
        Swal.fire('Gagal!', 'Uang bayar tidak cukup!', 'error');
        e.preventDefault();
        return;
    }
    
    e.preventDefault();
    
    // PENTING: Bersihkan memori agar transaksi selanjutnya tidak pakai member ini lagi
    sessionStorage.removeItem('member_tersimpan');

    mainkanSuara(800, 0.2, 'sine');
    setTimeout(() => { mainkanSuara(1200, 0.3, 'sine'); }, 100);
    Swal.fire({
        title: 'Memproses Pembayaran...', text: 'Mohon tunggu sebentar', timer: 1500, showConfirmButton: false, didOpen: () => { Swal.showLoading(); }
    }).then(() => { this.submit(); });
});
</script>

<?php include 'footer.php'; ?>