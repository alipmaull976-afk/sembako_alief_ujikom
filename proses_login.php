<?php
// Wajib ada session_start() biar sistem inget siapa yang login
session_start(); 
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // PERBAIKAN 1: Nama tabel sudah diganti jadi 'users'
    $sql = "SELECT * FROM users WHERE username='$username'";
    $query = mysqli_query($conn, $sql);

    if (!$query) {
        die("Query Error: " . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($query);

    if ($data) {
        // PERHATIAN: Pastikan password di database tidak di-hash (MD5/Bcrypt). 
        // Kalau pakai hash, kodenya bukan == tapi pakai password_verify() atau md5()
        if ($password == $data['password']) {
            
            // PERBAIKAN 2: Nama field dikembalikan jadi 'id_user' (tanpa s)
            $_SESSION['id_user']  = $data['id_user']; 
            $_SESSION['username'] = $data['username'];
            $_SESSION['role']     = $data['role']; 

            header("Location: login.php?pesan=sukses&role=" . $data['role']);
            exit;
        } else {
            header("Location: login.php?pesan=gagal");
            exit;
        }
    } else {
        header("Location: login.php?pesan=gagal");
        exit;
    }
}
?>