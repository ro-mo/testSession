-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 20, 2025 at 09:23 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `domanda`
--

CREATE TABLE `domanda` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `testo` text NOT NULL,
  `tipo` enum('multipla','libera') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `domanda`
--

INSERT INTO `domanda` (`id`, `test_id`, `testo`, `tipo`) VALUES
(20, 8, '2 + 2?', 'multipla'),
(25, 12, '4 + 4?', 'multipla'),
(26, 12, '2 + 2?', 'multipla'),
(27, 9, '2 + 2?', 'multipla'),
(30, 12, '4 + 4?', 'multipla');

-- --------------------------------------------------------

--
-- Table structure for table `risposta`
--

CREATE TABLE `risposta` (
  `id` int(11) NOT NULL,
  `domanda_id` int(11) NOT NULL,
  `testo` text NOT NULL,
  `corretta` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risposta`
--

INSERT INTO `risposta` (`id`, `domanda_id`, `testo`, `corretta`) VALUES
(39, 20, '4', 0),
(40, 20, '6', 0),
(49, 25, '8', 1),
(50, 25, '4', 0),
(51, 26, '4', 1),
(52, 26, '1', 0),
(53, 27, '4', 1),
(54, 27, '1', 0),
(59, 30, '8', 0),
(60, 30, '1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `risposta_utente`
--

CREATE TABLE `risposta_utente` (
  `id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `domanda_id` int(11) NOT NULL,
  `risposta_id` int(11) DEFAULT NULL,
  `testo_libero` text DEFAULT NULL,
  `data_risposta` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risposta_utente`
--

INSERT INTO `risposta_utente` (`id`, `utente_id`, `domanda_id`, `risposta_id`, `testo_libero`, `data_risposta`) VALUES
(13, 6, 25, 49, NULL, '2025-01-19 21:41:50'),
(14, 6, 26, 51, NULL, '2025-01-19 21:41:50');

-- --------------------------------------------------------

--
-- Table structure for table `svolgimento_test`
--

CREATE TABLE `svolgimento_test` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `data_inizio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svolgimento_test`
--

INSERT INTO `svolgimento_test` (`id`, `test_id`, `utente_id`, `data_inizio`) VALUES
(10, 8, 6, '2025-01-13 09:09:28'),
(11, 12, 6, '2025-01-19 21:41:44'),
(12, 9, 6, '2025-01-19 21:43:15'),
(13, 9, 8, '2025-01-19 21:46:41'),
(14, 12, 8, '2025-01-19 21:53:53');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `titolo` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `creatore` varchar(100) NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `classe` varchar(100) DEFAULT NULL,
  `visibile` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `titolo`, `descrizione`, `creatore`, `data_creazione`, `classe`, `visibile`) VALUES
(8, 'prova', 'informatica', 'fabio', '2025-01-13 08:21:45', '5AII', 1),
(9, 'quiz', 'quiz', 'fabio', '2025-01-19 20:36:18', '5AII', 1),
(12, 'prova2', 'prova', 'fabio', '2025-01-19 21:23:38', '5AII', 1);

-- --------------------------------------------------------

--
-- Table structure for table `utente`
--

CREATE TABLE `utente` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `login` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL DEFAULT 'password',
  `tipo` enum('docente','studente') NOT NULL DEFAULT 'studente',
  `classe` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utente`
--

INSERT INTO `utente` (`id`, `nome`, `cognome`, `login`, `password`, `tipo`, `classe`) VALUES
(6, 'edo', 'mene', 'edo', '$2y$10$aDqLunVJHWAycmaIXN6nj.wL9gu1dG9EAMAOfAhnS3ZClGXlMNiqm', 'studente', '5AII'),
(7, 'fabio', 'biscaro', 'fabio', '$2y$10$gyHBTAvb4IsVY0Gu3jYG9.6MdG6CVTc7taUxKMcAmzzOtjpF05U22', 'docente', ''),
(8, 'Matteo', 'De Luca', 'matte', '$2y$10$JK872wkY1CdEClGv3vZi9.LXER..lnnODlHhylR/L3JW3B911QHLC', 'studente', '5AII');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `domanda`
--
ALTER TABLE `domanda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `risposta`
--
ALTER TABLE `risposta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `domanda_id` (`domanda_id`);

--
-- Indexes for table `risposta_utente`
--
ALTER TABLE `risposta_utente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utente_id` (`utente_id`),
  ADD KEY `domanda_id` (`domanda_id`),
  ADD KEY `risposta_id` (`risposta_id`);

--
-- Indexes for table `svolgimento_test`
--
ALTER TABLE `svolgimento_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `utente_id` (`utente_id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creatore` (`creatore`);

--
-- Indexes for table `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_login_utente` (`login`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `domanda`
--
ALTER TABLE `domanda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `risposta`
--
ALTER TABLE `risposta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `risposta_utente`
--
ALTER TABLE `risposta_utente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `svolgimento_test`
--
ALTER TABLE `svolgimento_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `utente`
--
ALTER TABLE `utente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `domanda`
--
ALTER TABLE `domanda`
  ADD CONSTRAINT `domanda_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `test` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `risposta`
--
ALTER TABLE `risposta`
  ADD CONSTRAINT `risposta_ibfk_1` FOREIGN KEY (`domanda_id`) REFERENCES `domanda` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `risposta_utente`
--
ALTER TABLE `risposta_utente`
  ADD CONSTRAINT `risposta_utente_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `utente` (`id`),
  ADD CONSTRAINT `risposta_utente_ibfk_2` FOREIGN KEY (`domanda_id`) REFERENCES `domanda` (`id`),
  ADD CONSTRAINT `risposta_utente_ibfk_3` FOREIGN KEY (`risposta_id`) REFERENCES `risposta` (`id`);

--
-- Constraints for table `svolgimento_test`
--
ALTER TABLE `svolgimento_test`
  ADD CONSTRAINT `svolgimento_test_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `test` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `svolgimento_test_ibfk_2` FOREIGN KEY (`utente_id`) REFERENCES `utente` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_ibfk_1` FOREIGN KEY (`creatore`) REFERENCES `utente` (`login`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
