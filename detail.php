<?php
require_once 'config/database.php';
include 'includes/header.php';

$id = intval($_GET['id'] ?? 0);

$query = "SELECT p.*, k.nama_kategori 
          FROM products p 
          LEFT JOIN categories k ON p.id_kategori = k.id_kategori 
          WHERE p.id_produk = '$id'";
$result = mysqli_query($conn, $query);
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    echo "<div style='padding: 50px; text-align: center;'><h2>Produk tidak ditemukan!</h2><a href='index.php'>Kembali ke Beranda</a></div>";
    include 'includes/footer.php';
    exit;
}
?>

<div style="background: white; border-radius: 8px; padding: 30px; margin-top: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; gap: 40px; flex-wrap: wrap;">
    <!-- Gambar Produk -->
    <div style="flex: 1; min-width: 280px; max-width: 400px; height: 350px; background: #f8f9fa; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px solid #eee;">
        <?php if (!empty($produk['gambar']) && file_exists("assets/img/" . $produk['gambar'])) : ?>
            <img src="assets/img/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        <?php else : ?>
            <span style="color: #aaa;">[Gambar Tidak Tersedia]</span>
        <?php endif; ?>
    </div>

    <!-- Informasi Produk -->
    <div style="flex: 1.5; min-width: 280px;">
        <span style="background: #e2e8f0; color: #475569; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">
            <?php echo htmlspecialchars($produk['nama_kategori'] ?? 'Umum'); ?>
        </span>
        
        <h1 style="font-size: 28px; margin: 15px 0 10px; color: #1e293b;"><?php echo htmlspecialchars($produk['nama_produk']); ?></h1>
        
        <div style="font-size: 26px; color: #d32f2f; font-weight: bold; margin-bottom: 20px;">
            Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>
        </div>

        <div style="margin-bottom: 25px; color: #64748b; font-size: 15px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; padding: 15px 0;">
            <p>Stok Tersedia: <strong style="color: #333;"><?php echo $produk['stok']; ?> pcs</strong></p>
        </div>

        <form action="cart.php?action=add&id=<?php echo $produk['id_produk']; ?>" method="POST" style="display: flex; gap: 10px; align-items: center;">
    <input type="number" name="qty" value="1" min="1" max="<?php echo $produk['stok']; ?>" style="width: 70px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; text-align: center; font-size: 16px;">
    <button type="submit" class="btn-beli" style="padding: 12px 25px; font-size: 16px; width: auto;">+ Tambah ke Keranjang</button>
</form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>