<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hitung total item di keranjang
$total_cart_items = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $total_cart_items = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TokoHarian - Belanja Kebutuhan Sehari-hari</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">Toko<span>Harian</span></a>
            
            <form action="index.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Cari produk..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit">Cari</button>
            </form>

            <div class="nav-actions">
                <a href="cart.php" class="cart-btn">
                    🛒 Keranjang <?php if ($total_cart_items > 0) : ?><span style="background: #ffc107; color: #000; padding: 2px 7px; border-radius: 50%; font-size: 12px; font-weight: bold;"><?php echo $total_cart_items; ?></span><?php endif; ?>
                </a>
                
                <?php if (isset($_SESSION['login'])) : ?>
                    <a href="pesanan_saya.php" style="color: #ffffff; text-decoration: none; font-weight: bold; margin-left: 10px;">📦 Pesanan Saya</a>
                    <span style="color: #ffc107; font-weight: bold; margin-left: 10px;">
                        Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?>
                    </span>
                    <?php if ($_SESSION['role'] === 'admin') : ?>
                        <a href="admin/index.php" style="color: #ffc107;">[Dashboard Admin]</a>
                    <?php endif; ?>
                    <a href="logout.php" class="login-btn" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
                <?php else : ?>
                    <a href="login.php" class="login-btn">Masuk / Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="container">