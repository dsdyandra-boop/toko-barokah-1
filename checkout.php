<?php
ob_start();
session_start();
require_once 'config/database.php';

// Proteksi: Jika keranjang kosong, kembalikan ke index
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Ambil Detail Barang & Hitung Total
$ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
$query = "SELECT * FROM products WHERE id_produk IN ($ids)";
$result = mysqli_query($conn, $query);

$cart_items = [];
$total_bayar = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $qty = $_SESSION['cart'][$row['id_produk']];
    $subtotal = $row['harga'] * $qty;
    $total_bayar += $subtotal;

    $cart_items[] = [
        'id_produk' => $row['id_produk'],
        'nama_produk' => $row['nama_produk'],
        'harga' => $row['harga'],
        'qty' => $qty,
        'subtotal' => $subtotal
    ];
}

$error = '';

// PROCESS CHECKOUT FORM
if (isset($_POST['proses_checkout'])) {
    $nama_pemesan      = mysqli_real_escape_string($conn, trim($_POST['nama_pemesan']));
    $no_hp             = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
    $alamat            = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $metode_pembayaran = mysqli_real_escape_string($conn, trim($_POST['metode_pembayaran']));

    if (empty($nama_pemesan) || empty($no_hp)) {
        $error = 'Nama pemesan dan No WhatsApp/HP wajib diisi!';
    } else {
        $bukti_pembayaran = '';

        // Handle File Bukti Bayar QRIS
        if ($metode_pembayaran === 'qris' && isset($_FILES['bukti_pembayaran']['name']) && $_FILES['bukti_pembayaran']['name'] != "") {
            $folder_tujuan = 'assets/img/bukti/';
            if (!file_exists($folder_tujuan)) {
                mkdir($folder_tujuan, 0777, true);
            }

            $nama_file = time() . '_' . $_FILES['bukti_pembayaran']['name'];
            $tmp_name  = $_FILES['bukti_pembayaran']['tmp_name'];
            move_uploaded_file($tmp_name, $folder_tujuan . $nama_file);
            $bukti_pembayaran = $nama_file;
        }

        // Insert Order ke DB
        $query_order = "INSERT INTO orders (nama_pemesan, no_hp, alamat, total_bayar, metode_pembayaran, bukti_pembayaran, status) 
                        VALUES ('$nama_pemesan', '$no_hp', '$alamat', '$total_bayar', '$metode_pembayaran', '$bukti_pembayaran', 'pending')";

        if (mysqli_query($conn, $query_order)) {
            $id_order = mysqli_insert_id($conn);

            // Kurangi Stok Produk
            foreach ($cart_items as $item) {
                $id_p = $item['id_produk'];
                $qty  = $item['qty'];
                mysqli_query($conn, "UPDATE products SET stok = stok - $qty WHERE id_produk = '$id_p'");
            }

            // Kosongkan Keranjang
            unset($_SESSION['cart']);

            header("Location: checkout.php?sukses=1&order_id=" . $id_order);
            exit;
        } else {
            $error = 'Gagal memproses pesanan: ' . mysqli_error($conn);
        }
    }
}

include 'includes/header.php';
?>

