-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : sam. 26 avr. 2025 à 15:00
-- Version du serveur : 5.5.68-MariaDB
-- Version de PHP : 8.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `rouge_hexa_encr`
--

-- --------------------------------------------------------

--
-- Structure de la table `rghx_rxg_smi_anchor_stats`
--

CREATE TABLE `rghx_rxg_smi_anchor_stats` (
  `id` bigint(20) NOT NULL,
  `page_id` bigint(20) NOT NULL,
  `anchor_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` int(11) DEFAULT '0',
  `variations` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rghx_rxg_smi_links`
--

CREATE TABLE `rghx_rxg_smi_links` (
  `id` bigint(20) NOT NULL,
  `source_id` bigint(20) NOT NULL,
  `target_id` bigint(20) DEFAULT NULL,
  `target_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anchor_text` text COLLATE utf8mb4_unicode_ci,
  `link_text` text COLLATE utf8mb4_unicode_ci,
  `context` text COLLATE utf8mb4_unicode_ci,
  `nofollow` tinyint(1) DEFAULT '0',
  `sponsored` tinyint(1) DEFAULT '0',
  `ugc` tinyint(1) DEFAULT '0',
  `http_status` int(11) DEFAULT '0',
  `position` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external` tinyint(1) DEFAULT '0',
  `section` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` float DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rghx_rxg_smi_pages`
--

CREATE TABLE `rghx_rxg_smi_pages` (
  `id` bigint(20) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `h1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `word_count` int(11) DEFAULT '0',
  `post_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `juice_score` float DEFAULT '0',
  `inbound_links_count` int(11) DEFAULT '0',
  `outbound_links_count` int(11) DEFAULT '0',
  `last_crawled` datetime DEFAULT NULL,
  `depth` int(11) DEFAULT '0',
  `parent_id` bigint(20) DEFAULT '0',
  `anchor_diversity_score` float DEFAULT '0',
  `word_link_ratio` float DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rghx_rxg_smi_page_terms`
--

CREATE TABLE `rghx_rxg_smi_page_terms` (
  `id` bigint(20) NOT NULL,
  `page_id` bigint(20) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `taxonomy` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `term_id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `rghx_rxg_smi_anchor_stats`
--
ALTER TABLE `rghx_rxg_smi_anchor_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_anchor` (`page_id`,`anchor_text`(191)),
  ADD KEY `page_id` (`page_id`);

--
-- Index pour la table `rghx_rxg_smi_links`
--
ALTER TABLE `rghx_rxg_smi_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `source_id` (`source_id`),
  ADD KEY `target_id` (`target_id`),
  ADD KEY `external` (`external`),
  ADD KEY `section` (`section`),
  ADD KEY `position` (`position`);

--
-- Index pour la table `rghx_rxg_smi_pages`
--
ALTER TABLE `rghx_rxg_smi_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`),
  ADD KEY `post_type` (`post_type`),
  ADD KEY `juice_score` (`juice_score`),
  ADD KEY `depth` (`depth`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Index pour la table `rghx_rxg_smi_page_terms`
--
ALTER TABLE `rghx_rxg_smi_page_terms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `taxonomy` (`taxonomy`),
  ADD KEY `term_id` (`term_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `rghx_rxg_smi_anchor_stats`
--
ALTER TABLE `rghx_rxg_smi_anchor_stats`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rghx_rxg_smi_links`
--
ALTER TABLE `rghx_rxg_smi_links`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rghx_rxg_smi_pages`
--
ALTER TABLE `rghx_rxg_smi_pages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rghx_rxg_smi_page_terms`
--
ALTER TABLE `rghx_rxg_smi_page_terms`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
