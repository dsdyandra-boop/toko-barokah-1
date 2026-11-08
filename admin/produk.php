<?php
include 'header_admin.php';
require_once '../config/database.php';

$error = '';
$sukses = '';

// 1. TAMBAH PRODUK
if (isset($_POST['tambah_produk'])) {
    $nama_produk = mysqli_real_escape_string($conn, trim($_POST['nama_produk']));
    $barcode     = mysqli_real_escape_string($conn, trim($_POST['barcode']));
    $harga       = (int)$_POST['harga'];
    $stok        = (int)$_POST['stok'];
    $id_kategori = (int)$_POST['id_kategori'];

    if (empty($barcode)) {
        $barcode = '899' . substr(time(), -7) . rand(100, 999);
    }

    $cek_barcode = mysqli_query($conn, "SELECT id_produk FROM products WHERE barcode = '$barcode'");
    if (mysqli_num_rows($cek_barcode) > 0) {
        $error = 'Kode barcode sudah digunakan oleh produk lain!';
    } else {
        $gambar = 'default.jpg';
        if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != "") {
            $nama_file = time() . '_' . $_FILES['gambar']['name'];
            $tmp_name  = $_FILES['gambar']['tmp_name'];
            move_uploaded_file($tmp_name, "../assets/img/" . $nama_file);
            $gambar = $nama_file;
        }

        $query = "INSERT INTO products (nama_produk, barcode, harga, stok, gambar, id_kategori) 
                  VALUES ('$nama_produk', '$barcode', '$harga', '$stok', '$gambar', '$id_kategori')";
        
        if (mysqli_query($conn, $query)) {
            $sukses = 'Produk berhasil ditambahkan dengan barcode: ' . $barcode;
        } else {
            $error = 'Gagal menambah produk: ' . mysqli_error($conn);
        }
    }
}

// 2. EDIT PRODUK
if (isset($_POST['edit_produk'])) {
    $id_produk   = (int)$_POST['id_produk'];
    $nama_produk = mysqli_real_escape_string($conn, trim($_POST['nama_produk']));
    $barcode     = mysqli_real_escape_string($conn, trim($_POST['barcode']));
    $harga       = (int)$_POST['harga'];
    $stok        = (int)$_POST['stok'];
    $id_kategori = (int)$_POST['id_kategori'];

    $cek_barcode = mysqli_query($conn, "SELECT id_produk FROM products WHERE barcode = '$barcode' AND id_produk != '$id_produk'");
    if (mysqli_num_rows($cek_barcode) > 0) {
        $error = 'Kode barcode sudah digunakan oleh produk lain!';
    } else {
        $query_gambar = "";
        if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != "") {
            $nama_file = time() . '_' . $_FILES['gambar']['name'];
            $tmp_name  = $_FILES['gambar']['tmp_name'];
            move_uploaded_file($tmp_name, "../assets/img/" . $nama_file);
            $query_gambar = ", gambar = '$nama_file'";
        }

        $query = "UPDATE products SET 
                    nama_produk = '$nama_produk', 
                    barcode = '$barcode', 
                    harga = '$harga', 
                    stok = '$stok', 
                    id_kategori = '$id_kategori' 
                    $query_gambar 
                  WHERE id_produk = '$id_produk'";

        if (mysqli_query($conn, $query)) {
            $sukses = 'Data produk berhasil diperbarui!';
        } else {
            $error = 'Gagal memperbarui produk: ' . mysqli_error($conn);
        }
    }
}

// 3. HAPUS PRODUK
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM products WHERE id_produk = '$id_hapus'");
    header("Location: produk.php?pesan=Produk berhasil dihapus");
    exit;
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY nama_kategori ASC");
$cat_options = [];
while ($c = mysqli_fetch_assoc($categories)) {
    $cat_options[] = $c;
}

$query_produk = "SELECT p.*, c.nama_kategori 
                 FROM products p 
                 LEFT JOIN categories c ON p.id_kategori = c.id_kategori 
                 ORDER BY p.id_produk DESC";
$products = mysqli_query($conn, $query_produk);
?>

<h2>Kelola Produk & Barcode Scanner</h2>
<p style="color: #666; font-size: 14px; margin-bottom: 20px;">Support USB Scanner, Scanner Kamera HP / WebCam, & Auto-Lookup API.</p>

<?php if ($sukses) : ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
        ✅ <?php echo htmlspecialchars($sukses); ?>
    </div>
<?php endif; ?>

<?php if ($error) : ?>
    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
        ⚠️ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Form Tambah Produk -->
