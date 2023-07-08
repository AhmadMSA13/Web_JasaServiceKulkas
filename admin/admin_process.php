<?php
session_start();

// Memeriksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit();
}

// Menghubungkan ke database
$conn = mysqli_connect("localhost", "root", "", "jasa_kulkas");

// Memeriksa koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Memeriksa tindakan yang diambil dari form
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Tindakan: Menambahkan merek kulkas baru
    if ($action === 'addMerek') {
        $merek = $_POST['merek'];
        $tipe = $_POST['tipe'];

        // Menyiapkan pernyataan SQL
        $stmt = mysqli_prepare($conn, "INSERT INTO merek (merek_nama, tipe) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $merek, $tipe);

        // Menjalankan pernyataan SQL
        if (mysqli_stmt_execute($stmt)) {
            // Redirect kembali ke halaman admin setelah menambah merek kulkas
            header("Location: admin.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }

    // Tindakan: Menambahkan jenis kerusakan kulkas baru
    elseif ($action === 'addKerusakan') {
        $kerusakan = $_POST['kerusakan'];
        $harga = $_POST['harga'];

        // Menyiapkan pernyataan SQL
        $stmt = mysqli_prepare($conn, "INSERT INTO kerusakan (kerusakan_nama, harga_perbaikan) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "si", $kerusakan, $harga);

        // Menjalankan pernyataan SQL
        if (mysqli_stmt_execute($stmt)) {
            // Redirect kembali ke halaman admin setelah menambah jenis kerusakan kulkas
            header("Location: admin.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }

    // Tindakan: Menghapus merek kulkas
    elseif ($action === 'deleteMerek') {
        $merekId = $_POST['merekId'];

        // Menyiapkan pernyataan SQL
        $stmt = mysqli_prepare($conn, "DELETE FROM merek WHERE merek_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $merekId);

        // Menjalankan pernyataan SQL
        if (mysqli_stmt_execute($stmt)) {
            // Redirect kembali ke halaman admin setelah menghapus merek kulkas
            header("Location: admin.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }

    // Tindakan: Menghapus jenis kerusakan kulkas
    elseif ($action === 'deleteKerusakan') {
        $kerusakanId = $_POST['kerusakanId'];

        // Menyiapkan pernyataan SQL
        $stmt = mysqli_prepare($conn, "DELETE FROM kerusakan WHERE kerusakan_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $kerusakanId);

        // Menjalankan pernyataan SQL
        if (mysqli_stmt_execute($stmt)) {
            // Redirect kembali ke halaman admin setelah menghapus jenis kerusakan kulkas
            header("Location: admin.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>
