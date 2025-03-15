-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 15, 2025 at 09:35 AM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `collection_creator`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbtables`
--

CREATE TABLE `dbtables` (
  `did` int(11) NOT NULL,
  `fname` varchar(200) DEFAULT NULL,
  `database_description` varchar(250) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dbtables`
--

INSERT INTO `dbtables` (`did`, `fname`, `database_description`, `user_id`) VALUES
(1, 'new_collection', 'My First Databse', 1),
(2, 'sample_database', 'this is a sample database', 0),
(3, 'main_database', 'main database', 1),
(4, 'soil_data', 'this table have soil data', 2);

-- --------------------------------------------------------

--
-- Table structure for table `maintablename`
--

CREATE TABLE `maintablename` (
  `tid` int(11) NOT NULL,
  `database_name` varchar(50) NOT NULL,
  `tname` varchar(200) DEFAULT NULL,
  `table_fields` varchar(300) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `maintablename`
--

INSERT INTO `maintablename` (`tid`, `database_name`, `tname`, `table_fields`, `user_id`) VALUES
(1, 'new_collection', 'collection one', '`student_name` VARCHAR(200), `student_age` INT(11)', 0),
(2, 'new_collection', 'collection_new', '`student_name` VARCHAR(200), `student_age` VARCHAR(11)', 1),
(3, 'new_collection', 's', '`student_name` VARCHAR(200), `student_age` INT(11)', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `useremail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `useremail`, `password`) VALUES
(1, 'sahan', 'test@gmail.com', '$2y$10$Bv.cayBlW3PuJShH.vLY3OWoEN8Wtnb4P67hQ7QQPm9r2V.fbHc9i'),
(2, 'sahan', 'test123@gmail.com', '$2y$10$Z8Nu56fbeP0Y90dj05BHlOYutTV18GdA8seuKgDe8egW/5oJHO1NS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbtables`
--
ALTER TABLE `dbtables`
  ADD PRIMARY KEY (`did`);

--
-- Indexes for table `maintablename`
--
ALTER TABLE `maintablename`
  ADD PRIMARY KEY (`tid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbtables`
--
ALTER TABLE `dbtables`
  MODIFY `did` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `maintablename`
--
ALTER TABLE `maintablename`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
