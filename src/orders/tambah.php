<?php
// --- KODE DEBUGGING ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- AKHIR KODE DEBUGGING ---

include '../koneksi.php';

// Blok untuk memproses form HANYA jika method-nya POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi dasar: pastikan ada item yang dipesan
    if (empty($_POST['cake_id'])) {
        die("Tidak ada item yang dipilih. Proses dibatalkan.");
    }

    $customer_id = $_POST['customer_id'];
    $tanggal = $_POST['tanggal'];
    $total = $_POST['total'];

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // 1. Simpan data pesanan utama
        $query_order = "INSERT INTO orders (customer_id, tanggal, total) VALUES (?, ?, ?)";
        $stmt_order = mysqli_prepare($koneksi, $query_order);
        mysqli_stmt_bind_param($stmt_order, "isd", $customer_id, $tanggal, $total);
        if (!mysqli_stmt_execute($stmt_order)) {
            throw new Exception("Gagal menyimpan data pesanan utama: " . mysqli_stmt_error($stmt_order));
        }
        $order_id = mysqli_insert_id($koneksi); // Ambil ID pesanan yang baru saja dibuat
        mysqli_stmt_close($stmt_order);

        // 2. Siapkan statement untuk simpan item dan update stok
        $cake_ids = $_POST['cake_id'];
        $jumlahs = $_POST['jumlah'];
        $subtotals = $_POST['subtotal'];

        $stmt_insert_item = mysqli_prepare($koneksi, "INSERT INTO order_items (order_id, cake_id, jumlah, subtotal) VALUES (?, ?, ?, ?)");
        $stmt_update_stok = mysqli_prepare($koneksi, "UPDATE cakes SET stok = stok - ? WHERE id = ?");

        for ($i = 0; $i < count($cake_ids); $i++) {
            // 2a. Simpan item pesanan
            mysqli_stmt_bind_param($stmt_insert_item, "iiid", $order_id, $cake_ids[$i], $jumlahs[$i], $subtotals[$i]);
            if (!mysqli_stmt_execute($stmt_insert_item)) {
                throw new Exception("Gagal menyimpan item pesanan: " . mysqli_stmt_error($stmt_insert_item));
            }
            
            // 2b. Kurangi stok kue
            mysqli_stmt_bind_param($stmt_update_stok, "ii", $jumlahs[$i], $cake_ids[$i]);
            if (!mysqli_stmt_execute($stmt_update_stok)) {
                throw new Exception("Gagal mengupdate stok kue: " . mysqli_stmt_error($stmt_update_stok));
            }
        }
        mysqli_stmt_close($stmt_insert_item);
        mysqli_stmt_close($stmt_update_stok);

        // Jika semua berhasil, commit transaksi
        mysqli_commit($koneksi);
        header("Location: index.php?status=sukses_tambah");
        exit();

    } catch (Exception $e) {
        // Jika ada error, rollback semua perubahan
        mysqli_rollback($koneksi);
        // Tampilkan pesan error untuk debugging. Pada production, ini bisa diganti dengan redirect.
        die("Transaksi gagal: " . $e->getMessage());
    }
}

// Sertakan header setelah semua logika pemrosesan
include '../includes/header.php';

// Ambil data untuk form (customers dan cakes)
$customers = mysqli_query($koneksi, "SELECT id, nama FROM customers");
$cakes = mysqli_query($koneksi, "SELECT id, nama, harga, stok FROM cakes WHERE stok > 0");
?>

