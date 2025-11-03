<?php
// --- KODE DEBUGGING ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- AKHIR KODE DEBUGGING ---

include '../koneksi.php';

// =========================================================================
// BAGIAN 1: MEMPROSES FORM JIKA ADA DATA YANG DIKIRIM (METHOD POST)
// =========================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $customer_id = $_POST['customer_id'];
    $tanggal = $_POST['tanggal'];
    $total = $_POST['total'];

    // Jika semua item dihapus, perlakukan sebagai pembatalan/penghapusan pesanan
    if (empty($_POST['cake_id'])) {
        header("Location: hapus.php?id=$order_id&status=dibatalkan");
        exit();
    }

    mysqli_begin_transaction($koneksi);
    try {
        // Langkah 1: Ambil semua item pesanan LAMA untuk mengembalikan stok
        $query_old_items = "SELECT cake_id, jumlah FROM order_items WHERE order_id = ?";
        $stmt_old = mysqli_prepare($koneksi, $query_old_items);
        mysqli_stmt_bind_param($stmt_old, "i", $order_id);
        mysqli_stmt_execute($stmt_old);
        $result_old_items = mysqli_stmt_get_result($stmt_old);

        while ($item = mysqli_fetch_assoc($result_old_items)) {
            // Kembalikan stok untuk setiap item lama
            $query_restore_stock = "UPDATE cakes SET stok = stok + ? WHERE id = ?";
            $stmt_restore = mysqli_prepare($koneksi, $query_restore_stock);
            mysqli_stmt_bind_param($stmt_restore, "ii", $item['jumlah'], $item['cake_id']);
            mysqli_stmt_execute($stmt_restore);
        }
        mysqli_stmt_close($stmt_old);

        // Langkah 2: Hapus semua item pesanan LAMA dari tabel order_items
        $query_delete_items = "DELETE FROM order_items WHERE order_id = ?";
        $stmt_delete = mysqli_prepare($koneksi, $query_delete_items);
        mysqli_stmt_bind_param($stmt_delete, "i", $order_id);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);

        // Langkah 3: Update data pesanan UTAMA
        $query_update_order = "UPDATE orders SET customer_id = ?, tanggal = ?, total = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($koneksi, $query_update_order);
        mysqli_stmt_bind_param($stmt_update, "isdi", $customer_id, $tanggal, $total, $order_id);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);

        // Langkah 4: Masukkan kembali item pesanan yang BARU dan kurangi stoknya
        $cake_ids = $_POST['cake_id'];
        $jumlahs = $_POST['jumlah'];
        $subtotals = $_POST['subtotal'];

        $stmt_insert_item = mysqli_prepare($koneksi, "INSERT INTO order_items (order_id, cake_id, jumlah, subtotal) VALUES (?, ?, ?, ?)");
        $stmt_reduce_stock = mysqli_prepare($koneksi, "UPDATE cakes SET stok = stok - ? WHERE id = ?");

        for ($i = 0; $i < count($cake_ids); $i++) {
            // Masukkan item baru
            mysqli_stmt_bind_param($stmt_insert_item, "iiid", $order_id, $cake_ids[$i], $jumlahs[$i], $subtotals[$i]);
            mysqli_stmt_execute($stmt_insert_item);
            
            // Kurangi stok baru
            mysqli_stmt_bind_param($stmt_reduce_stock, "ii", $jumlahs[$i], $cake_ids[$i]);
            mysqli_stmt_execute($stmt_reduce_stock);
        }
        mysqli_stmt_close($stmt_insert_item);
        mysqli_stmt_close($stmt_reduce_stock);

        // Jika semua berhasil
        mysqli_commit($koneksi);
        header("Location: index.php?status=sukses_edit");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        die("Transaksi gagal: " . $e->getMessage());
    }
}

// =========================================================================
// BAGIAN 2: MENGAMBIL DATA UNTUK DITAMPILKAN DI FORM
// =========================================================================
include '../includes/header.php';

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header("Location: index.php");
    exit();
}

// Ambil data pesanan utama
$query_order = "SELECT * FROM orders WHERE id = ?";
$stmt_order = mysqli_prepare($koneksi, $query_order);
mysqli_stmt_bind_param($stmt_order, "i", $order_id);
mysqli_stmt_execute($stmt_order);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_order));

