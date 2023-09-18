<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

if (!isset($_SESSION['no_rekening'])) {
    header("HTTP/1.1 400 Bad Request");
    echo "No rekening tidak tersedia dalam sesi.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Metode HTTP ini tidak diizinkan pada endpoint ini.";
    exit();
}

$conn = new mysqli("localhost", "root", "", "homework_kel2");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$no_rekening = $_SESSION['no_rekening'];

$sql = "SELECT tgl_trans, ket FROM transaksi WHERE no_rekening = '$no_rekening' ORDER BY tgl_trans DESC LIMIT 5";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $transactions = array();
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    echo json_encode($transactions);
} else {
    header("HTTP/1.1 404 Not Found");
    echo "Transaksi tidak ditemukan.";
}

$conn->close();
?>