<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 30px;">
    <h3>➕ Tambah Produk Baru</h3>
    <form action="produk.php" method="POST" enctype="multipart/form-data" id="formTambahProduk" style="margin-top: 15px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
        <div>
            <label style="font-size: 13px; font-weight: bold;">Kode Barcode (Scan / Input)</label>
            <div style="display: flex; gap: 5px; margin-top: 5px;">
                <input type="text" name="barcode" id="input_barcode_tambah" autofocus placeholder="Scan USB / Kamera..." style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="button" onclick="startCameraScanner('input_barcode_tambah', 'input_nama_tambah', 'btn_lookup_tambah')" style="padding: 8px 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;" title="Scan pakai Kamera HP / WebCam">📷</button>
                <button type="button" id="btn_lookup_tambah" onclick="lookupBarcodeAPI('input_barcode_tambah', 'input_nama_tambah', 'btn_lookup_tambah')" style="padding: 8px 10px; background: #17a2b8; color: white; border: none; border-radius: 4px; cursor: pointer;" title="Cari Nama Produk di API Publik">🔍</button>
                <button type="button" onclick="generateAutoBarcode('input_barcode_tambah')" style="padding: 8px 10px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;" title="Generate Random Barcode">⚡</button>
            </div>
        </div>
        <div>
            <label style="font-size: 13px; font-weight: bold;">Nama Produk</label>
            <input type="text" name="nama_produk" id="input_nama_tambah" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-top: 5px;">
        </div>
        <div>
            <label style="font-size: 13px; font-weight: bold;">Kategori</label>
            <select name="id_kategori" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-top: 5px;">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($cat_options as $cat) : ?>
                    <option value="<?php echo $cat['id_kategori']; ?>"><?php echo htmlspecialchars($cat['nama_kategori']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="font-size: 13px; font-weight: bold;">Harga (Rp)</label>
            <input type="number" name="harga" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-top: 5px;">
        </div>
        <div>
            <label style="font-size: 13px; font-weight: bold;">Stok</label>
            <input type="number" name="stok" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-top: 5px;">
        </div>
        <div>
            <label style="font-size: 13px; font-weight: bold;">Foto Produk</label>
            <input type="file" name="gambar" accept="image/*" style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px; margin-top: 5px;">
        </div>
        <div style="grid-column: 1 / -1; margin-top: 10px;">
            <button type="submit" name="tambah_produk" class="btn-add" style="border: none; cursor: pointer;">Simpan Produk</button>
        </div>
    </form>
</div>

<!-- Tabel Daftar Produk -->
<h3>Daftar Produk & Label Barcode</h3>
<table class="table-admin" style="margin-top: 15px;">
    <thead>
        <tr>
            <th>Gambar</th>
            <th>Nama Produk</th>
            <th>Barcode</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi / Label</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($products) > 0) : ?>
            <?php while ($p = mysqli_fetch_assoc($products)) : ?>
                <tr>
                    <td>
                        <img src="../assets/img/<?php echo htmlspecialchars($p['gambar']); ?>" alt="foto" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                    </td>
                    <td><strong><?php echo htmlspecialchars($p['nama_produk']); ?></strong></td>
                    <td>
                        <span style="font-family: monospace; background: #eef2ff; padding: 4px 8px; border-radius: 4px; font-weight: bold; color: #3b52d4;">
                            <?php echo htmlspecialchars($p['barcode'] ?? '-'); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($p['nama_kategori'] ?? 'Uncategorized'); ?></td>
                    <td>Rp <?php echo number_format($p['harga'], 0, ',', '.'); ?></td>
                    <td><?php echo $p['stok']; ?></td>
                    <td>
                        <button onclick='openPrintBarcodeModal("<?php echo htmlspecialchars($p['nama_produk']); ?>", "<?php echo htmlspecialchars($p['barcode']); ?>", "<?php echo number_format($p['harga'], 0, ',', '.'); ?>")' class="btn-action btn-detail" style="border: none; cursor: pointer;">🏷️ Label</button>
                        <a href="javascript:void(0)" onclick='openEditModal(<?php echo json_encode($p); ?>)' class="btn-action btn-edit">Edit</a>
                        <a href="produk.php?hapus=<?php echo $p['id_produk']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus produk ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #777;">Belum ada data produk.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- MODAL CAMERA SCANNER -->
<div id="modalCamera" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); align-items: center; justify-content: center; z-index: 10000; padding: 15px;">
    <div style="background: white; padding: 20px; border-radius: 16px; width: 100%; max-width: 420px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <h3 style="margin-bottom: 10px;">📷 Scan Barcode Produk</h3>
        <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Arahkan kamera HP / WebCam ke garis Barcode</p>
        
        <div id="camera-preview" style="width: 100%; min-height: 250px; background: #000; border-radius: 12px; overflow: hidden; margin-bottom: 15px;"></div>

        <button type="button" onclick="stopCameraScanner()" style="width: 100%; padding: 12px; background: #dc3545; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">❌ Tutup Kamera</button>
    </div>
