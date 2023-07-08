<?php
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Mengambil informasi pengguna dari session
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Mengatur pesan selamat datang sesuai peran pengguna
if ($role == 'admin') {
    $welcomeMessage = "Selamat datang, Admin $username!";
} else {
    $welcomeMessage = "Selamat datang, Pelanggan $username!";
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
    <h1>Home</h1>
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
    <div class="home-container">
        <h2><?php echo $welcomeMessage; ?></h2>
        <p> 
            Kami adalah platform yang menyediakan layanan perbaikan dan pemeliharaan kulkas. 
            Kami memiliki tim ahli yang siap membantu Anda dalam memperbaiki berbagai masalah kulkas Anda. 
            Dari perbaikan umum hingga penggantian suku cadang, kami siap memberikan layanan terbaik. 
            Penting untuk dicatat bahwa kami tidak menyediakan layanan jual spare part kulkas. 
            Namun, kami dengan senang hati membantu Anda dalam memperbaiki kulkas Anda agar berfungsi dengan baik.
        </p>
    </div>
    <footer>
    <p>&copy; 2023 Jasa Service Kulkas. All rights reserved.</p>
    </footer>
</body>
</html>