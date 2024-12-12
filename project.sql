-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2024 at 06:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `quantity` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `name`, `price`, `image`, `quantity`) VALUES
(146, 0, 'Double Spring Waist Trimer | Highly Elastic Steel Double ', '749', 'double spring tummy trimmer.jpg', 1),
(147, 0, 'Yoga Ball 75 Cm Exercise Ball With Pump', '699', 'yoga ball.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

CREATE TABLE `membership` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `plan` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership`
--

INSERT INTO `membership` (`id`, `email`, `plan`, `created_at`, `expiry_date`) VALUES
(17, 'enowosh@gmail.com', 'Standard Plan', '2024-06-30 12:32:14', '2024-09-30 12:32:14');

--
-- Triggers `membership`
--
DELIMITER $$
CREATE TRIGGER `set_expiry_date` BEFORE INSERT ON `membership` FOR EACH ROW BEGIN
    SET NEW.expiry_date = ADDDATE(NEW.created_at, INTERVAL 3 MONTH);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `membership_plans`
--

CREATE TABLE `membership_plans` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `features` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership_plans`
--

INSERT INTO `membership_plans` (`id`, `name`, `price`, `features`) VALUES
(1, 'Basic Plan', 999.00, 'Smart workout plan\nAt home workouts'),
(2, 'Standard Plan', 2999.00, 'PRO Gyms\nSmart workout plan\nAt home workouts'),
(3, 'Premium Plan', 4999.00, 'ELITE Gyms & Classes\nPRO Gyms\nSmart workout plan\nAt home workouts\nPersonal Training');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `Quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `Quantity`) VALUES
(1, 'Yoga Meditation Mat For Unisex 6mm Thick', '699', 'yoga mat.jpg', 90),
(2, 'Yoga Ball 75 Cm Exercise Ball With Pump', '699', 'yoga ball.jpg', 50),
(3, 'Double Spring Waist Trimer | Highly Elastic Steel Double ', '749', 'double spring tummy trimmer.jpg', 75),
(4, 'RUBX Rubber Hex 2.5Kg Dumbbell Set | Rubber Coated Dumbbell-1 Pair', '1999', 'dumbell.jpg', 100),
(5, 'Black Weight Lifting Fitness Gym Gloves For Men And Women', '549.99', 'fitness gloves.jpg', 90),
(6, 'Strength Training Hand Grip Strengthener | Adjustable Hand Grip 10-40 Kg', '4.49', 'hand grip.jpg', 90),
(7, 'Spn Weight Bearing Jump Rope Skipping Rope Adjustable Sport ', '420', 'jump rope.jpg', 99),
(8, 'Muscleblaze Raw Whey Protein Powder 1Kg 33 Servings 100% 24g Protein 5.2g', '799', 'protein powder.jpg', 80),
(9, 'Stress Ball - Happy Smile Face Squishes Toys Stress Foam Balls for Soft Play', '399', 'stress ball.jpg', 78);

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `id` int(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(90) NOT NULL,
  `password` varchar(20) NOT NULL,
  `age` int(100) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `weight` int(50) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `name`, `email`, `password`, `age`, `gender`, `weight`, `phone`, `address`) VALUES
(34, 'enowosh', 'enowosh@gmail.com', 'Enowosh#1234', 21, 'male', 56, '981000000', 'shankharapur-5'),
(35, 'Admin', 'admin@gmail.com', 'Admin@1234', 25, 'male', 45, '988888888', 'shankharapur-5 palubari');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `membership`
--
ALTER TABLE `membership`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `membership_plans`
--
ALTER TABLE `membership_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD UNIQUE KEY `id_6` (`id`),
  ADD UNIQUE KEY `id_8` (`id`),
  ADD KEY `id_2` (`id`),
  ADD KEY `id_3` (`id`),
  ADD KEY `id_4` (`id`),
  ADD KEY `id_5` (`id`),
  ADD KEY `id_7` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `membership`
--
ALTER TABLE `membership`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `membership_plans`
--
ALTER TABLE `membership_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
