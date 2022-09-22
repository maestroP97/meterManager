-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 05 sep. 2022 à 16:45
-- Version du serveur :  8.0.21
-- Version de PHP : 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `meter_manager`
--

-- --------------------------------------------------------

--
-- Structure de la table `comptes`
--

CREATE TABLE `comptes` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Libelle` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `CanAdministrated` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `comptes`
--

INSERT INTO `comptes` (`id`, `Libelle`, `CanAdministrated`) VALUES
(1, 'Administrateur', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `UserName` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Pwd` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Token` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `TokenExpireDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `Compte` int DEFAULT '1',
  FOREIGN KEY(Compte) REFERENCES comptes(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`UserName`, `Pwd`, `id`, `Token`, `TokenExpireDate`, `Compte`) VALUES
('Admin', '7c4a8d09ca3762af61e59520943dc26494f8941b', 1, NULL, '2021-01-23 13:22:36', 1);

--
-- Structure de la table `entreprises`
--

DROP TABLE IF EXISTS `entreprises`;
CREATE TABLE IF NOT EXISTS `entreprises` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Nom` varchar(250) NOT NULL,
  `TypeUtilisateur` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `Localisation` varchar(250) CHARACTER SET utf8mb4 NOT NULL,
  `Telephone` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `UserId` int NOT NULL,
  `CreateAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `AdresseMail` varchar(100) DEFAULT NULL,
  `EditAt` datetime DEFAULT NULL,
  `EditedBy` int DEFAULT NULL,
  FOREIGN KEY(UserId) REFERENCES users(id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `compteurs`
--

DROP TABLE IF EXISTS `compteurs`;
CREATE TABLE IF NOT EXISTS `compteurs` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Libelle` varchar(250) NOT NULL,
  `Etat` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Entreprise` int DEFAULT NULL,
  `Lon` float DEFAULT NULL,
  `Lat` float DEFAULT NULL,
  `ImagePath` varchar(250) DEFAULT NULL,
  `Description` varchar(250) DEFAULT NULL,
  `NumeroCompteur` varchar(250) CHARACTER SET utf8mb4 DEFAULT NULL,
  `UserId` int NOT NULL,
  `CreateAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `CodeCompteur` varchar(20) DEFAULT NULL,
  `Secteur` varchar(20) DEFAULT NULL,
  `DiametreNominal` float NOT NULL,
  `DebitNominal` float NOT NULL,
  `ConsommationMensuelle` float NOT NULL,
  `EditAt` datetime DEFAULT NULL,
  `EditedBy` int DEFAULT NULL,
  `TypeCompteur` varchar(30) NOT NULL DEFAULT 'Compteur d''eau',
  FOREIGN KEY(Entreprise) REFERENCES entreprises(id),
  FOREIGN KEY(UserId) REFERENCES users(id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

