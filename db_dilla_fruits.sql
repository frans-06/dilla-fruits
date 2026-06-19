-- ============================================================
--  DATABASE : db_dilla_fruits
--  PROJECT  : Sistem Informasi Penjualan Buah Berbasis Web
--             Dilla Fruit's Padang
--  TOOL     : MySQL Workbench
--  CREATED  : 2025
-- ============================================================

USE db_dilla_fruits;

-- ============================================================
-- 1. TABEL ADMIN
--    Menyimpan data admin, kasir, dan owner (pemilik toko)
--    level: 'admin' | 'owner'
-- ============================================================
CREATE TABLE IF NOT EXISTS admin (
    id_admin    INT(11)      NOT NULL AUTO_INCREMENT,
    nama_admin  VARCHAR(100) NOT NULL,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    level       VARCHAR(20)  NOT NULL DEFAULT 'admin',
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_admin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 2. TABEL PELANGGAN
--    Menyimpan data akun pelanggan (registrasi mandiri)
-- ============================================================
CREATE TABLE IF NOT EXISTS pelanggan (
    id_pelanggan    INT(11)      NOT NULL AUTO_INCREMENT,
    nama_pelanggan  VARCHAR(100) NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    alamat          TEXT,
    no_hp           VARCHAR(15),
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_pelanggan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 3. TABEL BUAH
--    Menyimpan data produk buah yang dijual
-- ============================================================
CREATE TABLE IF NOT EXISTS buah (
    id_buah     INT(11)      NOT NULL AUTO_INCREMENT,
    nama_buah   VARCHAR(100) NOT NULL,
    harga       DOUBLE       NOT NULL DEFAULT 0,
    stok        INT(11)      NOT NULL DEFAULT 0,
    deskripsi   TEXT,
    gambar      VARCHAR(255) DEFAULT 'default.jpg',
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_buah)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 4. TABEL PEMESANAN
--    Menyimpan header pemesanan online dari pelanggan
--    status_pemesanan: 'menunggu' | 'dikonfirmasi' | 'ditolak' | 'selesai'
-- ============================================================
CREATE TABLE IF NOT EXISTS pemesanan (
    id_pemesanan        INT(11)     NOT NULL AUTO_INCREMENT,
    id_pelanggan        INT(11)     NOT NULL,
    tanggal_pemesanan   DATE        NOT NULL,
    total_harga         DOUBLE      NOT NULL DEFAULT 0,
    status_pemesanan    VARCHAR(50) NOT NULL DEFAULT 'menunggu',
    metode_pembayaran   VARCHAR(50) DEFAULT 'transfer',
    created_at          TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_pemesanan),
    CONSTRAINT fk_pemesanan_pelanggan
        FOREIGN KEY (id_pelanggan)
        REFERENCES pelanggan(id_pelanggan)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 5. TABEL DETAIL PEMESANAN
--    Menyimpan detail item buah dalam setiap pemesanan
-- ============================================================
CREATE TABLE IF NOT EXISTS detail_pemesanan (
    id_detail       INT(11) NOT NULL AUTO_INCREMENT,
    id_pemesanan    INT(11) NOT NULL,
    id_buah         INT(11) NOT NULL,
    jumlah          INT(11) NOT NULL DEFAULT 1,
    subtotal        DOUBLE  NOT NULL DEFAULT 0,
    PRIMARY KEY (id_detail),
    CONSTRAINT fk_detail_pemesanan
        FOREIGN KEY (id_pemesanan)
        REFERENCES pemesanan(id_pemesanan)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_detail_buah
        FOREIGN KEY (id_buah)
        REFERENCES buah(id_buah)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 6. TABEL TRANSAKSI PENJUALAN
--    Menyimpan data transaksi yang sudah diproses admin/kasir
-- ============================================================
CREATE TABLE IF NOT EXISTS transaksi_penjualan (
    id_transaksi        INT(11)   NOT NULL AUTO_INCREMENT,
    id_pemesanan        INT(11)   NOT NULL,
    tanggal_transaksi   DATE      NOT NULL,
    total_bayar         DOUBLE    NOT NULL DEFAULT 0,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_transaksi),
    CONSTRAINT fk_transaksi_pemesanan
        FOREIGN KEY (id_pemesanan)
        REFERENCES pemesanan(id_pemesanan)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 7. TABEL NOTA PENJUALAN
--    Menyimpan data nota cetak per transaksi
-- ============================================================
CREATE TABLE IF NOT EXISTS nota_penjualan (
    id_nota         INT(11)   NOT NULL AUTO_INCREMENT,
    id_transaksi    INT(11)   NOT NULL,
    tanggal_nota    DATE      NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_nota),
    CONSTRAINT fk_nota_transaksi
        FOREIGN KEY (id_transaksi)
        REFERENCES transaksi_penjualan(id_transaksi)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 8. TABEL LAPORAN PENJUALAN
--    Menyimpan rekap laporan penjualan per periode
-- ============================================================
CREATE TABLE IF NOT EXISTS laporan_penjualan (
    id_laporan_penjualan    INT(11)     NOT NULL AUTO_INCREMENT,
    periode                 VARCHAR(50) NOT NULL,
    total_penjualan         DOUBLE      NOT NULL DEFAULT 0,
    created_at              TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_laporan_penjualan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 9. TABEL LAPORAN PERSEDIAAN
--    Menyimpan snapshot stok buah per tanggal
-- ============================================================
CREATE TABLE IF NOT EXISTS laporan_persediaan (
    id_laporan_persediaan   INT(11)   NOT NULL AUTO_INCREMENT,
    id_buah                 INT(11),
    tanggal                 DATE      NOT NULL,
    stok_tersedia           INT(11)   NOT NULL DEFAULT 0,
    created_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_laporan_persediaan),
    CONSTRAINT fk_laporan_buah
        FOREIGN KEY (id_buah)
        REFERENCES buah(id_buah)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- DATA AWAL (SEED DATA)
-- ============================================================

-- ------------------------------------------------------------
-- Admin & Owner (password di-hash dengan MD5 untuk contoh)
-- Ganti dengan password hash yang lebih aman (password_hash)
-- di production gunakan: password_hash('password', PASSWORD_BCRYPT)
-- ------------------------------------------------------------
INSERT INTO admin (nama_admin, username, password, level) VALUES
('Administrator',   'admin',  MD5('admin123'),  'admin'),
('Kasir Satu',      'kasir1', MD5('kasir123'),  'admin'),
('Dilla Rahmawati', 'owner',  MD5('owner123'),  'owner');


-- ------------------------------------------------------------
-- Data Buah Contoh
-- ------------------------------------------------------------
INSERT INTO buah (nama_buah, harga, stok, deskripsi, gambar) VALUES
('Apel Fuji',       15000, 100, 'Apel Fuji segar import, manis dan renyah. Cocok untuk dikonsumsi langsung maupun jus.',         'apel_fuji.jpg'),
('Mangga Harum Manis', 12000, 150, 'Mangga harum manis pilihan dari petani lokal. Daging tebal, manis, dan sedikit asam.',       'mangga_harum.jpg'),
('Jeruk Siam',       8000, 200, 'Jeruk siam segar, kulitnya tipis dan mudah dikupas. Kaya vitamin C.',                           'jeruk_siam.jpg'),
('Anggur Hijau',    25000,  80, 'Anggur hijau seedless import, manis dan segar. Cocok untuk dessert dan camilan.',               'anggur_hijau.jpg'),
('Pisang Cavendish', 5000, 300, 'Pisang cavendish matang sempurna. Manis dan lembut, kaya energi dan potasium.',                 'pisang_cav.jpg'),
('Semangka',         6000, 120, 'Semangka merah tanpa biji. Segar dan manis, cocok untuk cuaca panas.',                          'semangka.jpg'),
('Pepaya California', 9000, 90, 'Pepaya california matang, daging oranye tebal, manis. Kaya vitamin A dan C.',                   'pepaya_cal.jpg'),
('Strawberry',      35000,  50, 'Strawberry segar dari Brastagi. Merah menggoda, asam manis. Cocok untuk topping dan jus.',      'strawberry.jpg'),
('Melon Golden',    10000,  70, 'Melon golden kuning, daging putih kekuningan. Manis, harum, dan menyegarkan.',                  'melon_golden.jpg'),
('Alpukat Mentega', 20000,  60, 'Alpukat mentega ukuran besar. Daging tebal, creamy, dan kaya lemak baik.',                     'alpukat.jpg');


-- ------------------------------------------------------------
-- Data Pelanggan Contoh
-- ------------------------------------------------------------
INSERT INTO pelanggan (nama_pelanggan, email, password, alamat, no_hp) VALUES
('Rina Marlina',    'rina@email.com',   MD5('rina123'),   'Jl. Sudirman No. 12, Padang',          '081234567001'),
('Budi Santoso',    'budi@email.com',   MD5('budi123'),   'Jl. Ahmad Yani No. 45, Padang Timur',  '081234567002'),
('Sari Dewi',       'sari@email.com',   MD5('sari123'),   'Jl. Diponegoro No. 7, Padang Barat',   '081234567003');


-- ============================================================
-- CONTOH TRANSAKSI LENGKAP (OPSIONAL — untuk testing)
-- ============================================================

-- Pemesanan pertama oleh Rina (id_pelanggan = 1)
INSERT INTO pemesanan (id_pelanggan, tanggal_pemesanan, total_harga, status_pemesanan, metode_pembayaran)
VALUES (1, CURDATE(), 75000, 'selesai', 'transfer');

-- Detail: 3 kg Apel Fuji + 3 kg Jeruk Siam
INSERT INTO detail_pemesanan (id_pemesanan, id_buah, jumlah, subtotal) VALUES
(1, 1, 3, 45000),
(1, 3, 3, 24000);

-- Update total sesuai detail
UPDATE pemesanan SET total_harga = 69000 WHERE id_pemesanan = 1;

-- Transaksi dari pemesanan tersebut
INSERT INTO transaksi_penjualan (id_pemesanan, tanggal_transaksi, total_bayar)
VALUES (1, CURDATE(), 69000);

-- Nota penjualan
INSERT INTO nota_penjualan (id_transaksi, tanggal_nota)
VALUES (1, CURDATE());

-- Kurangi stok buah setelah transaksi
UPDATE buah SET stok = stok - 3 WHERE id_buah = 1;
UPDATE buah SET stok = stok - 3 WHERE id_buah = 3;


-- ============================================================
-- VIEWS (OPSIONAL — mempermudah query di PHP)
-- ============================================================

-- View: detail pesanan lengkap dengan nama buah dan pelanggan
CREATE OR REPLACE VIEW v_detail_pemesanan AS
SELECT
    p.id_pemesanan,
    pl.nama_pelanggan,
    pl.no_hp,
    b.nama_buah,
    b.harga,
    dp.jumlah,
    dp.subtotal,
    p.tanggal_pemesanan,
    p.status_pemesanan,
    p.metode_pembayaran,
    p.total_harga
FROM pemesanan p
JOIN pelanggan pl    ON p.id_pelanggan  = pl.id_pelanggan
JOIN detail_pemesanan dp ON dp.id_pemesanan = p.id_pemesanan
JOIN buah b          ON dp.id_buah      = b.id_buah;


-- View: rekap transaksi harian
CREATE OR REPLACE VIEW v_rekap_transaksi AS
SELECT
    t.id_transaksi,
    t.tanggal_transaksi,
    pl.nama_pelanggan,
    t.total_bayar,
    n.id_nota
FROM transaksi_penjualan t
JOIN pemesanan p  ON t.id_pemesanan  = p.id_pemesanan
JOIN pelanggan pl ON p.id_pelanggan  = pl.id_pelanggan
LEFT JOIN nota_penjualan n ON n.id_transaksi = t.id_transaksi
ORDER BY t.tanggal_transaksi DESC;


-- View: stok buah saat ini
CREATE OR REPLACE VIEW v_stok_buah AS
SELECT
    id_buah,
    nama_buah,
    harga,
    stok,
    CASE
        WHEN stok = 0        THEN 'Habis'
        WHEN stok <= 10      THEN 'Menipis'
        WHEN stok <= 50      THEN 'Terbatas'
        ELSE                      'Tersedia'
    END AS status_stok
FROM buah
ORDER BY stok ASC;


-- ============================================================
-- VERIFIKASI — jalankan setelah import untuk cek hasil
-- ============================================================
-- SELECT * FROM admin;
-- SELECT * FROM buah;
-- SELECT * FROM pelanggan;
-- SELECT * FROM v_stok_buah;
-- SELECT * FROM v_detail_pemesanan;
-- SELECT * FROM v_rekap_transaksi;
-- ============================================================
