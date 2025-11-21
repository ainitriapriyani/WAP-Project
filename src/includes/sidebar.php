<style>
/* === Pastel 3D Linear Gradient + Glass + Neon Pink Glow (Upgraded 3 Colors) === */
#sidebar {
    background: linear-gradient(135deg,
        rgba(255, 240, 250, 0.85),
        rgba(255, 190, 220, 0.80),
        rgba(240, 170, 255, 0.80)
    );
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);

    border-right: 1px solid rgba(255, 140, 180, 0.35);

    /* Glow lembut */
    box-shadow:
        4px 0 25px rgba(0, 0, 0, 0.06),
        0 0 20px rgba(255, 140, 200, 0.25);

    /* Efek 3D */
    transform: perspective(900px) rotateY(0deg);

    position: fixed;
    top: 0;
    left: 0;
    width: 240px;
    height: 100vh;
    z-index: 2000;
    overflow-y: auto;
}

/* Header */
#sidebar .sidebar-header {
    padding: 20px;
    background: rgba(255, 230, 240, 0.70);
    border-bottom: 1px solid rgba(255, 130, 170, 0.25);
    backdrop-filter: blur(8px);
}

#sidebar .sidebar-header h3 {
    color: #e83e8c;
    margin: 0;
    font-weight: 700;
    text-shadow: 0 0 8px rgba(255, 120, 180, 0.45);
}

/* Menu */
#sidebar ul { margin: 0; padding: 0; list-style: none; }

#sidebar ul li a {
    padding: 12px 20px;
    display: block;
    color: #555;
    text-decoration: none;
    font-size: 1.06em;
    font-weight: 500;
    border-radius: 10px;

    /* inset halus */
    box-shadow:
        inset 2px 2px 4px rgba(200, 150, 170, 0.12),
        inset -2px -2px 5px rgba(255, 255, 255, 0.9);
    transition: 0.3s ease;
}

#sidebar ul li a:hover {
    background: linear-gradient(135deg, #ffe6f5, #ffd4ee, #fcd4ff);
    color: #e83e8c;
    transform: translateX(6px);

    /* soft glow */
    box-shadow: 0 0 12px rgba(255, 105, 180, 0.35);
}

/* === ACTIVE LINK (New Upgrade) === */
#sidebar ul li.active > a {
    background: linear-gradient(135deg,
        #ffcae8,
        #ffb3db,
        #f4a8ff
    );
    color: #e6007e;
    border-left: 4px solid #ff4fae;
    padding-left: 18px;

    /* Neon Glow + Inset Glass */
    box-shadow:
        0 0 18px rgba(255, 100, 180, 0.45),
        0 0 6px rgba(255, 120, 200, 0.35),
        inset 0 0 12px rgba(255, 160, 210, 0.35);
    transform: translateX(6px);
}

/* Icon warna */
#sidebar ul li a i {
    width: 20px;
    margin-right: 10px;
    color: #e83e8c;
}

#sidebar ul li.active > a i,
#sidebar ul li a:hover i {
    color: #ff2f92;
}

body { margin-left: 240px; }
</style>



<nav id="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-cookie-bite"></i> Cake Shop</h3>
    </div>

    <ul class="components">

        <li class="<?php echo (strpos($_SERVER['SCRIPT_NAME'], 'dashboard') !== false) ? 'active' : ''; ?>">
            <a href="../dashboard/">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>

        <li class="<?php echo (strpos($_SERVER['SCRIPT_NAME'], 'cakes') !== false) ? 'active' : ''; ?>">
            <a href="../cakes/">
                <i class="fas fa-birthday-cake"></i> Cakes
            </a>
        </li>

        <li class="<?php echo (strpos($_SERVER['SCRIPT_NAME'], 'customers') !== false) ? 'active' : ''; ?>">
            <a href="../customers/index.php">
                <i class="fas fa-users"></i> Customers
            </a>
        </li>

        <li class="<?php echo (strpos($_SERVER['SCRIPT_NAME'], 'orders') !== false) ? 'active' : ''; ?>">
            <a href="../orders/">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
        </li>

        <li>
            <a href="../menu.php" target="_blank">
                <i class="fas fa-store"></i> Lihat Menu
            </a>
        </li>

    </ul>
</nav>
