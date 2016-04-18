-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 16 Avril 2016 à 04:28
-- Version du serveur :  5.7.9
-- Version de PHP :  7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `swproject`
--

-- --------------------------------------------------------

--
-- Structure de la table `characters`
--

DROP TABLE IF EXISTS `characters`;
CREATE TABLE IF NOT EXISTS `characters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `id_person` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_person` (`id_person`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `film`
--

DROP TABLE IF EXISTS `film`;
CREATE TABLE IF NOT EXISTS `film` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `year` int(11) NOT NULL,
  `running_time` int(11) NOT NULL,
  `image` varchar(500) NOT NULL,
  `summary` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `has_role`
--

DROP TABLE IF EXISTS `has_role`;
CREATE TABLE IF NOT EXISTS `has_role` (
  `id_film` int(11) NOT NULL,
  `id_person` int(11) NOT NULL,
  `id_role` int(11) NOT NULL,
  KEY `id_film` (`id_film`),
  KEY `id_person` (`id_person`),
  KEY `id_role` (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `person`
--

DROP TABLE IF EXISTS `person`;
CREATE TABLE IF NOT EXISTS `person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `picture` varchar(500) NOT NULL,
  `summary` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `characters`
--
ALTER TABLE `characters`
  ADD CONSTRAINT `actor` FOREIGN KEY (`id_person`) REFERENCES `person` (`id`);

--
-- Contraintes pour la table `has_role`
--
ALTER TABLE `has_role`
  ADD CONSTRAINT `id_film` FOREIGN KEY (`id_film`) REFERENCES `film` (`id`),
  ADD CONSTRAINT `id_person` FOREIGN KEY (`id_person`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `id_role` FOREIGN KEY (`id_role`) REFERENCES `role` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
