-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2025 at 08:57 PM
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
-- Database: `petshop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `adoption`
--

CREATE TABLE `adoption` (
  `adoptID` int(11) NOT NULL,
  `petID` int(11) NOT NULL,
  `userID` int(10) UNSIGNED NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `contactNumber` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `payConfirm` tinyint(1) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `petID` int(11) NOT NULL,
  `UserID` int(11) UNSIGNED NOT NULL,
  `petName` varchar(100) NOT NULL,
  `animalType` varchar(50) NOT NULL,
  `petBreed` varchar(100) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `age` int(11) NOT NULL,
  `birthday` date NOT NULL,
  `size` varchar(10) NOT NULL,
  `weight` int(11) NOT NULL,
  `color` varchar(50) NOT NULL,
  `temperament` varchar(200) NOT NULL,
  `goodwith` varchar(200) NOT NULL,
  `health` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`petID`, `UserID`, `petName`, `animalType`, `petBreed`, `gender`, `age`, `birthday`, `size`, `weight`, `color`, `temperament`, `goodwith`, `health`, `description`, `status`, `image`) VALUES
(11, 10, 'Max', 'dog', 'Golden Retriever', 'male', 2, '2023-10-04', 'large', 70, 'Golden', 'Friendly, Energetic, Playful', 'Kids, Dogs, Cats', 'Vaccinated, Neutered, Microchipped', 'Max is a friendly and energetic golden retriever who loves playing fetch and going for long walks. He\'s great with children and other dogs, and has been trained in basic obedience commands. Max is looking for an active family who can provide him with plenty of exercise and attention.\r\n\r\nHe\'s up to date on all vaccinations, neutered, and microchipped. Max would do best in a home with a fenced yard where he can run and play safely.', 'Available', '1764758392-max.jpg'),
(12, 10, 'Luna', 'cat', 'Siamese', 'female', 3, '2022-06-16', 'average', 9, 'Cream', 'Affectionate, intelligent, playful, and highly people-oriented.', 'Adults, calm children, other cats; may be cautious with dogs.', 'Up-to-date on vaccines, dewormed, no known health conditions; slightly sensitive stomach.', 'Luna is a graceful Siamese with a curious and loving personality. She loves following people around, chatting with soft meows, and curling up in warm spots. Luna enjoys gentle playtime and lots of attention. She\'s a wonderful companion for a quiet home that appreciates an affectionate, talkative cat.', 'Available', '1764758899-luna.jpg'),
(13, 10, 'Coco', 'rabbit', 'Holland Lop', 'male', 1, '2024-09-14', 'small', 3, 'Cream', 'Gentle, calm, sweet-natured, enjoys petting, quiet.', 'Children, first-time rabbit owners, calm dogs, other rabbits.', 'Healthy, regular teeth checks recommended (common for lop breeds). Eats hay well.', 'Coco is a soft and cuddly Holland Lop who loves calm environments. She enjoys gentle brushing, exploring cozy spaces, and relaxing near her favorite humans. Coco is easy to handle and ideal for families who want a sweet, low-energy rabbit companion.', 'Available', '1764759074-coco.jpg'),
(14, 10, 'Charlie', 'dog', 'Beagle', 'male', 4, '2021-10-03', 'average', 14, 'Black, Brown, White', 'Friendly, energetic, curious, loyal, and social.', 'Kids, other dogs, active families; may chase small animals due to scent-hound instincts.', 'Healthy, vaccinated; ears require regular cleaning (prone to ear infections).', 'Charlie is a cheerful Beagle who loves outdoor adventures, sniffing trails, and playing with anyone who wants to join in. He’s affectionate, loves belly rubs, and enjoys time with people. Charlie is an excellent match for active households that enjoy walks and outdoor play.', 'Available', '1764759353-charlie.jpg'),
(15, 10, 'Oliver', 'cat', 'Maine Coon', 'male', 5, '2020-10-23', 'large', 19, 'Brown, Black', 'Gentle, relaxed, affectionate, playful, intelligent, “gentle giant.”', 'Kids, cats, cat-friendly dogs, families of all sizes.', 'Healthy; routine checks recommended for hips and heart (common for the breed).', 'Oliver is a majestic Maine Coon with a soft disposition and a playful spark. He loves being around people, enjoys interactive toys, and gets along well with other pets. Despite his large size, he’s very gentle and affectionate. Oliver is perfect for families wanting a friendly, social, and low-stress cat.', 'Available', '1764759534-oliver.jpg'),
(16, 10, 'Kiwi', 'bird', 'Parakeet', 'male', 1, '2025-06-05', 'small', 0, 'Green', 'Cheerful, social, vocal, alert, curious.', 'Other parakeets, older kids, calm households.', 'Healthy, active; wings and nails recently trimmed.', 'Kiwi is a bright and lively parakeet who loves chirping, exploring toys, and interacting with gentle voices. He enjoys a stimulating environment with mirrors, swings, and perches. Kiwi thrives with attention and is happiest when he can hear or see his human family nearby.', 'Available', '1764759743-kiwi.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(10) UNSIGNED NOT NULL,
  `DisplayName` varchar(255) NOT NULL,
  `UserName` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `DisplayName`, `UserName`, `Password`) VALUES
(1, 'Gian Vincent Tan', 'gianvincentt524', '$2y$10$BjK1nlKNOChgPHtgoY6SieV0rnjnD2pSACu2wIJteNtNqwZX4OdlC'),
(5, 'Nami ', 'buhaykoaymasaya456', '$2y$10$/jlQiBUz.5Qe02lNtaraieLK/glv/dTIWPIJbTUHzQWFfkd6nR1hG'),
(6, 'Gian Vincent Tan', 'gianvincentt5245', '$2y$10$qqVn8iTj6oPn7D8DwK2luOdrvCYy/7T4GtiQgjgKdhr1JuBEhsysC'),
(10, 'Administrator', 'Admin', '$2y$10$lIbmeKhhiegr6oZNAx.e2ux7ALfoGQFELYIPzqK5dh150w1C7Ul2K'),
(11, 'Don Christoper Abalos', 'InsanePaste', '$2y$10$Z4gMjwZlzyTbsq8JuHw0OeBiQqkG6Zbyh8iv.J6/8y2yYhOARTeYG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adoption`
--
ALTER TABLE `adoption`
  ADD PRIMARY KEY (`adoptID`),
  ADD KEY `petID` (`petID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`petID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `UserName` (`UserName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adoption`
--
ALTER TABLE `adoption`
  MODIFY `adoptID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `petID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adoption`
--
ALTER TABLE `adoption`
  ADD CONSTRAINT `adoption_ibfk_1` FOREIGN KEY (`petID`) REFERENCES `pets` (`petID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adoption_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
