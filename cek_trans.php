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
$sql = "SELECT nama_nasabah, no_rekening FROM nasabah WHERE id = $user_id";
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
        <h3>Transaksi Terakhir</h3>
        <p><?php echo $nama_nasabah; ?></p>
        <p>No Rekening : <?php echo $no_rekening; ?></p>
        <table id="transTable">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody id="transResult">
            </tbody>
        </table>
        <a href="cek_saldo.php" class="back">Kembali</a>
    </div>
    <style>
        #transTable {
            width: 100%;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("API/api_transactions.php", {
                method: 'GET'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("HTTP error, status = " + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    const transResult = document.getElementById("transResult");

                    transResult.innerHTML = "";
                    data.forEach(transaction => {
                        const row = document.createElement("tr");
                        const dateCell = document.createElement("td");
                        const typeCell = document.createElement("td");

                        dateCell.textContent = transaction.tgl_trans;
                        typeCell.textContent = transaction.ket;

                        row.appendChild(dateCell);
                        row.appendChild(typeCell);
                        transResult.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error("Error:", error);
                    document.getElementById("transResult").textContent = "Gagal mengambil transaksi.";
                });
        });
    </script>
</body>
</html>
