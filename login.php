<?php
session_start();

// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");

// Memeriksa koneksi ke database
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Memeriksa apakah pengguna sudah login sebelumnya
if (isset($_SESSION['username'])) {
    // Mengarahkan pengguna ke halaman yang sesuai dengan peran (role) mereka
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/admin_dashboard.php");
    } else {
        header("Location: home.php");
    }
    exit();
}

// Memeriksa apakah ada data yang dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mengambil data pengguna dari database berdasarkan username
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    // Memeriksa keberhasilan query dan apakah ada data pengguna dengan username yang sesuai
    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $stored_password = $row['password']; // Password yang disimpan di database
        $role = $row['role']; // Peran (role) pengguna

        // Memeriksa kecocokan password berdasarkan peran (role) pengguna
        if ($role === 'pelanggan' && password_verify($password, $stored_password)) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $role;

            // Mengarahkan pengguna ke halaman home.php
            header("Location: home.php");
            exit();
        } elseif ($role === 'admin' && $password === $stored_password) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $role;

            // Mengarahkan pengguna ke halaman admin_dashboard.php
            header("Location: admin/admin_dashboard.php");
            exit();
        } else {
            // Jika autentikasi gagal, tampilkan pesan error
            $_SESSION['errorMessage'] = "Username atau password salah.";
            header("Location: index.php");
            exit();
        }
    } else {
        // Jika autentikasi gagal, tampilkan pesan error
        $_SESSION['errorMessage'] = "Username atau password salah.";
        header("Location: index.php");
        exit();
    }
}
?>