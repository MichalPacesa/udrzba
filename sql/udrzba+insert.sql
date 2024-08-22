-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: 127.0.0.1
-- Čas generovania: Pi 19.Apr 2024, 13:53
-- Verzia serveru: 10.4.32-MariaDB
-- Verzia PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `udrzba`
--

/* Drop Database Create Database */

DROP DATABASE IF EXISTS udrzba;
CREATE DATABASE udrzba;
USE udrzba;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cinnost_opravy`
--

CREATE TABLE `cinnost_opravy` (
  `Cin_nazov` varchar(50) NOT NULL,
  `Cinnost_opravyID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `cinnost_opravy`
--

INSERT INTO `cinnost_opravy` (`Cin_nazov`, `Cinnost_opravyID`) VALUES
('Guličkove ložisko na šrotovník', 1),
('Výmena ložiska šrotovníka', 2),
('Výmena valca na šrotovníku', 3),
('Dotiahnutie hriadele čerpadla', 4),
('Vyčistenie čerpadla', 5),
('Oprava čerpadla', 6),
('Výmena čerpadla', 7),
('Vyčistenie ložiska', 8),
('Výmena ložiska', 9),
('Výmena teplomeru', 10),
('Výmena sita', 11),
('Oprava sita', 12),
('Výčistenie potrubia', 13),
('Zváranie potrubia', 14),
('Výmena spoja na potrubí', 15),
('Výmena tesnenia na potrubí', 16),
('Oprava riadiaceho prístroja', 17),
('Čistenie filtra', 18),
('Výmena snímača hladiny - kade', 19),
('Výmena snímača hladiny - fľaše', 20),
('Výmena poistného pretlakového ventilu', 21),
('Výmena balónikov na zátkovanie fliaš ', 22),
('Výmena poistného pretlakového ventilu', 23),
('Nastavenie dávkovania lepidla', 24);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cinnost_udrzby`
--

CREATE TABLE `cinnost_udrzby` (
  `Cin_datum_udrzby` datetime NOT NULL,
  `Cin_odpracovane_hodiny` double(10,2) UNSIGNED NOT NULL,
  `Cin_poznamka` varchar(200) DEFAULT NULL,
  `Cinnost_udrzbyID` int(10) UNSIGNED NOT NULL,
  `Planovana_udrzbaID` int(10) UNSIGNED NOT NULL,
  `PouzivatelID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `dodavatel`
--

