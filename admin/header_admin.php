<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek Role Admin secara Fleksibel
$is_admin = false;

if (isset($_SESSION['user'])) {
    if (is_array($_SESSION['user'])) {
        $role = $_SESSION['user']['role'] ?? $_SESSION['user']['level'] ?? '';
        if ($role === 'admin') $is_admin = true;
    } else if (is_string($_SESSION['user']) && strtolower($_SESSION['user']) === 'admin') {
        $is_admin = true;
    }
}

if (isset($_SESSION['admin']) || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') || (isset($_SESSION['level']) && $_SESSION['level'] === 'admin')) {
    $is_admin = true;
}

// Jika bukan admin, tendang ke login.php
if (!$is_admin) {
    header("Location: ../login.php?error=" . urlencode("Akses ditolak! Halaman ini khusus Admin."));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - TokoHarian</title>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f6f9; color: #333; display: flex; min-height: 100vh; }
        
        .sidebar { width: 240px; background: #1a1d2e; color: white; padding: 20px 0; flex-shrink: 0; }
        .sidebar h2 { padding: 0 20px 20px; font-size: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar ul { list-style: none; margin-top: 20px; }
        .sidebar ul li a { display: block; padding: 12px 20px; color: #a0aec0; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #2d3748; color: white; border-left: 4px solid #004494; }
        
        .sidebar ul li a.logout-link { color: #f87171; margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px; }
        .sidebar ul li a.logout-link:hover { background: #7f1d1d; color: white; border-left: 4px solid #dc3545; }

        .content { flex: 1; padding: 30px; overflow-y: auto; }

        .table-admin { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .table-admin th, .table-admin td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #edf2f7; font-size: 14px; }
        .table-admin th { background: #f8fafc; font-weight: 600; color: #4a5568; }

        .btn-action { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; display: inline-block; }
        .btn-edit { background: #ffc107; color: #1a1d2e; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-detail { background: #17a2b8; color: white; }
        .btn-add { background: #28a745; color: white; padding: 9px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; }

        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: capitalize; }
        .badge-pending { background: #ffeba8; color: #856404; }
        .badge-proses { background: #b8daff; color: #004085; }
        .badge-selesai { background: #c3e6cb; color: #155724; }
        .badge-batal { background: #f5c6cb; color: #721c24; }

        button, .btn-action, .btn-add { transition: transform 0.15s ease; cursor: pointer; }
        button:hover, .btn-action:hover, .btn-add:hover { transform: translateY(-2px); }

        @keyframes ripple-anim {
            to { transform: translate(-50%, -50%) scale(3); opacity: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Toko Barokah 1Admin</h2>
    <ul>
        <li><a href="index.php">📊 Dashboard</a></li>
        <li><a href="produk.php">📦 Kelola Produk</a></li>
        <li><a href="kategori.php">🏷️ Kelola Kategori</a></li>
        <li><a href="pesanan.php">🧾 Kelola Pesanan</a></li>
        <li><a href="../index.php" target="_blank">🌐 Lihat Toko</a></li>
        <li>
            <a href="logout.php" class="logout-link" onclick="return confirm('Apakah Anda yakin ingin keluar dari Admin Panel?')">
                🚪 Logout Admin
            </a>
        </li>
    </ul>
</div>

<div class="content">

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('button, .btn-action, .btn-add, .logout-link').forEach(button => {
        button.addEventListener('click', function (e) {
            let rect = this.getBoundingClientRect();
            let x = e.clientX - rect.left;
            let y = e.clientY - rect.top;

            let ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.width = '100px';
            ripple.style.height = '100px';
            ripple.style.background = 'rgba(255, 255, 255, 0.35)';
            ripple.style.borderRadius = '50%';
            ripple.style.transform = 'translate(-50%, -50%) scale(0)';
            ripple.style.animation = 'ripple-anim 0.45s linear';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.pointerEvents = 'none';

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 450);
        });
    });
});
</script>