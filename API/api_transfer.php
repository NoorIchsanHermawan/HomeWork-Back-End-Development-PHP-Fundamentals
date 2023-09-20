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
$sql = "SELECT id, no_rekening, nama_nasabah, saldo FROM nasabah WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_pengirim = $row['id'];
    $rek_pengirim = $row['no_rekening'];
    $nama_pengirim = $row['nama_nasabah'];
    $saldo_pengirim = $row['saldo'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $rek_penerima = $_POST['rek_penerima'];
        $jumlah_transfer = floatval($_POST['jumlah_transfer']);
        $keterangan = $_POST['ket'];

        $sql = "SELECT id, saldo FROM nasabah WHERE no_rekening = '$rek_penerima'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_penerima = $row['id'];
            $saldo_penerima = $row['saldo'];

            if ($saldo_pengirim >= $jumlah_transfer) {
                $conn->begin_transaction();

                $saldo_pengirim -= $jumlah_transfer;
                $sql = "UPDATE nasabah SET saldo = $saldo_pengirim WHERE id = $id_pengirim";
                $conn->query($sql);

                $saldo_penerima += $jumlah_transfer;
                $sql = "UPDATE nasabah SET saldo = $saldo_penerima WHERE id = $id_penerima";
                $conn->query($sql);

                $current_date = date("Y-m-d");
                $sql = "INSERT INTO transaksi (no_rekening, tgl_trans, ket) VALUES ('$rek_pengirim', '$current_date', '$keterangan')";
                $conn->query($sql);

                $conn->commit();

                echo json_encode(['message' => 'Transfer berhasil']);
            } else {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Saldo tidak mencukupi']);
            }
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(['error' => 'Penerima tidak ditemukan']);
        }
    } else {
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(['error' => 'Metode tidak diizinkan']);
    }
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['error' => 'Data nasabah tidak ditemukan']);
}

$conn->close();
?>