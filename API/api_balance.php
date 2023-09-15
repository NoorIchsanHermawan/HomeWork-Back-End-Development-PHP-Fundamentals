<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

$conn = new mysqli("localhost", "root", "", "homework_kel2");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT saldo FROM nasabah WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $saldo = $row['saldo'];
    echo json_encode(['saldo' => $saldo]);
} else {
    header("HTTP/1.1 404 Not Found");
    echo "Data nasabah tidak ditemukan.";
}

$conn->close();
?>
