<?php
ob_start();
session_start();
require_once 'config/database.php';

// Validasi ID Produk
$id_produk = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT p.*, c.nama_kategori 
          FROM products p 
          LEFT JOIN categories c ON p.id_kategori = c.id_kategori 
          WHERE p.id_produk = '$id_produk'";
$result = mysqli_query($conn, $query);
$produk = mysqli_fetch_assoc($result);

$error = '';

// Handling Tambah ke Keranjang
if (isset($_POST['tambah_keranjang'])) {
    $qty = (int)$_POST['qty'];
    if ($qty < 1) $qty = 1;

    if (!$produk) {
        $error = 'Produk tidak ditemukan!';
    } else if ($produk['stok'] <= 0) {
        $error = 'Maaf, stok produk ini sudah habis!';
    } else if ($qty > $produk['stok']) {
        $error = 'Jumlah pesanan melebihi stok yang tersedia (Sisa: ' . $produk['stok'] . ')!';
    } else {
        // Inisialisasi Session Cart jika belum ada
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Tambah/Update jumlah produk di keranjang
        if (isset($_SESSION['cart'][$id_produk])) {
            $_SESSION['cart'][$id_produk] += $qty;
        } else {
            $_SESSION['cart'][$id_produk] = $qty;
        }

        header("Location: keranjang.php?pesan=Produk berhasil ditambahkan ke keranjang");
        exit;
    }
}

include 'includes/header.php';
?>

<!-- CDN JsBarcode untuk Render Barcode Label -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<div style="max-width: 1000px; margin: 0 auto; padding: 25px 20px;">
    <!-- Tombol Kembali -->
    <a href="index.php" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: #666; font-size: 14px; font-weight: 600; margin-bottom: 20px;">
        &larr; Kembali ke Katalog
    </a>

    <?php if ($error) : ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($produk) : ?>
        <!-- CARD DETAIL PRODUK -->
        <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 35px; align-items: start;">
            
            <!-- KOLOM KIRI: FOTO PRODUK & LABEL BARCODE -->
            <div>
                <div style="width: 100%; height: 320px; background: #f8fafc; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px solid #eef2f6;">
                    <img src="assets/img/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                </div>

                <?php if (!empty($produk['barcode'])) : ?>
                    <!-- Card Preview Barcode Line -->
                    <div style="margin-top: 15px; background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px dashed #cbd5e1; text-align: center;">
                        <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Kode Barcode Resmi</span>
                        <div style="margin-top: 5px;">
                            <svg id="detail_barcode_svg"></svg>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- KOLOM KANAN: INFORMASI & FORM BELI -->
            <div style="display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                <div>
                    <!-- Kategori & Barcode Badge -->
                    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 10px;">
                        <span style="background: #eef2ff; color: #004494; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                            🏷️ <?php echo htmlspecialchars($produk['nama_kategori'] ?? 'Umum'); ?>
                        </span>
                        
                        <?php if (!empty($produk['barcode'])) : ?>
                            <span style="background: #f1f5f9; color: #475569; font-size: 12px; font-family: monospace; font-weight: 600; padding: 4px 8px; border-radius: 6px;">
                                <?php echo htmlspecialchars($produk['barcode']); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Nama Produk -->
                    <h1 style="font-size: 24px; color: #0f172a; margin-bottom: 12px; line-height: 1.3;">
                        <?php echo htmlspecialchars($produk['nama_produk']); ?>
                    </h1>

                    <!-- Harga Produk -->
                    <div style="font-size: 28px; font-weight: 800; color: #004494; margin-bottom: 15px;">
                        Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                    </div>

                    <hr style="border: none; border-top: 1px solid #f1f5f9; margin: 15px 0;">

                    <!-- Status Stok -->
                    <div style="margin-bottom: 25px;">
                        <span style="font-size: 13px; color: #64748b; font-weight: 600;">Status Stok: </span>
                        <?php if ($produk['stok'] > 0) : ?>
                            <span style="background: #dcfce7; color: #166534; font-size: 13px; font-weight: 700; padding: 3px 10px; border-radius: 20px;">
                                ✅ Tersedia (<?php echo $produk['stok']; ?> unit)
                            </span>
                        <?php else : ?>
                            <span style="background: #fee2e2; color: #991b1b; font-size: 13px; font-weight: 700; padding: 3px 10px; border-radius: 20px;">
                                ❌ Stok Habis
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form Tambah ke Keranjang -->
                <?php if ($produk['stok'] > 0) : ?>
                    <form action="detail.php?id=<?php echo $produk['id_produk']; ?>" method="POST" style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #eef2f6;">
                        <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px;">
                            Jumlah Pembelian:
                        </label>
                        
                        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                            <!-- Counter Qty -->
                            <div style="display: flex; align-items: center; border: 1.5px solid #cbd5e1; border-radius: 8px; background: white; overflow: hidden;">
                                <button type="button" onclick="changeQty(-1)" style="width: 38px; height: 38px; border: none; background: white; font-weight: bold; cursor: pointer; color: #334155;">-</button>
                                <input type="number" name="qty" id="qty_input" value="1" min="1" max="<?php echo $produk['stok']; ?>" readonly style="width: 50px; height: 38px; border: none; text-align: center; font-weight: 700; font-size: 15px; color: #0f172a; outline: none;">
                                <button type="button" onclick="changeQty(1)" style="width: 38px; height: 38px; border: none; background: white; font-weight: bold; cursor: pointer; color: #334155;">+</button>
                            </div>

                            <!-- Tombol Submit -->
                            <button type="submit" name="tambah_keranjang" style="flex: 1; min-width: 180px; height: 42px; background: #004494; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.2s;">
                                🛒 + Tambah ke Keranjang
                            </button>
                        </div>
                    </form>
                <?php else : ?>
                    <div style="background: #f1f5f9; color: #64748b; padding: 15px; border-radius: 8px; text-align: center; font-weight: 600; font-size: 14px;">
                        Produk ini sedang tidak dapat dipesan.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script>
        // Render Barcode SVG jika kode ada
        const barcodeVal = "<?php echo htmlspecialchars($produk['barcode'] ?? ''); ?>";
        if (barcodeVal && typeof JsBarcode !== 'undefined') {
            JsBarcode("#detail_barcode_svg", barcodeVal, {
                format: "CODE128",
                lineColor: "#0f172a",
                width: 2,
                height: 45,
                displayValue: true,
                fontSize: 13
            });
        }

        // Counter Qty JavaScript
        function changeQty(amount) {
            const input = document.getElementById('qty_input');
            const maxStok = <?php echo (int)$produk['stok']; ?>;
            let currentVal = parseInt(input.value) || 1;
            
            currentVal += amount;
            if (currentVal < 1) currentVal = 1;
            if (currentVal > maxStok) currentVal = maxStok;

            input.value = currentVal;
        }
        </script>
    <?php else : ?>
        <!-- State Jika Produk Tidak Ditemukan -->
        <div style="background: white; padding: 50px 20px; text-align: center; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <div style="font-size: 48px; margin-bottom: 10px;">📦</div>
            <h3 style="color: #0f172a; margin-bottom: 6px;">Produk Tidak Ditemukan</h3>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Produk yang kamu cari mungkin sudah dihapus atau kodenya tidak valid.</p>
            <a href="index.php" style="padding: 10px 20px; background: #004494; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">Lihat Produk Lain</a>
        </div>
    <?php endif; ?>
</div>

<?php 
include 'includes/footer.php'; 
ob_end_flush();
?>