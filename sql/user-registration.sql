-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2024 at 10:01 PM
-- Wersja serwera: 10.4.28-MariaDB
-- Wersja PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `user-registration`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `gniazdka`
--

CREATE TABLE `gniazdka` (
  `id` int(11) NOT NULL,
  `name` varchar(90) DEFAULT NULL,
  `description` varchar(90) DEFAULT NULL,
  `properties` varchar(90) DEFAULT NULL,
  `state` tinyint(1) DEFAULT NULL,
  `ListaPomieszczen_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `gniazdka`
--

INSERT INTO `gniazdka` (`id`, `name`, `description`, `properties`, `state`, `ListaPomieszczen_id`) VALUES
(0, 'nazwa', 'opsiek', 'wlasnosci', 0, 0),
(1, 'essa', 'essa2', 'essa3', 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `historiauzytkowania`
--

CREATE TABLE `historiauzytkowania` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `name` varchar(90) DEFAULT NULL,
  `Gniazdka_id` int(11) NOT NULL,
  `Data` datetime DEFAULT NULL,
  `tbl_member_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `listapomieszczen`
--

CREATE TABLE `listapomieszczen` (
  `id` int(11) NOT NULL,
  `name` varchar(90) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `listapomieszczen`
--

INSERT INTO `listapomieszczen` (`id`, `name`) VALUES
(0, 'Gościnny');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tbl_member`
--

CREATE TABLE `tbl_member` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `password` varchar(200) NOT NULL,
  `email` varchar(255) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `apartment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_member`
--

INSERT INTO `tbl_member` (`id`, `username`, `first_name`, `last_name`, `password`, `email`, `create_at`, `role`, `apartment_id`) VALUES
(1, 'abc', NULL, NULL, '$2y$10$jdyBFU0/BUNFiVYOJRpRbuI9cZhDdNcHb2Ojs6HqlZ5N/tFkRsDBS', 'abc@abc.com', '2024-04-17 14:59:03', 'user', NULL),
(2, 'mac', NULL, NULL, '$2y$10$Kofi2h49DqF5iy49lKSIFOj/utvUa0zk.uxYBqlNeR7hFeH7m2W2O', 'm@m.c', '2024-04-17 14:59:45', 'admin', NULL);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `gniazdka`
--
ALTER TABLE `gniazdka`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Gniazdka_ListaPomieszczen_idx` (`ListaPomieszczen_id`);

--
-- Indeksy dla tabeli `historiauzytkowania`
--
ALTER TABLE `historiauzytkowania`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_HistoriaUzytkowania_Gniazdka1_idx` (`Gniazdka_id`),
  ADD KEY `fk_HistoriaUzytkowania_tbl_member1_idx` (`tbl_member_id`);

--
-- Indeksy dla tabeli `listapomieszczen`
--
ALTER TABLE `listapomieszczen`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `tbl_member`
--
ALTER TABLE `tbl_member`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_member`
--
ALTER TABLE `tbl_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gniazdka`
--
ALTER TABLE `gniazdka`
  ADD CONSTRAINT `fk_Gniazdka_ListaPomieszczen` FOREIGN KEY (`ListaPomieszczen_id`) REFERENCES `listapomieszczen` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `historiauzytkowania`
--
ALTER TABLE `historiauzytkowania`
  ADD CONSTRAINT `fk_HistoriaUzytkowania_Gniazdka1` FOREIGN KEY (`Gniazdka_id`) REFERENCES `gniazdka` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_HistoriaUzytkowania_tbl_member1` FOREIGN KEY (`tbl_member_id`) REFERENCES `tbl_member` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
