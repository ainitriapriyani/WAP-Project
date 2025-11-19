<?php
include 'koneksi.php';

// 🔥 Ambil 3 kue paling laku
$query_bestsellers = "
    SELECT c.*, SUM(oi.jumlah) AS total_terjual
    FROM order_items oi
    JOIN cakes c ON oi.cake_id = c.id
    WHERE c.stok > 0
    GROUP BY oi.cake_id
    ORDER BY total_terjual DESC
    LIMIT 3";
$result_bestsellers = mysqli_query($koneksi, $query_bestsellers);

// 🍰 Ambil semua kue tersedia
$query_all_cakes = "SELECT * FROM cakes WHERE stok > 0 ORDER BY nama ASC";
$result_all_cakes = mysqli_query($koneksi, $query_all_cakes);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menu - Cake Shop</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

<style>
body { font-family: 'Poppins', sans-serif; background-color: #fffafc; }
.navbar-brand { font-family: 'Playfair Display', serif; font-size: 1.8rem; color: #e83e8c !important; }
.hero { background-color: #FDECEC; text-align: center; padding: 6rem 0; }
.hero h1 { font-family: 'Playfair Display', serif; color: #e83e8c; font-size: 3rem; }
.cake-card { border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 15px rgba(0,0,0,0.08); transition: .3s; }
.cake-card:hover { transform: translateY(-5px); box-shadow: 0 15px 25px rgba(0,0,0,0.1); }
.cake-card img { height: 230px; object-fit: cover; }
.cake-card .card-body { text-align: center; }
.cake-card .card-title { font-family: 'Playfair Display', serif; }
.cake-card .card-price { color: #e83e8c; font-weight: 600; }
.btn-custom { background-color: #e83e8c; color: #fff; border-radius: 30px; }
.btn-custom:hover { background-color: #d1307b; }
.section-title { font-family: 'Playfair Display', serif; text-align: center; margin: 3rem 0 2rem; }
.hot-badge { position: absolute; top: 10px; right: 10px; background: orangered; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 4px; }
.swiper { width: 100%; padding-bottom: 40px; }
.swiper-button-next, .swiper-button-prev { color: #e83e8c; }
.swiper-pagination-bullet-active { background: #e83e8c; }
.footer { background: #343a40; color: white; padding: 2rem 0; text-align: center; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fas fa-cookie-bite"></i> Cake Shop</a>
  </div>
</nav>

<header class="hero">
  <h1>Temukan Kue Favorit Anda</h1>
  <p>Dibuat dengan cinta dari bahan-bahan terbaik.</p>
</header>

<main class="container my-5">

<!-- 🔥 MENU TERLARIS -->
<?php if (mysqli_num_rows($result_bestsellers) > 0) : ?>
<section>
  <h2 class="section-title">Menu Terlaris</h2>
  <div class="swiper" id="swiper-hot">
    <div class="swiper-wrapper">
      <?php while ($cake = mysqli_fetch_assoc($result_bestsellers)) : ?>
      <div class="swiper-slide">
        <div class="card cake-card position-relative">
          <div class="hot-badge"><i class="fas fa-fire"></i> Hot</div>
          <img src="assets/img/<?= htmlspecialchars($cake['gambar']); ?>" class="card-img-top" alt="<?= htmlspecialchars($cake['nama']); ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($cake['nama']); ?></h5>
            <p class="text-muted small"><?= htmlspecialchars($cake['kategori']); ?></p>
            <p class="card-price">Rp <?= number_format($cake['harga'], 0, ',', '.'); ?></p>
            <button class="btn btn-custom mt-2" data-bs-toggle="modal" data-bs-target="#cakeModal<?= $cake['id']; ?>">Lihat Detail</button>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
  </div>
</section>
<?php endif; ?>

<!-- 🍰 SEMUA MENU -->
<section>
  <h2 class="section-title">Semua Menu</h2>
  <?php if (mysqli_num_rows($result_all_cakes) > 0) : ?>
  <div class="swiper" id="swiper-all">
    <div class="swiper-wrapper">
      <?php while ($cake = mysqli_fetch_assoc($result_all_cakes)) : ?>
      <div class="swiper-slide">
        <div class="card cake-card">
          <img src="assets/img/<?= htmlspecialchars($cake['gambar']); ?>" class="card-img-top" alt="<?= htmlspecialchars($cake['nama']); ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($cake['nama']); ?></h5>
            <p class="text-muted small"><?= htmlspecialchars($cake['kategori']); ?></p>
            <p class="card-price">Rp <?= number_format($cake['harga'], 0, ',', '.'); ?></p>
            <button class="btn btn-custom mt-2" data-bs-toggle="modal" data-bs-target="#cakeModal<?= $cake['id']; ?>">Lihat Detail</button>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
  </div>
  <?php else : ?>
    <p class="text-center text-muted">Belum ada kue tersedia.</p>
  <?php endif; ?>
</section>
</main>

<!-- MODAL DETAIL -->
<?php
$modals = mysqli_query($koneksi, "SELECT * FROM cakes WHERE stok > 0");
while ($cake = mysqli_fetch_assoc($modals)) :
?>
<div class="modal fade" id="cakeModal<?= $cake['id']; ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5><?= htmlspecialchars($cake['nama']); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6"><img src="assets/img/<?= htmlspecialchars($cake['gambar']); ?>" class="img-fluid rounded"></div>
          <div class="col-md-6">
            <h4><?= htmlspecialchars($cake['nama']); ?></h4>
            <p class="text-muted"><?= htmlspecialchars($cake['kategori']); ?></p>
            <p><strong>Harga:</strong> Rp <?= number_format($cake['harga'], 0, ',', '.'); ?></p>
            <p><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($cake['deskripsi'])); ?></p>
            <p><strong>Stok:</strong> <?= $cake['stok']; ?> buah</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endwhile; ?>

<footer class="footer"><p>&copy; <?= date('Y'); ?> Cake Shop. All Rights Reserved.</p></footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const options = {
    loop: false,
    grabCursor: true,
    pagination: { el: '.swiper-pagination', clickable: true },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    breakpoints: {
      320: { slidesPerView: 2, spaceBetween: 15 },
      768: { slidesPerView: 2, spaceBetween: 20 },
      992: { slidesPerView: 3, spaceBetween: 30 }
    }
  };

  // ✅ Inisialisasi dua slider secara terpisah
  new Swiper('#swiper-hot', options);
  new Swiper('#swiper-all', options);
});
</script>
</body>
</html>
