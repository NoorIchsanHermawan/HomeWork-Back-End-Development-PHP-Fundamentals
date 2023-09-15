<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "homework_kel2");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT no_rekening, nama_nasabah, email FROM nasabah WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $no_rekening = $row['no_rekening'];
    $nama_nasabah = $row['nama_nasabah'];
    $email = $row['email'];
} else {
    die("Data nasabah tidak ditemukan.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/indexstyle2.css">
</head>
<body>
    <div class="container">
        <p>Selamat Datang,</p>
        <h1><?php echo $nama_nasabah; ?></h1>
        
        <!--<a href="cek_saldo.php" class="info-button">
            <i class="ri-account-pin-box-fill"></i>
            Info
        </a> -->
        <button class="btn" id="cekSaldoButton"><i class="fa fa-user"><span>Info</span></i></button>
        <!--<button class="btn"><i class="fa fa-bars"></i></button>
        <button class="btn"><i class="fa fa-trash"></i></button>
        <button class="btn"><i class="fa fa-close"></i></button>
        <button class="btn"><i class="fa fa-folder"></i></button>-->
        <a href="logout.php" class="logout">Logout</a>
    </div>
    <script>
        document.getElementById('cekSaldoButton').addEventListener('click', function () {
            // Redirect ke halaman cek_saldo.php
            window.location.href = 'cek_saldo.php';
    });
    </script>
</body>
</html>