<div style="max-width: 950px; margin: 0 auto; padding: 25px 20px;">
    <?php if (isset($_GET['sukses'])) : ?>
        <!-- HLAMAN SUKSES TRANSAKSI -->
        <div style="background: white; border-radius: 16px; padding: 40px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
            <div style="font-size: 60px; margin-bottom: 10px;">🎉</div>
            <h2 style="color: #0f172a; margin-bottom: 8px;">Pesanan Berhasil Dibuat!</h2>
            <p style="color: #64748b; font-size: 15px; margin-bottom: 20px;">Nomor Pesanan Kamu: <strong style="color: #004494;">#ORD-<?php echo (int)$_GET['order_id']; ?></strong></p>
            <p style="color: #475569; font-size: 14px; max-width: 500px; margin: 0 auto 25px auto;">Tim Toko Barokah 1akan segera memproses pesanan kamu. Terima kasih sudah berbelanja!</p>
            <a href="index.php" style="padding: 12px 28px; background: #004494; color: white; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">Kembali ke Katalog</a>
        </div>
    <?php else : ?>
        <h2 style="font-size: 24px; color: #0f172a; margin-bottom: 5px;">💳 Checkout & Pembayaran</h2>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 25px;">Lengkapi data diri dan pilih metode pembayaran favorit kamu.</p>

        <?php if ($error) : ?>
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="checkout.php" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; align-items: start;">
            <!-- KOLOM KIRI: FORM PENGIRIMAN -->
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <h3 style="font-size: 16px; color: #0f172a; margin-bottom: 15px;">📋 Informasi Pemesan</h3>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 5px;">Nama Lengkap *</label>
                    <input type="text" name="nama_pemesan" required placeholder="Contoh: Arsa Javier" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 5px;">No. WhatsApp / HP *</label>
                    <input type="text" name="no_hp" required placeholder="Contoh: 08123456789" style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 5px;">Alamat Pengiriman (Opsional / Lengkap)</label>
                    <textarea name="alamat" rows="3" placeholder="Alamat lengkap tujuan pengiriman..." style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; font-family: sans-serif;"></textarea>
                </div>

                <h3 style="font-size: 16px; color: #0f172a; margin-bottom: 15px;">💳 Metode Pembayaran</h3>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <label style="flex: 1; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 12px; cursor: pointer; text-align: center;" id="label_qris">
                        <input type="radio" name="metode_pembayaran" value="qris" checked onclick="togglePayment('qris')">
                        <span style="font-weight: 700; font-size: 14px; margin-left: 5px;">📱 QRIS Digital</span>
                    </label>

                    <label style="flex: 1; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 12px; cursor: pointer; text-align: center;" id="label_cod">
                        <input type="radio" name="metode_pembayaran" value="cod" onclick="togglePayment('cod')">
                        <span style="font-weight: 700; font-size: 14px; margin-left: 5px;">💵 Cash / COD</span>
                    </label>
                </div>

                <!-- BOX QRIS PAYMENT -->
                <div id="qris_box" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px dashed #004494; text-align: center;">
                    <p style="font-size: 12px; color: #64748b; margin-bottom: 8px;">Scan Kode QRIS di bawah ini dengan Gopay/OVO/Dana/BCA/ShopeePay:</p>
                    <div style="background: white; padding: 10px; display: inline-block; border-radius: 8px; border: 1px solid #eef2f6;">
                        <!-- Menggunakan QR Code generator otomatis -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=TOKOHARIAN-PAYMENT-QRIS" alt="QRIS TokoHarian" width="160" height="160">
                    </div>
                    <p style="font-size: 12px; font-weight: bold; color: #004494; margin-top: 5px;">Toko Barokah 1Official QRIS</p>

                    <div style="margin-top: 15px; text-align: left;">
                        <label style="font-size: 12px; font-weight: 700; color: #334155;">Upload Bukti Transfer / Capture QRIS:</label>
                        <input type="file" name="bukti_pembayaran" accept="image/*" style="width: 100%; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px; font-size: 12px;">
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: RINGKASAN ORDER -->
            <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <h3 style="font-size: 16px; color: #0f172a; margin-bottom: 15px;">🛍️ Ringkasan Pesanan</h3>

                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 15px;">
                    <?php foreach ($cart_items as $ci) : ?>
                        <div style="display: flex; justify-content: space-between; font-size: 13px;">
                            <span><?php echo htmlspecialchars($ci['nama_produk']); ?> (x<?php echo $ci['qty']; ?>)</span>
                            <span style="font-weight: 700;">Rp <?php echo number_format($ci['subtotal'], 0, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr style="border: none; border-top: 1px solid #f1f5f9; margin: 15px 0;">

                <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 20px;">
                    <span>Total Tagihan</span>
                    <span style="color: #004494;">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></span>
                </div>

                <button type="submit" name="proses_checkout" style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer; box-shadow: 0 4px 12px rgba(40,167,69,0.2);">
                    ✅ Konfirmasi & Buat Pesanan
                </button>
            </div>
        </form>

        <script>
        function togglePayment(type) {
            const qrisBox = document.getElementById('qris_box');
            if (type === 'qris') {
                qrisBox.style.display = 'block';
            } else {
                qrisBox.style.display = 'none';
            }
        }
        </script>
    <?php endif; ?>
</div>

<?php 
include 'includes/footer.php'; 
ob_end_flush();
?>