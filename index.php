<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
include 'includes/header.php';

// Ambil data semua kategori untuk menu filter
$query_kategori  = "SELECT * FROM categories ORDER BY id_kategori ASC";
$result_kategori = mysqli_query($conn, $query_kategori);

// Filter Logic (Search & Kategori)
$where_clauses = [];
$title_section = "Produk Terbaru";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    $where_clauses[] = "nama_produk LIKE '%$search%'";
    $title_section = "Hasil Pencarian: \"" . htmlspecialchars($search) . "\"";
}

if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $id_kat = intval($_GET['kategori']);
    $where_clauses[] = "id_kategori = '$id_kat'";
    
    // Ambil nama kategori untuk judul
    $kat_name_query = mysqli_query($conn, "SELECT nama_kategori FROM categories WHERE id_kategori = '$id_kat'");
    if ($kat_data = mysqli_fetch_assoc($kat_name_query)) {
        $title_section = "Kategori: " . htmlspecialchars($kat_data['nama_kategori']);
    }
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Ambil data produk sesuai filter
$query_produk  = "SELECT * FROM products $where_sql ORDER BY id_produk DESC";
$result_produk = mysqli_query($conn, $query_produk);
?>

<!-- Banner Promo -->
<div class="banner">
    BANNER PROMO DISKON MINGGU INI
</div>

<!-- Kategori -->
<h2 class="section-title">Kategori Pilihan</h2>
<div class="category-grid">
    <a href="index.php" class="category-card <?php echo (!isset($_GET['kategori'])) ? 'active' : ''; ?>">Semua</a>
    <?php if (mysqli_num_rows($result_kategori) > 0) : ?>
        <?php while ($kat = mysqli_fetch_assoc($result_kategori)) : ?>
            <a href="index.php?kategori=<?php echo $kat['id_kategori']; ?>" 
               class="category-card <?php echo (isset($_GET['kategori']) && $_GET['kategori'] == $kat['id_kategori']) ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($kat['nama_kategori']); ?>
            </a>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<!-- Header Judul Produk -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
    <h2 class="section-title" style="margin-bottom: 0;"><?php echo $title_section; ?></h2>
    <?php if (isset($_GET['search']) || isset($_GET['kategori'])) : ?>
        <a href="index.php" style="color: #dc3545; text-decoration: none; font-size: 14px; font-weight: bold;">✕ Reset Filter</a>
    <?php endif; ?>
</div>

<!-- Produk Grid -->
<div class="product-grid">
    <?php if (mysqli_num_rows($result_produk) > 0) : ?>
        <?php while ($produk = mysqli_fetch_assoc($result_produk)) : ?>
            <div class="product-card">
                <div>
                    <a href="detail.php?id=<?php echo $produk['id_produk']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="product-image">
                            <?php if (!empty($produk['gambar']) && file_exists("assets/img/" . $produk['gambar'])) : ?>
                                <img src="assets/img/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                            <?php else : ?>
                                <span style="color: #94a3b8; font-size: 14px;">[Gambar Produk]</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-title"><?php echo htmlspecialchars($produk['nama_produk']); ?></div>
                    </a>
                </div>
                <div>
                    <div class="product-price">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></div>
                    <div style="display: flex; gap: 5px; margin-top: 8px;">
                        <a href="detail.php?id=<?php echo $produk['id_produk']; ?>" class="btn-beli" style="flex: 1; text-align: center; text-decoration: none; background: #6c757d;">Detail</a>
                        <form action="cart.php?action=add&id=<?php echo $produk['id_produk']; ?>" method="POST" style="flex: 1;">
                            <button type="submit" class="btn-beli" style="width: 100%;">+ Keranjang</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else : ?>
        <div style="grid-column: 1 / -1; background: white; padding: 30px; text-align: center; border-radius: 8px;">
            <p style="color: #777;">Produk yang Anda cari tidak ditemukan.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>