<?php
session_start();
require_once 'config/database.php';

// Inisialisasi session cart jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handler Aksi Keranjang
$action = $_GET['action'] ?? '';
$id     = intval($_GET['id'] ?? 0);

if ($action === 'add' && $id > 0) {
    $qty = intval($_POST['qty'] ?? 1);
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }
    header("Location: cart.php");
    exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['qty'] as $prod_id => $qty_val) {
        $qty_val = intval($qty_val);
        if ($qty_val <= 0) {
            unset($_SESSION['cart'][$prod_id]);
        } else {
            $_SESSION['cart'][$prod_id] = $qty_val;
        }
    }
    header("Location: cart.php");
    exit;
}

if ($action === 'delete' && $id > 0) {
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}

include 'includes/header.php';
?>

<h2 class="section-title" style="margin-top: 30px;">Keranjang Belanja</h2>

<?php if (!empty($_SESSION['cart'])) : ?>
    <?php
    $ids = implode(',', array_keys($_SESSION['cart']));
    $query = "SELECT * FROM products WHERE id_produk IN ($ids)";
    $result = mysqli_query($conn, $query);
    $grand_total = 0;
    ?>

    <form action="cart.php?action=update" method="POST">
        <table style="width: 100%; border-collapse: collapse; background: white; margin-top: 15px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <thead>
                <tr style="background: #0056b3; color: white; text-align: left;">
                    <th style="padding: 12px 15px;">Produk</th>
                    <th style="padding: 12px 15px;">Harga</th>
                    <th style="padding: 12px 15px; width: 120px;">Jumlah</th>
                    <th style="padding: 12px 15px;">Subtotal</th>
                    <th style="padding: 12px 15px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : 
                    $qty = $_SESSION['cart'][$row['id_produk']];
                    $subtotal = $row['harga'] * $qty;
                    $grand_total += $subtotal;
                ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                            <?php if (!empty($row['gambar']) && file_exists("assets/img/" . $row['gambar'])) : ?>
                                <img src="assets/img/<?php echo htmlspecialchars($row['gambar']); ?>" width="60" height="60" style="object-fit: cover; border-radius: 4px;">
                            <?php else : ?>
                                <div style="width: 60px; height: 60px; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #888;">No Image</div>
                            <?php endif; ?>
                            <div>
                                <strong style="font-size: 16px;"><?php echo htmlspecialchars($row['nama_produk']); ?></strong>
                            </div>
                        </td>
                        <td style="padding: 15px;">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td style="padding: 15px;">
                            <input type="number" name="qty[<?php echo $row['id_produk']; ?>]" value="<?php echo $qty; ?>" min="1" max="<?php echo $row['stok']; ?>" style="width: 70px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: center;">
                        </td>
                        <td style="padding: 15px; font-weight: bold; color: #d32f2f;">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="cart.php?action=delete&id=<?php echo $row['id_produk']; ?>" style="color: #dc3545; text-decoration: none; font-weight: bold;" onclick="return confirm('Hapus item ini dari keranjang?')">✕ Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; flex-wrap: wrap; gap: 15px;">
            <div>
                <button type="submit" class="btn-auth" style="width: auto; background: #6c757d; padding: 10px 20px;">🔄 Update Keranjang</button>
                <a href="cart.php?action=clear" style="color: #dc3545; text-decoration: none; font-weight: bold; margin-left: 15px;" onclick="return confirm('Kosongkan semua item di keranjang?')">Kosongkan Keranjang</a>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: right; min-width: 250px;">
                <span style="font-size: 14px; color: #666;">Total Pembayaran:</span>
                <div style="font-size: 24px; font-weight: bold; color: #0056b3; margin: 5px 0 15px;">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></div>
                <a href="checkout.php" class="btn-auth" style="display: block; text-align: center; text-decoration: none; background: #28a745;">Lanjut ke Checkout &rarr;</a>
            </div>
        </div>
    </form>
<?php else : ?>
    <div style="background: white; padding: 40px; text-align: center; border-radius: 8px; margin-top: 20px;">
        <p style="font-size: 18px; color: #666; margin-bottom: 20px;">Keranjang belanjaan kamu masih kosong.</p>
        <a href="index.php" class="btn-auth" style="display: inline-block; width: auto; text-decoration: none;">&larr; Mulai Belanja Sekarang</a>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>