<!-- Konten Utama -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Pesanan Baru</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard/">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Pesanan</a></li>
        <li class="breadcrumb-item active">Tambah Pesanan</li>
    </ol>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i>
            Form Tambah Pesanan
        </div>
        <div class="card-body">
            <form action="tambah.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_id" class="form-label">Pilih Pelanggan</label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="" disabled selected>-- Pilih Pelanggan --</option>
                            <?php while ($customer = mysqli_fetch_assoc($customers)) : ?>
                                <option value="<?= $customer['id']; ?>"><?= htmlspecialchars($customer['nama']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal" class="form-label">Tanggal Pesanan</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <hr>
                <h5>Detail Item Pesanan</h5>
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label for="cake-selector" class="form-label">Pilih Kue</label>
                        <select id="cake-selector" class="form-select">
                            <option value="" data-harga="0" data-stok="0" disabled selected>-- Pilih Kue untuk Ditambahkan --</option>
                            <?php mysqli_data_seek($cakes, 0); // Reset pointer
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
                        <!-- Item akan ditambahkan di sini oleh JavaScript -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total Keseluruhan:</th>
                            <th id="total-harga">Rp 0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

                <input type="hidden" id="total" name="total" value="0">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Pesanan
                </button>
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

        // Fungsi untuk format angka ke Rupiah
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        }

        // Fungsi untuk menghitung ulang total keseluruhan
        function calculateTotal() {
            let total = 0;
            orderItemsBody.querySelectorAll('tr').forEach(row => {
                const subtotal = parseFloat(row.querySelector('input.subtotal-input').value);
                total += subtotal;
            });
            totalHargaCell.textContent = formatRupiah(total);
            totalInput.value = total;
        }

        // Event listener untuk tombol "Tambah Item"
        addItemBtn.addEventListener('click', function() {
            const selectedOption = cakeSelector.options[cakeSelector.selectedIndex];
            if (!selectedOption.value) return; // Jangan lakukan apa-apa jika tidak ada kue yang dipilih

            const cakeId = selectedOption.value;
            const cakeName = selectedOption.text.split(' (Stok:')[0];
            const cakePrice = parseFloat(selectedOption.getAttribute('data-harga'));
            const maxStok = parseInt(selectedOption.getAttribute('data-stok'));
            let qty = parseInt(qtySelector.value);

            if (qty > maxStok) {
                alert(`Stok tidak mencukupi. Stok tersisa: ${maxStok}`);
                qty = maxStok; // Set ke stok maksimum jika melebihi
                qtySelector.value = qty;
            }

            if (qty < 1) {
                alert('Jumlah minimal adalah 1');
                qty = 1;
                qtySelector.value = 1;
            }

            const subtotal = cakePrice * qty;

            // Cek apakah item sudah ada di tabel
            const existingRow = orderItemsBody.querySelector(`tr[data-cake-id='${cakeId}']`);
            if (existingRow) {
                // Jika sudah ada, update jumlah dan subtotal
                const qtyInput = existingRow.querySelector('input.jumlah-input');
                let newQty = parseInt(qtyInput.value) + qty;
                if (newQty > maxStok) {
                    alert(`Stok tidak mencukupi. Stok tersisa: ${maxStok}`);
                    newQty = maxStok;
                }
                qtyInput.value = newQty;
                const newSubtotal = cakePrice * newQty;
                existingRow.querySelector('td:nth-child(4)').textContent = formatRupiah(newSubtotal);
                existingRow.querySelector('input.subtotal-input').value = newSubtotal;
            } else {
                // Jika belum ada, tambahkan baris baru
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
            // Reset pilihan
            cakeSelector.selectedIndex = 0;
            qtySelector.value = 1;
        });

        // Event listener untuk hapus item atau mengubah jumlah
        orderItemsBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item-btn') || e.target.closest('.remove-item-btn')) {
                e.target.closest('tr').remove();
                calculateTotal();
            }
        });

        orderItemsBody.addEventListener('input', function(e) {
            if (e.target.classList.contains('jumlah-input')) {
                const row = e.target.closest('tr');
                const price = parseFloat(e.target.getAttribute('data-harga'));
                const maxStok = parseInt(e.target.getAttribute('max'));
                let newQty = parseInt(e.target.value);

                if (newQty > maxStok) {
                    alert(`Stok tidak mencukupi. Stok tersisa: ${maxStok}`);
                    newQty = maxStok;
                    e.target.value = newQty;
                }
                 if (newQty < 1) {
                    newQty = 1;
                    e.target.value = 1;
                }

                const newSubtotal = price * newQty;
                row.querySelector('td:nth-child(4)').textContent = formatRupiah(newSubtotal);
                row.querySelector('input.subtotal-input').value = newSubtotal;
                calculateTotal();
            }
        });
    });
</script>

