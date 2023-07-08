<?php
session_start();
if (isset($_SESSION['errorMessage'])) {
    echo "<script>alert('" . $_SESSION['errorMessage'] . "');</script>";
    unset($_SESSION['errorMessage']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jasa Service Kulkas</title>
    <link rel="stylesheet" type="text/css" href="css/login_register.css">
</head>
<body>
    <h1>Selamat datang di Jasa Service Kulkas</h1>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>
        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</body>
</html>
