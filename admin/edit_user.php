<?php
session_start();

// Memeriksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");

// Memeriksa koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Memeriksa apakah id pengguna valid
if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $editUserQuery = "SELECT * FROM users WHERE id = $userId";
    $editUserResult = mysqli_query($conn, $editUserQuery);

    if (!$editUserResult || mysqli_num_rows($editUserResult) === 0) {
        die("Error: Pengguna tidak ditemukan.");
    }

    // Memeriksa apakah form pengeditan pengguna telah dikirim
    if (isset($_POST['edit'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $nomor = $_POST['nomor'];
        $alamat = $_POST['alamat'];

        // Melakukan pembaruan data pengguna
        $updateUserQuery = "UPDATE users SET username = '$username', email = '$email', nomor = '$nomor', alamat = '$alamat' WHERE id = $userId";
        $updateUserResult = mysqli_query($conn, $updateUserQuery);

        if (!$updateUserResult) {
            die("Error: " . mysqli_error($conn));
        }

        // Mengarahkan kembali ke halaman admin_users.php setelah pengeditan berhasil
        header("Location: admin_users.php");
        exit();
    }

    // Menampilkan form pengeditan pengguna
    $editUserRow = mysqli_fetch_assoc($editUserResult);
}
else {
    die("Error: ID pengguna tidak ditemukan.");
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
    <div class="edit-container">
        <h2>Edit Pengguna</h2>
        <form action="edit_user.php?id=<?php echo $userId; ?>" method="post">
            <input type="text" name="username" placeholder="username" value="<?php echo $editUserRow['username']; ?>" required><br>
            <input type="email" name="email" placeholder="Email" value="<?php echo $editUserRow['email']; ?>" required><br>
            <input type="text" name="nomor" placeholder="Nomor Telepon" value="<?php echo $editUserRow['nomor']; ?>" required><br>
            <textarea name="alamat" placeholder="Alamat" required><?php echo $editUserRow['alamat']; ?></textarea><br>
            <button type="submit" name="edit">Simpan</button>
        </form>
    </div>
</body>
</html>
