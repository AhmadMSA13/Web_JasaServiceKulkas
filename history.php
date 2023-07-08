<?php
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");

// Memeriksa koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Memeriksa aksi perubahan status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status']) && isset($_POST['layanan_id'])) {
    $status = $_POST['status'];
    $layanan_id = $_POST['layanan_id'];

    // Memperbarui status layanan
    $updateQuery = "UPDATE layanan SET status = '$status' WHERE layanan_id = $layanan_id";
    $updateResult = mysqli_query($conn, $updateQuery);

    // Memeriksa hasil query
    if (!$updateResult) {
        die("Error: " . mysqli_error($conn));
    }
}

// Mendapatkan riwayat layanan pengguna dari database
$username = $_SESSION['username'];
$riwayatQuery = "SELECT * FROM layanan WHERE username = '$username' ORDER BY tanggal_layanan DESC";
$riwayatResult = mysqli_query($conn, $riwayatQuery);

// Memeriksa hasil query
if (!$riwayatResult) {
    die("Error: " . mysqli_error($conn));
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
    <div class="riwayat-container">
        <h2>Riwayat Layanan</h2>
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
        <?php if (mysqli_num_rows($riwayatResult) > 0) : ?>
            <table>
                <tr>
                    <th>Tanggal Layanan</th>
                    <th>Status</th>
                    <th>Detail Layanan</th>
                    <th>Ubah Status</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($riwayatResult)) : ?>
                    <tr>
                        <td><?php echo $row['tanggal_layanan']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <a href="detail_layanan.php?id=<?php echo $row['layanan_id']; ?>">Lihat Detail</a>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'Dalam Proses') : ?>
                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                    <input type="hidden" name="layanan_id" value="<?php echo $row['layanan_id']; ?>">
                                    <select name="status">
                                        <option value="Dalam Proses">Dalam Proses</option>
                                        <option value="Selesai">Selesai</option>
                                    </select>
                                    <input type="submit" name="ubah_status" value="Ubah">
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>Tidak ada riwayat layanan.</p>
        <?php endif; ?>
    </div>
    <footer>
    <p>&copy; 2023 Jasa Service Kulkas. All rights reserved.</p>
    </footer>
</body>
</html>
