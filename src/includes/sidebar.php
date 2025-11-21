<style>
    /* Custom CSS "Pastel Style" untuk Sidebar */
 #sidebar {
    background: #ffffff;
    color: #495057;
    border-right: 1px solid #dee2e6;

    /* FIX klik tidak bisa */
    position: fixed;
    z-index: 9999;
    height: 100vh;
    overflow-y: auto;
}


    #sidebar .sidebar-header {
        padding: 20px;
        background: #f8f9fa; /* Header abu-abu sangat muda */
        border-bottom: 1px solid #dee2e6;
    }
    #sidebar .sidebar-header h3 {
        color: #e83e8c; /* Aksen warna Pink Pastel */
        font-weight: 600;
        margin-bottom: 0;
    }
    #sidebar .sidebar-header strong {
        display: none;
        font-size: 1.8em;
    }
    #sidebar ul.components {
        padding: 20px 0;
        border-bottom: 1px solid #dee2e6;
    }
    #sidebar ul li a {
        padding: 12px 20px;
        font-size: 1.1em;
        display: block;
        color: #495057; /* Warna link standar */
        text-decoration: none;
        transition: all 0.3s;
        font-weight: 500;
    }
    #sidebar ul li a:hover {
        color: #e83e8c; /* Warna link pink saat disentuh */
        background: #f8f9fa; /* Latar abu-abu muda saat disentuh */
    }
    #sidebar ul li.active > a, a[aria-expanded="true"] {
        color: #e83e8c; /* Teks pink untuk item aktif */
        background: #fdf2f7; /* Latar pink sangat muda untuk item aktif */
        border-left: 3px solid #e83e8c; /* Aksen pink di sisi kiri */
        padding-left: 17px;
    }
    #sidebar ul li a i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
        color: #e83e8c; /* Ikon dengan warna aksen pink */
    }
     #sidebar ul li.active > a i, #sidebar ul li a:hover i {
        color: #e83e8c; /* Ikon tetap pink saat aktif/disentuh */
    }
</style>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-cookie-bite"></i> Cake Shop</h3>
    </div>

    <ul class="list-unstyled components">
        <?php
        // Mendapatkan path skrip saat ini untuk menentukan halaman aktif
        $currentPage = $_SERVER['SCRIPT_NAME'];
        // Definisikan base path proyek Anda
        $base_path = '/cake_shop';
        ?>

        <li class="<?php echo (strpos($currentPage, 'dashboard/index.php') !== false) ? 'active' : ''; ?>">
            <a href="<?php echo $base_path; ?>/dashboard/">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </li>
        <li class="<?php echo (strpos($currentPage, 'customers/') !== false) ? 'active' : ''; ?>">
            <a href="<?php echo $base_path; ?>/customers/">
            <i class="fas fa-birthday-cake"></i>    
            
                Cakes
            </a>
        </li>
        <li class="<?php echo (strpos($currentPage, 'cakes/') !== false) ? 'active' : ''; ?>">
            <a href="<?php echo $base_path; ?>/cakes/">
            <i class="fas fa-users"></i>    
                Customers
            </a>
        </li>
        <li class="<?php echo (strpos($currentPage, 'orders/') !== false) ? 'active' : ''; ?>">
            <a href="<?php echo $base_path; ?>/orders/">
                <i class="fas fa-shopping-cart"></i>
                Orders
            </a>
        </li>
        <!-- Link baru untuk melihat menu pelanggan -->
        <li>
            <a href="<?php echo $base_path; ?>/menu.php" target="_blank">
                <i class="fas fa-store"></i>
                Lihat Menu
            </a>
        </li>
    </ul>
</nav>

