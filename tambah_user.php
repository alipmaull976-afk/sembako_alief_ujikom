<?php 
include 'config.php'; 
if($_SESSION['role'] != 'admin'){ header("Location: index.php"); exit; }

if(isset($_POST['simpan_user'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];
    
    // PASSWORD TANPA HASH (Teks Asli)
    $password = mysqli_real_escape_string($conn, $_POST['password']); 

    // Siapkan 'wadah' HTML untuk memanggil SweetAlert dari internet
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><style>body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }</style></head><body>";

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($cek) > 0){
        // ALERT KUNING: Jika Username Kembar
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Username sudah dipakai! Silakan cari nama lain.',
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'Kembali'
            }).then(() => { window.history.back(); });
        </script>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");
        if($insert){
            // ALERT HIJAU AESTHETIC: Berhasil (Otomatis Pindah Halaman)
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pengguna baru telah ditambahkan ke sistem.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => { window.location.href = 'daftar_user.php'; });
            </script>";
        } else {
            // ALERT MERAH: Gagal Simpan
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan data.',
                    confirmButtonColor: '#d33'
                }).then(() => { window.history.back(); });
            </script>";
        }
    }
    echo "</body></html>";
    exit; 
}

include 'header.php'; // Panggil Sidebar & CSS
?>

<h3 class="fw-bold mb-4">Tambah Pengguna Baru</h3>

<div class="row">
    <div class="col-md-6">
        <div class="card stat-card p-4">
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="fw-bold form-label">Username</label>
                    <input type="text" name="username" class="form-control bg-light border-0" placeholder="Misal: kasir_budi" required>
                </div>
                <div class="mb-3">
                    <label class="fw-bold form-label">Password</label>
                    <input type="password" name="password" class="form-control bg-light border-0" placeholder="Masukkan password" required>
                </div>
                <div class="mb-4">
                    <label class="fw-bold form-label">Hak Akses (Role)</label>
                    <select name="role" class="form-select bg-light border-0" required>
                        <option value="">-- Pilih Posisi --</option>
                        <option value="kasir">Kasir</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <button type="submit" name="simpan_user" class="btn btn-primary w-100 fw-bold rounded-pill mb-2">
                    <i class="fas fa-save me-2"></i> Simpan Pengguna
                </button>
                <a href="daftar_user.php" class="btn btn-light w-100 fw-bold rounded-pill border">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>