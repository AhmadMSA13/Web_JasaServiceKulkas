<?php
session_start();

// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");

// Memeriksa apakah pengguna sudah login sebelumnya
if (isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

// Memeriksa apakah ada data yang dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $nomor = $_POST['nomor'];
    $alamat = $_POST['alamat'];

    // Memeriksa apakah username sudah terdaftar
    $checkUsernameQuery = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $checkUsernameQuery);

    if (mysqli_num_rows($result) > 0) {
        // Jika username sudah terdaftar, tampilkan peringatan menggunakan message box
        echo '<script>alert("Username sudah terdaftar. Silakan gunakan username yang berbeda.")</script>';
    } else {
        // Mengenkripsi password menggunakan password_hash()
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Jika username belum terdaftar, tambahkan pengguna ke database
        $registerQuery = "INSERT INTO users (id, username, email, password, nomor, alamat, role) 
                          VALUES (NULL, '$username', '$email', '$hashedPassword', '$nomor', '$alamat', 'pelanggan')";

        if (mysqli_query($conn, $registerQuery)) {
            // Redirect ke halaman login setelah pendaftaran sukses
            header("Location: index.php");
            exit();
        } else {
            // Query gagal, cetak pesan kesalahan
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jasa Service Kulkas</title>
    <link rel="stylesheet" type="text/css" href="css/login_register.css">
</head>
<body>
    <h1>Pendaftaran Pengguna</h1>
    <div class="register-container">
        <h2>Daftar Akun</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="text" name="nomor" placeholder="Nomor Telepon" required><br>
            <textarea name="alamat" placeholder="Alamat" required></textarea><br>
            <input type="submit" value="Daftar">
        </form>
        <p>Sudah punya akun? <a href="index.php">Login di sini</a></p>
    </div>
</body>
</html>