</div>

<!-- Modal Preview Barcode -->
<div id="modalBarcode" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; padding: 25px; border-radius: 12px; width: 320px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3 id="modal_bc_title" style="margin-bottom: 5px;">Detail Barcode</h3>
        <p id="modal_bc_price" style="font-weight: bold; color: #0056b3; margin-bottom: 15px;"></p>
        
        <div id="printArea" style="background: white; padding: 15px; border: 1px dashed #ccc; border-radius: 8px;">
            <div id="print_title" style="font-size: 13px; font-weight: bold; margin-bottom: 5px;"></div>
            <svg id="barcode_svg"></svg>
            <div id="print_price" style="font-size: 12px; font-weight: bold; color: #333; margin-top: 5px;"></div>
        </div>

        <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
            <button onclick="printBarcodeLabel()" class="btn-add" style="margin: 0; border: none; cursor: pointer;">🖨️ Cetak</button>
            <button onclick="closeBarcodeModal()" style="padding: 8px 15px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Edit Produk -->
<div id="modalEdit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; padding: 25px; border-radius: 12px; width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3>✏️ Edit Produk</h3>
        <form action="produk.php" method="POST" enctype="multipart/form-data" style="margin-top: 15px; display: flex; flex-direction: column; gap: 12px;">
            <input type="hidden" name="id_produk" id="edit_id_produk">
            
            <div>
                <label style="font-size: 12px; font-weight: bold;">Kode Barcode</label>
                <div style="display: flex; gap: 5px;">
                    <input type="text" name="barcode" id="edit_barcode" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <button type="button" onclick="startCameraScanner('edit_barcode', 'edit_nama_produk', 'btn_lookup_edit')" style="padding: 8px 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">📷</button>
                    <button type="button" id="btn_lookup_edit" onclick="lookupBarcodeAPI('edit_barcode', 'edit_nama_produk', 'btn_lookup_edit')" style="padding: 8px 10px; background: #17a2b8; color: white; border: none; border-radius: 4px; cursor: pointer;">🔍</button>
                    <button type="button" onclick="generateAutoBarcode('edit_barcode')" style="padding: 8px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">⚡</button>
                </div>
            </div>
            <div>
                <label style="font-size: 12px; font-weight: bold;">Nama Produk</label>
                <input type="text" name="nama_produk" id="edit_nama_produk" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div>
                <label style="font-size: 12px; font-weight: bold;">Kategori</label>
                <select name="id_kategori" id="edit_id_kategori" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <?php foreach ($cat_options as $cat) : ?>
                        <option value="<?php echo $cat['id_kategori']; ?>"><?php echo htmlspecialchars($cat['nama_kategori']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Harga (Rp)</label>
                    <input type="number" name="harga" id="edit_harga" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Stok</label>
                    <input type="number" name="stok" id="edit_stok" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
            </div>
            <div>
                <label style="font-size: 12px; font-weight: bold;">Ganti Foto (Opsional)</label>
                <input type="file" name="gambar" accept="image/*" style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-top: 10px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeEditModal()" style="padding: 8px 15px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Batal</button>
                <button type="submit" name="edit_produk" class="btn-add" style="margin: 0; border: none; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
// 1. GENERATE BARCODE (Tombol ⚡)
window.generateAutoBarcode = function(targetInputId) {
    const randomBarcode = '899' + Math.floor(100000000 + Math.random() * 900000000);
    const targetInput = document.getElementById(targetInputId);
    if (targetInput) {
        targetInput.value = randomBarcode;
    }
};

// 2. LOOKUP API EKSTERNAL (Tombol 🔍)
window.lookupBarcodeAPI = async function(inputId, targetNameId, btnId) {
    const barcodeInput = document.getElementById(inputId);
    const nameInput    = document.getElementById(targetNameId);

    if (!barcodeInput || !nameInput) return;

    const barcode = barcodeInput.value.trim();
    if (!barcode) {
        alert('Silakan masukkan atau scan kode barcode terlebih dahulu!');
        barcodeInput.focus();
        return;
    }

    const btn = document.getElementById(btnId);
    const originalText = btn ? btn.innerHTML : '🔍';
    if (btn) btn.innerHTML = '⏳...';

    try {
        const response = await fetch(`https://world.openfoodfacts.org/api/v0/product/${barcode}.json`);
        const data = await response.json();

        if (data.status === 1 && data.product) {
            const productName = data.product.product_name_id || data.product.product_name || data.product.generic_name;
            if (productName) {
                nameInput.value = productName;
            } else {
                nameInput.focus();
            }
        } else {
            nameInput.focus();
        }
    } catch (error) {
        console.error('API Lookup Error:', error);
        nameInput.focus();
    } finally {
        if (btn) btn.innerHTML = originalText;
    }
};

// 3. SCANNER KAMERA HP (OPTIMASI BARCODE GARIS 1D)
let html5QrCode = null;

window.startCameraScanner = function(targetInputId, targetNameId, btnId) {
    if (typeof Html5Qrcode === 'undefined') {
        alert("Library Html5Qrcode belum dimuat. Pastikan terhubung ke internet.");
        return;
    }

    document.getElementById('modalCamera').style.display = 'flex';

    // Spesifikasikan format Barcode 1D agar fokus membaca garis minimarket
    const formatsToSupport = [
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.EAN_8,
        Html5QrcodeSupportedFormats.CODE_128,
        Html5QrcodeSupportedFormats.CODE_39,
        Html5QrcodeSupportedFormats.UPC_A,
        Html5QrcodeSupportedFormats.UPC_E,
        Html5QrcodeSupportedFormats.QR_CODE
    ];

    html5QrCode = new Html5Qrcode("camera-preview", { formatsToSupport: formatsToSupport });
    
    const config = { 
        fps: 20, 
        qrbox: { width: 280, height: 130 },
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true
        }
    };

    html5QrCode.start(
        { facingMode: "environment" },
        config,
        (decodedText) => {
            document.getElementById(targetInputId).value = decodedText;
            if (navigator.vibrate) navigator.vibrate(200);
            stopCameraScanner();
            lookupBarcodeAPI(targetInputId, targetNameId, btnId);
        },
        () => {}
    ).catch(err => {
        alert("Gagal membuka kamera: " + err);
        stopCameraScanner();
    });
};

