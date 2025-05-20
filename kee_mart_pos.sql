-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for kee_mart_pos
CREATE DATABASE IF NOT EXISTS `kee_mart_pos` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `kee_mart_pos`;

-- Dumping structure for table kee_mart_pos.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kee_mart_pos.categories: ~10 rows (approximately)
INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
	(1, 'Minuman', '2025-05-19 07:26:17'),
	(2, 'Makanan Ringan', '2025-05-19 07:26:17'),
	(3, 'Sembako', '2025-05-19 07:26:17'),
	(4, 'Perlengkapan Mandi', '2025-05-19 07:26:17'),
	(5, 'Perlengkapan Rumah', '2025-05-19 07:26:17'),
	(6, 'Obat-obatan', '2025-05-19 07:26:17'),
	(7, 'Rokok', '2025-05-19 07:26:17'),
	(8, 'Es Krim', '2025-05-19 07:26:17'),
	(9, 'Susu & Dairy', '2025-05-19 07:26:17'),
	(10, 'Makanan Instan', '2025-05-19 07:26:17');

-- Dumping structure for table kee_mart_pos.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` int DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kee_mart_pos.products: ~44 rows (approximately)
INSERT INTO `products` (`id`, `code`, `name`, `category_id`, `price`, `stock`, `image`, `created_at`) VALUES
	(1, 'DRK001', 'Coca Cola 1.5L', 1, 12000.00, 50, 'assets/images/products/beverage.jpg', '2025-05-19 07:26:17'),
	(2, 'DRK002', 'Sprite 1.5L', 1, 12000.00, 45, 'assets/images/products/beverage.jpg', '2025-05-19 07:26:17'),
	(3, 'DRK003', 'Fanta 1.5L', 1, 12000.00, 40, 'assets/images/products/beverage.jpg', '2025-05-19 07:26:17'),
	(4, 'DRK004', 'Teh Pucuk 350ml', 1, 4000.00, 100, 'assets/images/products/beverage.jpg', '2025-05-19 07:26:17'),
	(5, 'DRK005', 'Pocari Sweat 500ml', 1, 7000.00, 80, 'assets/images/products/beverage.jpg', '2025-05-19 07:26:17'),
	(6, 'DRK006', 'Aqua 600ml', 1, 3500.00, 200, 'assets/images/products/beverage.jpg', '2025-05-19 07:26:17'),
	(7, 'SNK001', 'Chitato Original 75g', 2, 9500.00, 60, 'assets/images/products/snack.jpg', '2025-05-19 07:26:17'),
	(8, 'SNK002', 'Lays Rumput Laut 75g', 2, 9500.00, 55, 'assets/images/products/snack.jpg', '2025-05-19 07:26:17'),
	(9, 'SNK003', 'Taro Net 65g', 2, 8500.00, 70, 'assets/images/products/snack.jpg', '2025-05-19 07:26:17'),
	(10, 'SNK004', 'Oreo Original 137g', 2, 10000.00, 80, 'assets/images/products/snack.jpg', '2025-05-19 07:26:17'),
	(11, 'SNK005', 'Good Time 72g', 2, 8500.00, 65, 'assets/images/products/snack.jpg', '2025-05-19 07:26:17'),
	(12, 'GRC001', 'Beras Pandan Wangi 5kg', 3, 68000.00, 30, 'assets/images/products/sembako.jpg', '2025-05-19 07:26:17'),
	(13, 'GRC002', 'Minyak Goreng 1L', 3, 23000.00, 50, 'assets/images/products/sembako.jpg', '2025-05-19 07:26:17'),
	(14, 'GRC003', 'Gula Pasir 1kg', 3, 15000.00, 100, 'assets/images/products/sembako.jpg', '2025-05-19 07:26:17'),
	(15, 'GRC004', 'Tepung Terigu 1kg', 3, 12000.00, 75, 'assets/images/products/sembako.jpg', '2025-05-19 07:26:17'),
	(16, 'GRC005', 'Telur 1kg', 3, 28000.00, 40, 'assets/images/products/sembako.jpg', '2025-05-19 07:26:17'),
	(17, 'BTH001', 'Shampoo Clear 170ml', 4, 23500.00, 40, 'assets/images/products/personal-care.jpg', '2025-05-19 07:26:17'),
	(18, 'BTH002', 'Sabun Lifebuoy 75g', 4, 4500.00, 100, 'assets/images/products/personal-care.jpg', '2025-05-19 07:26:17'),
	(19, 'BTH003', 'Pasta Gigi Pepsodent 225g', 4, 15500.00, 60, 'assets/images/products/personal-care.jpg', '2025-05-19 07:26:17'),
	(20, 'BTH004', 'Sikat Gigi Formula', 4, 7500.00, 50, 'assets/images/products/personal-care.jpg', '2025-05-19 07:26:17'),
	(21, 'HOM001', 'Rinso Cair 800ml', 5, 25000.00, 30, 'assets/images/products/household.jpg', '2025-05-19 07:26:17'),
	(22, 'HOM002', 'Baygon 600ml', 5, 38000.00, 25, 'assets/images/products/household.jpg', '2025-05-19 07:26:17'),
	(23, 'HOM003', 'Tissue Nice 250s', 5, 18000.00, 45, 'assets/images/products/household.jpg', '2025-05-19 07:26:17'),
	(24, 'HOM004', 'Pewangi Downy 900ml', 5, 35000.00, 35, 'assets/images/products/household.jpg', '2025-05-19 07:26:17'),
	(25, 'MED001', 'Paracetamol Strip', 6, 12000.00, 100, 'assets/images/products/medicine.jpg', '2025-05-19 07:26:17'),
	(26, 'MED002', 'Antangin Sachet', 6, 3500.00, 150, 'assets/images/products/682ae06be41f6.jpg', '2025-05-19 07:26:17'),
	(27, 'MED003', 'Minyak Kayu Putih 60ml', 6, 28000.00, 40, 'assets/images/products/medicine.jpg', '2025-05-19 07:26:17'),
	(28, 'MED004', 'Hansaplast Strip', 6, 9500.00, 80, 'assets/images/products/medicine.jpg', '2025-05-19 07:26:17'),
	(29, 'CIG001', 'Sampoerna Mild 16', 7, 29000.00, 50, 'assets/images/products/cigarette.jpg', '2025-05-19 07:26:17'),
	(30, 'CIG002', 'Gudang Garam Filter 12', 7, 19500.00, 60, 'assets/images/products/cigarette.jpg', '2025-05-19 07:26:17'),
	(31, 'CIG003', 'Marlboro Merah 20', 7, 35000.00, 40, 'assets/images/products/cigarette.jpg', '2025-05-19 07:26:17'),
	(32, 'ICE001', 'Magnum Classic', 8, 15000.00, 30, 'assets/images/products/ice-cream.jpg', '2025-05-19 07:26:17'),
	(33, 'ICE002', 'Cornetto Oreo', 8, 10000.00, 35, 'assets/images/products/ice-cream.jpg', '2025-05-19 07:26:17'),
	(34, 'ICE003', 'Paddle Pop Trico', 8, 5000.00, 50, 'assets/images/products/ice-cream.jpg', '2025-05-19 07:26:17'),
	(35, 'ICE004', 'Wall\'s Cup', 8, 4000.00, 40, 'assets/images/products/ice-cream.jpg', '2025-05-19 07:26:17'),
	(36, 'MLK001', 'Ultra Milk 1L', 9, 18000.00, 40, 'assets/images/products/dairy.jpg', '2025-05-19 07:26:17'),
	(37, 'MLK002', 'Indomilk 250ml', 9, 5000.00, 100, 'assets/images/products/dairy.jpg', '2025-05-19 07:26:17'),
	(38, 'MLK003', 'Yakult 5pcs', 9, 12000.00, 50, 'assets/images/products/dairy.jpg', '2025-05-19 07:26:17'),
	(39, 'MLK004', 'Keju Kraft Slice 5s', 9, 15000.00, 30, 'assets/images/products/dairy.jpg', '2025-05-19 07:26:17'),
	(40, 'INS001', 'Indomie Goreng', 10, 3500.00, 200, 'assets/images/products/instant-food.jpg', '2025-05-19 07:26:17'),
	(41, 'INS002', 'Pop Mie Ayam', 10, 5500.00, 150, 'assets/images/products/instant-food.jpg', '2025-05-19 07:26:17'),
	(42, 'INS003', 'Burung Dara 400g', 10, 12000.00, 50, 'assets/images/products/instant-food.jpg', '2025-05-19 07:26:17'),
	(43, 'INS004', 'Kopi ABC Sachet', 10, 1500.00, 300, 'assets/images/products/instant-food.jpg', '2025-05-19 07:26:17'),
	(44, 'INS005', 'Energen Coklat', 10, 2500.00, 150, 'assets/images/products/instant-food.jpg', '2025-05-19 07:26:17');

-- Dumping structure for table kee_mart_pos.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL,
  `cashier_id` int DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `change_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `cashier_id` (`cashier_id`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kee_mart_pos.transactions: ~0 rows (approximately)

-- Dumping structure for table kee_mart_pos.transaction_items
CREATE TABLE IF NOT EXISTS `transaction_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`),
  CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kee_mart_pos.transaction_items: ~0 rows (approximately)

-- Dumping structure for table kee_mart_pos.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','cashier') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table kee_mart_pos.users: ~0 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
	(1, 'admin', '$2y$10$1yzdGDHyHSf3tQs/6xO8/.rc0EPlRwJArxgcRX5eM5f/i7hpPevAO', 'admin', '2025-05-19 06:55:19');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
