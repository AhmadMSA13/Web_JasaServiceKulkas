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

// Memeriksa apakah parameter ID layanan telah diberikan
if (!isset($_GET['id'])) {
    header("Location: history.php");
    exit();
}

// Mendapatkan ID layanan dari parameter URL
$layanan_id = $_GET['id'];

// Mendapatkan detail layanan dari database
$username = $_SESSION['username'];
$detailQuery = "SELECT * FROM layanan WHERE layanan_id = $layanan_id AND username = '$username'";
$detailResult = mysqli_query($conn, $detailQuery);

// Memeriksa hasil query
if (!$detailResult || mysqli_num_rows($detailResult) == 0) {
    header("Location: history.php");
    exit();
}

// Mendapatkan data detail layanan
$detailData = mysqli_fetch_assoc($detailResult);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jasa Service Kulkas</title>
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="detail-container">
        <h2>Detail Layanan</h2>
        <table>
            <tr>
                <th>Merek Kulkas</th>
                <td><?php echo $detailData['merek_kulkas']; ?></td>
            </tr>
            <tr>
                <th>Model Kulkas</th>
                <td><?php echo $detailData['model_kulkas']; ?></td>
            </tr>
            <tr>
                <th>Jenis Kerusakan</th>
                <td><?php echo $detailData['jenis_kerusakan']; ?></td>
            </tr>
            <tr>
                <th>Tanggal Layanan</th>
                <td><?php echo $detailData['tanggal_layanan']; ?></td>
            </tr>
            <tr>
                <th>Tanggal Kunjungan</th>
                <td><?php echo $detailData['tanggal_kunjungan']; ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?php echo $detailData['status']; ?></td>
            </tr>
        </table><br>
        <button onclick="window.location.href='history.php'" class="btn-kembali">Kembali</button>
    </div>
</body>
</html>
