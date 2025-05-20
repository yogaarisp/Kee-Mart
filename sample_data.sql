-- Delete existing data
DELETE FROM transaction_items;
DELETE FROM transactions;
DELETE FROM products;
DELETE FROM categories;
DELETE FROM users WHERE username != 'admin';

-- Insert Categories
INSERT INTO categories (name) VALUES 
('Minuman'),
('Makanan Ringan'),
('Sembako'),
('Perlengkapan Mandi'),
('Perlengkapan Rumah'),
('Obat-obatan'),
('Rokok'),
('Es Krim'),
('Susu & Dairy'),
('Makanan Instan');

-- Get category IDs for reference
SET @minuman_id = (SELECT id FROM categories WHERE name = 'Minuman');
SET @snack_id = (SELECT id FROM categories WHERE name = 'Makanan Ringan');
SET @sembako_id = (SELECT id FROM categories WHERE name = 'Sembako');
SET @mandi_id = (SELECT id FROM categories WHERE name = 'Perlengkapan Mandi');
SET @rumah_id = (SELECT id FROM categories WHERE name = 'Perlengkapan Rumah');
SET @obat_id = (SELECT id FROM categories WHERE name = 'Obat-obatan');
SET @rokok_id = (SELECT id FROM categories WHERE name = 'Rokok');
SET @eskrim_id = (SELECT id FROM categories WHERE name = 'Es Krim');
SET @susu_id = (SELECT id FROM categories WHERE name = 'Susu & Dairy');
SET @instan_id = (SELECT id FROM categories WHERE name = 'Makanan Instan');

-- Insert Products
-- Minuman
INSERT INTO products (code, name, category_id, price, stock, image) VALUES
('DRK001', 'Coca Cola 1.5L', @minuman_id, 12000, 50, 'assets/images/products/beverage.jpg'),
('DRK002', 'Sprite 1.5L', @minuman_id, 12000, 45, 'assets/images/products/beverage.jpg'),
('DRK003', 'Fanta 1.5L', @minuman_id, 12000, 40, 'assets/images/products/beverage.jpg'),
('DRK004', 'Teh Pucuk 350ml', @minuman_id, 4000, 100, 'assets/images/products/beverage.jpg'),
('DRK005', 'Pocari Sweat 500ml', @minuman_id, 7000, 80, 'assets/images/products/beverage.jpg'),
('DRK006', 'Aqua 600ml', @minuman_id, 3500, 200, 'assets/images/products/beverage.jpg');

-- Makanan Ringan
INSERT INTO products (code, name, category_id, price, stock, image) VALUES
('SNK001', 'Chitato Original 75g', @snack_id, 9500, 60, 'assets/images/products/snack.jpg'),
('SNK002', 'Lays Rumput Laut 75g', @snack_id, 9500, 55, 'assets/images/products/snack.jpg'),
('SNK003', 'Taro Net 65g', @snack_id, 8500, 70, 'assets/images/products/snack.jpg'),
('SNK004', 'Oreo Original 137g', @snack_id, 10000, 80, 'assets/images/products/snack.jpg'),
('SNK005', 'Good Time 72g', @snack_id, 8500, 65, 'assets/images/products/snack.jpg');

-- Sembako
INSERT INTO products (code, name, category_id, price, stock, image) VALUES
('GRC001', 'Beras Pandan Wangi 5kg', @sembako_id, 68000, 30, 'assets/images/products/sembako.jpg'),
('GRC002', 'Minyak Goreng 1L', @sembako_id, 23000, 50, 'assets/images/products/sembako.jpg'),
('GRC003', 'Gula Pasir 1kg', @sembako_id, 15000, 100, 'assets/images/products/sembako.jpg'),
('GRC004', 'Tepung Terigu 1kg', @sembako_id, 12000, 75, 'assets/images/products/sembako.jpg'),
('GRC005', 'Telur 1kg', @sembako_id, 28000, 40, 'assets/images/products/sembako.jpg');

