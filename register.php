<?php
session_start();
require_once 'config/database.php';

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error = '';

if (isset($_POST['register'])) {
    $nama             = mysqli_real_escape_string($conn, trim($_POST['nama']));$email            = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password         =$_POST['password'];
    $confirm_password =$_POST['confirm_password'];

    if ($password !== $confirm_password) {$error = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek apakah email sudah terdaftar
        $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {$error = 'Email sudah terdaftar, silakan gunakan email lain!';
        } else {
            // Hash password dan simpan user baru dengan role 'customer'
            $password_hash = password_hash($password, PASSWORD_DEFAULT);$query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password_hash', 'customer')";
            
            if (mysqli_query($conn,$query)) {
                header("Location: login.php?pesan=Pendaftaran berhasil! Silakan masuk menggunakan akun baru kamu.");
                exit;
            } else {
                $error = 'Gagal mendaftar: ' . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - TokoHarian</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: #f0f3fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-wrapper {
            background: #ffffff;
            width: 100%;
            max-width: 960px;
            border-radius: 24px;
            display: flex;
            padding: 16px;
            box-shadow: 0 20px 40px rgba(0, 68, 148, 0.08);
            gap: 20px;
        }

        /* BANNER KIRI */
        .login-banner {
            flex: 1;
            background: linear-gradient(135deg, #4f6ef7 0%, #3b52d4 100%);
            border-radius: 20px;
            padding: 40px 30px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            min-height: 520px;
        }

        .banner-text h2 {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 12px;
        }

        .banner-text p {
            font-size: 14px;
            opacity: 0.85;
            line-height: 1.5;
        }

        .banner-img {
            width: 100%;
            max-width: 280px;
            margin: 20px auto