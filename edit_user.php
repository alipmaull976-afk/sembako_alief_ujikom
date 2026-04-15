<?php 
include 'config.php'; 
if($_SESSION['role'] != 'admin'){ header("Location: index.php"); exit; }

// Pastikan ada ID yang dikirim dari link
if(!isset($_GET['id'])){
    header("Location: daftar_user.php");
    exit;
}

$id_edit = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data user lama dari database untuk ditampilkan di form
$query_data = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id_edit'");
$data = mysqli_fetch_assoc($query_data);

// Jika tombol "Update Pengguna" diklik
if(isset($_POST['update_user'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];
    $password_baru = mysqli_real_escape_string($conn, $_POST['password']); 

    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><style>body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }</style></head><body>";

    // Cek apakah username sudah dipakai orang lain (kecuali dirinya sendiri)
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND id_user != '$id_edit'");
    if(mysqli_num_rows($cek) > 0){
        echo "<script>
            Swal.fire({
                icon: 'warning', title: 'Oops...', text: 'Username sudah dipakai! Silakan cari nama lain.', confirmButtonColor: '#ffc107'
            }).then(() => { window.history.back(); });
        </script>";
    } else {
        // Cek apakah password diisi atau dikosongkan
        if(!empty($password_baru)){
            // Jika diisi, update semua termasuk password (TANPA HASH MD5)
            $update = mysqli_query($conn, "UPDATE users SET username='$username', password='$password_baru', role='$role' WHERE id_user='$id_edit'");
        } else {
            // Jika dikosongkan, update username & role saja, password lama aman
            $update = mysqli_query($conn, "UPDATE users SET username='$username', role='$role' WHERE id_user='$id_edit'");
        }

        if($update){
            echo "<script>
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: 'Data pengguna berhasil diperbarui.', showConfirmButton: false, timer: 1500
                }).then(() => { window.location.href = 'daftar_user.php'; });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat mengupdate data.', confirmButtonColor: '#d33'
                }).then(() => { window.history.back(); });
            </script>";
        }
    }
    echo "</body></html>";
    exit; 
}

include 'header.php';
?>

<h3 class="fw-bold mb-4"> Edit Pengguna</h3>

<div class="row">
    <div class="col-md-6">
        <div class="card stat-card p-4">
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="fw-bold form-label">Username</label>
                    <input type="text" name="username" class="form-control bg-light border-0" value="<?= $data['username'] ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="fw-bold form-label">Password Baru <small class="text-danger fw-normal">(Kosongkan jika tidak ingin ganti sandi)</small></label>
                    <input type="password" name="password" class="form-control bg-light border-0" placeholder="Ketik sandi baru...">
                </div>
                
                <div class="mb-4">
                    <label class="fw-bold form-label">Hak Akses (Role)</label>
                    <select name="role" class="form-select bg-light border-0" required>
                        <option value="kasir" <?= ($data['role'] == 'kasir') ? 'selected' : '' ?>>Kasir</option>
                        <option value="admin" <?= ($data['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                
                <button type="submit" name="update_user" class="btn btn-warning w-100 fw-bold rounded-pill mb-2 text-dark">
                    <i class="fas fa-save me-2"></i> Update Pengguna
                </button>
                <a href="daftar_user.php" class="btn btn-light w-100 fw-bold rounded-pill border">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>