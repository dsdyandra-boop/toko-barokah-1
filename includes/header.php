<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

// Ambil data session user/customer
$user_session = $_SESSION['user'] ?? $_SESSION['customer'] ?? null;
$is_admin = false;

// Cek apakah role user adalah admin
if (is_array($user_session) && isset($user_session['role']) && $user_session['role'] === 'admin') {$is_admin = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TokoHarian - Minimarket E-Commerce</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f6f9; color: #333; }
        
        .btn-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer; }
        .btn-lift:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 68, 148, 0.2); }
        .btn-lift:active { transform: translateY(0); }

        .btn-press { transition: transform 0.15s ease; cursor: pointer; }
        .btn-press:hover { transform: scale(1.04); }
        .btn-press:active { transform: scale(0.95); }

        @keyframes ripple-anim {
            to { transform: translate(-50%, -50%) scale(3); opacity: 0; }
        }
    </style>
</head>
<body>

<header style="background: #004494; color: white; padding: 15px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;">
    <div style="max-width: 1100px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <a href="index.php" class="btn-press" style="color: white; text-decoration: none; font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
            🛒 <span>TokoHarian</span>
        </a>

        <div style="display: flex; gap: 14px; align-items: center;">
            <a href="index.php" class="btn-press" style="color: white; text-decoration: none; font-size: 14px; font-weight: 500;">Katalog</a>
            
            <a href="keranjang.php" class="btn-lift" style="background: rgba(255,255,255,0.15); color: white; padding: 7px 12px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                🛒 Keranjang
                <span style="background: #ffc107; color: #1a1d2e; padding: 2px 7px; border-radius: 10px; font-size: 11px; font-weight: bold;">
                    <?php echo $cart_count; ?>
                </span>
            </a>

            <?php if ($user_session) : ?>
                <span style="font-size: 13px; font-weight: 600; color: #e2e8f0;">
                    👤 <?php echo htmlspecialchars(is_array($user_session) ? ($user_session['nama'] ?? 'Customer') :$user_session); ?>
                </span>
                
                <?php if ($is_admin) : ?>
                    <a href="admin/index.php" class="btn-press" style="color: #ffc107; text-decoration: none; font-size: 13px; font-weight: 600; background: rgba(255,255,255,0.1); padding: 5px 10px; border-radius: 6px;">⚙️ Admin Panel</a>
                <?php endif; ?>

                <a href="logout.php" class="btn-press" style="color: #f87171; text-decoration: none; font-size: 13px; font-weight: 600;" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
            <?php else : ?>
                <a href="login.php" class="btn-press" style="color: white; text-decoration: none; font-size: 13px; font-weight: 600;">Login</a>
                <a href="login.php?action=register" class="btn-lift" style="background: #ffffff; color: #004494; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-lift, .btn-press').forEach(button => {
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