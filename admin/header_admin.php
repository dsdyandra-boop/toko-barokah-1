<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - TokoHarian</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-nav { background: #004494; padding: 10px 0; }
        .admin-nav a { color: white; text-decoration: none; margin-right: 15px; font-weight: 500; }
        .admin-nav a:hover { color: #ffc107; }
        .table-admin { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; }
        .table-admin th, .table-admin td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .table-admin th { background-color: #0056b3; color: white; }
        .btn-action { padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 13px; color: white; font-weight: bold; }
        .btn-edit { background-color: #ffc107; color: #333; }
        .btn-delete { background-color: #dc3545; }
        .btn-detail { background-color: #17a2b8; color: white; }
        .btn-add { background-color: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; margin-bottom: 15px; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-proses { background: #cce5ff; color: #004085; }
        .badge-selesai { background: #d4edda; color: #155724; }
        .badge-batal { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">Toko<span>Harian</span> <small style="font-size: 14px; color: #ffc107;">[Admin Panel]</small></a>
            <div class="nav-actions">
                <a href="../index.php">🌐 Lihat Toko</a>
                <a href="../logout.php" class="login-btn" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
            </div>
        </div>
    </header>
    <nav class="admin-nav">
        <div class="container">
            <a href="index.php">Dashboard</a>
            <a href="produk.php">Kelola Produk</a>
            <a href="kategori.php">Kelola Kategori</a>
            <a href="pesanan.php">Kelola Pesanan</a>
        </div>
    </nav>
    <main class="container" style="margin-top: 20px; margin-bottom: 40px;">