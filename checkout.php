<?php
session_start();
require_once 'config/database.php';

// Wajib Login untuk Checkout
if (!isset($_SESSION['login'])) {
    header("Location: login.php?pesan=Silakan login terlebih dahulu untuk checkout!");
    exit;
}

// Cek jika keranjang kosong
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$ids = implode(',', array_keys($_SESSION['cart']));
$query = "SELECT * FROM products WHERE id_produk IN ($ids)";
$result = mysqli_query($conn, $query);

$grand_total = 0;
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $qty = $_SESSION['cart'][$row['id_produk']];
    $subtotal = $row['harga'] * $qty;
    $grand_total += $subtotal;
    $items[] = [
        'id_produk' => $row['id_produk'],
        'harga' => $row['harga'],
        'qty' => $qty,
        'subtotal' => $subtotal
    ];
}

$success_order = false;
$order_id = 0;
$metode_pilihan = '';

if (isset($_POST['konfirmasi_bayar'])) {
    $metode = mysqli_real_escape_string($conn, $_POST['metode_pembayaran'] ?? 'cash');
    $metode_pilihan = $metode;

    // 1. Simpan ke tabel orders (termasuk metode pembayaran)
    $query_order = "INSERT INTO orders (id_user, total_harga, metode_pembayaran, status) VALUES ('$user_id', '$grand_total', '$metode', 'pending')";
    if (mysqli_query($conn, $query_order)) {
        $order_id = mysqli_insert_id($conn);

        // 2. Simpan ke order_details & kurangi stok produk
        foreach ($items as $item) {
            $p_id = $item['id_produk'];
            $p_qty = $item['qty'];
            $p_sub = $item['subtotal'];
            
            mysqli_query($conn, "INSERT INTO order_details (id_order, id_produk, qty, subtotal) VALUES ('$order_id', '$p_id', '$p_qty', '$p_sub')");
            mysqli_query($conn, "UPDATE products SET stok = stok - $p_qty WHERE id_produk = '$p_id'");
        }

        // 3. Bersihkan Keranjang Belanja
        unset($_SESSION['cart']);
        $success_order = true;
    }
}

include 'includes/header.php';
?>

<?php if ($success_order) : ?>
    <div style="background: white; padding: 40px; text-align: center; border-radius: 8px; margin-top: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 600px; margin-left: auto; margin-right: auto;">
        <h2 style="color: #28a745; margin-bottom: 10px;">🎉 Pesanan Berhasil Dibuat!</h2>
        <p style="color: #555; margin-bottom: 15px;">Nomor ID Pesanan Kamu: <strong>#TRX-<?php echo $order_id; ?></strong></p>
        
        <div style="background: #f8f9fa; border: 1px solid #e9ecef; padding: 15px; border-radius: 6px; margin-bottom: 25px;">
            <div style="font-size: 14px; color: #666;">Total Tagihan:</div>
            <div style="font-size: 24px; font-weight: bold; color: #d32f2f; margin: 5px 0;">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></div>
            <div style="font-size: 14px; margin-top: 5px;">Metode Pembayaran: <strong style="text-transform: uppercase; color: #0056b3;"><?php echo htmlspecialchars($metode_pilihan); ?></strong></div>
        </div>

        <?php if ($metode_pilihan === 'qris') : ?>
            <div style="background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 6px; margin-bottom: 20px; color: #856404;">
                <p style="font-weight: bold; margin-bottom: 8px;">📱 Silakan Scan QRIS Di Bawah Ini Untuk Pembayaran:</p>
                <div style="background: white; display: inline-block; padding: 10px; border-radius: 8px; border: 1px solid #ccc; margin: 10px 0;">
                    <!-- Gambar QRIS Placeholder -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=TokoHarian-TRX-<?php echo $order_id; ?>" alt="QRIS TokoHarian" width="180" height="180">
                </div>
                <p style="font-size: 13px; margin-top: 5px;">Simpan bukti transfer/pembayaran untuk diverifikasi penjual.</p>
            </div>
        <?php else : ?>
            <p style="font-size: 14px; color: #555; margin-bottom: 25px;">Silakan siapkan uang pas saat barang diterima / diambil.</p>
        <?php endif; ?>

        <a href="pesanan_saya.php" class="btn-auth" style="display: inline-block; width: auto; text-decoration: none;">Lihat Status Pesanan</a>
    </div>
