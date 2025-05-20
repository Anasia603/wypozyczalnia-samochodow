-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Maj 17, 2025 at 06:12 PM
-- Wersja serwera: 10.11.11-MariaDB-cll-lve
-- Wersja PHP: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `srv84416_wypozyczalnia`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klienci`
--

CREATE TABLE `klienci` (
  `id` int(11) NOT NULL,
  `imie` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nazwisko` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Zrzut danych tabeli `klienci`
--

INSERT INTO `klienci` (`id`, `imie`, `nazwisko`, `email`, `telefon`) VALUES
(1, 'Anastazja', 'Zhd', 'anastazja.p025@gmail.com', '511821025');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pojazdy`
--

CREATE TABLE `pojazdy` (
  `id` int(11) NOT NULL,
  `typ` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `marka` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `model` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rok_produkcji` int(11) NOT NULL,
  `cena_za_dzien` decimal(5,2) NOT NULL,
  `dostepnosc` tinyint(1) NOT NULL,
  `zdjecie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Zrzut danych tabeli `pojazdy`
--

INSERT INTO `pojazdy` (`id`, `typ`, `marka`, `model`, `rok_produkcji`, `cena_za_dzien`, `dostepnosc`, `zdjecie`) VALUES
(1, 'osobowy', 'Toyota', 'Corolla', 2020, 150.00, 1, 'toyota_corolla.jpg'),
(2, 'osobowy', 'Volkswagen', 'Golf', 2018, 140.00, 1, 'volkswagen_golf.jpg'),
(3, 'osobowy', 'Honda', 'Civic', 2021, 160.00, 1, 'honda_civic.jpg'),
(4, 'dostawczy', 'Ford', 'Transit', 2019, 200.00, 1, 'ford_transit.jpg'),
(5, 'dostawczy', 'Mercedes-Benz', 'Sprinter', 2020, 250.00, 1, 'mercedes_sprinter.jpg'),
(6, 'dostawczy', 'Renault', 'Master', 2018, 210.00, 1, 'renault_master.jpg'),
(7, 'skuter', 'Yamaha', 'NMAX', 2022, 80.00, 1, 'yamaha_nmax.jpg'),
(8, 'skuter', 'Honda', 'PCX 125', 2021, 75.00, 1, 'honda_pcx125.jpg'),
(9, 'skuter', 'Piaggio', 'Liberty 125', 2020, 70.00, 1, 'piaggio_liberty.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wypozyczenia`
--

CREATE TABLE `wypozyczenia` (
  `id` int(11) NOT NULL,
  `id_pojazdu` int(11) NOT NULL,
  `id_klienta` int(11) NOT NULL,
  `data_wypozyczenia` date NOT NULL,
  `data_zwrotu` date NOT NULL,
  `rzeczywista_data_zwrotu` date DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Zrzut danych tabeli `wypozyczenia`
--

INSERT INTO `wypozyczenia` (`id`, `id_pojazdu`, `id_klienta`, `data_wypozyczenia`, `data_zwrotu`, `rzeczywista_data_zwrotu`, `status`) VALUES
(1, 8, 1, '2025-05-17', '2025-05-19', NULL, 'zwrócony');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `klienci`
--
ALTER TABLE `klienci`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `pojazdy`
--
ALTER TABLE `pojazdy`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `wypozyczenia`
--
ALTER TABLE `wypozyczenia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pojazdu` (`id_pojazdu`),
  ADD KEY `id_klienta` (`id_klienta`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `klienci`
--
ALTER TABLE `klienci`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT dla tabeli `pojazdy`
--
ALTER TABLE `pojazdy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `wypozyczenia`
--
ALTER TABLE `wypozyczenia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `wypozyczenia`
--
ALTER TABLE `wypozyczenia`
  ADD CONSTRAINT `wypozyczenia_ibfk_1` FOREIGN KEY (`id_pojazdu`) REFERENCES `pojazdy` (`id`),
  ADD CONSTRAINT `wypozyczenia_ibfk_2` FOREIGN KEY (`id_klienta`) REFERENCES `klienci` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