if (!$order) {
    echo "<div class='container-fluid px-4'><div class='alert alert-danger mt-4'>Pesanan tidak ditemukan.</div></div>";
    include '../includes/footer.php';
    exit();
}

// Ambil item-item pesanan yang terkait
$query_items = "SELECT oi.cake_id, c.nama, c.harga, c.stok, oi.jumlah, oi.subtotal 
                FROM order_items oi 
                JOIN cakes c ON oi.cake_id = c.id 
                WHERE oi.order_id = ?";
$stmt_items = mysqli_prepare($koneksi, $query_items);
mysqli_stmt_bind_param($stmt_items, "i", $order_id);
mysqli_stmt_execute($stmt_items);
$order_items_result = mysqli_stmt_get_result($stmt_items);

// Ambil semua customer dan cake untuk dropdown
$customers = mysqli_query($koneksi, "SELECT id, nama FROM customers");
$cakes = mysqli_query($koneksi, "SELECT id, nama, harga, stok FROM cakes");
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Pesanan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard/">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Pesanan</a></li>
        <li class="breadcrumb-item active">Edit Pesanan #<?= $order_id; ?></li>
    </ol>

    <div class="card shadow-sm mb-4">
        <div class="card-header"><i class="fas fa-edit me-1"></i> Form Edit Pesanan</div>
        <div class="card-body">
            <form action="edit.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $order_id; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_id" class="form-label">Pelanggan</label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <?php while ($customer = mysqli_fetch_assoc($customers)): ?>
                                <option value="<?= $customer['id']; ?>" <?= ($customer['id'] == $order['customer_id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($customer['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal" class="form-label">Tanggal Pesanan</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($order['tanggal']); ?>" required>
                    </div>
                </div>

                <hr>
                <h5>Detail Item Pesanan</h5>
                <div class="row mb-3">
                     <div class="col-md-5">
                        <label for="cake-selector" class="form-label">Pilih Kue</label>
                        <select id="cake-selector" class="form-select">
                            <option value="" data-harga="0" data-stok="0" disabled selected>-- Pilih Kue untuk Ditambahkan --</option>
                            <?php mysqli_data_seek($cakes, 0); 
                            while ($cake = mysqli_fetch_assoc($cakes)) : ?>
                                <option value="<?= $cake['id']; ?>" data-harga="<?= $cake['harga']; ?>" data-stok="<?= $cake['stok']; ?>">
                                    <?= htmlspecialchars($cake['nama']); ?> (Stok: <?= $cake['stok']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="qty-selector" class="form-label">Jumlah</label>
                        <input type="number" id="qty-selector" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button type="button" id="add-item-btn" class="btn btn-info w-100">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kue</th>
                            <th style="width: 15%;">Jumlah</th>
                            <th style="width: 20%;">Harga Satuan</th>
                            <th style="width: 20%;">Subtotal</th>
                            <th style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="order-items">
                        <?php while($item = mysqli_fetch_assoc($order_items_result)): ?>
                            <tr data-cake-id="<?= $item['cake_id']; ?>">
                                <td>
                                    <?= htmlspecialchars($item['nama']); ?>
                                    <input type="hidden" name="cake_id[]" value="<?= $item['cake_id']; ?>">
                                </td>
                                <td><input type="number" name="jumlah[]" value="<?= $item['jumlah']; ?>" class="form-control jumlah-input" min="1" max="<?= $item['stok'] + $item['jumlah']; ?>" data-harga="<?= $item['harga']; ?>"></td>
                                <td>Rp <?= number_format($item['harga'], 0, ',', '.'); ?></td>
                                <td>Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-trash"></i></button>
                                    <input type="hidden" name="subtotal[]" class="subtotal-input" value="<?= $item['subtotal']; ?>">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total Keseluruhan:</th>
                            <th id="total-harga">Rp <?= number_format($order['total'], 0, ',', '.'); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

                <input type="hidden" id="total" name="total" value="<?= $order['total']; ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const addItemBtn = document.getElementById('add-item-btn');
        const cakeSelector = document.getElementById('cake-selector');
        const qtySelector = document.getElementById('qty-selector');
        const orderItemsBody = document.getElementById('order-items');
        const totalHargaCell = document.getElementById('total-harga');
        const totalInput = document.getElementById('total');

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        function calculateTotal() {
            let total = 0;
            orderItemsBody.querySelectorAll('tr').forEach(row => {
                const subtotal = parseFloat(row.querySelector('input.subtotal-input').value);
                total += subtotal;
            });
            totalHargaCell.textContent = formatRupiah(total);
            totalInput.value = total;
        }

        addItemBtn.addEventListener('click', function() {
            const selectedOption = cakeSelector.options[cakeSelector.selectedIndex];
            if (!selectedOption.value) return;

            const cakeId = selectedOption.value;
            const cakeName = selectedOption.text.split(' (Stok:')[0];
            const cakePrice = parseFloat(selectedOption.getAttribute('data-harga'));
            const maxStok = parseInt(selectedOption.getAttribute('data-stok'));
            let qty = parseInt(qtySelector.value);

            if (qty > maxStok) {
                alert(`Stok tidak mencukupi. Stok tersisa saat ini: ${maxStok}`);
                qty = maxStok;
                qtySelector.value = qty;
            }
             if (qty < 1) { qty = 1; qtySelector.value = 1; }

            const subtotal = cakePrice * qty;
            const existingRow = orderItemsBody.querySelector(`tr[data-cake-id='${cakeId}']`);

            if (existingRow) {
                const qtyInput = existingRow.querySelector('input.jumlah-input');
                const currentQty = parseInt(qtyInput.value);
                const maxAllowed = parseInt(qtyInput.getAttribute('max'));

                let newQty = currentQty + qty;
                if (newQty > maxAllowed) {
                    alert(`Stok tidak mencukupi. Stok maksimum yang bisa diinput: ${maxAllowed}`);
                    newQty = maxAllowed;
                }
                qtyInput.value = newQty;
                const newSubtotal = cakePrice * newQty;
                existingRow.querySelector('td:nth-child(4)').textContent = formatRupiah(newSubtotal);
                existingRow.querySelector('input.subtotal-input').value = newSubtotal;
            } else {
                const newRow = document.createElement('tr');
                newRow.setAttribute('data-cake-id', cakeId);
                newRow.innerHTML = `
                    <td>
                        ${cakeName}
                        <input type="hidden" name="cake_id[]" value="${cakeId}">
                    </td>
                    <td><input type="number" name="jumlah[]" value="${qty}" class="form-control jumlah-input" min="1" max="${maxStok}" data-harga="${cakePrice}"></td>
                    <td>${formatRupiah(cakePrice)}</td>
                    <td>${formatRupiah(subtotal)}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-trash"></i></button>
                        <input type="hidden" name="subtotal[]" class="subtotal-input" value="${subtotal}">
                    </td>
                `;
                orderItemsBody.appendChild(newRow);
            }
            calculateTotal();
            cakeSelector.selectedIndex = 0;
            qtySelector.value = 1;
        });

        orderItemsBody.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-item-btn');
            if (removeBtn) {
                if (confirm('Apakah Anda yakin ingin menghapus item ini dari pesanan?')) {
                    removeBtn.closest('tr').remove();
                    calculateTotal();
                }
            }
        });

        orderItemsBody.addEventListener('input', function(e) {
            if (e.target.classList.contains('jumlah-input')) {
                const row = e.target.closest('tr');
                const price = parseFloat(e.target.getAttribute('data-harga'));
                const maxStok = parseInt(e.target.getAttribute('max'));
                let newQty = parseInt(e.target.value);

                if (newQty > maxStok) {
                    alert(`Stok tidak mencukupi. Stok maksimum yang bisa diinput: ${maxStok}`);
                    newQty = maxStok;
                    e.target.value = newQty;
                }
                if (newQty < 1) { newQty = 1; e.target.value = 1; }

                const newSubtotal = price * newQty;
                row.querySelector('td:nth-child(4)').textContent = formatRupiah(newSubtotal);
                row.querySelector('input.subtotal-input').value = newSubtotal;
                calculateTotal();
            }
        });
    });
</script>

