<?php 
include 'config.php'; 
if($_SESSION['role'] != 'admin'){ header("Location: index.php"); exit; }
include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0"> Data Pengguna</h3>
    <a href="tambah_user.php" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
        <i class="fas fa-plus me-2"></i> Tambah User Baru
    </a>
</div>

<div class="card stat-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Role (Hak Akses)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $query = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC");
                while($u = mysqli_fetch_assoc($query)): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="fw-bold"><?= $u['username'] ?></td>
                    <td>
                        <?php if($u['role'] == 'admin'){ ?>
                            <span class="badge bg-primary px-3 py-2 rounded-pill">Admin</span>
                        <?php } else { ?>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Kasir</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="edit_user.php?id=<?= $u['id_user'] ?>" class="btn btn-sm btn-outline-warning rounded-pill me-1 text-dark fw-bold">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        
                        <a href="#" onclick="konfirmasiHapus(event, 'hapus_user.php?id=<?= $u['id_user'] ?>')" class="btn btn-sm btn-outline-danger rounded-pill">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function konfirmasiHapus(event, url) {
    event.preventDefault(); // Mencegah pindah halaman sebelum dikonfirmasi
    
    Swal.fire({
        title: 'Yakin mau hapus?',
        text: "Data pengguna ini tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33', // Merah
        cancelButtonColor: '#6c757d', // Abu-abu
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Kalau diklik "Ya", baru diarahkan ke file hapus_user.php
            window.location.href = url;
        }
    })
}
</script>

<?php include 'footer.php'; ?>