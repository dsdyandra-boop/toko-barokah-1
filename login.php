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

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['login']   = true;
            $_SESSION['user_id'] = $row['id_user'];
            $_SESSION['nama']    = $row['nama'];
            $_SESSION['email']   = $row['email'];
            $_SESSION['role']    = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }
    $error = 'Email atau password yang kamu masukkan salah!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - TokoHarian</title>
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
            min-height: 480px;
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
            margin: 20px auto 0;
            display: block;
            object-fit: contain;
            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.15));
        }

        /* FORM KANAN */
        .login-form-container {
            flex: 1.1;
            padding: 30px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: #004494;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .logo span { color: #ff9f00; }

        .form-header {
            margin-bottom: 24px;
        }

        .form-header h1 {
            font-size: 26px;
            font-weight: 700;
            color: #1a1d2e;
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 14px;
            color: #8c94a8;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            border: 1px solid #fca5a5;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-control {
            width: 100%;
            padding: 14px 16px;
            background-color: #f4f6fb;
            border: 1.5px solid transparent;
            border-radius: 12px;
            font-size: 14px;
            color: #1a1d2e;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-control::placeholder {
            color: #a0a7b8;
        }

        .input-control:focus {
            background-color: #ffffff;
            border-color: #4f6ef7;
            box-shadow: 0 0 0 4px rgba(79, 110, 247, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            cursor: pointer;
            color: #8c94a8;
            font-size: 16px;
        }

        .form-options {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        .forgot-link {
            font-size: 13px;
            color: #8c94a8;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover { color: #4f6ef7; }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: #4f6ef7;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 8px 16px rgba(79, 110, 247, 0.25);
        }

        .btn-submit:hover {
            background-color: #3b52d4;
            transform: translateY(-1px);
        }

        .register-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 14px;
            color: #8c94a8;
        }

        .register-footer a {
            color: #4f6ef7;
            font-weight: 600;
            text-decoration: none;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }

        /* RESPONSIVE UNTUK HP */
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                padding: 12px;
            }

            .login-banner {
                min-height: auto;
                padding: 30px 20px;
            }

            .banner-img {
                max-width: 180px;
            }

            .login-form-container {
                padding: 20px 10px;
            }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- Banner Sisi Kiri -->
    <div class="login-banner">
        <div class="banner-text">
            <h2>Simplify shopping with TokoHarian</h2>
            <p>Kelola dan penuhi kebutuhan harian keluarga kamu dengan cepat, hemat, dan praktis.</p>
        </div>
        <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-shopping-store-4438848-3718501.png" alt="TokoHarian Shopping Illustration" class="banner-img">
    </div>

    <!-- Form Sisi Kanan -->
    <div class="login-form-container">
        <a href="index.php" class="logo">
            🛍️ Toko<span>Harian</span>
        </a>

        <div class="form-header">
            <h1>Welcome Back</h1>
            <p>Silakan masuk ke akun kamu</p>
        </div>

        <?php if ($error) : ?>
            <div class="alert-error">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="email" name="email" class="input-control" placeholder="Email" required autocomplete="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="password" name="password" id="passwordInput" class="input-control" placeholder="Password" required>
                    <button type="button" class="toggle-password" id="toggleBtn" onclick="togglePasswordVisibility()">👁️</button>
                </div>
            </div>

            <div class="form-options">
                <a href="#" class="forgot-link" onclick="alert('Silakan hubungi Admin jika kamu lupa password.')">Lupa Password?</a>
            </div>

            <button type="submit" name="login" class="btn-submit">Login</button>
        </form>

        <div class="register-footer">
            Belum punya akun? <a href="register.php">Daftar Sekarang</a>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const pwdInput = document.getElementById('passwordInput');
    const toggleBtn = document.getElementById('toggleBtn');
    
    if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        toggleBtn.textContent = '🙈';
    } else {
        pwdInput.type = 'password';
        toggleBtn.textContent = '👁️';
    }
}
</script>

</body>
</html>