<!DOCTYPE html>
<html>
<head>
    <title>Jasa Service Kulkas</title>
    <link rel="stylesheet" type="text/css" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        function confirmDelete() {
            return confirm("Apakah Anda yakin ingin menghapus data ini?");
        }
    </script>
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

    <div class="admin-container">
    <h2>Merek Kulkas</h2>
    <input type="checkbox" id="check">
        <div class="sidebar">
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_users.php">Daftar Pelanggan</a></li>
                <li><a href="admin_services.php">Daftar Layanan</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    <form action="admin_process.php" method="post">
        <input type="hidden" name="action" value="addMerek">
        <label for="merek">Merek Kulkas:</label>
        <input type="text" id="merek" name="merek" required>

        <label for="tipe">Tipe Kulkas:</label>
        <div class="radio-group">
            <input type="radio" name="model" value="1 Pintu" required> 1 Pintu
            <input type="radio" name="model" value="2 Pintu" required> 2 Pintu
        </div>

        <button type="submit">Tambah Merek</button>
    </form>
    <!-- Tampilkan data merek kulkas dan operasi pengelolaan merek kulkas -->
    <table>
        <tr>
            <th>Merek Kulkas</th>
            <th>Tipe</th>
            <th>Aksi</th>
        </tr>
        <!-- Tampilkan data merek kulkas dari database -->
        <?php
        // Menghubungkan ke database
        $conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");

        // Memeriksa koneksi database
        if (!$conn) {
            die("Koneksi database gagal: " . mysqli_connect_error());
        }

        // Mendapatkan data merek kulkas dari tabel merek
        $query = "SELECT * FROM merek";
        $result = mysqli_query($conn, $query);

        // Menampilkan data merek kulkas ke dalam tabel
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['merek_nama'] . "</td>";
            echo "<td>" . $row['tipe'] . "</td>";
            echo "<td>";
            echo "<form action='admin_process.php' method='post' onsubmit='return confirmDelete();'>";
            echo "<input type='hidden' name='action' value='deleteMerek'>";
            echo "<input type='hidden' name='merekId' value='" . $row['merek_id'] . "'>";
            echo "<button type='submit'>Hapus</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }

        mysqli_close($conn);
        ?>
    </table>
</div>


    <div class="admin-container">
        <h2>Jenis Kerusakan Kulkas</h2>
        <form action="admin_process.php" method="post">
            <input type="hidden" name="action" value="addKerusakan">
            <label for="kerusakan">Jenis Kerusakan:</label>
            <input type="text" id="kerusakan" name="kerusakan" required>
            <label for="harga">Harga Perbaikan:</label>
            <input type="text" id="harga" name="harga" required><br>
            <button type="submit">Tambah Jenis Kerusakan</button>
        </form>
        <!-- Tampilkan data jenis kerusakan kulkas dan operasi pengelolaan jenis kerusakan kulkas -->
        <table>
            <tr>
                <th>Jenis Kerusakan</th>
                <th>Harga Perbaikan</th>
                <th>Aksi</th>
            </tr>
            <!-- Tampilkan data jenis kerusakan kulkas dari database -->
            <?php
            // Menghubungkan ke database
            $conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");

            // Memeriksa koneksi database
            if (!$conn) {
                die("Koneksi database gagal: " . mysqli_connect_error());
            }

            // Mendapatkan data jenis kerusakan kulkas dari tabel kerusakan
            $query = "SELECT * FROM kerusakan";
            $result = mysqli_query($conn, $query);

            // Menampilkan data jenis kerusakan kulkas ke dalam tabel
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['kerusakan_nama'] . "</td>";
                echo "<td>" . $row['harga_perbaikan'] . "</td>";
                echo "<td>";
                echo "<form action='admin_process.php' method='post' onsubmit='return confirmDelete();'>";
                echo "<input type='hidden' name='action' value='deleteKerusakan'>";
                echo "<input type='hidden' name='kerusakanId' value='" . $row['kerusakan_id'] . "'>";
                echo "<button type='submit'>Hapus</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }

            mysqli_close($conn);
            ?>
        </table>
    </div>
    <footer>
    <p>&copy; 2023 Jasa Service Kulkas. All rights reserved.</p>
    </footer>
</body>
</html>
