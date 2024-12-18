SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `project`
--

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
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;


