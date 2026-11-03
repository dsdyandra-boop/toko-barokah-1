<?php
require_once '../config/database.php';
include 'header_admin.php';

$error = '';
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY nama_kategori ASC");

if (isset($_POST['simpan'])) {
    $nama_produk = trim($_POST['nama_produk']);
    $id_kategori = intval($_POST['id_kategori']);
    $harga       = intval($_POST['harga']);
    $stok        = intval($_POST['stok']);
    $gambar_nama = 'default.jpg';

    if (empty($nama_produk) || $harga <= 0) {
        $error = "Nama produk dan harga wajib diisi dengan benar!";
    } else {
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['gambar']['tmp_name'];
            $file_name = $_FILES['gambar']['name'];
            $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed)) {
                $gambar_nama = time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($file_tmp, "../assets/img/" . $gambar_nama);
            } else {
                $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP!";
            }
        }

        if (empty($error)) {
            $query = "INSERT INTO products (id_kategori, nama_produk, harga, stok, gambar) 
                      VALUES ('$id_kategori', '$nama_produk', '$harga', '$stok', '$gambar_nama')";
            if (mysqli_query($conn, $query)) {
                header("Location: produk.php?pesan=Produk berhasil ditambahkan!");
                exit;
            } else {
                $error = "Gagal menambah produk: " . mysqli_error($conn);
            }
        }
    }
}
?>

<h2>Tambah Produk Baru</h2>

<?php if ($error) : ?>
    <div class="alert-error" style="margin-top: 10px;"><?php echo $error; ?></div>
<?php endif; ?>

<form action="produk_tambah.php" method="POST" enctype="multipart/form-data" style="background: white; padding: 20px; border-radius: 8px; margin-top: 15px; max-width: 600px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
    <div class="form-group">
        <label>Nama Produk</label>
        <input type="text" name="nama_produk" required placeholder="Contoh: Minyak Goreng 2L">
    </div>

    <div class="form-group">
        <label>Kategori</label>
        <select name="id_kategori" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="">-- Pilih Kategori --</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)) : ?>
                <option value="<?php echo $cat['id_kategori']; ?>"><?php echo htmlspecialchars($cat['nama_kategori']); ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Harga (Rp)</label>
        <input type="number" name="harga" required placeholder="Contoh: 15000">
    </div>

    <div class="form-group">
        <label>Stok</label>
        <input type="number" name="stok" value="10" required>
    </div>

    <div class="form-group">
        <label>Gambar Produk (Opsional)</label>
        <input type="file" name="gambar" accept="image/*">
    </div>

    <button type="submit" name="simpan" class="btn-auth" style="margin-top: 10px;">Simpan Produk</button>
    <a href="produk.php" style="display: inline-block; margin-top: 10px; margin-left: 10px; text-decoration: none; color: #666;">Batal</a>
</form>

<?php include 'footer_admin.php'; ?>