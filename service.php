<?php
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");

// Mendapatkan daftar merek kulkas dari database
$merekQuery = "SELECT * FROM merek";
$merekResult = mysqli_query($conn, $merekQuery);

// Mendapatkan daftar jenis kerusakan kulkas dari database
$kerusakanQuery = "SELECT * FROM kerusakan";
$kerusakanResult = mysqli_query($conn, $kerusakanQuery);

// Menangani layanan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $merek = $_POST['merek'];
    $model = $_POST['model'];
    $kerusakan = $_POST['kerusakan'];
    $tanggalLayanan = $_POST['tanggal_layanan'];
    $tanggalKunjungan = $_POST['tanggal_kunjungan'];
    $status = "Dalam Proses";
    $timestamp = date("Y-m-d H:i:s");

    // Mendapatkan harga perbaikan berdasarkan jenis kerusakan
    $hargaQuery = "SELECT harga_perbaikan FROM kerusakan WHERE kerusakan_nama = ?";
    $stmt = mysqli_prepare($conn, $hargaQuery);
    mysqli_stmt_bind_param($stmt, "s", $kerusakan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hargaPerbaikan);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Menyimpan layanan ke database
    $username = $_SESSION['username'];

    // Mendapatkan nama merek kulkas
    $merekQuery = "SELECT merek_nama FROM merek WHERE merek_id = ?";
    $stmt = mysqli_prepare($conn, $merekQuery);
    mysqli_stmt_bind_param($stmt, "i", $merek);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $merekNama);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Memperbarui kolom merek_kulkas pada tabel users
    $updateQuery = "UPDATE users SET merek_kulkas = ? WHERE username = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ss", $merekNama, $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Menyimpan layanan ke database
    $insertQuery = "INSERT INTO layanan (username, merek_kulkas, model_kulkas, jenis_kerusakan, tanggal_layanan, tanggal_kunjungan, status, timestamp) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "ssssssss", $username, $merekNama, $model, $kerusakan, $tanggalLayanan, $tanggalKunjungan, $status, $timestamp);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Mengarahkan pengguna kembali ke halaman profil setelah layanan berhasil
    header("Location: home.php");
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
    <div class="order-container">
        <h2>Formulir Layanan</h2>
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
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <label for="merek">Merek Kulkas:</label>
            <select name="merek" required>
                <?php while ($row = mysqli_fetch_assoc($merekResult)) : ?>
                    <option value="<?php echo $row['merek_id']; ?>"><?php echo $row['merek_nama']; ?></option>
                <?php endwhile; ?>
            </select><br>

            <label for="model">Model Kulkas:</label>
            <input type="radio" name="model" value="1 Pintu" required> 1 Pintu
            <input type="radio" name="model" value="2 Pintu" required> 2 Pintu<br>

            <label for="kerusakan">Jenis Kerusakan:</label>
            <select name="kerusakan" required>
                <?php while ($row = mysqli_fetch_assoc($kerusakanResult)) : ?>
                    <option value="<?php echo $row['kerusakan_nama']; ?>" data-harga="<?php echo $row['harga_perbaikan']; ?>"><?php echo $row['kerusakan_nama']; ?></option>
                <?php endwhile; ?>
            </select><br>

            <label for="tanggal_layanan">Tanggal Layanan:</label>
            <input type="date" name="tanggal_layanan" required><br>

            <label for="tanggal_kunjungan">Tanggal Kunjungan:</label>
            <input type="date" name="tanggal_kunjungan" required><br>

            <label for="harga_perbaikan">Harga Perbaikan:</label>
            <input type="text" name="harga_perbaikan" id="hargaPerbaikanInput" readonly><br>

            <label for="status">Status:</label>
            <input type="text" name="status" value="Dalam Proses" readonly><br>

            <input type="submit" name="kirim" value="Kirim">
        </form>
    </div>
    <script>
        // Mengambil nilai harga perbaikan berdasarkan pilihan kerusakan
        var kerusakanSelect = document.querySelector('select[name="kerusakan"]');
        var hargaPerbaikanInput = document.querySelector('input[name="harga_perbaikan"]');

        kerusakanSelect.addEventListener('change', function() {
            var hargaPerbaikan = this.options[this.selectedIndex].getAttribute('data-harga');
            hargaPerbaikanInput.value = hargaPerbaikan;
        });
    </script>
    <footer>
    <p>&copy; 2023 Jasa Service Kulkas. All rights reserved.</p>
    </footer>
</body>
</html>
