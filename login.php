<?php
// Tangkap pesan dan role
$pesan = isset($_GET['pesan']) ? $_GET['pesan'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Sembako Alief</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #007aff;
            --bg-gradient: linear-gradient(135deg, #e0f2fe 0%, #cceeff 100%);
        }

        body {
            background: var(--bg-gradient);
            font-family: 'Inter', sans-serif;
            min-height: 100vh; /* Ubah dari height ke min-height agar tidak terpotong saat scroll di HP */
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.02em;
            margin: 0;
            padding: 20px 0; /* Memberi ruang aman di atas dan bawah pada layar kecil */
        }

        .login-container { 
            width: 100%; 
            max-width: 420px; 
            padding: 0 15px; 
        }

        .login-card {
            background: white;
            border: none;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 122, 255, 0.1);
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 122, 255, 0.15);
        }

        .brand-logo { 
            color: var(--primary-color); 
            font-weight: 800; 
            font-size: 2rem; 
            letter-spacing: -0.05em; 
            margin-bottom: 5px;
        }

        .form-label-custom { 
            font-size: 0.85rem; 
            font-weight: 600; 
            color: #4b5563; 
            margin-bottom: 8px; 
            display: block; 
        }

        .input-group-custom { 
            position: relative; 
            margin-bottom: 20px; 
        }

        .input-group-custom i { 
            position: absolute; 
            left: 18px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #9ca3af; 
            z-index: 10; 
            font-size: 1.1rem; 
        }
        
        .form-control-custom {
            height: 55px; 
            background-color: #f3f4f6; 
            border: 2px solid #f3f4f6;
            border-radius: 12px; 
            padding-left: 50px; 
            font-weight: 500; 
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            background-color: white; 
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1); 
            color: #111827;
        }

        .btn-login {
            background-color: var(--primary-color); 
            border: none; 
            color: white;
            height: 55px; 
            border-radius: 12px; 
            font-weight: 700; 
            font-size: 1rem;
            letter-spacing: -0.02em; 
            transition: all 0.2s ease; 
            display: flex;
            align-items: center; 
            justify-content: center;
        }

        .btn-login:hover { 
            background-color: #0063cc; 
            transform: scale(1.01); 
        }

        .btn-login:active { 
            transform: scale(0.99); 
        }

        .footer-text { 
            font-size: 0.8rem; 
            color: #9ca3af; 
            margin-top: 30px; 
            text-align: center; 
        }

        /* TAMBAHAN ANIMASI GETAR */
        .shake-anim { animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both; border: 2px solid #dc3545; }
        @keyframes shake {
            10%, 90% { transform: translate3d(-2px, 0, 0); }
            20%, 80% { transform: translate3d(4px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-6px, 0, 0); }
            40%, 60% { transform: translate3d(6px, 0, 0); }
        }

        /* MEDIA QUERY UNTUK RESPONSIVITAS DI HP (DIPERBARUI) */
@media (max-width: 576px) {
    .login-card {
        padding: 25px 15px; /* Kurangi padding kartu secara drastis */
        border-radius: 15px;
    }
    
    .brand-logo {
        font-size: 1.5rem; /* Ukuran logo lebih kecil */
        margin-bottom: 2px;
    }
    
    .text-center.mb-4 {
        margin-bottom: 1.5rem !important; /* Perkecil jarak area atas */
    }

    .form-label-custom {
        font-size: 0.8rem;
        margin-bottom: 5px;
    }

    .form-control-custom {
        height: 42px; /* Input jauh lebih ramping */
        padding-left: 38px; /* Beri ruang agar teks tidak cepat terpotong */
        font-size: 0.85rem; /* Ukuran font di dalam input dikecilkan */
    }

    .input-group-custom i {
        left: 14px; /* Ikon digeser lebih ke kiri menyesuaikan padding */
        font-size: 0.95rem;
    }

    .btn-login {
        height: 42px; /* Tombol juga dirampingkan */
        font-size: 0.9rem;
    }

    .input-group-custom {
        margin-bottom: 15px; /* Jarak vertikal antar input dipersempit */
    }
    
    .mb-4 {
        margin-bottom: 1rem !important; 
    }
}
    </style>
</head>
<body>

<div class="login-container">
    <div class="card login-card <?= ($pesan == 'gagal') ? 'shake-anim' : '' ?>">
        <div class="card-body p-0">
            
            <div class="text-center mb-4">
                <i class="fas fa-store-alt fa-3x mb-3 text-primary"></i>
                <div class="brand-logo">SembakoAlief</div>
                <p class="text-muted fw-medium">Silakan masuk ke sistem kasir</p>
            </div>

            <form action="proses_login.php" method="POST" id="formLogin">
                
                <div class="mb-3">
                    <label class="form-label-custom">Username</label>
                    <div class="input-group-custom">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" class="form-control form-control-custom" placeholder="Masukkan username Anda" required autocomplete="off">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">Password</label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control form-control-custom" placeholder="Masukkan password Anda" required>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn btn-login w-100">
                        LOGIN<i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
                
            </form>

            <div class="footer-text">
                &copy; <?= date('Y') ?> Toko Sembako Alief. Indramayu.
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const soundSukses = new Audio('assets/audio/ta-da_yrvBrlS.mp3');
    const soundGagal = new Audio('assets/audio/error_CDOxCYm.mp3');

    // --- KALAU LOGIN GAGAL ---
    <?php if($pesan == 'gagal'){ ?>
        soundGagal.play().catch(e => console.log("Browser memblokir suara"));
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak!',
            text: 'Username atau Password salah.',
            confirmButtonColor: '#007aff',
            background: '#fff0f0'
        });
    <?php } ?>

    // --- KALAU LOGIN BERHASIL ---
    <?php if($pesan == 'sukses'){ ?>
        soundSukses.play().catch(e => console.log("Browser memblokir suara"));
        Swal.fire({
            icon: 'success',
            title: 'Login Berhasil!',
            text: 'Mengarahkan ke dashboard...',
            showConfirmButton: false,
            timer: 1500 // Notif muncul 1.5 detik sambil MP3 bunyi
        }).then(() => {
            // Setelah 1.5 detik, baru pindah ke dashboard masing-masing
            <?php if($role == 'admin'){ ?>
                window.location.href = 'admin_dashboard.php';
            <?php } else { ?>
                window.location.href = 'kasir_dashboard.php';
            <?php } ?>
        });
    <?php } ?>
</script>
</body>
</html>