<?php
include 'config.php';

// Pastikan ada ID yang dikirim
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Proses hapus data dari tabel member
    $query = "DELETE FROM member WHERE kode_member = '$id'";
    $hapus = mysqli_query($conn, $query);

    if ($hapus) {
        echo "<script>
            alert('Data Member berhasil dihapus!');
            window.location.href = 'admin_member.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menghapus data! Pastikan member ini tidak terikat dengan data transaksi.');
            window.location.href = 'admin_member.php';
        </script>";
    }
} else {
    // Jika tidak ada ID, kembalikan ke halaman member
    header("Location: admin_member.php");
    exit;
}
?>