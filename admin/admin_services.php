<?php
session_start();

// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");

// Memeriksa koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Memeriksa apakah ada pencarian yang dikirimkan
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    // Query untuk mencari pesanan berdasarkan nama pengguna atau merek kulkas
    $statusQuery = "SELECT layanan.*, users.username FROM layanan INNER JOIN users ON layanan.username = users.username WHERE layanan.status IN ('Dalam Proses', 'Selesai') AND (users.username LIKE '%$search%' OR layanan.merek_kulkas LIKE '%$search%') ORDER BY layanan.tanggal_layanan DESC";
} else {
    // Query untuk mendapatkan daftar pesanan dengan status 'Dalam Proses' dan 'Selesai' dari database
    $statusQuery = "SELECT layanan.*, users.username FROM layanan INNER JOIN users ON layanan.username = users.username WHERE layanan.status IN ('Dalam Proses', 'Selesai') ORDER BY layanan.tanggal_layanan DESC";
}

$statusResult = mysqli_query($conn, $statusQuery);

// Memeriksa hasil query
if (!$statusResult) {
    die("Error: " . mysqli_error($conn));
}

// Mendapatkan total jumlah pesanan
$totalservices = mysqli_num_rows($statusResult);

// Mendapatkan halaman saat ini
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Jumlah pesanan yang ditampilkan per halaman
$servicesPerPage = 5;

// Menghitung total halaman
$totalPages = ceil($totalservices / $servicesPerPage);

// Memeriksa apakah halaman yang diminta valid
if ($currentPage < 1 || $currentPage > $totalPages) {
    $currentPage = 1;
}

// Menghitung offset (awal data) untuk query
$offset = ($currentPage - 1) * $servicesPerPage;

// Mendapatkan Daftar Pesanan dari database
$orderQuery = "$statusQuery LIMIT $offset, $servicesPerPage";
$orderResult = mysqli_query($conn, $orderQuery);

// Memeriksa hasil query
if (!$orderResult) {
    die("Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jasa Service Kulkas</title>
    <link rel="stylesheet" type="text/css" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_users.php">Daftar Pelanggan</a></li>
            <li><a href="admin_services.php">Daftar Pesanan</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>

        <!-- Menu Mobile-->
        <label for="check" class="menu-moblie"><i class="fa-solid fa-bars fa-2x"></i></label>
    </nav>
    <div class="order-container">
        <h2>Daftar Layanan</h2>
        <input type="checkbox" id="check">
        <div class="sidebar">
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_users.php">Daftar Pelanggan</a></li>
                <li><a href="admin_services.php">Daftar Layanan</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <form action="admin_services.php" method="GET">
            <input type="text" name="search" placeholder="Cari pesanan">
            <button type="submit">Cari</button>
        </form>
        <?php if (mysqli_num_rows($orderResult) > 0) : ?>
            <table>
                <tr>
                    <th>Tanggal Layanan</th>
                    <th>Status</th>
                    <th>Nama Pengguna</th>
                    <th>Detail Pesanan</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($orderResult)) : ?>
                    <tr>
                        <td><?php echo $row['tanggal_layanan']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td>
                            <?php
                                echo "Merek Kulkas: " . $row['merek_kulkas'] . "<br>";
                                echo "Model Kulkas: " . $row['model_kulkas'] . "<br>";
                                echo "Jenis Kerusakan: " . $row['jenis_kerusakan'] . "<br>";
                                echo "Tanggal Kunjungan: " . $row['tanggal_kunjungan'] . "<br>";
                                echo "Status: " . $row['status'] . "<br>";
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <?php if ($totalPages > 1) : ?>
                <div class="pagination">
                    <?php if ($currentPage > 1) : ?>
                        <a class="active" href="admin_services.php?page=<?php echo $currentPage - 1; ?>">Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <?php if ($i == $currentPage) : ?>
                            <a class="active" href="admin_services.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php else : ?>
                            <a class="active" href="admin_services.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages) : ?>
                        <a class="active" href="admin_services.php?page=<?php echo $currentPage + 1; ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <p>Tidak ada pesanan dengan status 'Dalam Proses' atau 'Selesai'.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>&copy; 2023 Jasa Service Kulkas. All rights reserved.</p>
    </footer>
</body>
</html>
