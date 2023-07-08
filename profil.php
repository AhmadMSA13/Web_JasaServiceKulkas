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

    // Menangani pembaruan data pengguna
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        // Mengambil data dari form
        $newUsername = $_POST['username'];
        $email = $_POST['email'];
        $nomor = $_POST['nomor'];
        $alamat = $_POST['alamat'];

        // Memeriksa apakah ada file gambar yang diunggah
        if (isset($_FILES['gambar']) && $_FILES['gambar']['size'] > 0) {
            $gambar = $_FILES['gambar'];

            // Mengupload file gambar
            $gambarName = $gambar['name'];
            $gambarTmp = $gambar['tmp_name'];
            $gambarPath = "uploads/" . $gambarName;
            move_uploaded_file($gambarTmp, $gambarPath);

            // Memperbarui informasi pengguna di database termasuk kolom gambar
            $updateQuery = "UPDATE users SET username='$newUsername', email='$email', nomor='$nomor', alamat='$alamat', gambar='$gambarPath' WHERE username='$username'";
            mysqli_query($conn, $updateQuery);

            // Mengupdate session dengan username baru
            $_SESSION['username'] = $newUsername;

            // Mengarahkan pengguna kembali ke halaman profil setelah pembaruan berhasil
            header("Location: profil.php");
            exit();
        } else {
            // Jika pengguna tidak mengunggah gambar, hanya memperbarui informasi pengguna tanpa kolom gambar
            $updateQuery = "UPDATE users SET username='$newUsername', email='$email', nomor='$nomor', alamat='$alamat' WHERE username='$username'";
            mysqli_query($conn, $updateQuery);

            // Mengupdate session dengan username baru
            $_SESSION['username'] = $newUsername;

            // Mengarahkan pengguna kembali ke halaman profil setelah pembaruan berhasil
            header("Location: profil.php");
            exit();
        }
    }

    // Mengarahkan pengguna ke halaman "edit_password.php" saat tombol "Ubah Password" diklik
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
        header("Location: edit_password.php");
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
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="service.php">Service</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="about.html">About Us</a></li>
            <li><a href="contactus.html">Contact Us</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

        <!-- Menu Mobile-->
        <label for="check" class="menu-moblie"><i class="fa-solid fa-bars fa-2x"></i></label>
    </nav>
    <div class="profil-container">
        <h2>Profil Pelanggan</h2>
        <input type="checkbox" id="check">
        <div class="sidebar">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="service.php">Service</a></li>
                <li><a href="history.php">History</a></li>
                <li><a href="about.html">About Us</a></li>
                <li><a href="contactus.html">Contact Us</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <img id="foto-profil" src="<?php echo !empty($row['gambar']) ? $row['gambar'] : 'uploads/default_profile.png'; ?>" alt="Foto Profil"><br>
            <input type="file" id="upload-gambar" style="display: none;" name="gambar"><br>
            
            <label for="username">Nama:</label>
            <input type="text" name="username" value="<?php echo $row['username']; ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo $row['email']; ?>" required><br>

            <label for="nomor">Nomor:</label>
            <input type="text" name="nomor" value="<?php echo $row['nomor']; ?>" required><br>

            <label for="alamat">Alamat:</label>
            <textarea name="alamat" required><?php echo $row['alamat']; ?></textarea><br>

            <input type="submit" name="submit" value="Simpan" class="btn-save">
            <input type="submit" name="change_password" value="Ubah Password" class="btn-change-password">
        </form>
    </div>

    <script>
        // Mengaktifkan pengunggahan gambar saat gambar foto profil diklik
        const fotoProfil = document.getElementById('foto-profil');
        const uploadGambar = document.getElementById('upload-gambar');

        fotoProfil.addEventListener('click', function() {
            uploadGambar.click();
        });

        // Mengubah gambar foto profil secara otomatis setelah pengguna mengunggah gambar baru
        uploadGambar.addEventListener('change', function() {
            const file = this.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                fotoProfil.src = e.target.result;
            };

            reader.readAsDataURL(file);
        });
    </script>
    
    <footer>
        <p>&copy; 2023 Jasa Service Kulkas. All rights reserved.</p>
    </footer>
</body>
</html>