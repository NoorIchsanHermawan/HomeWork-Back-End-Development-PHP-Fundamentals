<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conn = new mysqli("localhost", "root", "", "homework_kel2"); 
    if ($conn->connect_error) {
        die("Koneksi database gagal: " . $conn->connect_error);
    }

    $sql = "SELECT id, password FROM nasabah WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header('Location: index.php');
            exit;
        } else {
            echo "Login gagal. Password salah."; 
        }
    } else {
        echo "Login gagal. Email tidak ditemukan."; 
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            background: #e9ebee;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
        }

        .container {
            max-width: 400px;
            padding: 50px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .container h1 {
            text-align: center;
        }

        .container label {
            display: block;
            margin-bottom: 10px;
            text-align: left; 
        }

        .container input[type="email"],
        .container input[type="password"] {
            width: 90%;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 10px;
            border: none;
            background-color: #eeeeef;
        }

        .container button {
            width: 100%;
            margin: 30px 0;
            padding: 10px;
            border: none;
            background-color: DodgerBlue;
            color: #fff;
            font-size: 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .container button:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="POST" action="">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            
            <label for="password">Password</label>
            <input type="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>