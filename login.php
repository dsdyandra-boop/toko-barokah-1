<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

// Cek jika sudah login
if (isset($_SESSION['user']) && is_array($_SESSION['user']) && !empty($_SESSION['user'])) {
    if (($_SESSION['user']['role'] ?? '') === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error = '';
$sukses = '';
$panel_state = (isset($_GET['action']) && $_GET['action'] === 'register') ? 'right-panel-active' : '';

// LOGIKA REGISTER
if (isset($_POST['register'])) {
    $panel_state = 'right-panel-active';
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $no_hp    = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
    $alamat   = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = 'Konfirmasi sandi tidak cocok!';
    } else {
        $cek_email = mysqli_query($conn, "SELECT id_user FROM users WHERE email = '$email'");
        if ($cek_email && mysqli_num_rows($cek_email) > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            $hash_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (nama, email, password, no_hp, alamat, role) 
                      VALUES ('$nama', '$email', '$hash_password', '$no_hp', '$alamat', 'customer')";

            if (mysqli_query($conn, $query)) {
                $sukses = 'Pendaftaran berhasil! Silakan masuk.';
                $panel_state = '';
            } else {
                $error = 'Gagal mendaftar: ' . mysqli_error($conn);
            }
        }
    }
}

// LOGIKA LOGIN
if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $query  = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['user'] = [
                'id_user' => $user['id_user'],
                'nama'    => $user['nama'],
                'email'   => $user['email'],
                'no_hp'   => $user['no_hp'] ?? '',
                'alamat'  => $user['alamat'] ?? '',
                'role'    => $user['role'] ?? 'customer'
            ];
            
            header("Location: " . ($user['role'] === 'admin' ? "admin/index.php" : "index.php"));
            exit;
        } else {
            $error = 'Kata sandi salah!';
        }
    } else {
        $error = 'Email tidak terdaftar!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk & Daftar - TokoHarian</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        body {
            background: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        .back-home {
            position: absolute;
            top: 24px;
            left: 28px;
            text-decoration: none;
            color: #004494;
            font-weight: 600;
            font-size: 14px;
            background: #ffffff;
            padding: 9px 16px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .back-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 68, 148, 0.15);
        }
        
        .auth-container h1 { font-weight: bold; margin: 0; font-size: 28px; color: #1a1d2e; }
        .auth-container p { font-size: 14px; font-weight: 400; line-height: 20px; letter-spacing: 0.3px; margin: 18px 0 28px; }
        .auth-container a { color: #555; font-size: 13px; text-decoration: none; margin: 12px 0; }
        .auth-container a:hover { color: #004494; text-decoration: underline; }
        
        .auth-container button {
            border-radius: 20px;
            border: 1px solid #004494;
            background-color: #004494;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in, background-color 0.2s ease;
            cursor: pointer;
            margin-top: 10px;
        }
        .auth-container button:hover { background-color: #003370; }
        .auth-container button:active { transform: scale(0.95); }
        .auth-container button:focus { outline: none; }
        .auth-container button.ghost { background-color: transparent; border-color: #FFFFFF; }
        .auth-container button.ghost:hover { background-color: rgba(255,255,255,0.15); }
        
        .auth-container form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 45px;
            height: 100%;
            text-align: center;
        }
        .auth-container input, .auth-container textarea {
            background-color: #f1f5f9;
            border: 1px solid transparent;
            padding: 12px 15px;
            margin: 6px 0;
            width: 100%;
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }
        .auth-container input:focus, .auth-container textarea:focus {
            background-color: #ffffff;
            border-color: #004494;
        }
        
        .auth-container {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.08), 0 10px 10px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
            width: 850px;
            max-width: 100%;
            min-height: 540px;
        }
        
        .form-container { position: absolute; top: 0; height: 100%; transition: all 0.6s ease-in-out; }
        .sign-in-container { left: 0; width: 50%; z-index: 2; }
        .auth-container.right-panel-active .sign-in-container { transform: translateX(100%); }
        
        .sign-up-container { left: 0; width: 50%; opacity: 0; z-index: 1; }
        .auth-container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }
        
        @keyframes show {
            0%, 49.99% { opacity: 0; z-index: 1; }
            50%, 100% { opacity: 1; z-index: 5; }
        }
        
        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }
        .auth-container.right-panel-active .overlay-container { transform: translateX(-100%); }
        
        .overlay {
            background: linear-gradient(135deg, #0066cc 0%, #004494 100%);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .auth-container.right-panel-active .overlay { transform: translateX(50%); }
        
        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        
        .overlay-left { transform: translateX(-20%); }
        .auth-container.right-panel-active .overlay-left { transform: translateX(0); }
        .overlay-right { right: 0; transform: translateX(0); }
        .auth-container.right-panel-active .overlay-right { transform: translateX(20%); }
        
        .alert-box { padding: 10px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 10px; width: 100%; text-align: left; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .alert-success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

    <a href="index.php" class="back-home">← Kembali ke Toko</a>

    <div class="auth-container <?php echo $panel_state; ?>" id="auth-container">
        
        <!-- PANEL DAFTAR -->
        <div class="form-container sign-up-container">
            <form action="login.php" method="POST">
                <h1 style="margin-bottom: 10px;">Buat Akun</h1>
                
                <?php if ($error && $panel_state == 'right-panel-active') : ?>
                    <div class="alert-box alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div style="display: flex; gap: 8px; width: 100%;">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required />
                    <input type="text" name="no_hp" placeholder="No. WhatsApp" required />
                </div>
                <input type="email" name="email" placeholder="Email Aktif" required />
                <textarea name="alamat" rows="2" placeholder="Alamat Pengiriman (Opsional)" style="resize:none;"></textarea>
                <div style="display: flex; gap: 8px; width: 100%;">
                    <input type="password" name="password" placeholder="Sandi" required />
                    <input type="password" name="confirm_password" placeholder="Konfirmasi" required />
                </div>
                
                <button type="submit" name="register">Daftar Sekarang</button>
            </form>
        </div>

        <!-- PANEL LOGIN -->
        <div class="form-container sign-in-container">
            <form action="login.php" method="POST">
                <h1 style="margin-bottom: 15px;">Masuk</h1>
                
                <?php if ($error && $panel_state == '') : ?>
                    <div class="alert-box alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($sukses) : ?>
                    <div class="alert-box alert-success">✅ <?php echo htmlspecialchars($sukses); ?></div>
                <?php endif; ?>

                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Kata Sandi" required />
                <a href="#">Lupa kata sandi?</a>
                <button type="submit" name="login">Masuk</button>
            </form>
        </div>

        <!-- PANEL OVERLAY (ANIMASI SLIDER) -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Selamat Datang Kembali!</h1>
                    <p>Tetap terhubung, silakan login dengan informasi akun Toko Barokah 1Anda.</p>
                    <button type="button" class="ghost" id="signIn">Masuk</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Halo, Sahabat!</h1>
                    <p>Belum punya akun? Daftarkan diri Anda dan mulai penuhi kebutuhan harian dengan mudah.</p>
                    <button type="button" class="ghost" id="signUp">Daftar</button>
                </div>
            </div>
        </div>
        
    </div>

    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('auth-container');

        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
            window.history.replaceState({}, document.title, "login.php");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
            window.history.replaceState({}, document.title, "login.php");
        });
    </script>

</body>
</html>
<?php ob_end_flush(); ?>