-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 26, 2025 at 02:25 PM
-- Server version: 8.0.42-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pkstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategorie`
--

CREATE TABLE `kategorie` (
  `id` int NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `zdjecie` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategorie`
--

INSERT INTO `kategorie` (`id`, `nazwa`, `zdjecie`) VALUES
(1, 'Motoryzacja', 'motoryzacja'),
(2, 'Dom i ogród', 'dom_i_ogrod'),
(3, 'Akcesoria', 'akcesoria'),
(4, 'Zabawki', 'zabawki'),
(5, 'Sprzęt elektroniczny', 'sprzet_elektro'),
(6, 'Żywność', 'zywnosc'),
(7, 'Sport i turystyka', 'sport_turystyka'),
(8, 'Zdrowie', 'zdrowie'),
(9, 'Kolekcje i sztuka', 'kolekcje_sztuka'),
(10, 'Kultura i rozrywka', 'kultura'),
(11, 'Nieruchomości', 'nieruchomosci');

-- --------------------------------------------------------

--
-- Table structure for table `koszyk`
--

CREATE TABLE `koszyk` (
  `id` int NOT NULL,
  `id_uzytkownika` int NOT NULL,
  `id_produktu` int NOT NULL,
  `ilosc` int DEFAULT '1',
  `data_dodania` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produkty`
--

CREATE TABLE `produkty` (
  `id` int NOT NULL,
  `nazwa` varchar(255) NOT NULL,
  `opis` text,
  `cena` decimal(10,2) NOT NULL,
  `zdjecie` varchar(255) DEFAULT NULL,
  `id_kategoria` int DEFAULT NULL,
  `tagi` varchar(255) DEFAULT NULL,
  `id_sprzedajacego` int DEFAULT NULL,
  `data_dodania` datetime DEFAULT CURRENT_TIMESTAMP,
  `data_wygasniecia` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produkty`
--

INSERT INTO `produkty` (`id`, `nazwa`, `opis`, `cena`, `zdjecie`, `id_kategoria`, `tagi`, `id_sprzedajacego`, `data_dodania`, `data_wygasniecia`) VALUES
(1, 'Opona do samochodu', 'Opona x4 do samochodu, citroen/mitshubishi tego typu samochody. Kontakt: 3891531314 fax.', 125.99, NULL, 1, NULL, 1, '2025-05-26 12:16:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `imie` varchar(100) DEFAULT NULL,
  `nazwisko` varchar(100) DEFAULT NULL,
  `rola` enum('uzytkownik','admin') DEFAULT 'uzytkownik',
  `data_rejestracji` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id`, `email`, `haslo`, `imie`, `nazwisko`, `rola`, `data_rejestracji`) VALUES
(1, 'test123@wp.pl', '$2y$10$RG/XnCGYujZY27jEZhmLIuPMZyI6r2ZMjTEB9nVoZAbCa9jbt0A5e', NULL, NULL, 'uzytkownik', '2025-05-25 21:17:10'),
(2, 'test1234@gmail.com', '$2y$10$hev.1dPQ5HvWEsdAiPQDvOlAY2ISBgPHqaRAhEMPAVOACTZis.x0m', 'Patry', 'Koc', 'uzytkownik', '2025-05-26 13:01:04'),
(3, 'test12345@wp.pl', '$2y$10$cpq00hshiPxTjtMeQdog0eUDpDBFrHKQ495NZy9jHrbvW9gNXXxIG', 'Patryk', 'Koc', 'uzytkownik', '2025-05-26 13:09:08');

-- --------------------------------------------------------

--
-- Table structure for table `zamowienia`
--

CREATE TABLE `zamowienia` (
  `id` int NOT NULL,
  `id_klienta` int DEFAULT NULL,
  `imie` varchar(100) DEFAULT NULL,
  `nazwisko` varchar(100) DEFAULT NULL,
  `adres` text,
  `email` varchar(255) DEFAULT NULL,
  `metoda_platnosci` varchar(50) DEFAULT NULL,
  `data_zamowienia` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zamowione_produkty`
--

CREATE TABLE `zamowione_produkty` (
  `id` int NOT NULL,
  `id_zamowienia` int DEFAULT NULL,
  `id_produktu` int DEFAULT NULL,
  `ilosc` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `koszyk`
--
ALTER TABLE `koszyk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`),
  ADD KEY `id_produktu` (`id_produktu`);

--
-- Indexes for table `produkty`
--
ALTER TABLE `produkty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategoria` (`id_kategoria`),
  ADD KEY `id_sprzedajacego` (`id_sprzedajacego`);
ALTER TABLE `produkty` ADD FULLTEXT KEY `nazwa` (`nazwa`,`opis`);

--
-- Indexes for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_klienta` (`id_klienta`);

--
-- Indexes for table `zamowione_produkty`
--
ALTER TABLE `zamowione_produkty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_zamowienia` (`id_zamowienia`),
  ADD KEY `id_produktu` (`id_produktu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `koszyk`
--
ALTER TABLE `koszyk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produkty`
--
ALTER TABLE `produkty`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `zamowienia`
--
ALTER TABLE `zamowienia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `zamowione_produkty`
--
ALTER TABLE `zamowione_produkty`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `koszyk`
--
ALTER TABLE `koszyk`
  ADD CONSTRAINT `koszyk_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`),
  ADD CONSTRAINT `koszyk_ibfk_2` FOREIGN KEY (`id_produktu`) REFERENCES `produkty` (`id`);

--
-- Constraints for table `produkty`
--
ALTER TABLE `produkty`
  ADD CONSTRAINT `produkty_ibfk_1` FOREIGN KEY (`id_kategoria`) REFERENCES `kategorie` (`id`),
  ADD CONSTRAINT `produkty_ibfk_2` FOREIGN KEY (`id_sprzedajacego`) REFERENCES `uzytkownicy` (`id`);

--
-- Constraints for table `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD CONSTRAINT `zamowienia_ibfk_1` FOREIGN KEY (`id_klienta`) REFERENCES `uzytkownicy` (`id`);

--
-- Constraints for table `zamowione_produkty`
--
ALTER TABLE `zamowione_produkty`
  ADD CONSTRAINT `zamowione_produkty_ibfk_1` FOREIGN KEY (`id_zamowienia`) REFERENCES `zamowienia` (`id`),
  ADD CONSTRAINT `zamowione_produkty_ibfk_2` FOREIGN KEY (`id_produktu`) REFERENCES `produkty` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
