<?php
require_once '../koneksi.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}
$order_id = intval($_GET['id']);

// ====================
// AMBIL DATA PESANAN
// ====================
$query_order = "
    SELECT 
        orders.id, orders.tanggal, orders.total, orders.status,
        orders.metode_pembayaran, orders.catatan,
        customers.nama AS customer_name,
        customers.address, customers.phone
    FROM orders
    JOIN customers ON customers.id = orders.customer_id
    WHERE orders.id = $order_id
";

$result_order = mysqli_query($koneksi, $query_order);
$order = mysqli_fetch_assoc($result_order);

if (!$order) die("Pesanan tidak ditemukan.");

// ====================
// AMBIL ITEM
// ====================
$query_items = "
    SELECT 
        cakes.nama AS cake_name,
        cakes.harga,
        cakes.gambar AS cake_image,
        order_items.jumlah,
        order_items.subtotal
    FROM order_items
    JOIN cakes ON cakes.id = order_items.cake_id
    WHERE order_items.order_id = $order_id
";

$items = mysqli_query($koneksi, $query_items);

// Format invoice
$invoice_no = "INV-" . str_pad($order['id'], 5, "0", STR_PAD_LEFT);

// ====================
// GENERATE HTML
// ====================
ob_start();
?>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #444; padding: 8px; }
        th { background: #f0f0f0; }
        h2 { margin-bottom: 5px; }
    </style>
</head>
<body>

<h2>Faktur Pesanan <?= $invoice_no ?></h2>
<p><strong>Tanggal:</strong> <?= date("d F Y", strtotime($order['tanggal'])) ?></p>

<h3>Data Pelanggan</h3>
<p><strong>Nama:</strong> <?= $order['customer_name'] ?><br>
<strong>Alamat:</strong> <?= $order['address'] ?><br>
<strong>Telepon:</strong> <?= $order['phone'] ?></p>

<table>
    <tr>
        <th>No</th>
        <th>Nama Kue</th>
        <th>Harga</th>
        <th>Jumlah</th>
        <th>Subtotal</th>
    </tr>

    <?php
    $no = 1;
    while ($item = mysqli_fetch_assoc($items)) : ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $item['cake_name'] ?></td>
            <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
            <td><?= $item['jumlah'] ?></td>
            <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<h3>Total: Rp <?= number_format($order['total'], 0, ',', '.') ?></h3>

</body>
</html>
<?php
$html = ob_get_clean();

// ====================
// DOMPDF EXECUTE
// ====================
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Download
$dompdf->stream("$invoice_no.pdf", ["Attachment" => true]);
?>
