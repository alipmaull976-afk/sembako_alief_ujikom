<?php
include 'config.php';

// Cek session, pastikan hanya admin yang bisa akses
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Siapkan wadah HTML & panggil library SweetAlert
echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><style>body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }</style></head><body>";

if (isset($_GET['id'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['id']);
    $id_login = $_SESSION['id_user']; 

    // PROTEKSI SPESIAL: Cegah admin menghapus dirinya sendiri
    if ($id_hapus == $id_login) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak!',
                text: 'Anda tidak bisa menghapus akun Anda sendiri saat sedang login.',
                confirmButtonColor: '#d33'
            }).then(() => { window.location.href='daftar_user.php'; });
        </script>";
        exit;
    }

    // Eksekusi Hapus Data
    $hapus = mysqli_query($conn, "DELETE FROM users WHERE id_user = '$id_hapus'");

    if ($hapus) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Data pengguna berhasil dihapus.',
                showConfirmButton: false,
                timer: 1500
            }).then(() => { window.location.href='daftar_user.php'; });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menghapus!',
                text: 'Pastikan user ini tidak terkait dengan data transaksi.',
                confirmButtonColor: '#d33'
            }).then(() => { window.location.href='daftar_user.php'; });
        </script>";
    }
} else {
    // Kalau ID tidak ada di URL, arahkan kembali
    echo "<script>window.location.href='daftar_user.php';</script>";
}

echo "</body></html>";
?>