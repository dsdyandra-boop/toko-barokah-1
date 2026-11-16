-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Nov 2026 pada 15.35
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_tokoharian`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id_kategori`, `nama_kategori`, `created_at`) VALUES
(1, 'Sembako', '2026-11-01 14:09:31'),
(2, 'Minuman', '2026-11-01 14:09:31'),
(3, 'Makanan Ringan', '2026-11-01 14:09:31'),
(4, 'perabotan', '2026-11-01 14:09:31'),
(5, 'Kebutuhan Mandi', '2026-11-01 14:09:31'),
(6, 'Ibu & Bayi', '2026-11-01 14:09:31'),
(7, 'Kecantikan', '2026-11-01 15:37:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id_order` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `total_harga` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL DEFAULT 'cash',
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `tanggal_order` timestamp NOT NULL DEFAULT current_timestamp(),
  `nama_pemesan` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `total_bayar` int(11) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id_order`, `id_user`, `total_harga`, `metode_pembayaran`, `status`, `tanggal_order`, `nama_pemesan`, `no_hp`, `alamat`, `total_bayar`, `bukti_pembayaran`) VALUES
(1, 2, 15000, 'cash', 'selesai', '2026-11-01 15:14:22', NULL, NULL, NULL, NULL, NULL),
(2, 4, 22500, 'cash', 'selesai', '2026-11-01 15:21:54', NULL, NULL, NULL, NULL, NULL),
(3, 2, 32000, 'qris', 'selesai', '2026-11-01 15:48:04', NULL, NULL, NULL, NULL, NULL),
(4, 2, 15000, 'cash', 'selesai', '2026-11-01 15:49:17', NULL, NULL, NULL, NULL, NULL),
(5, 2, 5000, 'cash', 'selesai', '2026-11-02 05:21:09', NULL, NULL, NULL, NULL, NULL),
(6, 2, 5000, 'cash', 'selesai', '2026-11-02 05:26:38', NULL, NULL, NULL, NULL, NULL),
(9, NULL, 0, 'cod', 'pending', '2026-11-08 16:13:48', 'Dyandra DS', '0891371741', 'wadas\r\njalan syekh quro', 10000, ''),
(10, NULL, 0, 'cod', 'pending', '2026-11-08 16:14:09', 'Dyandra DS', '906856834354', 'wadas\r\njalan syekh quro', 10000, ''),
(11, NULL, 0, 'cod', 'pending', '2026-11-12 15:35:17', 'Dyandra DS', '906856834354', 'wadas\r\njalan syekh quro', 10000, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_details`
--

CREATE TABLE `order_details` (
  `id_detail` int(11) NOT NULL,
  `id_order` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_details`
--

INSERT INTO `order_details` (`id_detail`, `id_order`, `id_produk`, `qty`, `subtotal`) VALUES
(1, 1, 11, 3, 15000),
(2, 2, 7, 1, 22500),
(3, 3, 8, 1, 32000),
(4, 4, 3, 1, 15000),
(5, 5, 11, 1, 5000),
(6, 6, 11, 1, 5000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id_produk` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `gambar` varchar(255) DEFAULT 'default.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id_produk`, `id_kategori`, `nama_produk`, `barcode`, `harga`, `stok`, `gambar`, `created_at`) VALUES
(1, 1, 'Beras Pandan Wangi 5kg', NULL, 75000, 50, 'default.jpg', '2026-11-01 14:09:31'),
(2, 1, 'Minyak Goreng 2L', NULL, 34500, 100, 'default.jpg', '2026-11-01 14:09:31'),
(3, 1, 'Gula Pasir 1kg', NULL, 15000, 79, 'default.jpg', '2026-11-01 14:09:31'),
(4, 2, 'Mie Instan Kuah', NULL, 3000, 200, 'default.jpg', '2026-11-01 14:09:31'),
(5, 2, 'Kopi Bubuk 165gr', NULL, 13500, 40, 'default.jpg', '2026-11-01 14:09:31'),
(6, 2, 'Susu UHT 1L', NULL, 18000, 60, 'default.jpg', '2026-11-01 14:09:31'),
(7, 5, 'Sabun Mandi Cair 450ml', NULL, 22500, 29, 'default.jpg', '2026-11-01 14:09:31'),
(8, 1, 'Deterjen Bubuk 1.8kg', '899399899163', 32000, 23, 'default.jpg', '2026-11-01 14:09:31'),
(11, 1, 'masako', '899615909128', 5000, 4, '1793545068_6ae7536c448a3.webp', '2026-11-01 14:53:23'),
(12, 5, 'Pantene', '899699573713', 10000, 7, '1793716411_pantene.jpg', '2026-11-03 14:33:31'),
(14, 7, 'POSH MEN BLUE EVEREST 150ML', '8998866109871', 21000, 5, '1794151279_posh men blue.jpg', '2026-11-08 15:21:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'xine', 'dsdyandra@gmail.com', '$2y$10$DqGiJaMsplXRTpmlKAdmcOfdnW9KLhsKe/TzghRsIh22x7368l6la', 'customer', '2026-11-01 14:20:59'),
(4, 'Admin Toko', 'admin@tokoharian.com', '$2y$10$yxv9TmwXGzKBw8Jbh.J3K.RnjB1/R8ZDlMxcVkee9.RO7Y2JioeDS', 'admin', '2026-11-01 14:32:02');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_produk`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `products` (`id_produk`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `categories` (`id_kategori`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
