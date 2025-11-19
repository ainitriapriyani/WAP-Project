CREATE DATABASE IF NOT EXISTS `cake_shop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE cake_shop;

-- Struktur tabel untuk admin
CREATE TABLE admin (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Memasukkan data default untuk admin dengan password yang sudah di-hash
INSERT INTO admin (id, username, password) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Struktur tabel untuk cakes
CREATE TABLE cakes (
  id int(11) NOT NULL AUTO_INCREMENT,
  nama varchar(100) NOT NULL,
  kategori varchar(50) DEFAULT NULL,
  harga decimal(10,2) NOT NULL,
  stok int(11) NOT NULL,
  gambar varchar(255) DEFAULT 'default.jpg',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Struktur tabel untuk customers
CREATE TABLE customers (
  id int(11) NOT NULL AUTO_INCREMENT,
  nama varchar(100) NOT NULL,
  alamat text DEFAULT NULL,
  telepon varchar(20) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Struktur tabel untuk orders
CREATE TABLE orders (
  id int(11) NOT NULL AUTO_INCREMENT,
  customer_id int(11) NOT NULL,
  tanggal date NOT NULL,
  total decimal(12,2) NOT NULL,
  status varchar(50) NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (id),
  KEY customer_id (customer_id),
  CONSTRAINT orders_ibfk_1 FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Struktur tabel untuk order_items
CREATE TABLE order_items (
  id int(11) NOT NULL AUTO_INCREMENT,
  order_id int(11) NOT NULL,
  cake_id int(11) NOT NULL,
  jumlah int(11) NOT NULL,
  subtotal decimal(12,2) NOT NULL,
  PRIMARY KEY (id),
  KEY order_id (order_id),
  KEY cake_id (cake_id),
  CONSTRAINT order_items_ibfk_1 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT order_items_ibfk_2 FOREIGN KEY (cake_id) REFERENCES cakes (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;