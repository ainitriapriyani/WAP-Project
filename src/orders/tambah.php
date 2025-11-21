<?php
// ===== DEBUG MODE =====
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ======================

include '../koneksi.php';

// =================== PROSES SIMPAN PESANAN ===================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST['cake_id']) || count($_POST['cake_id']) === 0) {
        die("Tidak ada item dipilih, pesanan tidak dapat diproses.");
    }

    $customer_id = $_POST['customer_id'];
    $tanggal     = $_POST['tanggal'];
    $total       = $_POST['total'];

    mysqli_begin_transaction($koneksi);

    try {
        // INSERT orders
        $sqlOrder = "INSERT INTO orders (customer_id, tanggal, total) VALUES (?, ?, ?)";
        $stmtOrder = mysqli_prepare($koneksi, $sqlOrder);
        mysqli_stmt_bind_param($stmtOrder, "isd", $customer_id, $tanggal, $total);
        mysqli_stmt_execute($stmtOrder);
        $order_id = mysqli_insert_id($koneksi);
        mysqli_stmt_close($stmtOrder);

        // INSERT items + Update stok
        $cake_ids  = $_POST['cake_id'];
        $jumlahs   = $_POST['jumlah'];
        $subtotals = $_POST['subtotal'];

        $stmtItem = mysqli_prepare($koneksi,
            "INSERT INTO order_items (order_id, cake_id, jumlah, subtotal) VALUES (?, ?, ?, ?)"
        );

        $stmtStok = mysqli_prepare($koneksi,
            "UPDATE cakes SET stok = stok - ? WHERE id = ?"
        );

        for ($i = 0; $i < count($cake_ids); $i++) {
            mysqli_stmt_bind_param($stmtItem, "iiid",
                $order_id, $cake_ids[$i], $jumlahs[$i], $subtotals[$i]
            );
            mysqli_stmt_execute($stmtItem);

            mysqli_stmt_bind_param($stmtStok, "ii",
                $jumlahs[$i], $cake_ids[$i]
            );
            mysqli_stmt_execute($stmtStok);
        }

        mysqli_stmt_close($stmtItem);
        mysqli_stmt_close($stmtStok);

        mysqli_commit($koneksi);

        header("Location: index.php?status=sukses_tambah");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        die("Transaksi Gagal: " . $e->getMessage());
    }
}

// =================== LOAD DATA ===================
include '../includes/header.php';

