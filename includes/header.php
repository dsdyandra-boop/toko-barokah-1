<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

// Ambil data session user/customer
$user_session = $_SESSION['user'] ?? $_SESSION['customer'] ?? null;
$is_admin = false;

if (is_array($user_session) && isset($user_session['role']) && $user_session['role'] === 'admin') {
    $is_admin = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Barokah 1 - Minimarket E-Commerce</title>
    <style>
        :root {
            --bg-offwhite: #f8faf9;
            --mint-soft: #eaf6f0;
            --mint-mid: #d3ede1;
            --sage-border: #dce8e2;
            --sage-hover: #a7cbb9;
            --sage-text: #536b60;
            --forest-primary: #156145;
            --forest-dark: #104934;
            --ink-dark: #132a21;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        html { scroll-behavior: smooth; }

        body {
            background-color: var(--bg-offwhite);
            background-image: linear-gradient(180deg, #edf7f2 0%, #f8faf9 380px, #f8faf9 100%);
            background-repeat: no-repeat;
            color: var(--ink-dark);
            min-height: 100vh;
            line-height: 1.5;
        }

        .site-header {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--sage-border);
            padding: 14px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 12px rgba(19, 42, 33, 0.03);
        }

        .nav-container {
            max-width: 1120px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .nav-brand {
            color: var(--ink-dark);
            text-decoration: none;
            font-size: 20px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.3px;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--mint-soft);
            border: 1px solid var(--mint-mid);
            color: var(--forest-primary);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .nav-menu {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-link {
            color: var(--sage-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 13px;
            border-radius: 10px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav-link:hover {
            background: var(--mint-soft);
            color: var(--forest-primary);
        }

        .nav-cart {
            background: var(--mint-soft);
            color: var(--forest-primary);
            border: 1px solid var(--mint-mid);
            padding: 7px 14px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.2s ease;
        }

        .nav-cart:hover {
            background: #dff2e9;
            border-color: var(--sage-hover);
        }

        .cart-badge {
            background: var(--forest-primary);
            color: var(--white);
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .nav-user-pill {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-dark);
            background: #f1f6f4;
            border: 1px solid var(--sage-border);
            padding: 6px 12px;
            border-radius: 999px;
        }

        .nav-admin-link {
            color: var(--forest-primary);
            background: var(--mint-soft);
            border: 1px solid var(--mint-mid);
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            padding: 7px 12px;
            border-radius: 10px;
        }

        .nav-logout {
            color: #b91c1c;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 7px 11px;
            border-radius: 10px;
            transition: background 0.2s ease;
        }

        .nav-logout:hover { background: #fef2f2; }

        .nav-btn-register {
            background: var(--forest-primary);
            color: var(--white);
            padding: 8px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(21, 97, 69, 0.18);
        }

        .nav-btn-register:hover { background: var(--forest-dark); }

        .btn-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            cursor: pointer;
        }
        .btn-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(21, 97, 69, 0.18);
        }
        .btn-lift:active { transform: translateY(0); }

        .btn-press {
            transition: transform 0.15s ease, background 0.2s ease, color 0.2s ease;
            cursor: pointer;
        }
        .btn-press:hover { transform: scale(1.03); }
        .btn-press:active { transform: scale(0.96); }

        .btn-shine {
            position: relative;
            overflow: hidden;
        }
        .btn-shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -70%;
            width: 30%;
            height: 200%;
            background: rgba(255, 255, 255, 0.35);
            transform: rotate(25deg);
            transition: none;
        }
        .btn-shine:hover::after {
            left: 140%;
            transition: all 0.65s ease-in-out;
        }

        @keyframes ripple-anim {
            to { transform: translate(-50%, -50%) scale(3); opacity: 0; }
        }

        @media (max-width: 640px) {
            .site-header { padding: 12px 16px; }
            .nav-brand { font-size: 18px; }
            .nav-menu { gap: 6px; }
            .nav-link { padding: 6px 10px; font-size: 13px; }
        }
    </style>
</head>
<body>

<header class="site-header">
    <div class="nav-container">
        <a href="index.php" class="nav-brand btn-press">
            <span class="brand-icon">🛒</span>
            <span>Toko Barokah 1</span>
        </a>

        <nav class="nav-menu">
            <a href="index.php" class="nav-link btn-press">Katalog</a>
            
            <a href="keranjang.php" class="nav-cart btn-lift">
                <span>Keranjang</span>
                <span class="cart-badge"><?php echo $cart_count; ?></span>
            </a>

            <?php if ($user_session) : ?>
                <a href="pesanan_saya.php" class="nav-link btn-press">Pesanan Saya</a>

                <span class="nav-user-pill">
                    👤 <?php echo htmlspecialchars(is_array($user_session) ? ($user_session['nama'] ?? 'Customer') : $user_session); ?>
                </span>
                
                <?php if ($is_admin) : ?>
                    <a href="admin/index.php" class="nav-admin-link btn-press">⚙️ Admin Panel</a>
                <?php endif; ?>

                <a href="logout.php" class="nav-logout btn-press" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
            <?php else : ?>
                <a href="login.php" class="nav-link btn-press">Login</a>
                <a href="login.php?action=register" class="nav-btn-register btn-lift">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-lift, .btn-press, .btn-shine').forEach(button => {
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