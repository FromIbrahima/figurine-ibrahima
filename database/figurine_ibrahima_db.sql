-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 06 oct. 2026 à 10:23
-- Version du serveur : 9.1.0
-- Version de PHP : 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `figurine_ibrahima_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20261006090000', '2026-10-06 09:14:45', 142);

-- --------------------------------------------------------

--
-- Structure de la table `figurines`
--

DROP TABLE IF EXISTS `figurines`;
CREATE TABLE IF NOT EXISTS `figurines` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `image_name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `owner_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_45D9EB617E3C61F9` (`owner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `figurines`
--

INSERT INTO `figurines` (`id`, `title`, `description`, `price`, `image_name`, `created_at`, `updated_at`, `owner_id`) VALUES
(1, 'mini-figurine-lego-ninjago-legacy-jay', NULL, 12.00, 'mini-figurine-lego-ninjago-legacy-jay-6ac4c5d2a830c336442200.jpg', '2026-10-06 09:56:34', '2026-10-06 09:56:34', 5),
(2, 'Cole_ST_infobox', NULL, 8.00, 'cole-st-infobox-6ac4c618a7ce9808610663.webp', '2026-10-06 09:57:44', '2026-10-06 09:57:44', 5),
(3, '839229c374_arena-hkcc26---mobile', NULL, 15.00, '839229c374-arena-hkcc26-mobile-6ac4c686746ec981221145.jpg', '2026-10-06 09:59:34', '2026-10-06 09:59:34', 5),
(4, 'figurine-collector-majin-vegeta-1a', NULL, 22.00, 'figurine-collector-majin-vegeta-1a-6ac4c7820a02e102162764.webp', '2026-10-06 10:03:46', '2026-10-06 10:03:46', 5),
(5, 'telechargement-50-768x768', NULL, 16.00, 'telechargement-50-768x768-6ac4c86e9cd95071199799.jpg', '2026-10-06 10:07:42', '2026-10-06 10:07:42', 6),
(6, 'evangelyne-figurine-collector-en-resine-wakfu', NULL, 14.00, 'evangelyne-figurine-collector-en-resine-wakfu-6ac4c88bb0f71291247519.jpg', '2026-10-06 10:08:11', '2026-10-06 10:08:11', 6),
(7, 'RENTO', NULL, 25.00, 'r-6ac4c8db0b206149805865.jpg', '2026-10-06 10:09:31', '2026-10-06 10:09:31', 6),
(8, 'figurine-lego-ninjago-dragons-rising-jay', NULL, 8.00, 'figurine-lego-ninjago-dragons-rising-jay-6ac4c9204fe1a596680873.jpg', '2026-10-06 10:10:40', '2026-10-06 10:10:40', 6),
(9, 'Designsanstitre_2_2b6cb2b6-88f6-4a08-8997-5c3e8af41f6c', NULL, 6.95, 'designsanstitre-2-2b6cb2b6-88f6-4a08-8997-5c3e8af41f6c-6ac4ca08e6a05501720307.jpg', '2026-10-06 10:14:32', '2026-10-06 10:14:32', 7),
(10, '608859715naruto-figurine-madara-uchiha', NULL, 16.95, '608859715naruto-figurine-madara-uchiha-6ac4cae789199341450645.jpg', '2026-10-06 10:18:15', '2026-10-06 10:18:15', 7),
(11, 'OIP', NULL, 10.00, 'oip-6ac4cb0ca7375214190098.webp', '2026-10-06 10:18:52', '2026-10-06 10:18:52', 7),
(12, 'Ras', NULL, 12.00, 'r-1-6ac4cb8a4b9ea887010753.jpg', '2026-10-06 10:20:58', '2026-10-06 10:20:58', 7);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `roles`, `password`, `firstname`, `lastname`, `image_name`, `is_verified`, `created_at`, `updated_at`) VALUES
(7, 'fdent@gmail.com', '[]', '$2y$13$tRyXAJyPvQ9k.OWVzCbSHemeRJh2f0B2/TGJGlktxSqXpEIGQ60Wy', 'dentodene', 'Fenade', 'default-avatar.png', 1, '2026-10-06 10:13:18', '2026-10-06 10:13:34'),
(6, 'wnone@outlook.com', '[]', '$2y$13$6oh3364IOE4wbv/OmA/olOOSAQdvszA7jzvmYwq5.4B7G7.5Shxa2', 'Wona none', 'dn', 'default-avatar.png', 1, '2026-10-06 10:05:30', '2026-10-06 10:05:57'),
(5, 'mike@gmail.com', '[]', '$2y$13$UURh1avoGHZ6IWPAk4ykXuC7z.EfAz4JvJPsWnJoSZRS9q.PiNzje', 'Mike', 'psy', 'default-avatar.png', 1, '2026-10-06 09:50:50', '2026-10-06 09:51:28');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