$customers = mysqli_query($koneksi, "SELECT id, nama FROM customers");
$cakes     = mysqli_query($koneksi, "SELECT id, nama, harga, stok FROM cakes WHERE stok > 0");

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Pesanan</h1>

    <div class="card shadow mb-4">
        <div class="card-header">
            <strong>Form Tambah Pesanan</strong>
        </div>
        <div class="card-body">
            <form action="" method="POST">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Pelanggan</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Pelanggan --</option>
                            <?php while ($c = mysqli_fetch_assoc($customers)): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Tanggal Pesanan</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="<?= date("Y-m-d") ?>" required>
                    </div>
                </div>

                <hr>
                <h5>Item Pesanan</h5>

                <div class="row mb-3">
                    <div class="col-md-5">
                        <label>Pilih Kue</label>
                        <select id="cake-selector" class="form-select">
                            <option disabled selected>-- Pilih Kue --</option>
                            <?php mysqli_data_seek($cakes, 0);
                            while ($cake = mysqli_fetch_assoc($cakes)): ?>
                                <option
                                    value="<?= $cake['id'] ?>"
                                    data-harga="<?= $cake['harga'] ?>"
                                    data-stok="<?= $cake['stok'] ?>"
                                >
                                    <?= htmlspecialchars($cake['nama']) ?> (Stok: <?= $cake['stok'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Jumlah</label>
                        <input type="number" id="qty-selector" value="1" min="1" class="form-control">
                    </div>

                    <div class="col-md-3 align-self-end">
                        <button type="button" id="add-item-btn" class="btn btn-info w-100">
                            + Tambah Item
                        </button>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kue</th>
                            <th width="15%">Qty</th>
                            <th width="20%">Harga</th>
                            <th width="20%">Subtotal</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="order-items"></tbody>

                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th id="total-harga">Rp 0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

                <input type="hidden" name="total" id="total" value="0">

                <button class="btn btn-primary" type="submit">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>

            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const orderItemsBody = document.getElementById("order-items");
    const addItemBtn     = document.getElementById("add-item-btn");
    const cakeSelector   = document.getElementById("cake-selector");
    const qtySelector    = document.getElementById("qty-selector");
    const totalHargaCell = document.getElementById("total-harga");
    const totalInput     = document.getElementById("total");

    function formatRupiah(num) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0
        }).format(num);
    }

    function hitungTotal() {
        let total = 0;
        orderItemsBody.querySelectorAll("tr").forEach(tr => {
            total += parseFloat(tr.querySelector(".subtotal-input").value);
        });
        totalHargaCell.textContent = formatRupiah(total);
        totalInput.value = total;
    }

    // Tambah item
    addItemBtn.addEventListener("click", () => {
        const opt = cakeSelector.options[cakeSelector.selectedIndex];
        if (!opt.value) return;

        const id    = opt.value;
        const nama  = opt.text.split(" (")[0];
        const harga = parseFloat(opt.dataset.harga);
        const stok  = parseInt(opt.dataset.stok);
        const qty   = parseInt(qtySelector.value);

        if (qty < 1 || qty > stok) return alert("Jumlah tidak valid!");

        const subtotal = harga * qty;

        // Cek apakah item sudah ada
        const existing = orderItemsBody.querySelector(`tr[data-id='${id}']`);
        if (existing) {
            const inputQty = existing.querySelector(".jumlah-input");
            let newQty = parseInt(inputQty.value) + qty;
            if (newQty > stok) newQty = stok;

            inputQty.value = newQty;
            const newSubtotal = harga * newQty;

            existing.querySelector(".subtotal-cell").textContent = formatRupiah(newSubtotal);
            existing.querySelector(".subtotal-input").value = newSubtotal;

            hitungTotal();
            return;
        }

        // Buat baris baru
        const tr = document.createElement("tr");
        tr.dataset.id = id;

        tr.innerHTML = `
            <td>${nama}<input type="hidden" name="cake_id[]" value="${id}"></td>
            <td>
                <input type="number" name="jumlah[]" class="form-control jumlah-input"
                       value="${qty}" min="1" max="${stok}" data-harga="${harga}">
            </td>
            <td>${formatRupiah(harga)}</td>
            <td class="subtotal-cell">${formatRupiah(subtotal)}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm delete-btn">Hapus</button>
                <input type="hidden" name="subtotal[]" class="subtotal-input" value="${subtotal}">
            </td>
        `;
        orderItemsBody.appendChild(tr);

        hitungTotal();
    });

    // Hapus item
    orderItemsBody.addEventListener("click", e => {
        if (e.target.classList.contains("delete-btn")) {
            e.target.closest("tr").remove();
            hitungTotal();
        }
    });

    // Update qty
    orderItemsBody.addEventListener("input", e => {
        if (e.target.classList.contains("jumlah-input")) {
            const qty   = parseInt(e.target.value);
            const harga = parseFloat(e.target.dataset.harga);
            const max   = parseInt(e.target.max);

            if (qty < 1) e.target.value = 1;
            if (qty > max) e.target.value = max;

            const newSubtotal = harga * e.target.value;

            const tr = e.target.closest("tr");
            tr.querySelector(".subtotal-cell").textContent = formatRupiah(newSubtotal);
            tr.querySelector(".subtotal-input").value = newSubtotal;

            hitungTotal();
        }
    });

});
// Validasi sebelum submit
document.querySelector("form").addEventListener("submit", function(e) {
    const customer = document.querySelector("select[name='customer_id']").value;
    const tanggal = document.querySelector("input[name='tanggal']").value;
    const items = document.querySelectorAll("#order-items tr");
    const total = parseFloat(document.getElementById("total").value);

    if (!customer) {
        e.preventDefault();
        alert("Harap pilih pelanggan!");
        return;
    }

    if (!tanggal) {
        e.preventDefault();
        alert("Harap pilih tanggal pesanan!");
        return;
    }

    if (items.length === 0) {
        e.preventDefault();
        alert("Harap tambah minimal 1 item pesanan!");
        return;
    }

    if (total <= 0) {
        e.preventDefault();
        alert("Harap tambahkan item pesanan dengan benar!");
        return;
    }
});

</script>