<?php else : ?>
    <h2 class="section-title" style="margin-top: 30px;">Konfirmasi Checkout Pesanan</h2>

    <form action="checkout.php" method="POST">
        <div style="display: flex; gap: 30px; flex-wrap: wrap; margin-top: 20px;">
            <!-- Ringkasan Item -->
            <div style="flex: 2; min-width: 300px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3>Detail Item Belanja</h3>
                <div style="margin-top: 15px;">
                    <?php foreach ($items as $it) : 
                        $p_q = mysqli_query($conn, "SELECT nama_produk FROM products WHERE id_produk = '{$it['id_produk']}'");
                        $p_data = mysqli_fetch_assoc($p_q);
                    ?>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding: 10px 0;">
                            <div>
                                <strong><?php echo htmlspecialchars($p_data['nama_produk']); ?></strong>
                                <div style="font-size: 13px; color: #666;"><?php echo $it['qty']; ?> x Rp <?php echo number_format($it['harga'], 0, ',', '.'); ?></div>
                            </div>
                            <div style="font-weight: bold;">
                                Rp <?php echo number_format($it['subtotal'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Form Bayar & Pilih Metode -->
            <div style="flex: 1; min-width: 280px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); height: fit-content;">
                <h3>Informasi Pemesan</h3>
                <p style="margin-top: 10px; font-size: 14px; color: #555;">Nama: <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong></p>
                <p style="font-size: 14px; color: #555;">Email: <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong></p>
                
                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">

                <h3>Pilih Metode Pembayaran</h3>
                <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 10px;">
                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; background: #fafafa;">
                        <input type="radio" name="metode_pembayaran" value="cash" checked style="width: auto;">
                        <div>
                            <strong>💵 Cash / Bayar di Tempat (COD)</strong>
                            <div style="font-size: 12px; color: #666;">Bayar langsung saat barang diterima</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; background: #fafafa;">
                        <input type="radio" name="metode_pembayaran" value="qris" style="width: auto;">
                        <div>
                            <strong>📱 QRIS (Transfer / E-Wallet)</strong>
                            <div style="font-size: 12px; color: #666;">Scan QRIS via GoPay, OVO, Dana, BCA, dll.</div>
                        </div>
                    </label>
                </div>

                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                
                <span style="font-size: 14px; color: #666;">Total Tagihan:</span>
                <div style="font-size: 24px; font-weight: bold; color: #d32f2f; margin: 5px 0 15px;">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></div>

                <button type="submit" name="konfirmasi_bayar" class="btn-auth" style="background: #28a745;">Buat Pesanan Sekarang</button>
            </div>
        </div>
    </form>
<?php endif; ?>
<!-- CDN SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($success_order) : ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: '🎉 Pesanan Berhasil Dibuat!',
        html: `
            <p style="font-size: 15px; color: #555; margin-top: 10px;">
                Nomor Transaksi: <strong>#TRX-<?php echo $order_id; ?></strong>
            </p>
            <p style="font-size: 14px; color: #666; margin-top: 5px;">
                Metode Pembayaran: <strong style="text-transform: uppercase; color: #0056b3;"><?php echo htmlspecialchars($metode_pilihan); ?></strong>
            </p>
        `,
        icon: 'success',
        confirmButtonText: '📦 Lihat Status Pesanan Saya',
        confirmButtonColor: '#28a745',
        showCancelButton: true,
        cancelButtonText: '🛒 Lanjut Belanja',
        cancelButtonColor: '#0056b3',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'pesanan_saya.php';
        } else {
            window.location.href = 'index.php';
        }
    });
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>