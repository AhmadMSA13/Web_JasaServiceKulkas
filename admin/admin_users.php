<?php
session_start();

// Memeriksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Memeriksa koneksi database
$conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Memproses aksi edit atau hapus pengguna
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'hapus') {
        if (isset($_GET['id'])) {
            // Memeriksa apakah id pengguna valid
            $userId = $_GET['id'];
            $deleteUserQuery = "DELETE FROM users WHERE id = $userId";
            $deleteUserResult = mysqli_query($conn, $deleteUserQuery);

            if (!$deleteUserResult) {
                die("Error: " . mysqli_error($conn));
            }

            // Mengarahkan kembali ke halaman admin_users.php setelah penghapusan berhasil
            header("Location: admin_users.php");
            exit();
        } else {
            die("Error: ID pengguna tidak ditemukan.");
        }
    } else {
        die("Error: Aksi tidak valid.");
    }
}

// Mendapatkan total jumlah pelanggan
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users WHERE role = 'pelanggan'";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);

// Memeriksa hasil query
if (!$totalUsersResult) {
    die("Error: " . mysqli_error($conn));
}

$totalUsersRow = mysqli_fetch_assoc($totalUsersResult);
$totalUsers = $totalUsersRow['total_users'];

// Mendapatkan halaman saat ini
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Jumlah pelanggan yang ditampilkan per halaman
$usersPerPage = 5;

// Menghitung total halaman
$totalPages = ceil($totalUsers / $usersPerPage);

// Memeriksa apakah halaman yang diminta valid
if ($currentPage < 1 || $currentPage > $totalPages) {
    $currentPage = 1;
}

// Menghitung offset (awal data) untuk query
$offset = ($currentPage - 1) * $usersPerPage;

// Mendapatkan Daftar Pelanggan dari database
$userQuery = "SELECT * FROM users WHERE role = 'pelanggan' LIMIT $offset, $usersPerPage";
$userResult = mysqli_query($conn, $userQuery);

// Memeriksa hasil query
if (!$userResult) {
    die("Error: " . mysqli_error($conn));
}

// Memproses pencarian pengguna
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $searchQuery = "SELECT * FROM users WHERE role = 'pelanggan' AND (username LIKE '%$search%' OR email LIKE '%$search%')";
    $userResult = mysqli_query($conn, $searchQuery);

    // Memeriksa hasil query pencarian
    if (!$userResult) {
        die("Error: " . mysqli_error($conn));
    }
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
            <li><a href="admin_services.php">Daftar Layanan</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>

        <!-- Menu Mobile-->
        <label for="check" class="menu-moblie"><i class="fa-solid fa-bars fa-2x"></i></label>
    </nav>
    <div class="user-container">
        <h2>Daftar Pelanggan</h2>
        <input type="checkbox" id="check">
        <div class="sidebar">
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_users.php">Daftar Pelanggan</a></li>
                <li><a href="admin_services.php">Daftar layanan</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
        <form action="admin_users.php" method="post">
            <input type="text" name="search" placeholder="Cari pengguna">
            <button type="submit">Cari</button>
        </form>
        <?php if (mysqli_num_rows($userResult) > 0) : ?>
            <table>
                <tr>
                    <th>Foto Profil</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Nomor Telepon</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($userResult)) : ?>
                    <tr>
                        <td>
                            <?php if (!empty($row['gambar'])) : ?>
                                <img src="../<?php echo $row['gambar']; ?>" alt="Foto Profil" width="150">
                            <?php else: ?>
                                <img src="../uploads/default_profile.png" alt="Default Foto Profil" width="150">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['nomor']; ?></td>
                        <td><?php echo $row['alamat']; ?></td>
                        <td>
                            <a class="btn-edit" href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a class="btn-delete" href="admin_users.php?action=hapus&id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <?php if ($totalPages > 1) : ?>
                <div class="pagination">
                    <?php if ($currentPage > 1) : ?>
                        <a class="active" href="admin_users.php?page=<?php echo $currentPage - 1; ?>">Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <?php if ($i == $currentPage) : ?>
                            <a class="active" href="admin_users.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php else : ?>
                            <a class="active" href="admin_users.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages) : ?>
                        <a class="active" href="admin_users.php?page=<?php echo $currentPage + 1; ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <p>Tidak ada pelanggan yang ditemukan.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>&copy; 2023 Jasa Service Kulkas. All rights reserved.</p>
    </footer>
</body>
</html>
