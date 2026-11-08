<?php
ob_start();
session_start();
require_once 'config/database.php';

// Inisialisasi Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 1. UPDATE KUANTITAS (QTY)
if (isset($_POST['update_qty'])) {
    $id_produk = (int)$_POST['id_produk'];
    $qty       = (int)$_POST['qty'];

    if ($qty <= 0) {
        unset($_SESSION['cart'][$id_produk]);
    } else {
        // Cek Stok Maksimal
        $q_stok = mysqli_query($conn, "SELECT stok FROM products WHERE id_produk = '$id_produk'");
        $data_stok = mysqli_fetch_assoc($q_stok);
        $stok_max = $data_stok['stok'] ?? 0;

        if ($qty > $stok_max) {
            $_SESSION['cart'][$id_produk] = $stok_max;
            $pesan_warning = "Jumlah disesuaikan dengan sisa stok ($stok_max unit).";
        } else {
            $_SESSION['cart'][$id_produk] = $qty;
        }
    }
    header("Location: keranjang.php" . (isset($pesan_warning) ? "?warning=" . urlencode($pesan_warning) : ""));
    exit;
}

// 2. HAPUS ITEM DARI KERANJANG
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    unset($_SESSION['cart'][$id_hapus]);
    header("Location: keranjang.php?pesan=Produk berhasil dihapus dari keranjang");
    exit;
}

// 3. KOSONGKAN KERANJANG
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header("Location: keranjang.php");
    exit;
}

// Ambil Data Produk dari DB berdasarkan Session Cart
$cart_items = [];
$total_belanja = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $query = "SELECT * FROM products WHERE id_produk IN ($ids)";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $qty = $_SESSION['cart'][$row['id_produk']];
        $subtotal = $row['harga'] * $qty;
        $total_belanja += $subtotal;

        $cart_items[] = [
            'id_produk' => $row['id_produk'],
            'nama_produk' => $row['nama_produk'],
            'barcode' => $row['barcode'],
            'gambar' => $row['gambar'],
            'harga' => $row['harga'],
            'stok' => $row['stok'],
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }
}

include 'includes/header.php';
?>

<div style="max-width: 1000px; margin: 0 auto; padding: 25px 20px;">
    <h2 style="font-size: 24px; color: #0f172a; margin-bottom: 5px;">🛒 Keranjang Belanja</h2>
    <p style="color: #64748b; font-size: 14px; margin-bottom: 25px;">Periksa kembali daftar belanjaan kamu sebelum melakukan checkout.</p>

    <?php if (isset($_GET['pesan'])) : ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            ✅ <?php echo htmlspecialchars($_GET['pesan']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['warning'])) : ?>
        <div style="background: #fff3cd; color: #856404; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            ⚠️ <?php echo htmlspecialchars($_GET['warning']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($cart_items)) : ?>
        <div style="display: grid; grid-template-columns: 1fr 320px; gap: 25px; align-items: start;">
            <!-- TABLE DAFTAR ITEM -->
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <span style="font-size: 14px; font-weight: 700; color: #334155;">Daftar Barang (<?php echo count($cart_items); ?>)</span>
                    <a href="keranjang.php?clear=1" onclick="return confirm('Yakin ingin mengosongkan keranjang?')" style="color: #dc3545; font-size: 13px; text-decoration: none; font-weight: 600;">🗑️ Kosongkan Keranjang</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ($cart_items as $item) : ?>
                        <div style="display: flex; gap: 15px; align-items: center; padding: 12px; border-radius: 8px; border: 1px solid #f1f5f9; background: #fafafa;">
                            <img src="assets/img/<?php echo htmlspecialchars($item['gambar']); ?>" width="65" height="65" style="object-fit: cover; border-radius: 8px;">
                            
                            <div style="flex: 1;">
                                <h4 style="font-size: 15px; color: #0f172a; margin-bottom: 4px;"><?php echo htmlspecialchars($item['nama_produk']); ?></h4>
                                <span style="font-size: 13px; font-weight: 700; color: #004494;">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></span>
                            </div>

                            <!-- Form Update Qty -->
                            <form action="keranjang.php" method="POST" style="display: flex; align-items: center; gap: 5px;">
                                <input type="hidden" name="id_produk" value="<?php echo $item['id_produk']; ?>">
                                <input type="number" name="qty" value="<?php echo $item['qty']; ?>" min="1" max="<?php echo $item['stok']; ?>" onchange="this.form.submit()" style="width: 55px; padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; text-align: center; font-weight: bold;">
                                <button type="submit" name="update_qty" style="display: none;"></button>
                            </form>

                            <div style="width: 110px; text-align: right; font-weight: 800; color: #0f172a; font-size: 14px;">
                                Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>
                            </div>

                            <a href="keranjang.php?hapus=<?php echo $item['id_produk']; ?>" onclick="return confirm('Hapus item ini?')" style="color: #94a3b8; text-decoration: none; font-size: 18px; padding: 0 5px;" title="Hapus">✕</a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 20px;">
                    <a href="index.php" style="color: #004494; text-decoration: none; font-size: 13px; font-weight: 700;">&larr; Lanjut Belanja Produk Lain</a>
                </div>
            </div>

            <!-- RINGKASAN TOTAL & CHECKOUT -->
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); position: sticky; top: 20px;">
                <h3 style="font-size: 16px; color: #0f172a; margin-bottom: 15px;">Ringkasan Belanja</h3>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #64748b;">
                    <span>Total Barang</span>
                    <span><?php echo array_sum(array_column($cart_items, 'qty')); ?> pcs</span>
                </div>

                <hr style="border: none; border-top: 1px solid #f1f5f9; margin: 12px 0;">

                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 16px; font-weight: 800; color: #0f172a;">
                    <span>Total Harga</span>
                    <span style="color: #004494;">Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
                </div>

                <a href="checkout.php" style="display: block; width: 100%; text-align: center; padding: 12px; background: #004494; color: white; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 14px; box-shadow: 0 4px 12px rgba(0,68,148,0.2);">
                    Lanjut ke Pembayaran &rarr;
                </a>
            </div>
        </div>
    <?php else : ?>
        <div style="background: white; padding: 50px 20px; text-align: center; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
            <div style="font-size: 48px; margin-bottom: 10px;">🛒</div>
            <h3 style="color: #0f172a; margin-bottom: 6px;">Keranjang Belanjaan Masih Kosong</h3>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Kamu belum memasukkan produk apapun ke keranjang.</p>
            <a href="index.php" style="padding: 10px 22px; background: #004494; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">Mulai Belanja Now</a>
        </div>
    <?php endif; ?>
</div>

<?php 
include 'includes/footer.php'; 
ob_end_flush();
?>