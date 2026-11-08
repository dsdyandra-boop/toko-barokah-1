<?php
session_start();
require_once 'config/database.php';

// Ambil Kategori untuk Filter
$query_kategori = "SELECT * FROM categories ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($conn, $query_kategori);

// Filter & Pencarian (Nama / Barcode)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$kategori_filter = (isset($_GET['kategori']) && $_GET['kategori'] !== '') ? (int)$_GET['kategori'] : '';

$query_produk = "SELECT p.*, c.nama_kategori 
                 FROM products p 
                 LEFT JOIN categories c ON p.id_kategori = c.id_kategori 
                 WHERE 1=1";

if ($search !== '') {
    $query_produk .= " AND (p.nama_produk LIKE '%$search%' OR p.barcode LIKE '%$search%')";
}

if ($kategori_filter !== '') {
    $query_produk .= " AND p.id_kategori = '$kategori_filter'";
}

$query_produk .= " ORDER BY p.id_produk DESC";
$result_produk = mysqli_query($conn, $query_produk);

include 'includes/header.php';
?>

<div style="max-width: 1100px; margin: 0 auto; padding: 20px;">
    <!-- Banner Katalog -->
    <div style="background: linear-gradient(135deg, #004494 0%, #0066cc 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 25px; box-shadow: 0 8px 20px rgba(0,68,148,0.15);">
        <h1 style="font-size: 26px; margin-bottom: 8px;">🛍️ Selamat Datang di TokoHarian</h1>
        <p style="opacity: 0.9; font-size: 14px;">Belanja kebutuhan harian praktis, cepat, dan lengkap.</p>
    </div>

    <!-- BAR PENCARIAN & FILTER KATEGORI -->
    <div style="background: white; padding: 15px 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 25px;">
        <form action="index.php" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 240px;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama produk / kode barcode..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #ddd; border-radius: 8px; outline: none; font-size: 14px;">
            </div>

            <!-- Filter Kategori -->
            <select name="kategori" onchange="this.form.submit()" style="padding: 10px; border: 1.5px solid #ddd; border-radius: 8px; outline: none; font-size: 14px; cursor: pointer;">
                <option value="">Semua Kategori</option>
                <?php while ($cat = mysqli_fetch_assoc($result_kategori)) : ?>
                    <option value="<?php echo $cat['id_kategori']; ?>" <?php echo ($kategori_filter === (int)$cat['id_kategori']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="btn-lift btn-shine" style="padding: 10px 20px; background: #004494; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Cari</button>
            
            <?php if ($search !== '' || $kategori_filter !== '') : ?>
                <a href="index.php" class="btn-press" style="padding: 10px 15px; background: #6c757d; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 500;">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- LIST PRODUK -->
    <h2 style="font-size: 20px; margin-bottom: 15px; color: #1a1d2e;">Katalog Produk</h2>

    <?php if (mysqli_num_rows($result_produk) > 0) : ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
            <?php while ($p = mysqli_fetch_assoc($result_produk)) : ?>
                <div style="background: white; border-radius: 12px; padding: 15px; border: 1px solid #eef2f6; box-shadow: 0 4px 10px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.03)';">
                    <div>
                        <img src="assets/img/<?php echo htmlspecialchars($p['gambar']); ?>" alt="<?php echo htmlspecialchars($p['nama_produk']); ?>" style="width: 100%; height: 140px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
                        
                        <?php if(!empty($p['barcode'])): ?>
                            <span style="font-size: 11px; font-family: monospace; background: #f0f4ff; color: #3b52d4; padding: 2px 6px; border-radius: 4px;">
                                🏷️ <?php echo htmlspecialchars($p['barcode']); ?>
                            </span>
                        <?php endif; ?>

                        <h3 style="font-size: 15px; font-weight: 600; color: #1a1d2e; margin: 6px 0; min-height: 40px; line-height: 1.3;">
                            <?php echo htmlspecialchars($p['nama_produk']); ?>
                        </h3>
                        <p style="font-size: 12px; color: #8c94a8; margin-bottom: 8px;"><?php echo htmlspecialchars($p['nama_kategori'] ?? 'Umum'); ?></p>
                        <p style="font-size: 16px; font-weight: 700; color: #004494; margin-bottom: 12px;">
                            Rp <?php echo number_format($p['harga'], 0, ',', '.'); ?>
                        </p>
                    </div>

                    <div>
                        <a href="detail.php?id=<?php echo $p['id_produk']; ?>" class="btn-lift btn-shine" style="display: block; width: 100%; text-align: center; padding: 9px; background: #f0f4ff; color: #004494; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px;">
                            + Keranjang
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div style="background: white; padding: 40px; text-align: center; border-radius: 12px; color: #666;">
            📦 Produk tidak ditemukan<?php echo $search ? ' untuk pencarian "' . htmlspecialchars($search) . '"' : ''; ?>.
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>