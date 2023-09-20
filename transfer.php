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

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Uang</title>
    <link rel="stylesheet" href="css/saldostyle.css">
</head>
<body>
    <div class="container">
        <h3>Transfer</h3>
        <p><?php echo $nama_nasabah; ?></p>
        <p>No Rekening : <?php echo $no_rekening; ?></p>
        <p id="saldoResult"></p><button id="refreshSaldoBtn">Refresh Saldo</button>
        <form id="transferForm">
            <label for="rek_penerima">Nomor Rekening Penerima:</label>
            <input type="text" id="rek_penerima" name="rek_penerima" required><br>

            <label for="jumlah_transfer">Jumlah Transfer (IDR):</label>
            <input type="number" id="jumlah_transfer" name="jumlah_transfer" required><br>

            <label for="keterangan">Keterangan Transfer:</label>
            <input type="text" id="ket" name="ket" required><br>

            <button type="submit">Kirim Transfer</button><label id="transferResult"></label>
        </form>
        <a href="index.php" class="back">Kembali ke Dashboard</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function loadSaldo() {
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

        function loadSaldo() {
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
        }

        document.getElementById("refreshSaldoBtn").addEventListener("click", function () {
            loadSaldo();
        });

        document.getElementById("transferForm").addEventListener("submit", function (e) {
        e.preventDefault();

            const rekPenerima = document.getElementById("rek_penerima").value;
            const jumlahTransfer = parseFloat(document.getElementById("jumlah_transfer").value);
            const keterangan = document.getElementById("ket").value; // Menambahkan ini untuk mengambil keterangan

            fetch("API/api_transfer.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `rek_penerima=${rekPenerima}&jumlah_transfer=${jumlahTransfer}&ket=${keterangan}`
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("HTTP error, status = " + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById("transferResult").textContent = data.message;
                    document.getElementById("rek_penerima").value = "";
                    document.getElementById("jumlah_transfer").value = "";
                    document.getElementById("ket").value = "";
                })
                .catch(error => {
                    console.error("Error:", error);
                    document.getElementById("transferResult").textContent = "Gagal melakukan transfer.";
                });
        });
    </script>
</body>
</html>