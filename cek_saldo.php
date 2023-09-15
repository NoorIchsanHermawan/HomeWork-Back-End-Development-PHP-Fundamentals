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
$sql = "SELECT nama_nasabah, no_rekening  FROM nasabah WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nama_nasabah = $row['nama_nasabah'];
    $no_rekening = $row['no_rekening'];
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
    <title>Cek Saldo</title>
    <link rel="stylesheet" href="css/saldostyle.css">
</head>
<body>
    <div class="container">
        <h3>Informasi</h3>
        <p><?php echo $nama_nasabah; ?></p>
        <p>No Rekening : <?php echo $no_rekening; ?></p>
        <button id="cekSaldoBtn">Cek Saldo</button>
        <p id="saldoResult"></p>
        <a href="index.php" class="back">Kembali ke Dashboard</a>
    </div>

    <script>
       document.getElementById("cekSaldoBtn").addEventListener("click", function () {
            fetch("API/api_balance.php")
                .then(response => {
                    if (!response.ok) {
                        throw new Error("HTTP error, status = " + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    const saldoFormatted = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }).format(data.saldo);
                    document.getElementById("saldoResult").textContent = "Saldo Anda: " + saldoFormatted;
                })
                .catch(error => {
                    console.error("Error:", error);
                    document.getElementById("saldoResult").textContent = "Gagal mengambil saldo.";
                });
        });

    </script>
</body>
</html>