CREATE TABLE `dodavatel` (
  `Dod_nazov` varchar(50) NOT NULL,
  `Dod_email` varchar(50) DEFAULT NULL,
  `Dod_telefon` varchar(20) DEFAULT NULL,
  `Dod_ulica_a_cislo` varchar(50) DEFAULT NULL,
  `Dod_mesto` varchar(50) DEFAULT NULL,
  `Dod_psc` varchar(5) DEFAULT NULL,
  `Dod_stat` varchar(50) DEFAULT NULL,
  `Dod_ico` varchar(8) DEFAULT NULL,
  `Dod_dic` varchar(10) DEFAULT NULL,
  `Dod_ic_dph` varchar(12) DEFAULT NULL,
  `DodavatelID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `dodavatel`
--

INSERT INTO `dodavatel` (`Dod_nazov`, `Dod_email`, `Dod_telefon`, `Dod_ulica_a_cislo`, `Dod_mesto`, `Dod_psc`, `Dod_stat`, `Dod_ico`, `Dod_dic`, `Dod_ic_dph`, `DodavatelID`) VALUES
('Siemens Slovakia a.s.', 'objednavky@siemens.sk', '+421555222', 'Trnavská 1', 'Bratislava', '95100', 'Slovenská republika', '123456', '1112345', '5556897', 1),
('Czech brewery s.r.o.', 'brewery@brewery.sk', '+421555999', 'Kolofíkovo nábřeží 30', 'Opava', '74575', 'Česká republika', '568925', '659874', '623598', 2),
('FTP ENGINEERING s.r.o.', 'ftp@engineering.sk', '+421555888', 'Pezinská cesta 5104', 'Malacky', '55588', 'Slovenská republika', '123456', '666555', '888222', 3),
('MaR TRADE, s. r. o', 'maar@trade.sk', '+421333666', 'Nanterská 8', 'Žilina', '01008', 'Slovenská republika', '555222', '555222', '666888', 4);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `kategoria`
--

CREATE TABLE `kategoria` (
  `Kat_nazov` varchar(50) NOT NULL,
  `KategoriaID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `kategoria`
--

INSERT INTO `kategoria` (`Kat_nazov`, `KategoriaID`) VALUES
('Sada náhradných dielov', 1),
('Tanky', 2),
('Spojovací materiál', 3),
('Tesnenia', 4),
('Potrubia', 5),
('Ventily', 6),
('Čerpadlá', 7),
('Hadice', 8),
('Montážny materiál', 9),
('Ložiská', 10),
('Filtre', 11),
('Snímače', 12),
('Riadiace systémy', 13),
('Spotrebný materiál', 14),
('Iné', 15);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `nachadza`
--

CREATE TABLE `nachadza` (
  `Dok_nazov_dielu` varchar(50) NOT NULL,
  `Dok_jednotka` varchar(10) NOT NULL,
  `Dok_mnozstvo` int(10) UNSIGNED NOT NULL,
  `Dok_cena_za_jednotku` double(10,2) UNSIGNED NOT NULL,
  `Dok_celkova_cena` double(10,2) UNSIGNED NOT NULL,
  `NachadzaID` int(10) UNSIGNED NOT NULL,
  `Skladovy_dokladID` int(10) UNSIGNED NOT NULL,
  `Nahradny_dielID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `nahradny_diel`
--

CREATE TABLE `nahradny_diel` (
  `Diel_evidencne_cislo` varchar(20) NOT NULL,
  `Diel_nazov` varchar(50) NOT NULL,
  `Diel_popis` varchar(100) DEFAULT NULL,
  `Diel_jednotka` varchar(10) NOT NULL,
  `Diel_mnozstvo` int(10) UNSIGNED NOT NULL,
  `Diel_umiestnenie` varchar(30) NOT NULL,
  `Diel_datum_prevzatia` date DEFAULT NULL,
  `Diel_zarucna_doba` int(10) UNSIGNED DEFAULT NULL,
  `Nahradny_dielID` int(10) UNSIGNED NOT NULL,
  `KategoriaID` int(10) UNSIGNED NOT NULL,
  `StrojID` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `nahradny_diel`
--

INSERT INTO `nahradny_diel` (`Diel_evidencne_cislo`, `Diel_nazov`, `Diel_popis`, `Diel_jednotka`, `Diel_mnozstvo`, `Diel_umiestnenie`, `Diel_datum_prevzatia`, `Diel_zarucna_doba`, `Nahradny_dielID`, `KategoriaID`, `StrojID`) VALUES
('0', 'Sada na výmenu valcov pre drvič sladu', NULL, 'ks', 1, 'H1', NULL, 24, 1, 1, 2),
('0', 'Guličkové ložisko - šrotovník', NULL, 'ks', 1, 'H1', NULL, 6, 2, 10, 2),
('0', 'Hriadeľ na kalové čerpadlo', NULL, 'ks', 1, 'H1', NULL, 24, 3, 7, 5),
('0', 'Valec na šrotovník', NULL, 'ks', 1, 'H1', NULL, 24, 4, 15, 2),
('2020001', 'Odstredivé čerpadlo', NULL, 'ks', 1, 'S1', NULL, 24, 5, 7, 1),
('2020005', ' Magnetické čerpadlo', NULL, 'ks', 1, 'C1', NULL, 24, 6, 7, 3);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `oprava`
--

CREATE TABLE `oprava` (
  `Opr_datum_opravy` datetime NOT NULL,
  `Opr_popis` varchar(100) DEFAULT NULL,
  `Opr_odpracovane_hodiny` int(11) NOT NULL,
  `OpravaID` int(10) UNSIGNED NOT NULL,
  `Cinnost_opravyID` int(10) UNSIGNED NOT NULL,
  `PouzivatelID` int(10) UNSIGNED NOT NULL,
  `PoruchaID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `planovana_udrzba`
--

CREATE TABLE `planovana_udrzba` (
  `Plan_nazov` varchar(50) NOT NULL,
  `Plan_datum_cas_od` datetime NOT NULL,
  `Plan_datum_cas_do` datetime NOT NULL,
  `Plan_popis` varchar(100) DEFAULT NULL,
  `Plan_perioda` varchar(20) NOT NULL,
  `Plan_aktivna` tinyint(1) NOT NULL,
  `Planovana_udrzbaID` int(10) UNSIGNED NOT NULL,
  `StrojID` int(10) UNSIGNED DEFAULT NULL,
  `PouzivatelID` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `porucha`
--

CREATE TABLE `porucha` (
  `Por_nazov` varchar(50) NOT NULL,
  `Por_popis` varchar(100) DEFAULT NULL,
  `Por_stav` int(11) NOT NULL,
  `Por_datum_vzniku` datetime NOT NULL,
  `Por_datum_pridelenia` datetime DEFAULT NULL,
  `PoruchaID` int(10) UNSIGNED NOT NULL,
  `StrojID` int(10) UNSIGNED DEFAULT NULL,
  `PouzivatelID` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `porucha`
--

INSERT INTO `porucha` (`Por_nazov`, `Por_popis`, `Por_stav`, `Por_datum_vzniku`, `Por_datum_pridelenia`, `PoruchaID`, `StrojID`, `PouzivatelID`) VALUES
('Nevyčkuje fľaše !', 'Vyraďovačka vyraďuje zle zavíčkované fľaše !', 2, '2024-04-04 11:00:00', '2024-04-14 21:50:18', 1, 16, 4),
('Zle umýva fľaše !', 'Vyraďovačka vyraďuje znečistené fľaše !', 1, '2024-04-01 10:00:00', '0000-00-00 00:00:00', 2, 14, NULL),
('Nešrotuje obilie', 'Divné zvuky na motore, šrotovník nejde', 2, '2024-04-14 21:57:00', '2024-04-14 22:00:37', 3, 2, 5),
('Pokazené čerpadlo', 'Nejde čerpadlo na slad medzi kaďami', 4, '2024-04-14 22:00:00', '2024-04-14 22:05:06', 7, 4, 5),
('Kvapká tesnenie Spilka', 'Na potrubí za spilkou na spoji kvapká', 1, '2024-04-14 22:05:00', '2024-04-14 22:06:30', 8, 7, 7),
('Kvapkajúci ventil', 'Kvapkajúci ventil na plničke', 4, '2024-04-14 22:06:00', '2024-04-14 22:08:56', 9, 16, 7),
('Pokazený snímač hladiny', 'Nesignalizuje príliš vysokú hladinu!', 1, '2024-04-14 22:09:00', '2024-04-14 22:13:24', 12, 13, 5),
('Zlé lepenie etikiet na fľašiach', 'Etikety sa odlepujú...', 1, '2024-04-14 22:17:00', '2024-04-14 22:18:14', 13, 23, 11),
('Príliš vysoký tlak na buferi', 'Signalizuje príliš vysoký tlak na buferi !', 1, '2024-04-14 22:19:00', '2024-04-14 22:20:09', 14, 13, 11),
('Kvapká tesnenie na potrubí', 'Kvapká na potrubí medzi buferom a plničkou !', 1, '2024-04-14 22:20:00', '2024-04-14 22:22:27', 17, 13, 7);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `pouziva`
--

CREATE TABLE `pouziva` (
  `Opr_nazov_dielu` varchar(50) DEFAULT NULL,
  `Opr_mnozstvo` int(10) UNSIGNED NOT NULL,
  `Opr_jednotka` varchar(20) NOT NULL,
  `PouzivaID` int(10) UNSIGNED NOT NULL,
  `Nahradny_dielID` int(10) UNSIGNED DEFAULT NULL,
  `PoruchaID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `pouzivatel`
--

CREATE TABLE `pouzivatel` (
  `Pouz_meno` varchar(50) NOT NULL,
  `Pouz_heslo` varchar(60) NOT NULL,
  `PouzivatelID` int(10) UNSIGNED NOT NULL,
  `RolaID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `pouzivatel`
--

INSERT INTO `pouzivatel` (`Pouz_meno`, `Pouz_heslo`, `PouzivatelID`, `RolaID`) VALUES
('pacesa', '$2y$10$PkoUOQoXRWAeyk/3TxhPUeYcdO0b6sykbkjWW8EQWWvJo0CExw4x.', 1, 1),
('pekar', '$2y$10$J9TD1zDSKsCRDdO/NmC9OemD/v9HsmsXACquf01bVY8ueOJgAj/gK', 2, 2),
('kollar', '$2y$10$KnFVDcu2MSkXJUNc7ksjp.8WmWYdsRyjs.MvTm2HUfP.Y.tScaK5y', 3, 3),
('smrek', '$2y$10$gx.lAHrPDmVM8r61GfbVaevKBUM8rHPiihshmj5YYtb7bIDpOTG.S', 4, 4),
('novak', '$2y$10$DYsvaD8SZdEtd6fH1m/qDOVbdiQgIh89J4QpnXzibAbR.PH0DNhMq', 5, 4),
('sabova', '$2y$10$39ds.qi0CZylsVw9/3KUDOnqhjdwtzlkXD7oiQ4zQbrKXUVQi5yjO', 6, 3),
('hrubos', '$2y$10$EZqoLYw/LNBSV/6U6W6CXOfAU1zDlA/9c8wTuJFsXEExIpiepj7ZC', 7, 4),
('jakubik', '$2y$10$M7w2yZupMQ7H3UET24EfkO8NHVWMJtGgX1e97xjS1mwF9LeTh4iBa', 8, 4),
('novotny', '$2y$10$epNHYlTAAFYiHyMLovSptuCoSMyEqtCl64keHKUmNc9xiqXb33Kru', 9, 2),
('kristof', '$2y$10$tmXNoh5vGPme2KDwxm4VPO/VFnJbaRS88SgJ3Y0FMVhiHzFO24AKu', 10, 1),
('tomasik', '$2y$10$Wb0dv89UfJ6IcGVHCRr6c.KZRPgN/WA9yZMfpRxHHwlxBDyX25pQu', 11, 4),
('boros', '$2y$10$F7HZ0olSB5I.c2dwBrO7hOlEjfP6MTHQzXAxKzwtFrWUEqeSnGmmC', 12, 1),
('matusik', '$2y$10$qxRdsAQAoB21ZEYEjklkbe7fYjAZHenbmVGMPw6zv0wEolcpicCU6', 13, 4),
('hlavac', '$2y$10$zt9wvmKdmifqr9CB/37EK.Qw.ThLqgpltRWTJXTJngK6P7ceLjpqO', 14, 1),
('urban', '$2y$10$w4L6rUByG3DpcoT5b8db6.OyvkqgUCnApNHucBNd5eHAD8QWUDdfS', 15, 4),
('hric', '$2y$10$mUwaaBt0SgcIypIFhvLdzOs3Q8QC2bYSskZhX1gizCtxVrn8hOVXG', 16, 2);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `poziadavka_na_nahradny_diel`
--

CREATE TABLE `poziadavka_na_nahradny_diel` (
  `Poz_stav` int(11) NOT NULL,
  `Poz_poznamka` varchar(200) DEFAULT NULL,
  `Poziadavka_na_nahradny_dielID` int(10) UNSIGNED NOT NULL,
  `PouzivatelID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `priloha`
--

CREATE TABLE `priloha` (
  `Nazov_suboru` varchar(50) NOT NULL,
  `PrilohaID` int(10) UNSIGNED NOT NULL,
  `StrojID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `rola`
--

CREATE TABLE `rola` (
  `Rola_nazov` varchar(30) NOT NULL,
  `RolaID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `rola`
--

INSERT INTO `rola` (`Rola_nazov`, `RolaID`) VALUES
('Systemovy administrátor', 1),
('Vedúci údržby', 2),
('Vedúci výroby', 3),
('Servisný technik', 4);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `skladovy_doklad`
--

CREATE TABLE `skladovy_doklad` (
  `Dok_evidencne_cislo` int(10) UNSIGNED NOT NULL,
  `Dok_typ` int(10) UNSIGNED NOT NULL,
  `Dok_datum` date NOT NULL,
  `Dok_suma` double(10,2) UNSIGNED NOT NULL,
  `Dok_poznamka` varchar(200) DEFAULT NULL,
  `Skladovy_dokladID` int(10) UNSIGNED NOT NULL,
  `DodavatelID` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `stroj`
--

CREATE TABLE `stroj` (
  `Stroj_nazov` varchar(50) NOT NULL,
  `Stroj_popis` varchar(100) DEFAULT NULL,
  `Stroj_vyrobca` varchar(30) NOT NULL,
  `Stroj_umiestnenie` varchar(30) NOT NULL,
  `Stroj_datum_vyroby` date DEFAULT NULL,
  `Stroj_vyrobne_cislo` varchar(20) NOT NULL,
  `Stroj_evidencne_cislo` varchar(20) NOT NULL,
  `Stroj_datum_prevzatia` date DEFAULT NULL,
  `Stroj_zarucna_doba` int(11) DEFAULT NULL,
  `StrojID` int(10) UNSIGNED NOT NULL,
  `DodavatelID` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `stroj`
--

INSERT INTO `stroj` (`Stroj_nazov`, `Stroj_popis`, `Stroj_vyrobca`, `Stroj_umiestnenie`, `Stroj_datum_vyroby`, `Stroj_vyrobne_cislo`, `Stroj_evidencne_cislo`, `Stroj_datum_prevzatia`, `Stroj_zarucna_doba`, `StrojID`, `DodavatelID`) VALUES
('Kaďa s vodou', 'Automatizované premiešanie sladu s vodou riadené  PLC. S7', 'Siemens', 'Hala 1', '2019-04-01', 'S-123987', '2020-001', '2020-04-09', 60, 1, NULL),
('Šrotovník na slad', 'MMR-600 Stroj na mletie sladových zŕn', 'Czech brewery system', 'Hala 1', '2014-04-01', 'SK-Si-5007', '2016-002', '2016-04-22', 36, 2, 2),
('Silo na slad', 'Silo na skladovanie obilia  s automatickou váhou a dopravníkom\r\nPLC Simatic S7', 'Siemens', 'Vonkajšok', '2019-04-01', 'SK-Si-555', '2019-001', '2015-12-10', 36, 3, 1),
('Varňa', 'Zariadenie na výrobu mladiny', 'Siemens', 'Hala 1', '2016-03-21', 'SK-Si-555', '2019-002', '2019-12-10', 36, 4, 1),
('Sceďovačka', 'Filtrovanie mladiny so sitom', 'Czech brewery s.r.o.', '', '2015-04-16', 'CBS-052', '2016-001', '2016-06-09', 24, 5, 3),
('Chladič', 'Schladenie mladiny s  PLC S7 200 od Siemens a regulátor otáčok VLT HVAC  od Danfoss', 'Siemens', 'Hala 1', '2019-04-09', 'SK-Si-5008', '2019-003', '2020-06-09', 60, 6, 1),
('Fermentátor', 'Spilka - kvasná nádoba', 'Czech brewery system', 'Hala 1', '2015-06-10', 'CBS-558', '2016-003', '2016-06-01', 24, 7, 1),
('Mlátová veža', 'Zhromaždenie odpadu po filtrácii kvasu', 'Czech brewery system', 'Hala 1', '2016-01-04', 'CBS-255', '2016-004', '2016-06-15', 24, 8, 2),
('Ležiaci tank č.1', 'Tank na zrenie mladiny s teplomerom a riadiacim prístrojom na tlak. Teplota 2 °C a tlak 0,8 bar', 'C.B.System  a  Siemens', 'Hala 2', '2019-08-12', 'CBS-2019 558', '2020-004', '2020-02-15', 24, 9, 2),
('Ležiaci tank č.2', 'Tank na zrenie mladiny s teplomerom a riadiacim prístrojom na tlak. Teplota 2 °C a tlak 0,8 bar', 'C.B.System + Siemens', 'Hala 2', '2019-04-16', 'CBS-2019 559', '2020-002', '2024-06-11', 24, 10, 2),
('Kremíkový filter', 'Filtračné zariadenie na pivo', 'Siemens', 'Hala 2', '2018-04-17', 'SK-Si-555222', '2020-003', '2020-08-11', 72, 11, 1),
('Pasterizátor', 'Pasterizácia piva', 'Siemens Slovakia', 'Hala 3', '2018-03-13', 'SK-Si-5008', '2019-004', '2019-09-02', 36, 12, 1),
('Bufer', 'Nádrž na vyrobené pivo so snímačmi', 'Czech brewery system', 'Hala 3', '2015-04-15', 'S-123987', '2016-005', '2016-06-14', 24, 13, 1),
('Umývacia linka - fľaše', 'Umývanie sklenených fliaš', 'Siemens', 'Hala 3', '2019-04-10', 'Si-A-5009', '2017-006', '2016-06-14', 60, 14, 1),
('Umývacia linka - kade', 'Umývanie kadí', 'Siemens', 'Hala 2', '2019-03-05', 'Si-A-555888', '2019-005', '2020-06-08', 36, 15, 1),
('Plnička na fľaše', 'Plnenie fliaš s kontrolou výšky hladiny', 'Siemens', 'Hala 3', '2019-04-08', 'Si-A-5008', '2019-006', '2019-06-10', 36, 16, 1),
('Plnička na plechovky', 'Sterilizácia a plnenie plechoviek na pivo', 'Siemens', 'Hala 3', '2019-02-05', 'Si-A-5008', '2019-007', '2019-06-12', 60, 17, 1),
('Plnička na sudy', 'Plnenie piva do sudov', 'Siemens', 'Hala 3', '2010-06-02', 'Si-A-5008', '2010-001', '2010-07-13', 60, 18, 1),
('Vyraďovačka na fľaše', 'Kontrola hladiny nápoja vo fľašiach a kontrola správne zavíčkovanie', 'Siemens', 'Hala 3', '2019-04-23', 'Si-A-5009', '2020-006', '2020-06-17', 60, 19, 1),
('Vyraďovačka na plechovky', 'Kontrola hladiny nápojov v plechovkách a správne uzatvorenie plechoviek', 'Siemens Slovakia', 'Hala 3', '2020-04-01', 'Si-A-5010', '2020-008', '2021-08-23', 60, 20, 1),
('Pasterizátor na fľaše', 'Pasterizácia uzatvorených fliaš', 'Siemens', 'Hala 3', '2020-06-29', 'Si-A-5010', '2020-009', '2020-07-27', 60, 21, 1),
('Pasterizátor na plechovky', 'Pasterizácia uzatvorených plechoviek', 'Siemens', 'Hala 3', '2020-04-13', 'Si-A-5011', '2020-010', '2020-08-17', 60, 22, 1),
('Etiketovač na flaše', 'Naliepanie etikiet na flaše', 'Siemens', 'Hala 3', '2021-02-02', 'Si-A-5009', '2021-001', '2021-03-02', 60, 23, 1),
('Etiketovač na plechovky', 'Naliepanie etikiet na plechovky', 'Siemens', 'Hala 3', '2021-03-10', 'Si-A-5012', '2021-002', '2021-03-08', 60, 24, 1),
('Vkladač na fľaše', 'Automatické vkladanie fliaš do bedničiek', 'Siemens', 'Hala 3', '2021-03-21', 'Si-A-5015', '2021-003', '2021-05-11', 60, 25, 1),
('Vkladač na plechovky', 'Automatické vkladanie plechoviek do debničiek', 'Siemens Slovakia', 'Hala 3', '2021-04-26', 'Si-A-5050', '2021-005', '2021-06-08', 60, 26, 1),
('Paletizátor debničiek', 'Automatické ukladanie debničiek na palety', 'Siemens', 'Hala 4', '2022-09-15', 'Si-A-5010', '2021-011', '2023-03-15', 60, 27, 1),
('Balička', 'Automatické balenie paliet do fólie', 'Siemens', 'Hala 4', '2022-07-20', 'SK-Si-5111', '2023-001', '2023-09-11', 60, 28, 1),
('Exam', 'Kontrola čistoty prázdnych fliaš', 'Siemens', 'Hala 2', '2016-04-25', 'Si-A-5088', '2018-001', '2018-07-16', 36, 29, 1);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `uvedeny`
--

CREATE TABLE `uvedeny` (
  `Poz_nazov_dielu` varchar(50) DEFAULT NULL,
  `Poz_mnozstvo` int(10) UNSIGNED NOT NULL,
  `Poz_jednotka` varchar(20) NOT NULL,
  `UvedenyID` int(10) UNSIGNED NOT NULL,
  `Nahradny_dielID` int(10) UNSIGNED DEFAULT NULL,
  `Poziadavka_na_nahradny_dielID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `zamestnanec`
--

CREATE TABLE `zamestnanec` (
  `Zam_meno` varchar(20) NOT NULL,
  `Zam_priezvisko` varchar(50) NOT NULL,
  `Zam_datum_narodenia` date DEFAULT NULL,
  `Zam_email` varchar(50) NOT NULL,
  `Zam_telefon` varchar(20) NOT NULL,
  `Zam_ulica_a_cislo` varchar(50) NOT NULL,
  `Zam_mesto` varchar(50) NOT NULL,
  `Zam_psc` varchar(5) NOT NULL,
  `Zam_pozicia` varchar(30) NOT NULL,
  `Zam_datum_nastupu` date DEFAULT NULL,
  `Zam_poznamka` varchar(200) DEFAULT NULL,
  `ZamestnanecID` int(10) UNSIGNED NOT NULL,
  `PouzivatelID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `zamestnanec`
--

INSERT INTO `zamestnanec` (`Zam_meno`, `Zam_priezvisko`, `Zam_datum_narodenia`, `Zam_email`, `Zam_telefon`, `Zam_ulica_a_cislo`, `Zam_mesto`, `Zam_psc`, `Zam_pozicia`, `Zam_datum_nastupu`, `Zam_poznamka`, `ZamestnanecID`, `PouzivatelID`) VALUES
('Michal', 'Pačesa', '2000-06-22', 'michal.pacesa4@gmail.com', '+421903157761', 'Majcichov 62', 'Majcichov', '91922', 'Systémový administrátor', '2022-06-22', NULL, 1, 1),
('Adam', 'Pekár', '1988-05-12', 'Pekar@gmail.com', '+421903123456', 'Hlavná 32', 'Bratislava', '81103', 'Vedúci údržby', '2024-01-22', NULL, 2, 2),
('Roman', 'Kollár', '1995-04-21', 'KollarRoman@gmail.com', '+421903137319', 'Kvetná 10', 'Trnava', '91701', 'Vedúci výroby', '2023-08-12', NULL, 3, 3),
('Daniel', 'Smrek', '1970-01-01', 'Smrek@gmail.com', '+421903111225', 'Dlhá 12', 'Trnava', '91701', 'Elektromechanik', '2024-03-09', '', 4, 4),
('Martin', 'Novák', '1970-01-01', 'novak.martin@gmail.com', '+421902345678', 'Hviezdoslavova 5', 'Košice', '04001', 'Elektromechanik', '2023-05-10', '', 5, 5),
('Barbora', 'Sábová', '1970-01-01', 'sabova.barbora@gmail.com', '+421905678912', 'Družstevná 24', 'Prešov', '08001', 'Vedúci výroby', '2022-12-17', '', 6, 6),
('Jozef', 'Hruboš', '1970-01-01', 'hrubos.jozef@gmail.com', '+421904567890', 'Školská 7', 'Banská Bystrica', '97501', 'Opravár potrubia', '2024-02-28', '', 7, 7),
('Mária', 'Jakubíková', '1970-01-01', 'jakubik.maria@gmail.com', '+421908765432', 'Mierová 14', 'Nitra', '94901', 'Opravár potrubia', '2023-11-11', '', 8, 8),
('Peter', 'Novotný', '1970-01-01', 'peter.novotny@gmail.com', '+421901234567', 'Hlavná 10', 'Žilina', '01001', 'Vedúci údržby', '2022-08-20', '', 9, 9),
('Eva', 'Kristofíková', '1970-01-01', 'eva.kristofikova@gmail.com', '+421907654321', 'Továrenská 3', 'Trenčín', '91101', 'Systémový administrátor', '2023-09-15', '', 10, 10),
('Miroslav', 'Tomášik', '1970-01-01', 'miroslav.tomasik@gmail.com', '+421906543210', 'Dlhá 20', 'Poprad', '05801', 'Technik servisu strojov', '2024-04-01', '', 11, 11),
('Zuzana', 'Borošová', '1970-01-01', 'zuzana.borosova@gmail.com', '+421902345678', 'Jedlíková 8', 'Košice', '04011', 'Systémový administrátor', '2023-02-14', '', 12, 12),
('Miroslav', 'Matušík', '1970-01-01', 'miroslav.matusik@gmail.com', '+421901234567', 'Hlavná 10', 'Žilina', '01001', 'Technik údržby', '2023-07-10', '', 13, 13),
('Janka', 'Hlavačová', '1970-01-01', 'janka.hlavacova@gmail.com', '+421907654321', 'Továrenská 3', 'Trenčín', '91101', 'Systémový administrátor', '2024-01-05', '', 14, 14),
('Peter', 'Urbán', '1970-01-01', 'peter.urban@gmail.com', '+421906543210', 'Dlhá 20', 'Poprad', '05801', 'Technik servisu strojov', '2022-10-20', '', 15, 15),
('Veronika', 'Hričová', '1970-01-01', 'veronika.hricova@gmail.com', '+421902345678', 'Jedlíková 8', 'Košice', '04011', 'Vedúci údržby', '2023-04-14', '', 16, 16);

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `cinnost_opravy`
--
ALTER TABLE `cinnost_opravy`
  ADD PRIMARY KEY (`Cinnost_opravyID`);

--
-- Indexy pre tabuľku `cinnost_udrzby`
--
ALTER TABLE `cinnost_udrzby`
  ADD PRIMARY KEY (`Cinnost_udrzbyID`),
  ADD KEY `FK_Cinnost_udrzby_sa_vykonava` (`Planovana_udrzbaID`),
  ADD KEY `FK_Cinnost_udrzby_vykonava` (`PouzivatelID`);

--
-- Indexy pre tabuľku `dodavatel`
--
ALTER TABLE `dodavatel`
  ADD PRIMARY KEY (`DodavatelID`);

--
-- Indexy pre tabuľku `kategoria`
--
ALTER TABLE `kategoria`
  ADD PRIMARY KEY (`KategoriaID`);

--
-- Indexy pre tabuľku `nachadza`
--
ALTER TABLE `nachadza`
  ADD PRIMARY KEY (`NachadzaID`),
  ADD KEY `FK_Nachadza_Skladovy_doklad` (`Skladovy_dokladID`),
  ADD KEY `FK_Nachadza_Nahradny_diel` (`Nahradny_dielID`);

--
-- Indexy pre tabuľku `nahradny_diel`
--
ALTER TABLE `nahradny_diel`
  ADD PRIMARY KEY (`Nahradny_dielID`),
  ADD KEY `FK_Nahradny_diel_patri_do` (`KategoriaID`),
  ADD KEY `FK_Nahradny_diel_patri_ku` (`StrojID`);

--
-- Indexy pre tabuľku `oprava`
--
ALTER TABLE `oprava`
  ADD PRIMARY KEY (`OpravaID`),
  ADD KEY `FK_Oprava_obsahuje` (`Cinnost_opravyID`),
  ADD KEY `FK_Oprava_vykonava` (`PouzivatelID`),
  ADD KEY `FK_Oprava_odstranuje` (`PoruchaID`);

--
-- Indexy pre tabuľku `planovana_udrzba`
--
ALTER TABLE `planovana_udrzba`
  ADD PRIMARY KEY (`Planovana_udrzbaID`),
  ADD KEY `FK_Planovana_udrzba_sa_vykonava_na` (`StrojID`),
  ADD KEY `FK_Planovana_udrzba_ma_pridelenu` (`PouzivatelID`);

--
-- Indexy pre tabuľku `porucha`
--
ALTER TABLE `porucha`
  ADD PRIMARY KEY (`PoruchaID`),
  ADD KEY `FK_Porucha_nastala_na` (`StrojID`),
  ADD KEY `FK_Porucha_ma_pridelenu` (`PouzivatelID`);

--
-- Indexy pre tabuľku `pouziva`
--
ALTER TABLE `pouziva`
  ADD PRIMARY KEY (`PouzivaID`),
  ADD KEY `FK_Pouziva_Porucha` (`PoruchaID`),
  ADD KEY `FK_Pouziva_Nahradny_diel` (`Nahradny_dielID`);

--
-- Indexy pre tabuľku `pouzivatel`
--
ALTER TABLE `pouzivatel`
  ADD PRIMARY KEY (`PouzivatelID`),
  ADD KEY `FK_Pouzivatel_ma_pridelenu` (`RolaID`);

--
-- Indexy pre tabuľku `poziadavka_na_nahradny_diel`
--
ALTER TABLE `poziadavka_na_nahradny_diel`
  ADD PRIMARY KEY (`Poziadavka_na_nahradny_dielID`),
  ADD KEY `FK_Poziadavka_na_nahradny_diel_podava` (`PouzivatelID`);

--
-- Indexy pre tabuľku `priloha`
--
ALTER TABLE `priloha`
  ADD PRIMARY KEY (`PrilohaID`),
  ADD KEY `FK_Priloha_patri_ku` (`StrojID`);

--
-- Indexy pre tabuľku `rola`
--
ALTER TABLE `rola`
  ADD PRIMARY KEY (`RolaID`);

--
-- Indexy pre tabuľku `skladovy_doklad`
--
ALTER TABLE `skladovy_doklad`
  ADD PRIMARY KEY (`Skladovy_dokladID`),
  ADD KEY `FK_Skladovy_doklad_je_zapisany_na` (`DodavatelID`);

--
-- Indexy pre tabuľku `stroj`
--
ALTER TABLE `stroj`
  ADD PRIMARY KEY (`StrojID`),
  ADD KEY `FK_Stroj_dodava` (`DodavatelID`);

--
-- Indexy pre tabuľku `uvedeny`
--
ALTER TABLE `uvedeny`
  ADD PRIMARY KEY (`UvedenyID`),
  ADD KEY `FK_Uvedeny_Nahradny_diel` (`Nahradny_dielID`),
  ADD KEY `FK_Uvedeny_Poziadavka_na_nahradny_diel` (`Poziadavka_na_nahradny_dielID`);

--
-- Indexy pre tabuľku `zamestnanec`
--
ALTER TABLE `zamestnanec`
  ADD PRIMARY KEY (`ZamestnanecID`),
  ADD KEY `FK_Zamestnanec_Pouzivatel` (`PouzivatelID`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `cinnost_opravy`
--
ALTER TABLE `cinnost_opravy`
  MODIFY `Cinnost_opravyID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pre tabuľku `cinnost_udrzby`
--
ALTER TABLE `cinnost_udrzby`
  MODIFY `Cinnost_udrzbyID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `dodavatel`
--
ALTER TABLE `dodavatel`
  MODIFY `DodavatelID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pre tabuľku `kategoria`
--
ALTER TABLE `kategoria`
  MODIFY `KategoriaID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pre tabuľku `nachadza`
--
ALTER TABLE `nachadza`
  MODIFY `NachadzaID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `nahradny_diel`
--
ALTER TABLE `nahradny_diel`
  MODIFY `Nahradny_dielID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pre tabuľku `oprava`
--
ALTER TABLE `oprava`
  MODIFY `OpravaID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `planovana_udrzba`
--
ALTER TABLE `planovana_udrzba`
  MODIFY `Planovana_udrzbaID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `porucha`
--
ALTER TABLE `porucha`
  MODIFY `PoruchaID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pre tabuľku `pouziva`
--
ALTER TABLE `pouziva`
  MODIFY `PouzivaID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `pouzivatel`
--
ALTER TABLE `pouzivatel`
  MODIFY `PouzivatelID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pre tabuľku `poziadavka_na_nahradny_diel`
--
ALTER TABLE `poziadavka_na_nahradny_diel`
  MODIFY `Poziadavka_na_nahradny_dielID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `priloha`
--
ALTER TABLE `priloha`
  MODIFY `PrilohaID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `rola`
--
ALTER TABLE `rola`
  MODIFY `RolaID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pre tabuľku `skladovy_doklad`
--
ALTER TABLE `skladovy_doklad`
  MODIFY `Skladovy_dokladID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `stroj`
--
ALTER TABLE `stroj`
  MODIFY `StrojID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pre tabuľku `uvedeny`
--
ALTER TABLE `uvedeny`
  MODIFY `UvedenyID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `zamestnanec`
--
ALTER TABLE `zamestnanec`
  MODIFY `ZamestnanecID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `cinnost_udrzby`
--
ALTER TABLE `cinnost_udrzby`
  ADD CONSTRAINT `FK_Cinnost_udrzby_sa_vykonava` FOREIGN KEY (`Planovana_udrzbaID`) REFERENCES `planovana_udrzba` (`Planovana_udrzbaID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Cinnost_udrzby_vykonava` FOREIGN KEY (`PouzivatelID`) REFERENCES `pouzivatel` (`PouzivatelID`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `nachadza`
--
ALTER TABLE `nachadza`
  ADD CONSTRAINT `FK_Nachadza_Nahradny_diel` FOREIGN KEY (`Nahradny_dielID`) REFERENCES `nahradny_diel` (`Nahradny_dielID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Nachadza_Skladovy_doklad` FOREIGN KEY (`Skladovy_dokladID`) REFERENCES `skladovy_doklad` (`Skladovy_dokladID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `nahradny_diel`
--
ALTER TABLE `nahradny_diel`
  ADD CONSTRAINT `FK_Nahradny_diel_patri_do` FOREIGN KEY (`KategoriaID`) REFERENCES `kategoria` (`KategoriaID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Nahradny_diel_patri_ku` FOREIGN KEY (`StrojID`) REFERENCES `stroj` (`StrojID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `oprava`
--
ALTER TABLE `oprava`
  ADD CONSTRAINT `FK_Oprava_obsahuje` FOREIGN KEY (`Cinnost_opravyID`) REFERENCES `cinnost_opravy` (`Cinnost_opravyID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Oprava_odstranuje` FOREIGN KEY (`PoruchaID`) REFERENCES `porucha` (`PoruchaID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Oprava_vykonava` FOREIGN KEY (`PouzivatelID`) REFERENCES `pouzivatel` (`PouzivatelID`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `planovana_udrzba`
--
ALTER TABLE `planovana_udrzba`
  ADD CONSTRAINT `FK_Planovana_udrzba_ma_pridelenu` FOREIGN KEY (`PouzivatelID`) REFERENCES `pouzivatel` (`PouzivatelID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Planovana_udrzba_sa_vykonava_na` FOREIGN KEY (`StrojID`) REFERENCES `stroj` (`StrojID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `porucha`
--
ALTER TABLE `porucha`
  ADD CONSTRAINT `FK_Porucha_ma_pridelenu` FOREIGN KEY (`PouzivatelID`) REFERENCES `pouzivatel` (`PouzivatelID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Porucha_nastala_na` FOREIGN KEY (`StrojID`) REFERENCES `stroj` (`StrojID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `pouziva`
--
ALTER TABLE `pouziva`
  ADD CONSTRAINT `FK_Pouziva_Nahradny_diel` FOREIGN KEY (`Nahradny_dielID`) REFERENCES `nahradny_diel` (`Nahradny_dielID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Pouziva_Porucha` FOREIGN KEY (`PoruchaID`) REFERENCES `porucha` (`PoruchaID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `pouzivatel`
--
ALTER TABLE `pouzivatel`
  ADD CONSTRAINT `FK_Pouzivatel_ma_pridelenu` FOREIGN KEY (`RolaID`) REFERENCES `rola` (`RolaID`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `poziadavka_na_nahradny_diel`
--
ALTER TABLE `poziadavka_na_nahradny_diel`
  ADD CONSTRAINT `FK_Poziadavka_na_nahradny_diel_podava` FOREIGN KEY (`PouzivatelID`) REFERENCES `pouzivatel` (`PouzivatelID`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `priloha`
--
ALTER TABLE `priloha`
  ADD CONSTRAINT `FK_Priloha_patri_ku` FOREIGN KEY (`StrojID`) REFERENCES `stroj` (`StrojID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `skladovy_doklad`
--
ALTER TABLE `skladovy_doklad`
  ADD CONSTRAINT `FK_Skladovy_doklad_je_zapisany_na` FOREIGN KEY (`DodavatelID`) REFERENCES `dodavatel` (`DodavatelID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `stroj`
--
ALTER TABLE `stroj`
  ADD CONSTRAINT `FK_Stroj_dodava` FOREIGN KEY (`DodavatelID`) REFERENCES `dodavatel` (`DodavatelID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `uvedeny`
--
ALTER TABLE `uvedeny`
  ADD CONSTRAINT `FK_Uvedeny_Nahradny_diel` FOREIGN KEY (`Nahradny_dielID`) REFERENCES `nahradny_diel` (`Nahradny_dielID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Uvedeny_Poziadavka_na_nahradny_diel` FOREIGN KEY (`Poziadavka_na_nahradny_dielID`) REFERENCES `poziadavka_na_nahradny_diel` (`Poziadavka_na_nahradny_dielID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `zamestnanec`
--
ALTER TABLE `zamestnanec`
  ADD CONSTRAINT `FK_Zamestnanec_Pouzivatel` FOREIGN KEY (`PouzivatelID`) REFERENCES `pouzivatel` (`PouzivatelID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
