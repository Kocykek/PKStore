-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 05, 2025 at 01:53 PM
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
(1, 'Opona do samochodu', 'Opona x4 do samochodu, citroen/mitshubishi tego typu samochody. Kontakt: 3891531314 fax.', 125.99, NULL, 1, NULL, 1, '2025-05-26 12:16:48', NULL),
(13, 'Olej silnikowy 5W30', 'Wysokiej jakości olej silnikowy do samochodów osobowych.', 89.99, 'olej.jpg', 1, 'motoryzacja,olej,samochód', 1, '2025-05-26 00:00:00', '2025-12-31 00:00:00'),
(14, 'Hamak ogrodowy', 'Wygodny hamak do relaksu w ogrodzie.', 149.90, 'hamak.jpg', 2, 'ogród,wypoczynek,hamak', 2, '2025-05-26 00:00:00', '2025-10-01 00:00:00'),
(15, 'Etui na telefon', 'Silikonowe etui do najnowszych modeli smartfonów.', 29.99, 'etui.jpg', 3, 'akcesoria,telefon,etui', 1, '2025-05-26 00:00:00', '2025-11-30 00:00:00'),
(16, 'Pluszowy miś XXL', 'Ogromny pluszowy miś, idealny prezent dla dziecka.', 199.00, 'mis.jpg', 4, 'zabawki,pluszak,prezent', 3, '2025-05-26 00:00:00', '2025-12-25 00:00:00'),
(17, 'Słuchawki gamingowe RGB', 'Bezprzewodowe słuchawki z podświetleniem RGB i mikrofonem.', 269.99, 'sluchawki_gaming.jpg', 5, 'elektronika,słuchawki,gaming', 2, '2025-05-26 00:00:00', '2025-12-31 00:00:00'),
(18, 'Miód wielokwiatowy 1kg', 'Naturalny miód z lokalnej pasieki.', 39.99, 'miod.jpg', 6, 'żywność,miód,eko', 4, '2025-05-26 00:00:00', '2025-09-30 00:00:00'),
(19, 'Plecak trekkingowy 40L', 'Wygodny i wytrzymały plecak idealny na górskie wyprawy.', 189.00, 'plecak.jpg', 7, 'sport,turystyka,plecak', 3, '2025-05-26 00:00:00', '2025-11-01 00:00:00'),
(20, 'Ciśnieniomierz nadgarstkowy', 'Elektroniczny ciśnieniomierz z pamięcią ostatnich pomiarów.', 119.00, 'cisnieniomierz.jpg', 8, 'zdrowie,medycyna,ciśnienie', 1, '2025-05-26 00:00:00', '2025-12-31 00:00:00'),
(21, 'Obraz olejny - krajobraz', 'Ręcznie malowany obraz olejny na płótnie.', 349.00, 'obraz.jpg', 9, 'sztuka,obraz,krajobraz', 5, '2025-05-26 00:00:00', '2026-01-01 00:00:00'),
(22, 'Gra planszowa \"Strategia 2025\"', 'Wciągająca gra strategiczna dla całej rodziny.', 119.99, 'gra_planszowa.jpg', 10, 'gra,rozrywka,planszówka', 2, '2025-05-26 00:00:00', '2025-10-15 00:00:00'),
(23, 'Działka budowlana 800m²', 'Atrakcyjna działka pod budowę domu w spokojnej okolicy.', 150000.00, 'dzialka.jpg', 11, 'nieruchomość,działka,budowa', 4, '2025-05-26 00:00:00', '2026-05-26 00:00:00'),
(24, 'Klocki konstrukcyjne', 'Zestaw klocków do budowania dla dzieci.', 89.99, 'klocki_konstrukcyjne.jpg', 4, 'zabawki,dzieci,klocki', 1, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(25, 'Młotek uniwersalny', 'Solidny młotek do użytku domowego i warsztatowego.', 35.50, 'mlotek_universalny.jpg', 2, 'narzedzia,dom,warsztat', 2, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(26, 'Smartwatch sportowy', 'Zegarek z funkcjami fitness i monitorowaniem tętna.', 299.00, 'smartwatch_sportowy.jpg', 7, 'sport,elektronika,fitness', 3, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(27, 'Zestaw garnków', 'Komplet garnków ze stali nierdzewnej.', 259.00, 'garnki_stal.jpg', 2, 'kuchnia,dom,garnki', 4, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(28, 'Pojemnik na żywność', 'Hermetyczny pojemnik do przechowywania jedzenia.', 19.99, 'pojemnik_zywnosc.jpg', 6, 'kuchnia,zywnosc,przechowywanie', 5, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(29, 'Kask rowerowy', 'Bezpieczny kask na rower dla dorosłych.', 120.00, 'kask_rowerowy.jpg', 7, 'sport,turystyka,rower', 1, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(30, 'Zegarek na rękę', 'Elegancki zegarek na każdą okazję.', 199.99, 'zegarek_reka.jpg', 5, 'elektronika,moda,zegarek', 2, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(31, 'Zestaw narzędzi ogrodniczych', 'Narzędzia do pielęgnacji ogrodu.', 149.00, 'narzedzia_ogrodnicze.jpg', 2, 'ogrod,dom,narzedzia', 3, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(32, 'Lalka Barbie', 'Popularna lalka dla dziewczynek.', 59.99, 'lalka_barbie.jpg', 4, 'zabawki,dzieci,lalka', 4, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(33, 'Laptop gamingowy', 'Wydajny laptop do gier komputerowych.', 4500.00, 'laptop_gamingowy.jpg', 5, 'elektronika,gaming,laptop', 5, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(34, 'Zestaw do grillowania', 'Akcesoria do grillowania dla miłośników BBQ.', 199.00, 'grill_akcesoria.jpg', 7, 'sport,turystyka,grill', 1, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(35, 'Mata do jogi', 'Antypoślizgowa mata do ćwiczeń jogi.', 75.00, 'mata_joga.jpg', 7, 'sport,fitness,joga', 2, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(36, 'Książka kucharska', 'Przepisy na zdrowe i smaczne dania.', 39.99, 'ksiazka_kucharska.jpg', 10, 'ksiazka,kultura,kuchnia', 3, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(37, 'Rower miejski', 'Lekki rower do codziennej jazdy po mieście.', 1200.00, 'rower_miejski.jpg', 7, 'sport,turystyka,rower', 4, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(38, 'Okulary przeciwsłoneczne', 'Stylowe okulary na lato.', 99.99, 'okulary_przeciwsloneczne.jpg', 5, 'moda,elektronika,okulary', 5, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(39, 'Zestaw do malowania', 'Farby i pędzle dla początkujących artystów.', 45.00, 'zestaw_malarski.jpg', 9, 'sztuka,malowanie,farby', 1, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(40, 'Lodówka turystyczna', 'Przenośna lodówka na wyjazdy i pikniki.', 350.00, 'lodowka_turystyczna.jpg', 7, 'sport,turystyka,lodowka', 2, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(41, 'Smartfon', 'Nowoczesny smartfon z dużym ekranem.', 2300.00, 'smartfon.jpg', 5, 'elektronika,telefon,smartfon', 3, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(42, 'Krem nawilżający', 'Krem do codziennej pielęgnacji skóry.', 55.00, 'krem_nawilzajacy.jpg', 8, 'zdrowie,pielegnacja,krem', 4, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(43, 'Lampa biurkowa LED', 'Regulowana lampa do czytania i pracy.', 80.00, 'lampa_biurkowa.jpg', 2, 'dom,bio,oswietlenie', 5, '2025-05-26 23:30:23', '2026-05-26 23:30:23'),
(45, 'conquest stroj cosplay2', 'test123', 255.00, 'img_6834edda585129.86379476.jpg', 4, '0', 5, '2025-05-26 22:40:26', '2025-05-29 12:00:00'),
(46, 'conquest stroj cosplay', 'feafea', 4314.00, 'img_6835003e1dfa45.42725387.jpg', 9, '0', 5, '2025-05-26 23:58:54', '2025-05-29 20:00:00'),
(47, 'wozek logo', 'dostajesz logo .png o wozku sklepowym do wykorzystania na twoim sklepie internetowym', 25.56, 'img_683569b7ca0953.04205390.png', 3, '0', 5, '2025-05-27 07:28:55', '2025-05-30 10:00:00'),
(48, 'Test1324132', 'testys531256134613', 112.00, 'img_683cb9fea91429.12413517.png', 4, '0', 6, '2025-06-01 20:37:18', '2025-06-19 08:00:00'),
(49, 'ukw logo', 'nowoczesne logo ukw', 25.00, 'img_683d51535e56b3.95097098.jpg', 3, '0', 5, '2025-06-02 07:22:59', '2025-06-26 08:00:00');

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
(3, 'test12345@wp.pl', '$2y$10$cpq00hshiPxTjtMeQdog0eUDpDBFrHKQ495NZy9jHrbvW9gNXXxIG', 'Patryk', 'Koc', 'uzytkownik', '2025-05-26 13:09:08'),
(4, 'patryk@wp.pl', '$2y$10$qXG4XVcuuUmtwKMCW1EkW.U5VPPvqmfqKGN23hp7KnffEvbQtBT7a', 'patryk', 'koc', 'uzytkownik', '2025-05-26 19:58:20'),
(5, 'patryk123@wp.pl', '$2y$10$BTaV5Lna9HPTS8tHnla7nuvY.G/s3f8nhpbzHkGOOrMHZ0gSe7dli', 'patryk123', 'patryk123', 'uzytkownik', '2025-05-26 22:02:01'),
(6, 'test24151@wp.pl', '$2y$10$2GB6zFGviVYCOfo0aciaDOT/HkFqLnVGl09n.QPYGNKfruS5F4l3y', 'patryk', 'koc', 'uzytkownik', '2025-05-27 01:53:30'),
(7, 'pkstore@admin.pl', '$2y$10$LAobkE0mn1mw2LRIkUnq7uPrCF5CNVM2E2Ec2xA0XVsuHgWZRFwzS', 'Patryk', 'Koc', 'uzytkownik', '2025-05-27 09:52:26'),
(8, 'test1234@wp.pl', '$2y$10$WrN55OSlo4VHR7p13NhMC.dM6/FU0buMbgWX8fkUfv.KukvTMEvNO', 'test123', 'test123', 'uzytkownik', '2025-06-01 22:38:04');

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
  `data_zamowienia` datetime DEFAULT CURRENT_TIMESTAMP,
  `typ_dostawy` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `zamowienia`
--

INSERT INTO `zamowienia` (`id`, `id_klienta`, `imie`, `nazwisko`, `adres`, `email`, `metoda_platnosci`, `data_zamowienia`, `typ_dostawy`) VALUES
(5, 5, 'patryk123', 'patryk123', 'Lipa 7, 64-820 Szamocin\'', 'patryk123@wp.pl', 'przelew', '2025-05-27 00:05:22', 'dpd'),
(6, 5, 'patryk123', 'patryk123', 'Lipa 7, 64-820 Szamocin', 'patryk123@wp.pl', 'przelew', '2025-05-27 00:06:01', 'dpd'),
(7, 5, 'patryk123', 'patryk123', 'Lipa 7, 64-820 Szamocni', 'patryk123@wp.pl', 'karta', '2025-05-27 00:27:51', 'inpost'),
(8, 5, 'patryk123', 'patryk123', 'Lipa 8, 64-820 Szamocin', 'patryk123@wp.pl', 'karta', '2025-05-27 00:29:08', 'dpd'),
(9, 5, 'patryk123', 'patryk123', 'Lipa 9, 64-820 Szamocin', 'patryk123@wp.pl', 'karta', '2025-05-27 01:27:24', 'inpost'),
(10, 5, 'patryk123', 'patryk123', 'r34143, 64-820 4321', 'patryk123@wp.pl', 'przelew', '2025-05-27 01:29:22', 'dpd'),
(11, 5, 'patryk123', 'patryk123', '631ok31, 53-820 szamocin', 'patryk123@wp.pl', 'przy_odbiorze', '2025-05-27 01:30:15', 'inpost'),
(12, 5, 'patryk123', 'patryk123', 'Lipa 10, 64-820 Szamocin', 'patryk123@wp.pl', 'karta', '2025-05-27 01:59:11', 'dpd'),
(13, 5, 'patryk123', 'patryk123', 'Lipa 11, 64-820 Szamocin', 'patryk123@wp.pl', 'przy_odbiorze', '2025-05-27 09:27:21', 'inpost'),
(14, 7, 'Patryk', 'Koc', 'Lipa 7, 64-820 Szamocin', 'pkstore@admin.pl', 'przelew', '2025-05-27 09:53:08', 'inpost'),
(15, 5, 'patryk123', 'patryk123', 'Lipa 7, 64-820 Szamocin', 'patryk123@wp.pl', 'przelew', '2025-06-01 21:45:44', 'inpost'),
(16, 8, 'test123', 'test123', '631ok31, 64-820 Szamocin', 'test1234@wp.pl', 'przy_odbiorze', '2025-06-01 22:38:39', 'dpd'),
(17, 5, 'patryk123', 'patryk123', 'Lipa 7, 64-820 Szamocin', 'patryk123@wp.pl', 'przy_odbiorze', '2025-06-02 09:20:25', 'inpost');

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
-- Dumping data for table `zamowione_produkty`
--

INSERT INTO `zamowione_produkty` (`id`, `id_zamowienia`, `id_produktu`, `ilosc`) VALUES
(1, 6, 17, 1),
(2, 6, 15, 1),
(3, 7, 27, 1),
(4, 7, 13, 1),
(5, 8, 27, 1),
(6, 8, 13, 1),
(7, 9, 25, 1),
(8, 9, 14, 1),
(9, 10, 30, 1),
(10, 11, 26, 1),
(11, 12, 25, 2),
(12, 12, 19, 1),
(13, 12, 16, 1),
(14, 13, 15, 1),
(15, 13, 18, 1),
(16, 13, 30, 1),
(17, 14, 26, 1),
(18, 15, 25, 1),
(19, 15, 1, 1),
(20, 16, 28, 3),
(21, 17, 1, 3);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `produkty`
--
ALTER TABLE `produkty`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `zamowienia`
--
ALTER TABLE `zamowienia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `zamowione_produkty`
--
ALTER TABLE `zamowione_produkty`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