window.stopCameraScanner = function() {
    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
            document.getElementById('modalCamera').style.display = 'none';
        }).catch(() => {
            document.getElementById('modalCamera').style.display = 'none';
        });
    } else {
        document.getElementById('modalCamera').style.display = 'none';
    }
};

// Prevent Enter key USB Scanner
document.addEventListener('DOMContentLoaded', () => {
    const bcTambah = document.getElementById('input_barcode_tambah');
    const bcEdit   = document.getElementById('edit_barcode');

    if (bcTambah) {
        bcTambah.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                lookupBarcodeAPI('input_barcode_tambah', 'input_nama_tambah', 'btn_lookup_tambah');
            }
        });
    }

    if (bcEdit) {
        bcEdit.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                lookupBarcodeAPI('edit_barcode', 'edit_nama_produk', 'btn_lookup_edit');
            }
        });
    }
});

// Modal Barcode Label
function openPrintBarcodeModal(nama, barcode, harga) {
    if (!barcode || barcode === '-') {
        alert('Produk ini belum memiliki kode barcode!');
        return;
    }
    document.getElementById('modal_bc_title').innerText = nama;
    document.getElementById('modal_bc_price').innerText = 'Rp ' + harga;
    document.getElementById('print_title').innerText = nama;
    document.getElementById('print_price').innerText = 'Rp ' + harga;

    if (typeof JsBarcode !== 'undefined') {
        JsBarcode("#barcode_svg", barcode, {
            format: "CODE128",
            lineColor: "#000",
            width: 2,
            height: 50,
            displayValue: true,
            fontSize: 14
        });
    }

    document.getElementById('modalBarcode').style.display = 'flex';
}

function closeBarcodeModal() {
    document.getElementById('modalBarcode').style.display = 'none';
}

function printBarcodeLabel() {
    const printContent = document.getElementById('printArea').innerHTML;
    const win = window.open('', '', 'width=400,height=400');
    win.document.write('<html><head><title>Cetak Label Barcode</title><style>body{font-family:sans-serif; text-align:center; padding:20px;}</style></head><body>');
    win.document.write(printContent);
    win.document.write('</body></html>');
    win.document.close();
    win.focus();
    setTimeout(() => { win.print(); win.close(); }, 300);
}

// Modal Edit
function openEditModal(produk) {
    document.getElementById('edit_id_produk').value = produk.id_produk;
    document.getElementById('edit_nama_produk').value = produk.nama_produk;
    document.getElementById('edit_barcode').value = produk.barcode || '';
    document.getElementById('edit_id_kategori').value = produk.id_kategori;
    document.getElementById('edit_harga').value = produk.harga;
    document.getElementById('edit_stok').value = produk.stok;
    document.getElementById('modalEdit').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('modalEdit').style.display = 'none';
}
</script>

<?php include 'footer_admin.php'; ?>