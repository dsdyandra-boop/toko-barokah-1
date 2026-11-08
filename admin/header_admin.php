<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek Autentikasi & Role Admin
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CDN Library Barcode SVG & Camera Scanner (Wajib Terhubung Internet) -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: #f4f6fb;
            color: #1a1d2e;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: #004494;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .admin-brand {
            font-size: 20px;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }
        .admin-brand span { color: #ff9f00; }

        nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        nav a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }

        nav a:hover, nav a.active {
            color: #ffffff;
            font-weight: 600;
        }

        .btn-logout {
            background: #dc3545;
            color: white;
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        main {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 30px auto;
            padding: 0 20px;
        }

        .table-admin {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .table-admin th, .table-admin td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        .table-admin th {
            background-color: #0056b3;
            color: white;
        }

        .btn-action {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            color: white;
            display: inline-block;
        }

        .btn-edit { background-color: #ffc107; color: #333; }
        .btn-delete { background-color: #dc3545; }
        .btn-detail { background-color: #17a2b8; color: white; }
        .btn-add {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-proses { background: #cce5ff; color: #004085; }
        .badge-selesai { background: #d4edda; color: #155724; }
        .badge-batal { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<header>
    <a href="index.php" class="admin-brand">🛍️ Toko<span>Harian</span> <small style="font-size:12px; opacity:0.8; font-weight:normal;">[Admin Panel]</small></a>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="produk.php">Produk & Barcode</a>
        <a href="kategori.php">Kategori</a>
        <a href="pesanan.php">Pesanan</a>
        <a href="../index.php" target="_blank">🌐 Lihat Toko</a>
        <a href="../logout.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    </nav>
</header>

<main>