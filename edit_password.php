<?php
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");

// Mengambil informasi pengguna dari session
$username = $_SESSION['username'];

// Mengambil data pengguna dari database
$query = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($conn, $query);

// Memeriksa apakah ada data pengguna
if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);

    // Menangani pembaruan password pengguna
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Mengambil data dari form
        $passwordLama = $_POST['password_lama'];
        $passwordBaru = $_POST['password_baru'];
        $konfirmasiPassword = $_POST['konfirmasi_password'];

        // Memeriksa kecocokan password lama
        if (password_verify($passwordLama, $row['password'])) {
            // Memeriksa kecocokan password baru dan konfirmasi password
            if ($passwordBaru === $konfirmasiPassword) {
                // Memeriksa apakah password baru sama dengan password lama
                if ($passwordBaru !== $passwordLama) {
                    // Mengenkripsi password baru
                    $hashedPassword = password_hash($passwordBaru, PASSWORD_DEFAULT);

                    // Memperbarui password pengguna di database
                    $updateQuery = "UPDATE users SET password='$hashedPassword' WHERE username='$username'";
                    mysqli_query($conn, $updateQuery);

                    // Mengarahkan pengguna kembali ke halaman profil setelah pembaruan berhasil
                    header("Location: profil.php");
                    exit();
                } else {
                    $errorMessage = "Password baru harus berbeda dengan password lama.";
                }
            } else {
                $errorMessage = "Password baru dan konfirmasi password tidak cocok.";
            }
        } else {
            $errorMessage = "Password lama tidak cocok.";
        }
    }

    // Mengarahkan pengguna ke halaman "profil.php" saat tombol "Batal" diklik
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel'])) {
        header("Location: profil.php");
        exit();
    }
} else {
    echo "Data pengguna tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jasa Service Kulkas</title>
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="profil-container">
        <h2>Ubah Password</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <label for="password_lama">Password Lama:</label>
            <input type="password" name="password_lama" required><br>

            <label for="password_baru">Password Baru:</label>
            <input type="password" name="password_baru" required><br>

            <label for="konfirmasi_password">Konfirmasi Password:</label>
            <input type="password" name="konfirmasi_password" required><br>

            <?php if (isset($errorMessage)): ?>
                <p class="error"><?php echo $errorMessage; ?></p>
            <?php endif; ?>

            <input type="submit" name="change_password" value="Ubah Password" class="btn-change-password">
            <input type="submit" name="cancel" value="Batal" class="btn-cancel" formnovalidate>
        </form>
    </div>
</body>
</html>