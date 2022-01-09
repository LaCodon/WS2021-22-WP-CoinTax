-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.6.4-MariaDB - mariadb.org binary distribution
-- Server Betriebssystem:        Win64
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Exportiere Datenbank Struktur für cointax
CREATE DATABASE IF NOT EXISTS `cointax` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci */;
USE `cointax`;

-- Exportiere Struktur von Tabelle cointax.coin
CREATE TABLE IF NOT EXISTS `coin` (
  `coin_id` int(10) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `coingecko_id` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `thumbnail_url` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`coin_id`),
  UNIQUE KEY `symbol` (`symbol`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Exportiere Daten aus Tabelle cointax.coin: ~13 rows (ungefähr)
DELETE FROM `coin`;
/*!40000 ALTER TABLE `coin` DISABLE KEYS */;
INSERT INTO `coin` (`coin_id`, `symbol`, `name`, `coingecko_id`, `thumbnail_url`) VALUES
	(1, 'EUR', 'Euro', NULL, 'https://assets.coingecko.com/coins/images/5164/small/EURS_300x300.png?1550571779'),
	(2, 'BTC', 'Bitcoin', 'bitcoin', 'https://assets.coingecko.com/coins/images/1/small/bitcoin.png?1547033579'),
	(7, 'TXL', 'Tixl', 'tixl-new', 'https://assets.coingecko.com/coins/images/12432/small/Tixl-Logo-200-transparent.png?1610248504'),
	(8, 'ADA', 'Cardano', 'cardano', 'https://assets.coingecko.com/coins/images/975/small/cardano.png?1547034860'),
	(9, 'ETH', 'Ethereum', 'ethereum', 'https://assets.coingecko.com/coins/images/279/small/ethereum.png?1595348880'),
	(10, 'BUSD', 'Binance USD', 'binance-usd', 'https://assets.coingecko.com/coins/images/9576/small/BUSD.png?1568947766'),
	(11, 'DOT', 'Polkadot', 'polkadot', 'https://assets.coingecko.com/coins/images/12171/small/aJGBjJFU_400x400.jpg?1597804776'),
	(12, 'XRP', 'XRP', 'ripple', 'https://assets.coingecko.com/coins/images/44/small/xrp-symbol-white-128.png?1605778731'),
	(13, 'LUNA', 'Terra', 'terra-luna', 'https://assets.coingecko.com/coins/images/8284/small/luna1557227471663.png?1567147072'),
	(14, 'BNB', 'Binance Coin', 'binancecoin', 'https://assets.coingecko.com/coins/images/825/small/binance-coin-logo.png?1547034615'),
	(15, 'OGN', 'Origin Protocol', 'origin-protocol', 'https://assets.coingecko.com/coins/images/3296/small/op.jpg?1547037878'),
	(16, 'RVN', 'Ravencoin', 'ravencoin', 'https://assets.coingecko.com/coins/images/3412/small/ravencoin.png?1548386057'),
	(17, 'USDT', 'Tether', 'tether', 'https://assets.coingecko.com/coins/images/325/small/Tether-logo.png?1598003707');
/*!40000 ALTER TABLE `coin` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle cointax.coin_value
CREATE TABLE IF NOT EXISTS `coin_value` (
  `coin_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `eur_value` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `datetime_utc` datetime NOT NULL,
  `coin_id` int(11) NOT NULL,
  PRIMARY KEY (`coin_value_id`),
  KEY `datetime_utc` (`datetime_utc`),
  KEY `value_to_coin` (`coin_id`),
  CONSTRAINT `value_to_coin` FOREIGN KEY (`coin_id`) REFERENCES `coin` (`coin_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Exportiere Daten aus Tabelle cointax.coin_value: ~104 rows (ungefähr)
DELETE FROM `coin_value`;
/*!40000 ALTER TABLE `coin_value` DISABLE KEYS */;
INSERT INTO `coin_value` (`coin_value_id`, `eur_value`, `datetime_utc`, `coin_id`) VALUES
	(2, '37.0445905707060000000000000', '2021-11-01 00:00:00', 11),
	(3, '0.8636097545705400000000000', '2021-11-09 00:00:00', 10),
	(4, '564.9109322466600000000000000', '2021-11-09 00:00:00', 14),
	(5, '2100.5675978708000000000000000', '2021-04-16 00:00:00', 9),
	(6, '453.6716756233800000000000000', '2021-04-16 00:00:00', 14),
	(7, '0.8427817075430000000000000', '2021-09-07 00:00:00', 10),
	(8, '0.8502180421841900000000000', '2021-08-25 00:00:00', 10),
	(9, '37239.9388387410000000000000000', '2021-08-09 00:00:00', 2),
	(10, '0.8213406907545700000000000', '2021-05-22 00:00:00', 10),
	(11, '38340.0202191300000000000000000', '2021-05-17 00:00:00', 2),
	(12, '46274.2552812880000000000000000', '2021-04-20 00:00:00', 2),
	(13, '52786.1944405240000000000000000', '2021-04-16 00:00:00', 2),
	(14, '50399.5356885180000000000000000', '2021-04-12 00:00:00', 2),
	(15, '47281.0654813760000000000000000', '2021-04-08 00:00:00', 2),
	(16, '49682.8148478500000000000000000', '2021-04-06 00:00:00', 2),
	(17, '49486.9124856050000000000000000', '2021-04-05 00:00:00', 2),
	(18, '47277.4920424580000000000000000', '2021-03-29 00:00:00', 2),
	(19, '0.8407108132782600000000000', '2021-03-21 00:00:00', 17),
	(20, '49256.5126041630000000000000000', '2021-03-18 00:00:00', 2),
	(21, '47746.8466235180000000000000000', '2021-03-17 00:00:00', 2),
	(22, '47981.1507840840000000000000000', '2021-03-13 00:00:00', 2),
	(23, '40960.3372001340000000000000000', '2021-02-25 00:00:00', 2),
	(24, '356.8412112631000000000000000', '2021-09-10 00:00:00', 14),
	(25, '350.3109976537100000000000000', '2021-09-09 00:00:00', 14),
	(26, '418.9238789171500000000000000', '2021-09-07 00:00:00', 14),
	(27, '402.0607688165500000000000000', '2021-08-25 00:00:00', 14),
	(28, '387.6155597548800000000000000', '2021-08-21 00:00:00', 14),
	(29, '290.6119175858200000000000000', '2021-08-09 00:00:00', 14),
	(30, '214.4393549101000000000000000', '2021-05-24 00:00:00', 14),
	(31, '264.6032198436100000000000000', '2021-05-22 00:00:00', 14),
	(32, '464.0783815185000000000000000', '2021-05-17 00:00:00', 14),
	(33, '418.2598369240900000000000000', '2021-04-20 00:00:00', 14),
	(34, '439.2856415602900000000000000', '2021-04-12 00:00:00', 14),
	(35, '317.5449319108200000000000000', '2021-04-08 00:00:00', 14),
	(36, '310.9536389255100000000000000', '2021-04-06 00:00:00', 14),
	(37, '296.6948765920000000000000000', '2021-04-05 00:00:00', 14),
	(38, '1.1812763251122000000000000', '2021-03-29 00:00:00', 15),
	(39, '43661.5417746150000000000000000', '2021-03-26 00:00:00', 2),
	(40, '0.1847129982470000000000000', '2021-03-18 00:00:00', 16),
	(41, '1.0466163623933000000000000', '2021-03-17 00:00:00', 8),
	(42, '0.8672260287171700000000000', '2021-03-13 00:00:00', 8),
	(43, '45969.8362970020000000000000000', '2021-03-10 00:00:00', 2),
	(44, '0.1498408955396700000000000', '2021-02-25 00:00:00', 16),
	(45, '1338.0200122698000000000000000', '2021-02-25 00:00:00', 9),
	(46, '16.8037295092080000000000000', '2021-08-09 00:00:00', 11),
	(47, '1.3771316956132000000000000', '2021-04-20 00:00:00', 15),
	(48, '0.7679145279217500000000000', '2021-04-06 00:00:00', 12),
	(49, '2800.7081484649000000000000000', '2021-08-21 00:00:00', 9),
	(50, '3321.3657576919000000000000000', '2021-09-07 00:00:00', 9),
	(51, '1.2015475763174000000000000', '2021-05-17 00:00:00', 12),
	(52, '2.4503374658559000000000000', '2021-04-08 00:00:00', 15),
	(53, '0.1850042325139800000000000', '2021-03-21 00:00:00', 16),
	(54, '0.1076307169258000000000000', '2021-11-11 00:00:00', 16),
	(55, '2906.1071592974000000000000000', '2021-09-10 00:00:00', 9),
	(56, '2958.8008219867000000000000000', '2021-09-09 00:00:00', 9),
	(57, '1740.3174803615000000000000000', '2021-05-24 00:00:00', 9),
	(58, '57377.2065894240000000000000000', '2021-11-15 00:00:00', 2),
	(59, '13599.0433469630000000000000000', '2020-11-15 00:00:00', 2),
	(60, '54624.7169635870000000000000000', '2021-11-03 00:00:00', 2),
	(61, '0.1118364994193400000000000', '2021-11-16 00:00:00', 16),
	(62, '0.0684785383382930000000000', '2021-06-01 00:00:00', 16),
	(63, '0.1045785354101900000000000', '2021-11-01 00:00:00', 16),
	(64, '0.0217735910098690000000000', '2020-08-17 00:00:00', 16),
	(65, '0.0456097666262160000000000', '2021-07-17 00:00:00', 16),
	(66, '15016.2462733840000000000000000', '2020-11-20 00:00:00', 2),
	(67, '453.9673617421200000000000000', '2021-11-01 00:00:00', 14),
	(68, '18.1788554438210000000000000', '2019-11-18 00:00:00', 14),
	(69, '254.9039292759800000000000000', '2021-07-18 00:00:00', 14),
	(70, '0.0926634501232380000000000', '2021-10-18 00:00:00', 16),
	(71, '0.3138706416637000000000000', '2021-05-06 00:00:00', 7),
	(72, '542.8624733996300000000000000', '2021-05-06 00:00:00', 14),
	(73, '0.8328244914856900000000000', '2021-05-06 00:00:00', 10),
	(74, '0.8252645871484400000000000', '2021-06-07 00:00:00', 10),
	(75, '322.2406876752700000000000000', '2021-06-07 00:00:00', 14),
	(76, '0.1155386908692800000000000', '2021-06-07 00:00:00', 7),
	(77, '0.1175729414686600000000000', '2021-08-01 00:00:00', 7),
	(78, '282.2828389345900000000000000', '2021-08-01 00:00:00', 14),
	(79, '0.1629515477395700000000000', '2021-08-25 00:00:00', 7),
	(80, '0.3287498797888200000000000', '2021-09-07 00:00:00', 7),
	(81, '0.8408287151718300000000000', '2021-08-01 00:00:00', 10),
	(82, '455.7906848823900000000000000', '2021-10-31 00:00:00', 14),
	(83, '0.1704533361801200000000000', '2021-11-05 00:00:00', 7),
	(84, '485.6041503700300000000000000', '2021-11-05 00:00:00', 14),
	(85, '0.8694417077710200000000000', '2021-11-05 00:00:00', 10),
	(86, '0.1470096813067600000000000', '2021-05-22 00:00:00', 7),
	(87, '0.8615084869690800000000000', '2021-10-22 00:00:00', 10),
	(88, '406.6702554557600000000000000', '2021-10-22 00:00:00', 14),
	(89, '0.1992987346040100000000000', '2021-10-22 00:00:00', 7),
	(90, '47831.0611679250000000000000000', '2021-05-06 00:00:00', 2),
	(91, '48864.6390908390000000000000000', '2021-03-21 00:00:00', 2),
	(92, '0.4153944666215100000000000', '2021-03-22 00:00:00', 7),
	(93, '48446.4637259900000000000000000', '2021-03-22 00:00:00', 2),
	(94, '431.6238807888200000000000000', '2021-10-21 00:00:00', 14),
	(95, '35.7845734073780000000000000', '2021-10-25 00:00:00', 13),
	(96, '410.6449247053600000000000000', '2021-10-25 00:00:00', 14),
	(97, '0.8633014223034100000000000', '2021-10-25 00:00:00', 17),
	(98, '52556.4976924250000000000000000', '2021-10-25 00:00:00', 2),
	(99, '26597.7041757130000000000000000', '2021-07-17 00:00:00', 2),
	(100, '53090.0786851270000000000000000', '2021-10-18 00:00:00', 2),
	(101, '0.1847127937202500000000000', '2021-10-18 00:00:00', 7),
	(102, '417.5529972891400000000000000', '2021-10-19 00:00:00', 14),
	(103, '0.2042608615102900000000000', '2021-10-21 00:00:00', 7),
	(104, '56825.6962065380000000000000000', '2021-10-21 00:00:00', 2),
	(105, '53799.4690479710000000000000000', '2021-10-22 00:00:00', 2),
	(106, '0.8847145793138500000000000', '2021-11-30 00:00:00', 10),
	(107, '552.2633580444300000000000000', '2021-11-30 00:00:00', 14),
	(108, '0.1486181944148700000000000', '2021-11-30 00:00:00', 7),
	(109, '25.7578201846100000000000000', '2020-10-25 00:00:00', 14),
	(110, '30.6124088927980000000000000', '2021-01-01 00:00:00', 14);
/*!40000 ALTER TABLE `coin_value` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle cointax.order
CREATE TABLE IF NOT EXISTS `order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `base_transaction` int(11) DEFAULT NULL,
  `quote_transaction` int(11) DEFAULT NULL,
  `fee_transaction` int(11) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `order_to_base` (`base_transaction`),
  KEY `order_to_fee` (`fee_transaction`),
  KEY `order_to_quote` (`quote_transaction`),
  CONSTRAINT `order_to_base` FOREIGN KEY (`base_transaction`) REFERENCES `transaction` (`transaction_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `order_to_fee` FOREIGN KEY (`fee_transaction`) REFERENCES `transaction` (`transaction_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `order_to_quote` FOREIGN KEY (`quote_transaction`) REFERENCES `transaction` (`transaction_id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Exportiere Daten aus Tabelle cointax.order: ~0 rows (ungefähr)
DELETE FROM `order`;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` (`order_id`, `base_transaction`, `quote_transaction`, `fee_transaction`) VALUES
	(1, 1009, 1010, 1011),
	(2, 1012, 1013, 1014),
	(3, 1015, 1016, 1017),
	(4, 1018, 1019, 1020),
	(5, 1021, 1022, 1023);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle cointax.payment_info
CREATE TABLE IF NOT EXISTS `payment_info` (
  `payment_info_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `iban` varchar(34) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `bic` varchar(11) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `year` varchar(4) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `fulfilled` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `failed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`payment_info_id`),
  UNIQUE KEY `user_id_year` (`user_id`,`year`),
  CONSTRAINT `user_to_payment` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Exportiere Daten aus Tabelle cointax.payment_info: ~0 rows (ungefähr)
DELETE FROM `payment_info`;
/*!40000 ALTER TABLE `payment_info` DISABLE KEYS */;
INSERT INTO `payment_info` (`payment_info_id`, `user_id`, `iban`, `bic`, `year`, `fulfilled`, `failed`) VALUES
	(1, 33, 'DE43123453453565342345', 'MBISCCDE', '2021', 1, 0),
	(2, 33, 'DE43123453453565342345', 'MBISCCDE', '2020', 0, 1);
/*!40000 ALTER TABLE `payment_info` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle cointax.transaction
CREATE TABLE IF NOT EXISTS `transaction` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `datetime_utc` datetime NOT NULL,
  `type` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `coin_id` int(11) NOT NULL,
  `coin_value` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `datetime_utc` (`datetime_utc`),
  KEY `transaction_to_coin` (`coin_id`),
  KEY `transaction_to_user` (`user_id`),
  CONSTRAINT `transaction_to_coin` FOREIGN KEY (`coin_id`) REFERENCES `coin` (`coin_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `transaction_to_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1024 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Exportiere Daten aus Tabelle cointax.transaction: ~0 rows (ungefähr)
DELETE FROM `transaction`;
/*!40000 ALTER TABLE `transaction` DISABLE KEYS */;
INSERT INTO `transaction` (`transaction_id`, `user_id`, `datetime_utc`, `type`, `coin_id`, `coin_value`) VALUES
	(1009, 33, '2020-10-25 11:25:00', 'send', 1, '342.8600000000000000000000000'),
	(1010, 33, '2020-10-25 11:25:00', 'receive', 9, '1.0000000000000000000000000'),
	(1011, 33, '2020-10-25 11:25:00', 'send', 14, '0.0100000000000000000000000'),
	(1012, 33, '2021-10-25 10:34:00', 'send', 1, '3633.3400000000000000000000000'),
	(1013, 33, '2021-10-25 10:34:00', 'receive', 9, '1.0000000000000000000000000'),
	(1014, 33, '2021-10-25 10:34:00', 'send', 14, '0.0100000000000000000000000'),
	(1015, 33, '2021-12-10 11:35:00', 'send', 9, '1.5000000000000000000000000'),
	(1016, 33, '2021-12-10 11:35:00', 'receive', 1, '5179.7900000000000000000000000'),
	(1017, 33, '2021-12-10 11:35:00', 'send', 1, '5.1800000000000000000000000'),
	(1018, 33, '2020-09-10 10:37:00', 'send', 1, '20.7700000000000000000000000'),
	(1019, 33, '2020-09-10 10:37:00', 'receive', 14, '1.0000000000000000000000000'),
	(1020, 33, '2020-09-10 10:37:00', 'send', 1, '0.0200000000000000000000000'),
	(1021, 33, '2021-01-01 11:40:00', 'send', 14, '0.5000000000000000000000000'),
	(1022, 33, '2021-01-01 11:40:00', 'receive', 1, '16.1500000000000000000000000'),
	(1023, 33, '2021-01-01 11:40:00', 'send', 14, '0.0100000000000000000000000');
/*!40000 ALTER TABLE `transaction` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle cointax.user
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `first_name` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_name` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Exportiere Daten aus Tabelle cointax.user: ~4 rows (ungefähr)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`, `email`, `first_name`, `last_name`, `password`) VALUES
	(33, 'max.mustermann@example.com', 'Max', 'Mustermann', '$2y$10$ifZAy5Xh65kTJsaHC2dTLO/WHGjQ9eXCixPzhXqmBZ3Go18NKtsoa');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