-- Perlengkapan Mandi
INSERT INTO products (code, name, category_id, price, stock, image) VALUES
('BTH001', 'Shampoo Clear 170ml', @mandi_id, 23500, 40, 'assets/images/products/personal-care.jpg'),
('BTH002', 'Sabun Lifebuoy 75g', @mandi_id, 4500, 100, 'assets/images/products/personal-care.jpg'),
('BTH003', 'Pasta Gigi Pepsodent 225g', @mandi_id, 15500, 60, 'assets/images/products/personal-care.jpg'),
('BTH004', 'Sikat Gigi Formula', @mandi_id, 7500, 50, 'assets/images/products/personal-care.jpg');

-- Perlengkapan Rumah
INSERT INTO products (code, name, category_id, price, stock, image) VALUES
('HOM001', 'Rinso Cair 800ml', @rumah_id, 25000, 30, 'assets/images/products/household.jpg'),
('HOM002', 'Baygon 600ml', @rumah_id, 38000, 25, 'assets/images/products/household.jpg'),
('HOM003', 'Tissue Nice 250s', @rumah_id, 18000, 45, 'assets/images/products/household.jpg'),
('HOM004', 'Pewangi Downy 900ml', @rumah_id, 35000, 35, 'assets/images/products/household.jpg');

-- Obat-obatan
INSERT INTO products (code, name, category_id, price, stock, image) VALUES
('MED001', 'Paracetamol Strip', @obat_id, 12000, 100, 'assets/images/products/medicine.jpg'),
('MED002', 'Antangin Sachet', @obat_id, 3500, 150, 'assets/images/products/medicine.jpg'),
('MED003', 'Minyak Kayu Putih 60ml', @obat_id, 28000, 40, 'assets/images/products/medicine.jpg'),
('MED004', 'Hansaplast Strip', @obat_id, 9500, 80, 'assets/images/products/medicine.jpg');

-- Rokok
INSERT INTO products (code, name, category_id, price, stock, image) VALUES
('CIG001', 'Sampoerna Mild 16', @rokok_id, 29000, 50, 'assets/images/products/cigarette.jpg'),
('CIG002', 'Gudang Garam Filter 12', @rokok_id, 19500, 60, 'assets/images/products/cigarette.jpg'),
('CIG003', 'Marlboro Merah 20', @rokok_id, 35000, 40, 'assets/images/products/cigarette.jpg');

-- Es Krim
INSERT INTO products (code, name, category_id, price, stock, image) VALUES
('ICE001', 'Magnum Classic', @eskrim_id, 15000, 30, 'assets/images/products/ice-cream.jpg'),
('ICE002', 'Cornetto Oreo', @eskrim_id, 10000, 35, 'assets/images/products/ice-cream.jpg'),
('ICE003', 'Paddle Pop Trico', @eskrim_id, 5000, 50, 'assets/images/products/ice-cream.jpg'),
('ICE004', 'Wall''s Cup', @eskrim_id, 4000, 40, 'assets/images/products/ice-cream.jpg');

-- Susu & Dairy
INSERT INTO products (code, name, category_id, price, stock, image) VALUES
('MLK001', 'Ultra Milk 1L', @susu_id, 18000, 40, 'assets/images/products/dairy.jpg'),
('MLK002', 'Indomilk 250ml', @susu_id, 5000, 100, 'assets/images/products/dairy.jpg'),
('MLK003', 'Yakult 5pcs', @susu_id, 12000, 50, 'assets/images/products/dairy.jpg'),
('MLK004', 'Keju Kraft Slice 5s', @susu_id, 15000, 30, 'assets/images/products/dairy.jpg');

-- Makanan Instan
INSERT INTO products (code, name, category_id, price, stock, image) VALUES
('INS001', 'Indomie Goreng', @instan_id, 3500, 200, 'assets/images/products/instant-food.jpg'),
('INS002', 'Pop Mie Ayam', @instan_id, 5500, 150, 'assets/images/products/instant-food.jpg'),
('INS003', 'Burung Dara 400g', @instan_id, 12000, 50, 'assets/images/products/instant-food.jpg'),
('INS004', 'Kopi ABC Sachet', @instan_id, 1500, 300, 'assets/images/products/instant-food.jpg'),
('INS005', 'Energen Coklat', @instan_id, 2500, 150, 'assets/images/products/instant-food.jpg');
