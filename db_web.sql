-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 26, 2024 at 10:51 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `account_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role_id` int DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `role` enum('admin','user','manager-user','manager-product','manager-category') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`account_id`, `username`, `password`, `email`, `role_id`, `status`, `role`) VALUES
(6, 'long', '$2y$10$01SG7B.v.j9MpvduJuQxo.Qj5pCnwXNKFmUWZdHGvKNp3x8PPdRgq', 'long@gmail.com', NULL, 'active', 'user'),
(7, 'vy nguyễn', '$2y$10$YnT1lzFYsgKP/Q7jT0KP7.vn78nhFrOp.QOrwWEsihUFJprx6QTJa', 'vy@gmail.com', NULL, 'active', 'manager-user'),
(8, 'hung', '$2y$10$LzAoJtojobC9ZIvF7/gMF.TlsfTbA/3AA2eeq8ODrK52qkkWwT1va', 'hung@gmail.com', NULL, 'active', 'manager-product'),
(9, 'nhi', '$2y$10$6DCSKufVgYjAEEMrwo4qFe.c1mjPZWPjsR3bxEwdyHzfsxmalBHpW', 'nhi@gmail.com', NULL, 'active', 'manager-category'),
(12, 'yenvy', '$2y$10$DlwLvMbUu1POKAvHb3PlWumF914drqOVB1Mx5Yk5EAlfwM9Ojz/2i', 'yenvy@gmail.com', NULL, 'active', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `billdetails`
--

DROP TABLE IF EXISTS `billdetails`;
CREATE TABLE IF NOT EXISTS `billdetails` (
  `billdetail_id` int NOT NULL AUTO_INCREMENT,
  `bill_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`billdetail_id`),
  KEY `bill_id` (`bill_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

DROP TABLE IF EXISTS `bills`;
CREATE TABLE IF NOT EXISTS `bills` (
  `bill_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `payment_method` enum('cash','credit_card','paypal') NOT NULL,
  `payment_status` enum('paid','unpaid','failed') DEFAULT 'unpaid',
  `payment_date` datetime DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`bill_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `brand_id` int NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(100) NOT NULL,
  `description` text,
  `logo_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(1, 1, 10, 1, '2024-12-02 01:41:29');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `description` text,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `status`) VALUES
(1, 'Nam', NULL, 1),
(2, 'Nữ', NULL, 1),
(3, 'Trẻ em', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
CREATE TABLE IF NOT EXISTS `discounts` (
  `discount_id` int NOT NULL AUTO_INCREMENT,
  `discount_code` varchar(50) NOT NULL,
  `discount_percentage` int NOT NULL,
  `start_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `end_date` datetime DEFAULT NULL,
  `description` text,
  `order_id` int DEFAULT NULL,
  PRIMARY KEY (`discount_id`),
  UNIQUE KEY `discount_code` (`discount_code`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

DROP TABLE IF EXISTS `orderdetails`;
CREATE TABLE IF NOT EXISTS `orderdetails` (
  `orderdetail_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`orderdetail_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `order_status` enum('pending','completed','shipped','cancelled') DEFAULT 'pending',
  `shipping_address` text,
  `total_amount` decimal(10,2) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `ward` varchar(100) NOT NULL,
  `note` text,
  `payment_method` enum('cod','paypal') DEFAULT 'cod',
  `payment_id` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending' COMMENT 'pending,processing,shipping,completed,cancelled',
  `account_id` int DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text,
  `stock_quantity` int DEFAULT '0',
  `image_url` varchar(255) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `product_type` varchar(255) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `brand_id` (`brand_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `price`, `description`, `stock_quantity`, `image_url`, `size`, `color`, `brand_id`, `category_id`, `product_type`, `gender`) VALUES
(1, 'Set Áo Phông và Quần Jean Cho Bé Trai', 250000.00, 'Mô tả: Bộ set gồm áo flannel sọc và quần dài ấm áp. Chất liệu vải flannel mềm mại, ấm áp, rất thích hợp cho mùa đông. Áo có cổ và khuy cài tiện lợi.\nChất liệu: Flannel, Cotton', 10, 'img/quanao/1.jpg\n', 'S,M,L', NULL, NULL, 3, NULL, 'boy'),
(2, 'Bộ Set Áo Thun và Quần Legging Cho Bé Gái', 150000.00, 'Mô tả: Bộ set bao gồm áo thun tay dài và quần legging có họa tiết hoa văn tươi sáng. Chất liệu cotton co giãn, giúp bé thoải mái trong các hoạt động thể thao hoặc chơi đùa suốt cả ngày.\nChất liệu: Cotton', 15, 'img/quanao/2.jpg', 'S,M,L', NULL, NULL, 3, 'the-thao', 'girl'),
(3, 'Áo Thun Tay Dài Cho Bé Trai', 200000.00, 'Mô tả: Áo thun tay ngắn màu xanh dương với họa tiết hình siêu đơn giản. Chất liệu cotton mềm mại, thoáng mát, dễ chịu cho bé trong mùa hè. Cổ áo tròn, đường may chắc chắn và dễ dàng mặc vào và tháo ra.\nChất liệu: Cotton', 10, 'img/quanao/3.jpg', 'S,M,L', 'brown', NULL, 3, NULL, 'boy'),
(4, 'Đầm Công Chúa Cho Bé Gái', 200000.00, 'Mô tả: Đầm công chúa dễ thương với váy xòe bồng bềnh, chất liệu vải satin mềm mại, kết hợp với dây đai phía sau. Cổ áo cao, trang trí hoa văn tinh tế, phù hợp cho các dịp tiệc tùng hay sự kiện đặc biệt.\nChất liệu: Satin, Tulle', 20, 'img/quanao/4.jpg', 'S,M,L', 'purple', NULL, 3, NULL, 'girl'),
(5, 'Đầm Búp Bê Cho Bé Gái', 150000.00, 'Mô tả: Đầm búp bê xòe với thiết kế dễ thương, có họa tiết bông hoa và viền ren tinh tế. Chất liệu satin mềm mại, phù hợp cho các buổi tiệc hoặc dịp lễ hội. Đầm có dây đai phía sau để điều chỉnh vừa vặn với cơ thể bé.\nChất liệu: Satin, Ren', 30, 'img/quanao/5.jpg', 'S,M,L', 'red,white', NULL, 3, NULL, 'girl'),
(6, 'Set Áo Bằng Vải Flannel và Quần Dài Cho Bé', 100000.00, 'Mô tả: Bộ set gồm áo flannel sọc và quần dài ấm áp. Chất liệu vải flannel mềm mại, ấm áp, rất thích hợp cho mùa đông. Áo có cổ và khuy cài tiện lợi.\nChất liệu: Flannel, Cotton', 15, 'img/quanao/6.jpg\n', 'S,M,L,XL', '', NULL, 3, NULL, NULL),
(7, 'Áo sweater cho bé gái với Hình Hello Kitty', 199000.00, 'Mô tả: Áo sweater xinh xắn cho bé gái. Chất liệu cotton mềm mại, thoáng mát, giúp bé thoải mái vận động và chơi đùa.\nChất liệu: nỉ', 20, 'img/quanao/7.jpg', 'S,M,L', 'pastel', NULL, 3, NULL, 'girl'),
(8, 'Bộ Set áo sọc tay dài Và Quần Kaki Cho Bé', 250000.00, 'Mô tả: Bộ set bao gồm áo tay dài và quần kaki cho bé trai. Áo sơ mi có cổ, thiết kế họa tiết kẻ sọc nhẹ nhàng, kết hợp với quần kaki màu be tạo nên sự lịch sự và thời trang. Bộ đồ này phù hợp cho các buổi tiệc hoặc sự kiện đặc biệt.\nChất liệu: Cotton, Kaki', 20, 'img/quanao/8.jpg', 'S,M,L,XL', NULL, NULL, 3, NULL, 'boy'),
(9, 'Set đồ tết cho bé gái', 129000.00, 'Đồ Tết cho em bé được thiết kế dễ thương, trang nhã và mang đậm không khí Tết cổ truyền của người Việt.', 10, 'img/quanao/9.jpg', 'S,M,L,XL', 'red', NULL, 3, NULL, 'girl'),
(10, 'Bộ áo thun và quần lưng cao cho bé gái', 99000.00, 'Mô tả: Bộ áo thun dài tay có hình họa tiết vui nhộn như hoa đào, chim én, kết hợp với quần lưng cao hoặc quần short thoải mái.\nPhù hợp: Bé gái từ 2-5 tuổi.\nChất liệu: Cotton, vải thun.', 12, 'img/quanao/10.jpg', 'S,M,L', 'blue', NULL, 3, NULL, 'girl'),
(11, 'Quần jean ống rộng nam kèm với áo thun hình ngôi sao 4 cánh', 350000.00, 'Mô tả: Bộ đồ bao gồm áo thun kết hợp với quần chinos hoặc quần jean. Bộ trang phục này mang lại sự thoải mái và năng động, phù hợp với các buổi gặp gỡ bạn bè hoặc gia đình trong những ngày Tết.\nPhù hợp: Nam giới từ 20-40 tuổi.\nChất liệu: Jean, cotton, vải thun.', 20, 'img/quanao/11.jpg', 'M,L,XL,XXL', NULL, NULL, 1, 'quan', NULL),
(12, 'Quần kaki be kết hợp áo thun sọc hai màu ', 250000.00, 'Mô tả: Áo thun nam - Đơn giản nhưng đầy phong cách: Áo thun nam với 2 màu sắc và kiểu dáng đa dạng, giúp bạn dễ dàng phối đồ cho mọi hoàn cảnh. Vải cotton thoáng khí, mang lại sự dễ chịu cho bạn trong mọi hoạt động hàng ngày.Kết hợp với quần jeans nam, với chất liệu denim cao cấp, sẽ là lựa chọn hoàn hảo cho mọi phong cách từ trẻ trung đến cổ điển', 20, 'img/quanao/12.jpg', 'M,L,XL,XXL,XXXL', NULL, NULL, 1, 'quan', NULL),
(13, 'Quần kaki be kết hợp áo thun basic ', 215000.00, 'Mô tả: Áo thun nam - Đơn giản nhưng đầy phong cách: Áo thun nam với 2 màu sắc và kiểu dáng đa dạng, giúp bạn dễ dàng phối đồ cho mọi hoàn cảnh. Vải cotton thoáng khí, mang lại sự dễ chịu cho bạn trong mọi hoạt động hàng ngày.Kết hợp với quần jeans nam, với chất liệu denim cao cấp, sẽ là lựa chọn hoàn hảo cho mọi phong cách từ trẻ trung đến cổ điển', 30, 'img/quanao/13.jpg', 'L,XL,XXL', 'black', NULL, 1, 'quan', NULL),
(14, 'Áo teelab xương khủng long kết hợp quần jean ống rộng', 299000.00, 'Mô tả: Áo thun nam - Đơn giản nhưng đầy phong cách: Áo thun nam với 2 màu sắc và kiểu dáng đa dạng, giúp bạn dễ dàng phối đồ cho mọi hoàn cảnh. Vải cotton thoáng khí, mang lại sự dễ chịu cho bạn trong mọi hoạt động hàng ngày.Kết hợp với quần jeans nam, với chất liệu denim cao cấp, sẽ là lựa chọn hoàn hảo cho mọi phong cách từ trẻ trung đến cổ điển', 40, 'img/quanao/14.jpg', 'M,L,XL', 'pastel, jean', NULL, 1, 'quan', NULL),
(15, 'Áo khoác NowSaiGon', 500000.00, 'Mô tả:Áo khoác nam - Đẹp mắt và ấm áp: \"Khám phá bộ sưu tập áo khoác nam mùa đông, với thiết kế hiện đại và chất liệu giữ ấm vượt trội. Dù là áo khoác da hay áo khoác bomber, bạn vẫn luôn giữ phong cách thời thượng và sành điệu.\"', 30, 'img/quanao/15.jpg', 'M,L,XL,XXL', 'Black', NULL, 1, NULL, NULL),
(16, 'Áo polo thể thao số 24', 200000.00, 'Mô tả:Chất liệu vải thấm hút mồ hôi, giúp bạn luôn cảm thấy khô ráo và thoải mái trong suốt cả ngày. Với màu sắc đa dạng, từ truyền thống đến hiện đại, áo polo là sự lựa chọn lý tưởng cho những ai yêu thích phong cách thể thao nhưng không kém phần lịch sự.', 20, 'img/quanao/16.jpg', 'M,L,XL', 'grey,black', NULL, 1, 'the-thao', NULL),
(17, 'Áo thun SWE  sọc liền kề tạo nét độc lạ', 200000.00, 'Mô tả:Chất liệu vải thấm hút mồ hôi, giúp bạn luôn cảm thấy khô ráo và thoải mái trong suốt cả ngày. Với màu sắc đa dạng, từ truyền thống đến hiện đại, áo thun là sự lựa chọn lý tưởng cho những ai yêu thích phong cách đơn giản nhưng không kém phần lịch sự.', 20, 'img/quanao/17.jpg', 'M,L,XL', 'grey,black', NULL, 1, 'ao-thun', NULL),
(18, 'Áo polo basic hai màu SWE', 200000.00, 'Mô tả:Chất liệu vải thấm hút mồ hôi, giúp bạn luôn cảm thấy khô ráo và thoải mái trong suốt cả ngày. Với màu sắc trắng đen, từ truyền thống đến hiện đại, áo polo là sự lựa chọn lý tưởng cho những ai yêu thích phong cách nhưng không kém phần lịch sự.', 20, 'img/quanao/18.jpg', 'M,L,XL', 'grey,black', NULL, 1, NULL, NULL),
(19, 'Áo thun nam DirtyCoin và quần jean (kèm túi)', 400000.00, 'Mô tả:Áo thun nam - Đơn giản nhưng đầy phong cách: \"Áo thun nam với nhiều màu sắc và kiểu dáng đa dạng, giúp bạn dễ dàng phối đồ cho mọi hoàn cảnh. Vải cotton thoáng khí, mang lại sự dễ chịu cho bạn trong mọi hoạt động hàng ngày. Quần jeans nam, với chất liệu denim cao cấp, sẽ là lựa chọn hoàn hảo cho mọi phong cách từ trẻ trung đến cổ điển. Dễ dàng phối hợp với áo thun, sơ mi hay áo khoác để tạo nên vẻ ngoài ấn tượng.', 20, 'img/quanao/19.jpg', 'M,L,XL', 'grey,black', NULL, 1, 'quan', NULL),
(20, 'Áo thun nam DirtyCoin và quần sọt ', 320000.00, 'Mô tả: Áo thun nam với nhiều màu sắc và kiểu dáng đa dạng, giúp bạn dễ dàng phối đồ cho mọi hoàn cảnh. Vải cotton thoáng khí, mang lại sự dễ chịu cho bạn trong mọi hoạt động hàng ngày. Quần kaki ngắn, với chất liệu cao cấp, sẽ là lựa chọn hoàn hảo cho mọi phong cách từ trẻ trung đến cổ điển. Dễ dàng phối hợp với áo thun để tạo nên vẻ ngoài ấn tượng.', 20, 'img/quanao/20.jpg', 'M,L,XL', 'red, white', NULL, 1, 'quan', NULL),
(21, 'Sơ Mi Dài Tay Nữ - Phong cách thanh lịch, dễ dàng kết hợp, thanh thoát.', 120000.00, 'Mô tả:Sơ mi dài tay nữ là lựa chọn lý tưởng cho những buổi làm việc hoặc gặp gỡ khách hàng. Vải mềm mại, không nhăn, dễ dàng kết hợp với quần tây hoặc chân váy, tạo nên vẻ ngoài thanh lịch và tinh tế', 20, 'img/quanao/21.jpg', 'S,M,L', NULL, NULL, 2, NULL, NULL),
(22, 'Set Đồ Nữ - Tự tin và phong cách', 220000.00, 'Mô tả:\"Set đồ nữ gồm áo và chân váy(quần giả váy) đồng bộ, mang đến vẻ ngoài thời thượng và hài hòa. Chất liệu vải mềm mịn và thoải mái, giúp bạn luôn tự tin và năng động khi đi làm, đi chơi hoặc dự tiệc.\"', 20, 'img/quanao/22.jpg', 'S,M,L', NULL, NULL, 2, NULL, NULL),
(23, 'Set Đồ Nữ Dạo Phố - Thoải mái và phong cách', 250000.00, 'Mô tả:\"Set đồ nữ gồm áo kiểu mẫu tay dài có kèm cột cổ và chân váy(quần giả váy) đồng bộ, mang đến vẻ ngoài thời thượng và hài hòa. Chất liệu vải mềm mịn và thoải mái, giúp bạn luôn tự tin và năng động khi đi làm, đi chơi hoặc dự tiệc.\"', 20, 'img/quanao/23.jpg', 'S,M,L', NULL, NULL, 2, 'ao', NULL),
(24, 'Set đồ nữ áo hai dây kèm áo ngoài đi kèm chân váy(tặng túi đi kèm)', 380000.00, 'Mô tả:\"Set đồ nữ dạo phố với thiết kế trẻ trung, phù hợp cho các buổi đi chơi hay gặp gỡ bạn bè. Sự kết hợp giữa áo phông và quần short hoặc chân váy giúp bạn luôn thoải mái nhưng vẫn giữ được vẻ ngoài sành điệu', 20, 'img/quanao/24.jpg', 'S,M,L', NULL, NULL, 2, 'chan-vay', NULL),
(25, 'Áo sơ mi nữ-kèm chân váy', 250000.00, 'Mô tả:Set đồ công sở nữ bao gồm áo sơ mi kết hợp với chân váy tạo nên một bộ trang phục lịch sự và hiện đại. Dễ dàng mix match để tạo nên vẻ ngoài vừa chuyên nghiệp vừa thời trang.', 20, 'img/quanao/25.jpg', 'S,M,L', NULL, NULL, 2, 'chan-vay', NULL),
(26, 'Áo thun nữ phong cách trẻ trung năng động- Quần short', 200000.00, 'Mô tả:Áo thun nữ với kiểu dáng đơn giản, dễ phối đồ là sự lựa chọn hoàn hảo cho những ngày dạo phố hay đi học. Chất liệu vải cotton thoáng mát, thấm hút mồ hôi giúp bạn luôn thoải mái suốt cả ngày dài.', 20, 'img/quanao/26.jpg', 'XS,S,M,L', NULL, NULL, 2, 'ao', NULL),
(27, 'Áo thun oversize nữ hình cái bánh- Quần short đi kèm', 220000.00, 'Mô tả:\"Áo thun oversize nữ mang đến vẻ ngoài phóng khoáng và thời trang. Phối cùng quần jeans hoặc chân váy, bạn sẽ dễ dàng tạo ra phong cách street style năng động và cá tính.', 20, 'img/quanao/27.jpg', 'S,M,L', NULL, NULL, 2, 'ao', NULL),
(28, 'Áo BabyTee ôm sát người-Quần dài ống rộng', 260000.00, 'Mô tả:\"Áo babytee nữ mang đến vẻ ngoài phóng khoáng và thời trang. Phối cùng quần jeans trắng, bạn sẽ dễ dàng tạo ra phong cách street style năng động và cá tính.', 20, 'img/quanao/28.jpg', 'XS,S,M,L', NULL, NULL, 2, 'ao', NULL),
(29, 'Áo Sơ mi croptop -Chân váy tennis', 210000.00, 'Mô tả:\"Sơ mi dài tay nữ là lựa chọn lý tưởng cho những buổi làm việc hoặc gặp gỡ khách hàng. Vải mềm mại, không nhăn, dễ dàng kết hợp chân váy tennis, tạo nên vẻ ngoài thanh lịch và tinh tế.', 20, 'img/quanao/29.jpg', 'S,M,L', NULL, NULL, 2, 'chan-vay', NULL),
(30, 'Áo thun oversize the weird', 190000.00, 'Mô tả: Áo thun oversize nữ mang đến vẻ ngoài phóng khoáng và thời trang. Phối cùng quần jeans hoặc chân váy, bạn sẽ dễ dàng tạo ra phong cách street style năng động và cá tính', 20, 'img/quanao/30.jpg', 'S,M,L', NULL, NULL, 2, 'ao', NULL),
(31, 'Đầm Nữ - Lãng mạn và thanh lịch', 200000.00, 'Với thiết kế nữ tính và chất liệu vải mềm mại, đầm nữ là lựa chọn hoàn hảo cho những dịp tiệc tùng hay dạo phố. Những chi tiết như ren, nơ hay xếp ly sẽ giúp bạn nổi bật và tự tin hơn mỗi khi ra ngoài', 30, 'img/quanao/31.jpg', 'S,M,L,XL', 'red', NULL, 2, 'dam', NULL),
(32, 'Áo thể thao nam-Dễ dàng vận động, đầy phong cách', 200000.00, 'Áo thun thể thao nam với chất liệu co giãn tốt, giúp bạn thoải mái trong mọi động tác thể thao. Họa tiết đơn giản, màu sắc tươi sáng, là món đồ không thể thiếu trong tủ đồ của những tín đồ thể thao', 30, 'img/quanao/32.jpg', 'M,L,XL', 'red', NULL, 1, 'the-thao', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `rating` int NOT NULL,
  `review_text` text,
  `review_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` text,
  `permissions` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`, `permissions`) VALUES
(1, 'user', 'haha', 'products,categories,orders,users'),
(2, 'manager-product', NULL, 'products,categories'),
(5, 'manager-order', NULL, 'users');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

DROP TABLE IF EXISTS `shipping`;
CREATE TABLE IF NOT EXISTS `shipping` (
  `shipping_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `shipping_method` enum('standard','express','free') DEFAULT 'standard',
  `shipping_cost` decimal(10,2) DEFAULT NULL,
  `estimated_delivery_time` datetime DEFAULT NULL,
  PRIMARY KEY (`shipping_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role_id` int DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role_id`, `status`) VALUES
(1, 'haha', '123', 'haha@gmail.com', 1, 'active');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account`
--
ALTER TABLE `account`
  ADD CONSTRAINT `account_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `billdetails`
--
ALTER TABLE `billdetails`
  ADD CONSTRAINT `billdetails_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`bill_id`),
  ADD CONSTRAINT `billdetails_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
