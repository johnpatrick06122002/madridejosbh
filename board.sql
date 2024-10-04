-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2024 at 01:10 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bhouse`
--

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` varchar(80) NOT NULL,
  `gender` varchar(80) NOT NULL,
  `contact_number` varchar(80) NOT NULL,
  `Address` varchar(80) NOT NULL,
  `landlord_id` int(100) NOT NULL,
  `bhouse_id` varchar(100) NOT NULL,
  `status` varchar(80) NOT NULL,
  `ratings` float NOT NULL,
  `feedback` varchar(1000) NOT NULL,
  `date_posted` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`id`, `name`, `age`, `gender`, `contact_number`, `Address`, `landlord_id`, `bhouse_id`, `status`, `ratings`, `feedback`, `date_posted`) VALUES
(7, 'Alter Lopez', '27', 'Male', '+639393612023', 'Mancilang, Madridejos, Cebu', 6, '80382', 'Approved', 4, 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2023-02-09'),
(8, 'Merbeth Polloso', '21', 'Female', '+639393612023', 'Lipayran, Bantayan, Cebu', 4, '8642', 'Approved', 0, '', '2023-02-09'),
(9, 'Manuel Jarina', '21', 'Male', '+639393612023', 'SIlay, Negros Occidental', 4, '8642', 'Approved', 0, '', '2023-02-09'),
(10, 'Clifford Client Santillan', '20', 'Male', '+639268011321', 'Brgy. Kabac', 1, '29164', 'Approved', 0, '', '2023-02-09'),
(11, 'Dianna Lyn Cena', '20', 'Female', '+639052953926', 'brgy. putian', 1, '29164', 'Approved', 0, '', '2023-02-09'),
(12, 'Agustin Mulle', '20', 'Male', '+639635020844', 'brgy. san agustin', 1, '29164', 'Approved', 0, '', '2023-02-09'),
(13, 'Arnejan Lorca', '22', 'Male', '+639657384732', 'brgy. san agustin', 1, '29164', 'Approved', 0, '', '2023-02-09'),
(14, 'Virsel Villarino', '20', 'Male', '+639565370308', 'brgy. bunakan', 2, '49742', 'Approved', 4, 'di', '2023-02-09'),
(15, 'Mylene Carallas', '20', 'Female', '+639685630325', 'brgy. kabangbang', 2, '49742', 'Approved', 0, 'GOOD', '2023-02-09'),
(16, 'Rochemlyn Catalba', '20', 'Female', '+639051211648', 'brgy. tabagak', 2, '49742', 'Approved', 0, 'HAPPY', '2023-02-09'),
(17, 'Shierley Mae Solliano', '20', 'Female', '+639472480173', 'Brgy. Sillion', 2, '49742', 'Approved', 0, 'HI', '2023-02-09'),
(18, 'Queenie Jane Bilbao', '20', 'Female', '+639275104214', 'Brgy. Kodia', 3, '67420', 'Approved', 0, '', '2023-02-09'),
(19, 'Kyla Marie Arranguez', '20', 'Female', '+639457021856', 'Brgy. Kangwayan', 3, '67420', 'Approved', 0, '', '2023-02-09'),
(20, 'Jorissa Carcosia', '20', 'Female', '+630912481192', 'Bgy. Talangnan, Madridejos, Cebu', 6, '80382', 'Approved', 0, '', '2023-02-09'),
(21, 'Jeraldene Illustrimo', '20', 'Female', '+630967011126', 'Brgy. Maalat', 6, '80382', 'Approved', 0, '', '2023-02-09'),
(22, 'Roxanne Carano-o', '19', 'Female', '+639519331843', 'brgy. atop-atop', 6, '80382', 'Approved', 0, '', '2023-02-09'),
(23, 'Ronel Secuya', '20', 'Male', '+639146726108', 'brgy. kabangbang', 6, '80382', 'Approved', 0, '', '2023-02-09'),
(24, 'Erwin Santander', '19', 'Male', '+639503019089', 'brgy.mancilang', 12, '42981', 'Approved', 0, '', '2023-02-08'),
(25, 'Manilyn De Mesa', '20', 'Female', '+639516270424', 'brgy.malbago', 12, '42981', 'Approved', 0, '', '2023-02-09'),
(26, 'Erica Adlit', '21', 'Female', '+639158239643', 'brgy.tarong', 12, '42981', '', 0, '', '2023-02-09'),
(27, 'Joann Rebamonte', '20', 'Female', '+639382451653', 'brgy.kodia', 12, '42981', '', 0, '', '2023-02-09'),
(28, 'Inna Santillan', '20', 'Female', '+639500367075', 'brgy.kaongkod', 12, '42981', '', 0, '', '2023-02-09'),
(29, 'Rosemarie Alontaga', '21', 'Female', '+639676974712', 'brgy.sillion', 12, '42981', '', 0, '', '2023-02-09'),
(30, 'Mary Rose Manalop', '20', 'Female', '+639635683820', 'bgry.kangkaibe', 13, '47941', '', 0, '', '2023-02-09'),
(31, 'Joemarie Escarro', '20', 'Female', '+639315635476', 'brgy.kangkaibe', 13, '47941', '', 0, '', '2023-02-09'),
(32, 'Jemaica Corridor', '20', 'Female', '+639276678477', 'brgy.kodia', 14, '68804', '', 0, '', '2023-02-09'),
(33, 'Marvin Sarabia', '22', 'Female', '+639307654323', 'brgy.tabagak', 14, '68804', '', 0, '', '2023-02-09'),
(34, 'Marjorie L. Bausin', '20', 'Female', '+639380791759', 'brgy. san agustin', 14, '68804', '', 0, '', '2023-02-09'),
(35, 'Jordan Descartin', '20', 'Male', '+639393609429', 'brgy.kaongkod', 14, '68804', '', 0, '', '2023-02-09'),
(36, 'Mary Joy Desabille', '20', 'Female', '+639197937999', 'brgy.san agustin', 14, '68804', '', 0, '', '2023-02-09'),
(37, 'Jamel M. Pahuriray', '20', 'Male', '+639471793354', 'brgy.atop-atop', 14, '68804', '', 0, '', '2023-02-09'),
(38, 'Rhacel Pastor', '20', 'Female', '+639694447760', 'brgy. san agustin', 15, '67367', '', 0, '', '2023-02-09'),
(39, 'Marifel Dela Pena', '20', 'Female', '+639304875436', 'brgy. atop-atop', 15, '67367', '', 0, '', '2023-02-09'),
(40, 'Flora May Negrido', '20', 'Female', '+639701759074', 'brgy. atop-atop', 15, '67367', '', 0, '', '2023-02-09'),
(41, 'Rojard Santillan', '20', 'Male', '+639481764255', 'brgy.kaongkod', 16, '59275', '', 0, '', '2023-02-09'),
(42, 'Jessamae Batiancila', '20', 'Female', '+639071010053', 'brgy. san agustin', 16, '59275', '', 0, '', '2023-02-09'),
(43, 'Claugine Sotes ', '20', 'Male', '+639502319053', 'brgy.mancilang', 16, '59275', '', 0, '', '2023-02-09'),
(44, 'Meca Jean Compra', '20', 'Female', '+639704973164', 'brgy.mancilang', 17, '55644', '', 0, '', '2023-02-09'),
(45, 'Remy Espleguera', '20', 'Female', '+639481754993', 'brgy.maricaban', 17, '55644', '', 0, '', '2023-02-09'),
(46, 'Ruel Espleguera ', '20', 'Male', '+639452433578', 'brgy.maricaban', 17, '55644', '', 0, '', '2023-02-09'),
(47, 'Althea Ilosorio', '21', 'Female', '+639309824537', 'brgy.maricaban', 17, '55644', '', 0, '', '2023-02-09'),
(48, 'Danilyn Ysulan', '20', 'Female', '+639487621344', 'brgy.mancilang', 18, '84123', '', 0, '', '2023-02-09'),
(49, 'Verlyn Joy Desuyo', '19', 'Female', '+639453288731', 'brgy. san agustin', 18, '84123', '', 0, '', '2023-02-09'),
(50, 'Jennifer Quezon', '20', 'Female', '+639453257521', 'brgy. san agustin', 18, '84123', '', 0, '', '2023-02-09'),
(51, 'John Rey Bayon-on', '20', 'Male', '+639634526127', 'brgy. atop-atop', 19, '88835', '', 0, '', '2023-02-09'),
(52, 'Elmer Tiongzon', '21', 'Male', '+639638763290', 'brgy.balidbid', 19, '88835', '', 0, '', '2023-02-09'),
(53, 'Mark Andre Santillan', '19', 'Male', '+639487263537', 'brgy.kampingganon', 19, '88835', '', 0, '', '2023-02-09'),
(54, 'Laycy Quizon', '20', 'Female', '+639502826252', 'brgy.bantigue', 20, '56792', '', 0, '', '2023-02-09'),
(55, 'Fil Mark Dela Pena', '20', 'Male', '+639307252351', 'brgy.suba', 20, '56792', '', 0, '', '2023-02-09'),
(56, 'Elmira Sacro', '19', 'Female', '+639452377132', 'brgy. san agustin', 20, '56792', '', 0, '', '2023-02-09'),
(57, 'Ohwens Vinzon', '21', 'Male', '+639515242614', 'brgy.sungko', 21, '48201', '', 0, '', '2023-02-09'),
(58, 'John Dave Villarino', '23', 'Male', '+639508262452', 'brgy.bantigue', 21, '48201', '', 0, '', '2023-02-09'),
(59, 'Daven Desabille', '23', 'Male', '+639307741361', 'brgy.pooc', 21, '48201', '', 0, '', '2023-02-09'),
(60, 'Rommel Carascal', '22', 'Male', '+639752334256', 'brgy.binaobao', 21, '48201', '', 0, '', '2023-02-09'),
(61, 'Alona Jane Cordova', '23', 'Female', '+639482614337', 'brgy.ticad', 22, '52351', '', 0, '', '2023-02-09'),
(62, 'Rayver Villacstin', '20', 'Male', '+639324522625', 'brgy.kabac', 22, '52351', '', 0, '', '2023-02-09'),
(63, 'Alejandra Maria', '21', 'Female', '+639306242325', 'brgy.ticad', 22, '52351', '', 0, '', '2023-02-09'),
(64, 'Jizan Arcenas', '21', 'Male', '+639482356372', 'brgy.suba', 17, '55644', '', 0, '', '2023-02-09'),
(65, 'Alter Lopez', '21', 'Male', '+639667444443', 'brgy. san agustin', 2, '49742', 'Approved', 0, '', '2023-02-09'),
(66, 'Sadako TV', '21', 'Male', '+639932685247', 'Talangnan, Madridejos, Cebu', 2, '49742', 'Approved', 0, '', '2024-07-02'),
(71, 'Steph Curry', '21', 'Male', '+639932685248', 'Talangnan, Madridejos, Cebu', 2, '47586', 'Approved', 0, '', '2024-07-10'),
(72, 'HI Hello', '24', 'Female', '+639932685247', 'Atop-Atop, Bantayan, Cebu', 2, '47586', 'Approved', 0, '', '2024-07-17');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(100) NOT NULL,
  `file_name` varchar(1000) NOT NULL,
  `rental_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `file_name`, `rental_id`) VALUES
(1, '319350181_3528205807399098_7574708102915797146_n.jpg', 49742),
(2, '318793843_695087685318499_4927870333407687896_n.jpg', 49742),
(3, '319712509_1255160028399834_2891128535582240831_n.jpg', 49742),
(4, '318865155_831196754855712_1046553415451571587_n.jpg', 49742),
(5, '319202091_687711276258467_2616320356618321017_n.jpg', 67420),
(6, '319301143_490155126543396_1488915345303805261_n.jpg', 67420),
(7, '319398751_1576271656143644_7333585625655305784_n.jpg', 67420),
(8, '319192273_861508731856569_1086864154705813976_n.jpg', 67420),
(9, '315530714_694773658543231_2512873439984577557_n.jpg', 29164),
(10, '316020538_805541784011788_6599158340626758338_n.jpg', 29164),
(11, '315520737_541688830712386_4425588198559822899_n.jpg', 29164),
(12, '319285633_534125662090721_8079977625671238846_n.jpg', 80382),
(13, '319408733_678242380378633_3688061559774504296_n.jpg', 80382),
(14, '319305936_2314851522021843_5124053784924353182_n.jpg', 80382),
(15, '319142824_823777658698730_4363000098125751608_n.jpg', 80382),
(16, '319265353_828515764920999_5233980133024770306_n.jpg', 8642),
(17, '319224932_5420103994778700_3465244343071650637_n.jpg', 8642),
(18, 'suncitte (4).jpg', 42981),
(19, 'suncitte (3).jpg', 42981),
(20, 'suncitte (kitchen).jpg', 42981),
(21, 'suncitte (cr).jpg', 42981),
(22, 'suncitte (6).jpg', 42981),
(23, 'suncitte (thumb).jpg', 42981),
(24, 'chavez (thumb).jpg', 47941),
(25, 'chavez (4).jpg', 47941),
(26, 'chavez (3).jpg', 47941),
(27, 'chavez (2).jpg', 47941),
(28, 'artem (1).jpg', 68804),
(29, 'artem (5).jpg', 68804),
(30, 'artem (4).jpg', 68804),
(31, 'artem (3).jpg', 68804),
(32, 'artem (thumb).jpg', 68804),
(33, 'hermenia (2).jpg', 67367),
(34, 'hermenia (3).jpg', 67367),
(35, 'hermenia (thumb).jpg', 67367),
(36, 'danilo (1).jpg', 59275),
(37, 'danilo (4).jpg', 59275),
(38, 'danilo (3).jpg', 59275),
(39, 'danilo (thumb).jpg', 59275),
(40, 'jomar (thumb).jpg', 55644),
(41, 'jomar (6).jpg', 55644),
(42, 'jomar (5).jpg', 55644),
(43, 'jomar (4).jpg', 55644),
(44, 'jomar (3).jpg', 55644),
(45, 'jomar (2).jpg', 55644),
(46, 'janeth (1).jpg', 84123),
(47, 'janeth (cr).jpg', 84123),
(48, 'janeth (5).jpg', 84123),
(49, 'janeth (4).jpg', 84123),
(50, 'janeth (3).jpg', 84123),
(51, 'janeth (thumb).jpg', 84123),
(52, 'vesymenda.jpg', 88835),
(53, 'victor (1).jpg', 56792),
(54, 'victor (cr).jpg', 56792),
(55, 'victor (thumb).jpg', 56792),
(56, 'victor (2).jpg', 56792),
(57, 'edna (1).jpg', 48201),
(58, 'edna (3).jpg', 48201),
(59, 'edna (thumb).jpg', 48201),
(60, 'elpidio (5).jpg', 52351),
(61, 'elpidio (hall).jpg', 52351),
(62, 'elpidio (cr).jpg', 52351),
(63, 'elpidio (kit).jpg', 52351),
(64, 'elpidio (thumb).jpg', 52351),
(65, 'herme (2).jpg', 67367),
(66, 'herme (3).jpg', 67367),
(67, 'herme (1).jpg', 67367),
(68, 'PLAZA VERDE (Main).jpg', 22464),
(69, 'PLAZA VERDE (2).jpg', 22464),
(70, 'PLAZA VERDE (3).jpg', 22464),
(71, 'janeth (3).jpg', 47586),
(72, 'janeth (4).jpg', 47586),
(73, 'janeth (5).jpg', 47586),
(74, 'chavez (3).jpg', 49742),
(75, 'chavez (4).jpg', 49742),
(76, 'chavez (thumb).jpg', 49742),
(77, 'victor (2).jpg', 29164),
(78, 'victor (cr).jpg', 29164),
(79, 'victor (thumb).jpg', 29164);

-- --------------------------------------------------------

--
-- Table structure for table `landlords`
--

CREATE TABLE `landlords` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `Address` varchar(80) NOT NULL,
  `profile_photo` varchar(80) NOT NULL,
  `contact_number` varchar(80) NOT NULL,
  `facebook` varchar(80) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landlords`
--

INSERT INTO `landlords` (`id`, `name`, `email`, `Address`, `profile_photo`, `contact_number`, `facebook`, `password`, `status`) VALUES
(1, 'Tonie Verder', 'tonieverde@gmail.com', 'Mancilang, Madridejos, Cebu', '9.jpg', '+639393612023', 'http://facebook.com/tonieverde', '123', 'Approved'),
(2, 'Mariah Lezel Hiis', 'MariahLezelHyir@yahoo.com', 'Purok Mauswagon, Brgy. Pili, Madridejos, Cebu', '9.jpg', '+639393612023', 'http://facebook.com/mariahlezel', '123', 'Approved'),
(3, 'Gina Ilosorio', 'rowelailosorio@yahoo.com', 'Purok Mauswagon, Brgy. Pili, Madridejos, Cebu', 'female.jpg', '+639393612023', 'http://facebook.com/ilosoriogina', '123', 'Approved'),
(4, 'Carmen Alon', 'carmenalon@gmail.com', 'Purok Mauswagon, Brgy. Pili, Madridejos, Cebu', 'female.jpg', '+639393612023', 'http://facebook.com/carmenalon', '123', 'Approved'),
(6, 'Amelita Espleguera', 'edmel@gmail.com', 'Bunakan, Madridejos, Cebu', 'female.jpg', '+639393612023', 'http://facebook.com/edmels', '123', 'Approved'),
(12, 'Suncitte Locaylocay', 'angelicaquiamco@gmail.com', 'Purok 10, Barangay Poblacion, Madridejos, Cebu', 'female.jpg', '+639319720509', 'http://facebook.com/angelicaquiamco', '123', 'Approved'),
(13, 'Wilfreda Chavez', 'wilfredachavez@gmail.com', 'Purok 9, Barangay Poblacion, Madridejos, Cebu', 'female.jpg', '+639393612023', 'http://facebook.com/wilfredachavez', '123', 'Approved'),
(14, 'Artemio Jarina', 'artemiojarina@gmail.com', 'Purok 9, Barangay Poblacion, Madridejos, Cebu', 'male.jpg', '+639215754454', 'http://facebook.com/artemiojarina', '123', 'Approved'),
(15, 'Hermenia Medallo', 'hermeniamedallo@gmail.com', 'Purok 8, Barangay Poblacion, Madridejos, Cebu', 'female.jpg', '+639394054120', 'http://facebook.com/medallohermenia', '123', 'Approved'),
(16, 'Danilo Rebamonte', 'kirbyrebamonte@yahoo.com', 'Purok 16, Barangay Poblacion, Madridejos, Cebu', 'male.jpg', '+639274463991', 'http://facebook.com/danilorebamonte', '123', 'Approved'),
(17, 'Jomar Molijons', 'jomarmolijon@yahoo.com', 'Purok 8, Barangay Poblacion, Madridejos, Cebu', 'male.jpg', '+639393612023', 'http://facebook.com/jomarmolijon', '123', 'Approved'),
(18, 'Janeth', 'janeth@gmail.com', 'Purok 8, Barangay Poblacion, Madridejos, Cebu', 'female.jpg', '+639393612023', 'http://facebook.com/janeth', '123', 'Approved'),
(19, 'Vesymenda Forrosuelo', 'vesymenda@gmail.com', 'Purok 8, Barangay Poblacion, Madridejos, Cebu', 'female.jpg', '+639393612023', 'http://facebook.com/vesymendaforrosuelo', '123', 'Approved'),
(20, 'Victor Almodiel', 'victoralmodiel@gmail.com', 'Purok 7, Barangay Poblacion, Madridejos, Cebu', 'male.jpg', '+639393612023', 'http://facebook.com/victoralmodiel', '123', 'Approved'),
(21, 'Edna Pelayo', 'ednapelayo@gmail.com', 'Purok 8, Barangay Poblacion, Madridejos, Cebu', 'female.jpg', '+639393612023', 'http://facebook.com/ednapelayo', '123', 'Approved'),
(22, 'Elpidio Cordova', 'elpideiocordova@gmail.com', 'Purok 8, Barangay Poblacion, Madridejos, Cebu', 'male.jpg', '+639393612023', 'http://facebook.com/elpidiocordova', '123', 'Approved'),
(37, 'azaza', 'kenethducay12@gmail.com', 'Tarong, Madridejos, Cebu', 'male.jpg', '+639932685248', 'https://www.facebook.com/keneth.batusbatusan', '123', ''),
(38, 'afafa', 'kenethducay12@gmail.com', 'Atop-Atop, Bantayan, Cebu', 'male.jpg', '+639932685247', 'https://www.facebook.com/erica.adlit', '123', '');

-- --------------------------------------------------------

--
-- Table structure for table `rental`
--

CREATE TABLE `rental` (
  `id` int(100) NOT NULL,
  `rental_id` int(255) NOT NULL,
  `landlord_id` int(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `contact_number` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `slots` varchar(100) NOT NULL,
  `map` varchar(1000) NOT NULL,
  `photo` varchar(1000) NOT NULL,
  `monthly` float DEFAULT NULL,
  `description` varchar(10000) NOT NULL,
  `wifi` varchar(80) NOT NULL,
  `water` varchar(80) NOT NULL,
  `kuryente` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rental`
--

INSERT INTO `rental` (`id`, `rental_id`, `landlord_id`, `title`, `contact_number`, `address`, `slots`, `map`, `photo`, `monthly`, `description`, `wifi`, `water`, `kuryente`) VALUES
(0, 47586, 2, 'Happy Birthdays', '', 'Malbago, Madridejos, Cebu', '10', '', 'janeth (1).jpg', 3300, '<div><br></div>', 'no', 'no', 'no'),
(1, 49742, 2, 'LEZEL BOARDING HOUSE', '', 'Purok Mauswagon, Brgy. Pili, Madridejos, Cebu', '32', '', 'chavez (3).jpg', 600, '<div><br></div>', 'no', 'no', 'no'),
(2, 67420, 3, 'GINA BOARDING HOUSE', '', 'Purok Mauswagon, Brgy. Pili, Madridejos, Cebu', '8', '', 'chavez (3).jpg', 700, '<div><br></div>', 'no', 'no', 'no'),
(3, 29164, 1, 'PLAZA VERDE', '', 'Mancilang, Madidejos, Cebu', '14', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'chavez (3).jpg', 1000, '<div><br></div>', 'yes', 'yes', 'no'),
(4, 80382, 6, 'EDMELS BOARDING HOUSE', '', 'Purok Kalubihan, Brgy. Bunakan, Madridejos, Cebu', '18', 'https://maps.google.com/maps?q=11.260677389698051,123.72298119294382&t=&z=15&ie=UTF8&iwloc=&output=embed', '319305936_2314851522021843_5124053784924353182_n.jpg', 500, '<div><br></div>', 'no', 'no', 'no'),
(5, 8642, 4, 'CDA DORM', '', 'Purok Mauswagon, Brgy. Pili, Madridejos, Cebu', '2', '', '', 1200, '<div><br></div>', 'no', 'no', 'no'),
(6, 42981, 12, 'SUNCITTE BOARDING HOUSE', '', 'Purok 10, Poblacion, Madridejos, Cebu', '20', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'suncitte (thumb).jpg', 650, '<div><br></div>', 'no', 'no', 'no'),
(7, 47941, 13, 'CHAVEZ BOARDING HOUSE', '', 'Purok 9, Poblacion, Madridejos, Cebu', '4', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'chavez (thumb).jpg', 600, '<div><br></div>', 'no', 'no', 'no'),
(8, 68804, 14, 'JARINA Boarding House', '', 'Purok 9, Poblacion, Madridejos, Cebu', '24', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'artem (thumb).jpg', 500, '<div><br></div>', 'no', 'no', 'no'),
(9, 67367, 15, 'HERMENIAS Boarding House', '', 'Purok 8, Poblacion, Madridejos, Cebu', '16', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'herme (1).jpg', 600, '<div><br></div>', 'no', 'no', 'no'),
(10, 59275, 16, 'REBAMONTES Boarding House', '', 'Purok 16, Poblacion, Madridejos, Cebu', '16', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'danilo (thumb).jpg', 600, '<div><br></div>', 'no', 'no', 'no'),
(11, 55644, 17, 'JOMAR Boarding House', '', 'Purok 8, Poblacion, Madridejos, Cebu', '36', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'jomar (thumb).jpg', 650, '<div><br></div>', 'no', 'no', 'no'),
(12, 84123, 18, 'JANETH Boarding House', '', 'Purok 3, Poblacion, Madridejos, Cebu', '10', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'janeth (thumb).jpg', 600, '<div><br></div>', 'no', 'no', 'no'),
(13, 88835, 19, 'MINDA Boarding House', '', 'Purok 9, Poblacion, Madridejos, Cebu', '18', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'vesymenda.jpg', 600, '<div><br></div>', 'no', 'no', 'no'),
(14, 56792, 20, 'LUTONG NANAY BH', '', 'Purok 8, Poblacion, Madridejos, Cebu', '24', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'victor (thumb).jpg', 650, '<div><br></div>', 'no', 'no', 'no'),
(15, 48201, 21, 'PELAYO Boarding House', '', 'Purok 8, Poblacion, Madridejos, Cebu', '30', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'edna (thumb).jpg', 700, '<div><br></div>', 'no', 'no', 'no'),
(16, 52351, 22, 'MAKRAM Boarding House', '', 'Purok 8, Poblacion, Madridejos, Cebu', '36', 'https://maps.google.com/maps?q=11.21367525852147,123.73793119189466&t=&z=15&ie=UTF8&iwloc=&output=embed', 'elpidio (thumb).jpg', 800, '<div><br></div>', 'no', 'no', 'no');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `landlords`
--
ALTER TABLE `landlords`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `landlords`
--
ALTER TABLE `landlords`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
