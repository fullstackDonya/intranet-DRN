-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : mar. 28 oct. 2025 à 12:22
-- Version du serveur : 5.7.39
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
CREATE DATABASE IF NOT EXISTS `drn`;
USE `drn`;
--
-- Base de données : `drn`
--

-- --------------------------------------------------------

--
-- Structure de la table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `type` enum('call','email','meeting','task','note','demo','follow_up') NOT NULL,
  `subject` varchar(200) NOT NULL,
  `description` text,
  `company_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `opportunity_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled','overdue') DEFAULT 'scheduled',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `duration_minutes` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `outcome` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `activities`
--

INSERT INTO `activities` (`id`, `type`, `subject`, `description`, `company_id`, `contact_id`, `opportunity_id`, `assigned_to`, `due_date`, `completed_at`, `status`, `priority`, `duration_minutes`, `location`, `outcome`, `created_at`, `updated_at`) VALUES
(1, 'call', 'Appel découverte', 'Premier contact avec Tech Innov', NULL, 1, 1, 1, '2025-08-23 13:33:27', NULL, 'completed', 'medium', NULL, NULL, NULL, '2025-08-30 11:33:27', '2025-08-30 11:33:27'),
(2, 'email', 'Envoi devis', 'Envoi du devis DEV-2025001', NULL, 1, 1, 1, '2025-08-24 13:33:27', NULL, 'completed', 'medium', NULL, NULL, NULL, '2025-08-30 11:33:27', '2025-08-30 11:33:27'),
(3, 'meeting', 'Démo CRM', 'Démo en visio avec GreenFood', NULL, 3, 2, 1, '2025-08-27 13:33:27', NULL, 'completed', 'high', NULL, NULL, NULL, '2025-08-30 11:33:27', '2025-08-30 11:33:27'),
(4, 'task', 'Relance BatiPro', 'Relancer pour renouvellement support', NULL, 4, 3, 1, '2025-09-01 13:33:27', NULL, 'scheduled', 'low', NULL, NULL, NULL, '2025-08-30 11:33:27', '2025-08-30 11:33:27');

-- --------------------------------------------------------

--
-- Structure de la table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`, `details`, `customer_id`) VALUES
(1, 1, 'login', NULL, NULL, NULL, '{\"ip\": \"127.0.0.1\"}', NULL, NULL, '2025-08-30 11:33:27', NULL, NULL),
(2, 1, 'create', 'companies', 1, NULL, '{\"name\": \"Tech Innov\"}', NULL, NULL, '2025-08-30 11:33:27', NULL, NULL),
(3, 1, 'update', 'opportunities', 2, NULL, '{\"stage\": \"negotiation\"}', NULL, NULL, '2025-08-30 11:33:27', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `analytics_metrics`
--

CREATE TABLE `analytics_metrics` (
  `id` int(11) NOT NULL,
  `metric_name` varchar(100) NOT NULL,
  `metric_value` decimal(15,2) NOT NULL,
  `metric_date` date NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `opportunity_id` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `analytics_metrics`
--

INSERT INTO `analytics_metrics` (`id`, `metric_name`, `metric_value`, `metric_date`, `user_id`, `company_id`, `opportunity_id`, `category`, `created_at`) VALUES
(1, 'revenue', '5000.00', '2025-08-30', 1, NULL, NULL, 'sales', '2025-08-30 11:33:27'),
(2, 'revenue', '2000.00', '2025-08-30', 1, NULL, NULL, 'sales', '2025-08-30 11:33:27'),
(3, 'revenue', '1500.00', '2025-08-30', 1, NULL, NULL, 'sales', '2025-08-30 11:33:27'),
(4, 'conversion_rate', '35.50', '2025-08-30', 1, NULL, NULL, 'kpi', '2025-08-30 11:33:27'),
(5, 'active_clients', '2.00', '2025-08-30', 1, NULL, NULL, 'kpi', '2025-08-30 11:33:27'),
(6, 'opportunities', '4.00', '2025-08-30', 1, NULL, NULL, 'kpi', '2025-08-30 11:33:27');

-- --------------------------------------------------------

--
-- Structure de la table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `related_type` enum('email','activity','quote','company','contact','opportunity') NOT NULL,
  `related_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `automations`
--

CREATE TABLE `automations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trigger_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audience_filter` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'all',
  `status` enum('active','paused') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `open_rate` float DEFAULT '0',
  `click_rate` float DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `automations`
--

INSERT INTO `automations` (`id`, `user_id`, `customer_id`, `name`, `type`, `trigger_type`, `audience_filter`, `status`, `open_rate`, `click_rate`, `created_at`, `updated_at`) VALUES
(1, 7, 9, 'new', 'custom', 'purchase', 'prospects', 'paused', 0, 0, '2025-10-09 16:58:25', '2025-10-09 17:05:42'),
(2, 7, 9, 'new', 'custom', 'signup', 'company_13', 'active', 0, 0, '2025-10-13 22:15:14', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `automation_logs`
--

CREATE TABLE `automation_logs` (
  `id` int(11) NOT NULL,
  `automation_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('sent','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'sent',
  `response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `automation_steps`
--

CREATE TABLE `automation_steps` (
  `id` int(11) NOT NULL,
  `automation_id` int(11) NOT NULL,
  `step_order` int(11) NOT NULL,
  `action_type` enum('email','wait','sms') COLLATE utf8mb4_unicode_ci DEFAULT 'email',
  `content` text COLLATE utf8mb4_unicode_ci,
  `delay_hours` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cahier_des_charges`
--

CREATE TABLE `cahier_des_charges` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `project_goals` text NOT NULL,
  `target_audience` text NOT NULL,
  `features` text NOT NULL,
  `platform` varchar(50) NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `deadline` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `cahier_des_charges`
--

INSERT INTO `cahier_des_charges` (`id`, `user_id`, `phone`, `client_name`, `company_name`, `contact_email`, `project_goals`, `target_audience`, `features`, `platform`, `budget`, `deadline`, `created_at`) VALUES
(1, NULL, '', 'Donya', 'we', 'd@gmail.com', 'm', 'm', 'ee', 'site_web', '2002.00', '2024-12-26', '2024-12-22 10:42:53'),
(2, NULL, '', 'Donya', 'we', 'd@gmail.com', 'm', 'm', 'ee', 'site_web', '2002.00', '2024-12-26', '2024-12-22 10:52:11'),
(3, NULL, '', 'Donyaaaaa', 'fffffff', 'donya.bcontact@gmail.com', 'mmmm', 'mmmm', 'eefff', 'site_web', '209.00', '2024-12-26', '2024-12-22 11:05:04'),
(4, NULL, '0651893988', 'Donya', 'we', 'donyab16@gmail.com', 'test', 'test:::', 'test;:;;', 'application_mobile', '21111.00', '2025-04-25', '2025-04-01 12:36:22'),
(5, NULL, '0651893988', 'Donya', 'weaa', 'ELi@gmail.com', 't', 't', 't', 'site_web', '333333.00', '2025-04-30', '2025-04-01 12:39:14'),
(6, 7, '0651893988', 'DD', 'GV', 'ELi@gmail.com', 'LKJ', 'ÙML', 'M', 'application_mobile', '3.00', '2025-08-22', '2025-08-21 10:15:40');

-- --------------------------------------------------------

--
-- Structure de la table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `automation_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `sender_name` varchar(150) DEFAULT NULL,
  `sender_email` varchar(150) DEFAULT NULL,
  `audience` varchar(100) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recipients_emails` text,
  `recipients_count` int(11) DEFAULT '0',
  `recipients` int(11) DEFAULT '0',
  `open_rate` float DEFAULT '0',
  `click_rate` float DEFAULT '0',
  `opens_count` int(11) DEFAULT '0',
  `clicks_count` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `campaigns`
--

INSERT INTO `campaigns` (`id`, `name`, `type`, `automation_id`, `subject`, `sender_name`, `sender_email`, `audience`, `customer_id`, `status`, `scheduled_at`, `created_at`, `recipients_emails`, `recipients_count`, `recipients`, `open_rate`, `click_rate`, `opens_count`, `clicks_count`) VALUES
(1, 'test campagne', 'automation', 1, 'test', 'Mon Entreprise', 'contact@drn.fr', 'prospects', 9, 'draft', NULL, '2025-10-08 22:53:15', NULL, 0, 0, 0, 0, 0, 0),
(2, 'Prospect', 'promotional', 1, 'prospect clients', 'drn', 'noreply@drn.com', 'company_13', 9, 'scheduled', '2025-10-14 11:57:00', '2025-10-11 15:37:01', '[\"d.benferroudj@ecole-ipssi.net\",\"info@greenenergy.fr\"]', 2, 2, 0, 0, 0, 0),
(3, 'prospect', 'promotional', 1, 'prospect client', 'drn', 'noreply@drn.com', 'all', 9, 'active', '2025-10-30 12:08:00', '2025-10-11 15:37:46', '[\"d.benferroudj@ecole-ipssi.net\",\"info@greenenergy.fr\"]', 2, 2, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `campaign_events`
--

CREATE TABLE `campaign_events` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `event_type` enum('open','click') NOT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `url` text,
  `ip` varbinary(16) DEFAULT NULL,
  `ua` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `response_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `response` varchar(255) DEFAULT NULL,
  `conversation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `chat`
--

INSERT INTO `chat` (`id`, `user_id`, `message`, `is_admin`, `is_closed`, `response_to`, `created_at`, `response`, `conversation_id`) VALUES
(1, '::1', 'hh', 0, 0, NULL, '2024-07-17 22:53:07', 'hhh', NULL),
(2, '::1', 'La conversation a été fermée.', 0, 0, NULL, '2024-07-17 22:59:25', NULL, NULL),
(3, '::1', 'jj', 0, 0, NULL, '2024-07-17 23:00:46', 'ggg', NULL),
(4, '::1', 'La conversation a été fermée.', 0, 0, NULL, '2024-07-17 23:01:04', NULL, NULL),
(5, '::1', 'La conversation a été fermée.', 0, 0, NULL, '2024-07-17 23:02:18', NULL, NULL),
(6, '::1', 'La conversation a été fermée.', 0, 0, NULL, '2024-07-18 11:05:47', NULL, NULL),
(7, '::1', 'La conversation a été fermée.', 0, 0, NULL, '2024-07-18 11:08:40', NULL, NULL),
(8, '::1', 'La conversation a été fermée.', 0, 0, NULL, '2024-07-18 11:10:40', NULL, NULL),
(9, '::1', 'La conversation a été fermée.', 0, 0, NULL, '2024-07-18 11:10:52', NULL, NULL),
(10, '::1', 'hh', 0, 0, NULL, '2024-07-18 11:11:00', NULL, NULL),
(11, '::1', 'La conversation a été fermée.', 0, 0, NULL, '2024-07-18 11:11:03', NULL, NULL),
(12, '::1', 'La conversation a été fermée.', 1, 0, NULL, '2024-07-18 11:17:33', NULL, NULL),
(13, '::1', 'bb', 0, 0, NULL, '2024-07-18 11:18:50', NULL, NULL),
(14, '::1', 'gg', 0, 0, NULL, '2024-07-18 11:18:56', NULL, NULL),
(15, '::1', 'jj', 0, 0, NULL, '2024-07-18 11:27:22', 're en', 4),
(16, '::1', 'jj', 0, 0, NULL, '2024-07-18 11:28:06', NULL, 4),
(17, '::1', 'jjjj', 0, 0, NULL, '2024-07-18 11:28:23', NULL, 5),
(18, '::1', 'hhh', 1, 0, NULL, '2024-07-18 11:32:58', 'bonjour ', 12),
(19, '::1', 'h', 0, 0, NULL, '2024-07-18 11:38:36', NULL, 12),
(20, '::1', 'gg', 0, 0, NULL, '2024-07-18 11:39:25', NULL, 13),
(21, '::1', 'jh', 0, 0, NULL, '2024-07-18 11:39:40', NULL, 14),
(22, '::1', 'kk', 0, 0, NULL, '2024-07-18 11:54:41', NULL, 15),
(23, '::1', 'll', 0, 0, NULL, '2024-07-18 11:56:41', NULL, 16),
(24, '::1', 'mlm', 0, 0, NULL, '2024-07-18 11:57:42', NULL, 17),
(25, '::1', 'sal', 0, 0, NULL, '2024-07-18 11:58:26', 'jjjjjjjj', 18),
(26, '::1', 'k', 0, 0, NULL, '2024-07-18 12:01:45', NULL, 18),
(27, '::1', 'm', 0, 0, NULL, '2024-07-18 12:02:13', NULL, 19),
(28, '::1', 'kkk', 0, 0, NULL, '2024-07-18 12:02:33', NULL, 19),
(29, '::1', 'lll', 0, 0, NULL, '2024-07-18 12:02:40', NULL, 20),
(30, '::1', 'ggg', 1, 0, NULL, '2024-07-18 18:56:18', 'reponse', 21),
(31, '::1', 'hello', 1, 0, NULL, '2024-07-18 19:04:31', 'reponse', 23),
(32, '::1', 'jj', 1, 0, NULL, '2024-07-18 19:05:38', 'reponse', 23),
(33, '::1', 'bonjour', 0, 0, NULL, '2024-07-18 19:10:40', 'reponse', 24),
(34, '::1', 'hhh', 0, 0, NULL, '2024-07-18 19:18:05', 'reponse', 25),
(35, '::1', 'gggggggg', 0, 0, NULL, '2024-07-18 19:19:21', 'gestes', 26),
(36, '::1', 'hh', 0, 0, NULL, '2024-07-18 19:21:44', 'jhgfdc', 26),
(37, '::1', 'fertt', 0, 0, NULL, '2024-07-18 19:30:50', 'reponse', 28),
(38, '::1', 'ff', 0, 0, NULL, '2024-07-18 19:31:48', 'reponse', 28),
(39, '::1', 'gg', 1, 0, NULL, '2024-07-18 19:34:11', 'reponse', 28),
(40, '::1', 'hhh', 0, 0, NULL, '2024-07-18 19:36:32', NULL, 29),
(41, '::1', 'ju', 0, 0, NULL, '2024-07-18 20:58:21', NULL, 30),
(42, '::1', 'dd', 0, 0, NULL, '2024-07-18 21:00:33', NULL, 32),
(43, '::1', 'ddd', 0, 0, NULL, '2024-07-18 21:01:11', NULL, 33),
(44, '::1', 'dd', 0, 0, NULL, '2024-07-18 21:02:11', NULL, 33),
(45, '::1', 'e', 0, 0, NULL, '2024-07-18 21:02:47', NULL, 34),
(46, '::1', 'gg', 0, 0, NULL, '2024-07-18 23:39:37', 'jhgf', 46),
(47, '::1', 'kjh', 0, 0, NULL, '2024-07-18 23:41:11', NULL, 46),
(48, '::1', 'kj', 0, 0, NULL, '2024-07-18 23:41:15', NULL, 46),
(49, '::1', 'lk', 0, 0, NULL, '2024-07-18 23:46:11', 'lkj', 51),
(50, '::1', 'lkj', 0, 0, NULL, '2024-07-18 23:48:12', NULL, 52),
(51, '::1', 'bonjour', 0, 0, NULL, '2024-07-18 23:52:06', NULL, 53),
(52, '::1', 'lkj', 1, 0, NULL, '2024-07-18 23:53:54', 'kjh', 54),
(53, '::1', 'mlk', 1, 0, NULL, '2024-07-18 23:54:43', 'kjhg', 55),
(54, '::1', 'lkj', 0, 0, NULL, '2024-07-18 23:57:08', 'mlkjh', 56),
(55, '::1', 'kj', 0, 0, NULL, '2024-07-18 23:59:13', 'kjhgf', 56),
(56, '::1', 'k', 0, 0, 56, '2024-07-19 00:01:52', 'kjh', 57),
(57, '::1', 'lkj', 0, 0, NULL, '2024-07-19 00:09:40', 'dfgh', 58),
(58, '::1', 'xdc', 0, 0, NULL, '2024-07-19 01:12:14', NULL, 59),
(59, '::1', 'hello', 0, 0, NULL, '2024-09-22 12:58:08', 'de', 62),
(60, '::1', 'j', 0, 0, NULL, '2024-09-22 12:59:24', NULL, 62),
(61, '::1', 'bonjoir', 1, 0, NULL, '2024-09-22 12:59:46', 'b', 63),
(62, '::1', 'j', 0, 0, NULL, '2024-09-22 13:40:28', NULL, 63);

-- --------------------------------------------------------

--
-- Structure de la table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `industry` varchar(50) DEFAULT NULL,
  `segment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT 'France',
  `employee_count` int(11) DEFAULT NULL,
  `annual_revenue` decimal(15,2) DEFAULT NULL,
  `status` enum('prospect','client','partner','inactive') DEFAULT 'prospect',
  `satisfaction` decimal(3,1) DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1',
  `satisfaction_score` decimal(3,1) DEFAULT NULL,
  `employees_count` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `interne_customer` tinyint(1) NOT NULL DEFAULT '0',
  `validation_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `companies`
--

INSERT INTO `companies` (`id`, `name`, `industry`, `segment`, `website`, `phone`, `email`, `address`, `city`, `postal_code`, `country`, `employee_count`, `annual_revenue`, `status`, `satisfaction`, `source`, `assigned_to`, `notes`, `created_at`, `updated_at`, `is_active`, `satisfaction_score`, `employees_count`, `customer_id`, `interne_customer`, `validation_code`) VALUES
(13, 'GreenEnergy Group', 'Technologie', NULL, 'https://www.greenenergy.fr', '+33 2 34 56 78 90', 'info@greenenergy.fr', '120 Avenue Verte', 'Nantes', '44000', 'France', 150, '7500000.00', 'client', NULL, 'Salon Energies 2025', 1, 'Client stratégique avec engagement RSE fort.', '2025-09-08 11:41:07', '2025-09-17 18:58:59', 1, '8.5', 150, 9, 1, '2323'),
(14, 'gg gg', '', '<br /><b>Deprecated</b>:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in <b>/Applications/MAMP/htdocs/PP/drn/WEB/crm/customers-edit.php</b> on line <b>62</b><br />', 'https://webitech.fr/vcard.php', '0651893987', 'd.benferroudj@ecole-ipssi.net', '2 , Rue Emile Deslandres', 'Paris', NULL, 'France', 233, '222209999.00', 'client', '10.0', NULL, 7, '', '2025-09-20 15:42:06', '2025-10-13 23:38:27', 1, NULL, NULL, 9, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `budget` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timeline` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_preference` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'email',
  `status` enum('nouveau','en_cours','traite','archive') COLLATE utf8mb4_unicode_ci DEFAULT 'nouveau',
  `is_primary` tinyint(1) DEFAULT '0',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `submission_time` int(11) NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contacts`
--

INSERT INTO `contacts` (`id`, `company_id`, `name`, `email`, `company`, `phone`, `subject`, `message`, `budget`, `timeline`, `contact_preference`, `status`, `is_primary`, `assigned_to`, `created_at`, `updated_at`, `ip_address`, `submission_time`, `first_name`, `last_name`, `position`) VALUES
(1, NULL, 'D', 'd@gmail.com', NULL, NULL, '', 'Ttt', NULL, NULL, 'email', 'nouveau', 0, NULL, '2024-07-08 14:19:33', '2025-08-20 22:44:13', '', 0, NULL, NULL, NULL),
(2, NULL, 'gg gg', 'donyab16@gmail.com', NULL, NULL, '', 'gf', NULL, NULL, 'email', 'nouveau', 0, NULL, '2024-07-10 17:37:15', '2025-08-20 22:44:13', '', 0, NULL, NULL, NULL),
(3, NULL, 'dd', 'donyab16@gmail.com', NULL, NULL, '', 'gfd', NULL, NULL, 'email', 'nouveau', 0, NULL, '2024-07-16 13:12:28', '2025-08-20 22:44:13', '', 0, NULL, NULL, NULL),
(4, NULL, 'dd', 'donyab16@gmail.com', NULL, NULL, '', 'gfd', NULL, NULL, 'email', 'nouveau', 0, NULL, '2024-07-16 22:53:08', '2025-08-20 22:44:13', '', 0, NULL, NULL, NULL),
(5, NULL, 'Donya BENFERROUDJ', 'd@gmail.com', 'TEST', '+33651893987', 'site-web', 'HGT', 'moins-1000', 'urgent', 'meeting', 'nouveau', 0, NULL, '2025-08-21 17:28:50', '2025-08-21 17:28:50', '::1', 1755797330, NULL, NULL, NULL),
(6, NULL, '', 'alice.martin@techinnov.fr', NULL, '0612345678', '', '', NULL, NULL, 'email', 'nouveau', 1, 1, '2025-08-30 11:29:33', '2025-08-30 11:29:33', '', 0, 'Alice', 'Martin', 'CTO'),
(7, NULL, '', 'jean.dupont@techinnov.fr', NULL, '0623456789', '', '', NULL, NULL, 'email', 'nouveau', 0, 1, '2025-08-30 11:29:33', '2025-08-30 11:29:33', '', 0, 'Jean', 'Dupont', 'CEO'),
(8, NULL, '', 'sophie.durand@greenfood.fr', NULL, '0634567890', '', '', NULL, NULL, 'email', 'nouveau', 1, 1, '2025-08-30 11:29:33', '2025-08-30 11:29:33', '', 0, 'Sophie', 'Durand', 'Responsable Achats'),
(9, NULL, '', 'paul.leroy@batipro.fr', NULL, '0645678901', '', '', NULL, NULL, 'email', 'nouveau', 1, 1, '2025-08-30 11:29:33', '2025-08-30 11:29:33', '', 0, 'Paul', 'Leroy', 'Directeur'),
(10, NULL, 'Dounya BENFERROUDJ', 'donya.bcontact@gmail.com', 'nvp', '+33651893987', 'analytics', 'nvp', '3000-5000', 'flexible', 'email', 'nouveau', 0, NULL, '2025-09-03 12:47:31', '2025-09-03 12:47:31', '::1', 1756903651, NULL, NULL, NULL),
(11, NULL, 'Donya BENFERROUDJ', 'donyab16@gmail.com', 'TEST2', '+33651893987', 'app-mobile', 'gg', 'moins-1000', '1-2-mois', 'email', 'nouveau', 0, NULL, '2025-09-15 21:16:50', '2025-09-15 21:16:50', '::1', 1757971010, NULL, NULL, NULL),
(12, NULL, 'Donya BENFERROUDJ', 'donyab16@gmail.com', 'TEST2', '+33651893987', 'site-web', 'jhg', 'moins-1000', 'urgent', 'email', 'nouveau', 0, NULL, '2025-09-15 21:26:28', '2025-09-15 21:26:28', '::1', 1757971588, NULL, NULL, NULL),
(13, NULL, 'gg', 't@gg.com', 'ds', '0987654321', 'site-web', 'dd', 'moins-1000', '1-2-mois', 'email', 'nouveau', 0, NULL, '2025-09-15 21:33:56', '2025-09-15 21:33:56', '::1', 1757972036, NULL, NULL, NULL),
(14, NULL, 'Dounya BENFERROUDJ', 'donya.bcontact@gmail.com', 'TEST', '+33651893987', 'app-mobile', 'mlkj', '1000-3000', '1-2-mois', 'email', 'nouveau', 0, NULL, '2025-09-15 21:45:31', '2025-09-15 21:45:31', '::1', 1757972731, NULL, NULL, NULL),
(15, NULL, 'Donya BENFERROUDJ', 'donyab16@gmail.com', 'TEST2', '+33651893987', 'site-web', 'cv', '1000-3000', 'urgent', 'email', 'nouveau', 0, NULL, '2025-09-15 21:58:14', '2025-09-15 21:58:14', '::1', 1757973494, NULL, NULL, NULL),
(16, NULL, 'Donya BENFERROUDJ', 'donyab16@gmail.com', 'TEST', '+33651893987', 'hebergement', 'lkj', 'moins-1000', 'urgent', 'email', 'nouveau', 0, NULL, '2025-09-15 22:03:44', '2025-09-15 22:03:44', '::1', 1757973824, NULL, NULL, NULL),
(17, NULL, 'test test', 'test@gmail.com', 'Webitech', '+33642986046', 'site-web', 'hgf', '1000-3000', 'urgent', 'email', 'nouveau', 0, NULL, '2025-09-15 22:08:52', '2025-09-15 22:08:52', '::1', 1757974132, NULL, NULL, NULL),
(18, NULL, 'Dounya BENFERROUDJ', 'donya.bcontact@gmail.com', 'TEST2', '+33651893987', 'app-mobile', 'sd', '3000-5000', '1-2-mois', 'email', 'nouveau', 0, NULL, '2025-09-15 22:14:00', '2025-09-15 22:14:00', '::1', 1757974440, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `contact_requests`
--

CREATE TABLE `contact_requests` (
  `id` int(11) NOT NULL,
  `request_id` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `request_type` enum('contact','chat_contact','callback') DEFAULT 'contact',
  `status` enum('pending','contacted','completed','cancelled') DEFAULT 'pending',
  `notes` text,
  `contacted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `is_closed` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `topic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `conversations`
--


-- --------------------------------------------------------

--
-- Structure de la table `crm_contacts`
--

CREATE TABLE `crm_contacts` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','do_not_contact') DEFAULT 'active',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `country` varchar(50) DEFAULT NULL,
  `role` enum('admin','manager','client','partner') DEFAULT 'client',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `postal_code` varchar(20) DEFAULT NULL,
  `validation_code` varchar(20) DEFAULT NULL,
  `powerbi_token` text,
  `powerbi_token_expiry` datetime DEFAULT NULL,
  `powerbi_client_secret` text,
  `powerbi_tenant_id` varchar(255) DEFAULT NULL,
  `powerbi_workspace_id` varchar(255) DEFAULT NULL,
  `powerbi_report_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `customers`
--


-- --------------------------------------------------------


--
-- Structure de la table `emails`
--

CREATE TABLE `emails` (
  `id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `from_email` varchar(255) NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `cc_email` text,
  `bcc_email` text,
  `subject` varchar(500) NOT NULL,
  `body_html` text,
  `body_text` text,
  `message_id` varchar(255) DEFAULT NULL,
  `thread_id` varchar(255) DEFAULT NULL,
  `is_outbound` tinyint(1) DEFAULT '1',
  `is_read` tinyint(1) DEFAULT '0',
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `erp_companies`
--

CREATE TABLE `erp_companies` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `siret` varchar(20) DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `country` varchar(120) DEFAULT 'France',
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(180) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `naf` varchar(20) DEFAULT NULL,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `erp_companies`
--

INSERT INTO `erp_companies` (`id`, `customer_id`, `name`, `siret`, `address_line1`, `address_line2`, `postal_code`, `city`, `country`, `phone`, `email`, `created_at`, `updated_at`, `naf`, `notes`) VALUES
(1, 9, 'Dounya BENFERROUDJ', '453453', '2, rue emile deslandres', '75013', NULL, NULL, 'France', '0651893987', 'test@test.com', '2025-10-23 09:42:06', '2025-10-24 14:36:50', '123', '');

-- --------------------------------------------------------

--
-- Structure de la table `erp_employees`
--

CREATE TABLE `erp_employees` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `first_name` varchar(120) NOT NULL,
  `last_name` varchar(120) NOT NULL,
  `email` varchar(180) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `base_salary` decimal(10,2) DEFAULT '0.00',
  `job_title` varchar(180) DEFAULT NULL,
  `department` varchar(180) DEFAULT NULL,
  `contract_type` enum('CDI','CDD','Freelance','Stage','Alternance') DEFAULT 'CDI',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `erp_employees`
--

INSERT INTO `erp_employees` (`id`, `company_id`, `customer_id`, `first_name`, `last_name`, `email`, `hire_date`, `base_salary`, `job_title`, `department`, `contract_type`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'Dounya', 'BENFERROUDJ', 'donya.bcontact@gmail.com', '2025-10-23', '2223.00', 'dev', '75', 'Freelance', 'active', '2025-10-22 12:07:37', NULL),
(2, NULL, NULL, 'Dounya', 'BENFERROUDJ', 'donya.bcontact@gmail.com', '2025-10-23', '2944.00', 'da', '75', 'CDI', 'active', '2025-10-22 19:23:15', '2025-10-22 19:23:27'),
(3, NULL, 9, 'test', 'test', 'test@gmail.com', '2025-10-24', '2999.00', 'da', '75', 'CDI', 'active', '2025-10-23 10:03:59', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `erp_inventory`
--

CREATE TABLE `erp_inventory` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `location` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `description` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `erp_payrolls`
--

CREATE TABLE `erp_payrolls` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `period` char(7) NOT NULL COMMENT 'YYYY-MM',
  `gross_salary` decimal(10,2) NOT NULL,
  `bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `overtime` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `employee_contrib` decimal(10,2) NOT NULL,
  `employer_contrib` decimal(10,2) NOT NULL,
  `net_pay` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `erp_payrolls`
--

--
-- Structure de la table `erp_sales`
--

CREATE TABLE `erp_sales` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `erp_shifts`
--

CREATE TABLE `erp_shifts` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `role` varchar(150) DEFAULT NULL,
  `notes` text,
  `company_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `erp_shifts`
--


--
-- Structure de la table `erp_stock`
--

CREATE TABLE `erp_stock` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `erp_stock`
--

INSERT INTO `erp_stock` (`id`, `customer_id`, `product_name`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(1, 9, '', 1, '200.00', '2025-10-23 20:11:35', '2025-10-23 20:11:45');

-- --------------------------------------------------------

--
-- Structure de la table `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `folders`
--

INSERT INTO `folders` (`id`, `company_id`, `assigned_to`, `name`, `description`, `created_at`, `updated_at`, `status_id`) VALUES
(1, 13, 7, 'Test', '', '2025-09-08 16:10:53', '2025-09-08 19:21:47', 5),
(2, 13, 7, 'Test', '', '2025-09-08 16:10:58', '2025-09-08 19:21:41', 5),
(3, 14, 7, 'gg gg', '', '2025-09-20 17:42:25', '2025-09-20 17:42:25', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `forms`
--

CREATE TABLE `forms` (
  `id` int(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `form_data` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `date_upload` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `images`
--

INSERT INTO `images` (`id`, `user_id`, `url`, `type`, `date_upload`) VALUES
(1, 7, 'uploads/Donya/68bc3491a949a_WSBP.PNG', 'image/png', '2025-09-06 15:18:09'),
(2, 7, 'uploads/Donya/68bc3571a2239_courchevelav-hover.png', 'image/png', '2025-09-06 15:21:53');

-- --------------------------------------------------------

--
-- Structure de la table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('en_attente','envoyée','payée','annulée') DEFAULT 'en_attente',
  `issued_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `invoices`
--

INSERT INTO `invoices` (`id`, `folder_id`, `invoice_number`, `amount`, `status`, `issued_at`, `paid_at`) VALUES
(1, 2, 'F2025-0001', '6390.00', 'en_attente', '2025-10-10 12:04:16', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `is_deleted_sender` tinyint(1) DEFAULT '0',
  `is_deleted_receiver` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `messages`
--


--
-- Structure de la table `missions`
--

CREATE TABLE `missions` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `departure` varchar(255) DEFAULT NULL,
  `arrival` varchar(255) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `driver` varchar(255) DEFAULT NULL,
  `vehicle` varchar(255) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT '0.00',
  `status_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'vtc',
  `project` varchar(255) DEFAULT NULL,
  `product` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `missions`
--

--
-- Structure de la table `msg_conversations`
--

CREATE TABLE `msg_conversations` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL,
  `type` enum('direct','group','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `msg_messages`
--

CREATE TABLE `msg_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `sender_type` enum('user','company') NOT NULL,
  `sender_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `msg_participants`
--

CREATE TABLE `msg_participants` (
  `id` int(10) UNSIGNED NOT NULL,
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `participant_type` enum('user','company') NOT NULL,
  `participant_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `msg_receipts`
--

CREATE TABLE `msg_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `newsletter`
--

INSERT INTO `newsletter` (`id`, `email`, `created_at`) VALUES
(1, 'd@gmail.com', '2024-07-16 13:16:11'),
(2, 'donyab16@gmail.com', '2024-07-16 22:50:47'),
(4, 'donya.bcontact@gmail.com', '2025-09-03 12:33:15'),
(15, 'donya.bt@gmail.com', '2025-09-03 12:43:32'),
(19, 'donya.J@gmail.com', '2025-09-03 12:47:04');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `related_type` varchar(50) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `notifications`
--


--
-- Structure de la table `opportunities`
--

CREATE TABLE `opportunities` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `customer_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `status_id` int(11) DEFAULT NULL,
  `stage` enum('qualification','needs_analysis','proposal','negotiation','closed_won','closed_lost') DEFAULT 'qualification',
  `probability` int(11) DEFAULT '0',
  `amount` decimal(15,2) DEFAULT '0.00',
  `expected_close_date` date DEFAULT NULL,
  `actual_close_date` date DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `competitor` varchar(100) DEFAULT NULL,
  `loss_reason` text,
  `next_action` text,
  `next_action_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `opportunities`
--


--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `participants`
--

CREATE TABLE `participants` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(100) NOT NULL,
  `img` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `participants`
--

--
-- Structure de la table `pipeline_stages`
--

CREATE TABLE `pipeline_stages` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `order_position` int(11) NOT NULL,
  `probability_default` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `color_code` varchar(7) DEFAULT '#007bff',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `pipeline_stages`
--

INSERT INTO `pipeline_stages` (`id`, `name`, `order_position`, `probability_default`, `is_active`, `color_code`, `created_at`) VALUES
(1, 'Qualification', 1, 10, 1, '#6c757d', '2025-07-20 11:59:50'),
(2, 'Analyse des besoins', 2, 25, 1, '#17a2b8', '2025-07-20 11:59:50'),
(3, 'Proposition', 3, 50, 1, '#ffc107', '2025-07-20 11:59:50'),
(4, 'Négociation', 4, 75, 1, '#fd7e14', '2025-07-20 11:59:50'),
(5, 'Fermé gagné', 5, 100, 1, '#28a745', '2025-07-20 11:59:50'),
(6, 'Fermé perdu', 6, 0, 1, '#dc3545', '2025-07-20 11:59:50'),
(7, 'Qualification', 1, 10, 1, '#6c757d', '2025-07-20 12:09:31'),
(8, 'Analyse des besoins', 2, 25, 1, '#17a2b8', '2025-07-20 12:09:31'),
(9, 'Proposition', 3, 50, 1, '#ffc107', '2025-07-20 12:09:31'),
(10, 'Négociation', 4, 75, 1, '#fd7e14', '2025-07-20 12:09:31'),
(11, 'Fermé gagné', 5, 100, 1, '#28a745', '2025-07-20 12:09:31'),
(12, 'Fermé perdu', 6, 0, 1, '#dc3545', '2025-07-20 12:09:31'),
(13, 'Qualification', 1, 10, 1, '#6c757d', '2025-07-20 12:15:30'),
(14, 'Analyse des besoins', 2, 25, 1, '#17a2b8', '2025-07-20 12:15:30'),
(15, 'Proposition', 3, 50, 1, '#ffc107', '2025-07-20 12:15:30'),
(16, 'Négociation', 4, 75, 1, '#fd7e14', '2025-07-20 12:15:30'),
(17, 'Fermé gagné', 5, 100, 1, '#28a745', '2025-07-20 12:15:30'),
(18, 'Fermé perdu', 6, 0, 1, '#dc3545', '2025-07-20 12:15:30');

-- --------------------------------------------------------

--
-- Structure de la table `plans`
--

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text,
  `content` longtext,
  `author_id` int(11) DEFAULT '1',
  `status` varchar(20) DEFAULT 'published',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `posts`
--

-- --------------------------------------------------------

--
-- Structure de la table `powerbi_reports`
--

CREATE TABLE `powerbi_reports` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `report_id` varchar(255) NOT NULL,
  `workspace_id` varchar(255) NOT NULL,
  `embed_url` text NOT NULL,
  `access_token` text,
  `token_expires_at` datetime DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `allowed_roles` json DEFAULT NULL,
  `refresh_schedule` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `powerbi_reports`
--

INSERT INTO `powerbi_reports` (`id`, `name`, `report_id`, `workspace_id`, `embed_url`, `access_token`, `token_expires_at`, `category`, `description`, `is_active`, `allowed_roles`, `refresh_schedule`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard Ventes', 'sample-report-1', 'sample-workspace-1', 'https://app.powerbi.com/reportEmbed', NULL, NULL, 'sales', 'Tableau de bord principal des ventes', 1, NULL, NULL, '2025-07-20 12:10:32', '2025-07-20 12:10:32'),
(2, 'Analyse Clients', 'sample-report-2', 'sample-workspace-1', 'https://app.powerbi.com/reportEmbed', NULL, NULL, 'customers', 'Analyse détaillée de la base clients', 1, NULL, NULL, '2025-07-20 12:10:32', '2025-07-20 12:10:32'),
(3, 'Performance Équipe', 'sample-report-3', 'sample-workspace-1', 'https://app.powerbi.com/reportEmbed', NULL, NULL, 'team', 'Suivi des performances de l\'équipe commerciale', 1, NULL, NULL, '2025-07-20 12:10:32', '2025-07-20 12:10:32'),
(4, 'Prévisions', 'sample-report-4', 'sample-workspace-1', 'https://app.powerbi.com/reportEmbed', NULL, NULL, 'forecasting', 'Prévisions de ventes et tendances', 1, NULL, NULL, '2025-07-20 12:10:32', '2025-07-20 12:10:32'),
(5, 'Dashboard Ventes', 'sample-report-1', 'sample-workspace-1', 'https://app.powerbi.com/reportEmbed', NULL, NULL, 'sales', 'Tableau de bord principal des ventes', 1, NULL, NULL, '2025-07-20 12:15:30', '2025-07-20 12:15:30'),
(6, 'Analyse Clients', 'sample-report-2', 'sample-workspace-1', 'https://app.powerbi.com/reportEmbed', NULL, NULL, 'customers', 'Analyse détaillée de la base clients', 1, NULL, NULL, '2025-07-20 12:15:30', '2025-07-20 12:15:30'),
(7, 'Performance Équipe', 'sample-report-3', 'sample-workspace-1', 'https://app.powerbi.com/reportEmbed', NULL, NULL, 'team', 'Suivi des performances de l\'équipe commerciale', 1, NULL, NULL, '2025-07-20 12:15:30', '2025-07-20 12:15:30'),
(8, 'Prévisions', 'sample-report-4', 'sample-workspace-1', 'https://app.powerbi.com/reportEmbed', NULL, NULL, 'forecasting', 'Prévisions de ventes et tendances', 1, NULL, NULL, '2025-07-20 12:15:30', '2025-07-20 12:15:30');

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('website','ecommerce','portfolio','blog','landing') COLLATE utf8mb4_unicode_ci DEFAULT 'website',
  `status` enum('active','completed','paused','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `progress` int(11) DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `published_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `name`, `title`, `description`, `type`, `status`, `progress`, `start_date`, `end_date`, `created_at`, `updated_at`, `published_url`) VALUES
(1, 1, 'Boutique E-commerce', '', 'Site de vente en ligne pour produits artisanaux', 'ecommerce', '', 0, NULL, NULL, '2025-07-31 10:47:29', '2025-08-29 10:47:29', NULL),
(2, 1, 'Portfolio Personnel', '', 'Mon portfolio professionnel avec mes réalisations', 'portfolio', '', 0, NULL, NULL, '2025-08-05 10:47:29', '2025-08-23 10:47:29', NULL),
(3, 1, 'Site Corporate', '', 'Site vitrine pour entreprise de consulting', '', '', 0, NULL, NULL, '2025-08-10 10:47:29', '2025-08-30 10:47:29', NULL),
(4, 2, 'Blog Voyage', '', 'Blog sur les voyages et découvertes', 'blog', '', 0, NULL, NULL, '2025-08-12 10:47:29', '2025-08-28 10:47:29', NULL),
(5, 2, 'Landing Page Produit', '', 'Page de présentation pour un nouveau produit', 'landing', '', 0, NULL, NULL, '2025-08-20 10:47:29', '2025-08-27 10:47:29', NULL),
(6, 3, 'Portfolio Graphiste', '', 'Portfolio pour un graphiste freelance', 'portfolio', '', 0, NULL, NULL, '2025-08-15 10:47:29', '2025-08-29 10:47:29', NULL),
(7, 3, 'Boutique Mode', '', 'E-commerce pour vêtements tendance', 'ecommerce', '', 0, NULL, NULL, '2025-08-18 10:47:29', '2025-08-29 10:47:29', NULL),
(8, 4, 'Site Corporate', '', 'Site vitrine pour une PME', '', '', 0, NULL, NULL, '2025-07-21 10:47:29', '2025-07-31 10:47:29', NULL),
(9, 7, 'nvp', 'nvp', 'nvp', 'blog', 'active', 0, NULL, NULL, '2025-09-01 10:32:18', '2025-09-01 10:32:18', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `project_analytics`
--

CREATE TABLE `project_analytics` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `views` int(11) DEFAULT '0',
  `visitors` int(11) DEFAULT '0',
  `bounce_rate` decimal(5,2) DEFAULT '0.00',
  `avg_session_duration` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `project_analytics`
--

INSERT INTO `project_analytics` (`id`, `project_id`, `date`, `views`, `visitors`, `bounce_rate`, `avg_session_duration`, `created_at`) VALUES
(1, 1, '2025-08-30', 151, 57, '42.67', 245, '2025-08-30 10:52:49'),
(2, 2, '2025-08-30', 275, 70, '36.23', 153, '2025-08-30 10:52:49'),
(3, 3, '2025-08-30', 142, 76, '45.51', 224, '2025-08-30 10:52:49'),
(4, 1, '2025-08-29', 114, 97, '30.56', 197, '2025-08-30 10:52:49'),
(5, 2, '2025-08-29', 277, 147, '31.13', 120, '2025-08-30 10:52:49'),
(6, 3, '2025-08-29', 278, 65, '28.18', 112, '2025-08-30 10:52:49'),
(7, 1, '2025-08-28', 296, 123, '47.35', 201, '2025-08-30 10:52:49'),
(8, 2, '2025-08-28', 160, 147, '54.95', 110, '2025-08-30 10:52:49'),
(9, 3, '2025-08-28', 153, 67, '26.94', 261, '2025-08-30 10:52:49'),
(10, 1, '2025-08-27', 267, 126, '35.00', 171, '2025-08-30 10:52:49'),
(11, 2, '2025-08-27', 259, 140, '29.18', 295, '2025-08-30 10:52:49'),
(12, 3, '2025-08-27', 195, 94, '48.45', 216, '2025-08-30 10:52:49'),
(13, 1, '2025-08-26', 214, 61, '51.14', 100, '2025-08-30 10:52:49'),
(14, 2, '2025-08-26', 178, 144, '42.06', 100, '2025-08-30 10:52:49'),
(15, 3, '2025-08-26', 158, 97, '39.49', 298, '2025-08-30 10:52:49'),
(16, 1, '2025-08-25', 204, 113, '43.13', 123, '2025-08-30 10:52:49'),
(17, 2, '2025-08-25', 254, 100, '31.58', 215, '2025-08-30 10:52:49'),
(18, 3, '2025-08-25', 144, 89, '33.94', 161, '2025-08-30 10:52:49'),
(19, 1, '2025-08-24', 227, 77, '39.04', 201, '2025-08-30 10:52:49'),
(20, 2, '2025-08-24', 126, 65, '35.39', 156, '2025-08-30 10:52:49'),
(21, 3, '2025-08-24', 173, 50, '52.27', 206, '2025-08-30 10:52:49'),
(22, 1, '2025-08-23', 288, 61, '47.70', 185, '2025-08-30 10:52:49'),
(23, 2, '2025-08-23', 275, 60, '51.24', 113, '2025-08-30 10:52:49'),
(24, 3, '2025-08-23', 243, 87, '47.05', 209, '2025-08-30 10:52:49'),
(25, 1, '2025-08-22', 204, 146, '33.38', 198, '2025-08-30 10:52:49'),
(26, 2, '2025-08-22', 223, 112, '32.47', 176, '2025-08-30 10:52:49'),
(27, 3, '2025-08-22', 131, 114, '47.58', 265, '2025-08-30 10:52:49'),
(28, 1, '2025-08-21', 276, 143, '25.22', 148, '2025-08-30 10:52:49'),
(29, 2, '2025-08-21', 138, 73, '42.98', 157, '2025-08-30 10:52:49'),
(30, 3, '2025-08-21', 229, 88, '53.61', 225, '2025-08-30 10:52:49'),
(31, 1, '2025-08-20', 154, 97, '41.84', 176, '2025-08-30 10:52:49'),
(32, 2, '2025-08-20', 145, 149, '33.74', 194, '2025-08-30 10:52:49'),
(33, 3, '2025-08-20', 195, 148, '39.79', 201, '2025-08-30 10:52:49'),
(34, 1, '2025-08-19', 110, 123, '40.52', 177, '2025-08-30 10:52:49'),
(35, 2, '2025-08-19', 175, 124, '41.86', 217, '2025-08-30 10:52:49'),
(36, 3, '2025-08-19', 152, 103, '51.46', 262, '2025-08-30 10:52:49'),
(37, 1, '2025-08-18', 184, 116, '26.28', 146, '2025-08-30 10:52:49'),
(38, 2, '2025-08-18', 106, 95, '31.18', 229, '2025-08-30 10:52:49'),
(39, 3, '2025-08-18', 226, 70, '29.40', 121, '2025-08-30 10:52:49'),
(40, 1, '2025-08-17', 120, 68, '44.15', 224, '2025-08-30 10:52:49'),
(41, 2, '2025-08-17', 139, 62, '26.49', 271, '2025-08-30 10:52:49'),
(42, 3, '2025-08-17', 129, 65, '34.89', 137, '2025-08-30 10:52:49'),
(43, 1, '2025-08-16', 289, 67, '26.54', 243, '2025-08-30 10:52:49'),
(44, 2, '2025-08-16', 188, 55, '53.76', 223, '2025-08-30 10:52:49'),
(45, 3, '2025-08-16', 144, 75, '43.36', 158, '2025-08-30 10:52:49'),
(65, 4, '2025-08-30', 96, 51, '20.57', 127, '2025-08-30 10:52:49'),
(66, 5, '2025-08-30', 121, 76, '22.70', 188, '2025-08-30 10:52:49'),
(67, 6, '2025-08-30', 203, 53, '20.90', 119, '2025-08-30 10:52:49'),
(68, 4, '2025-08-29', 83, 67, '31.27', 258, '2025-08-30 10:52:49'),
(69, 5, '2025-08-29', 98, 80, '51.31', 84, '2025-08-30 10:52:49'),
(70, 6, '2025-08-29', 195, 93, '45.22', 221, '2025-08-30 10:52:49'),
(71, 4, '2025-08-28', 84, 93, '55.68', 92, '2025-08-30 10:52:49'),
(72, 5, '2025-08-28', 181, 44, '54.05', 210, '2025-08-30 10:52:49'),
(73, 6, '2025-08-28', 91, 46, '50.94', 128, '2025-08-30 10:52:49'),
(74, 4, '2025-08-27', 82, 53, '35.69', 96, '2025-08-30 10:52:49'),
(75, 5, '2025-08-27', 122, 42, '55.96', 87, '2025-08-30 10:52:49'),
(76, 6, '2025-08-27', 156, 63, '43.75', 205, '2025-08-30 10:52:49'),
(77, 4, '2025-08-26', 183, 60, '52.50', 247, '2025-08-30 10:52:49'),
(78, 5, '2025-08-26', 110, 48, '42.89', 107, '2025-08-30 10:52:49'),
(79, 6, '2025-08-26', 85, 87, '40.48', 150, '2025-08-30 10:52:49'),
(80, 4, '2025-08-25', 141, 101, '29.71', 174, '2025-08-30 10:52:49'),
(81, 5, '2025-08-25', 214, 101, '51.58', 127, '2025-08-30 10:52:49'),
(82, 6, '2025-08-25', 223, 109, '24.74', 187, '2025-08-30 10:52:49'),
(83, 4, '2025-08-24', 172, 54, '48.01', 181, '2025-08-30 10:52:49'),
(84, 5, '2025-08-24', 189, 106, '42.57', 254, '2025-08-30 10:52:49'),
(85, 6, '2025-08-24', 103, 98, '53.85', 196, '2025-08-30 10:52:49'),
(86, 4, '2025-08-23', 183, 71, '40.72', 86, '2025-08-30 10:52:49'),
(87, 5, '2025-08-23', 173, 109, '25.09', 195, '2025-08-30 10:52:49'),
(88, 6, '2025-08-23', 204, 48, '47.21', 204, '2025-08-30 10:52:49'),
(89, 4, '2025-08-22', 144, 34, '20.68', 242, '2025-08-30 10:52:49'),
(90, 5, '2025-08-22', 149, 79, '48.13', 197, '2025-08-30 10:52:49'),
(91, 6, '2025-08-22', 104, 99, '53.10', 177, '2025-08-30 10:52:49'),
(92, 4, '2025-08-21', 113, 70, '53.93', 210, '2025-08-30 10:52:49'),
(93, 5, '2025-08-21', 92, 48, '55.86', 224, '2025-08-30 10:52:49'),
(94, 6, '2025-08-21', 126, 42, '53.37', 208, '2025-08-30 10:52:49'),
(95, 4, '2025-08-20', 87, 40, '40.21', 102, '2025-08-30 10:52:49'),
(96, 5, '2025-08-20', 98, 47, '49.67', 88, '2025-08-30 10:52:49'),
(97, 6, '2025-08-20', 81, 103, '41.32', 246, '2025-08-30 10:52:49'),
(98, 4, '2025-08-19', 83, 58, '48.02', 158, '2025-08-30 10:52:49'),
(99, 5, '2025-08-19', 90, 34, '22.55', 107, '2025-08-30 10:52:49'),
(100, 6, '2025-08-19', 164, 59, '27.08', 216, '2025-08-30 10:52:49'),
(101, 4, '2025-08-18', 120, 35, '42.30', 181, '2025-08-30 10:52:49'),
(102, 5, '2025-08-18', 102, 34, '53.79', 88, '2025-08-30 10:52:49'),
(103, 6, '2025-08-18', 184, 56, '43.29', 244, '2025-08-30 10:52:49'),
(104, 4, '2025-08-17', 202, 57, '31.84', 157, '2025-08-30 10:52:49'),
(105, 5, '2025-08-17', 119, 32, '34.70', 213, '2025-08-30 10:52:49'),
(106, 6, '2025-08-17', 169, 91, '22.92', 87, '2025-08-30 10:52:49'),
(107, 4, '2025-08-16', 229, 97, '29.63', 200, '2025-08-30 10:52:49'),
(108, 5, '2025-08-16', 173, 39, '48.16', 111, '2025-08-30 10:52:49'),
(109, 6, '2025-08-16', 192, 49, '58.06', 85, '2025-08-30 10:52:49'),
(128, 7, '2025-08-30', 97, 46, '30.44', 92, '2025-08-30 10:52:49'),
(129, 8, '2025-08-30', 70, 68, '56.38', 170, '2025-08-30 10:52:49'),
(130, 7, '2025-08-29', 175, 65, '58.73', 78, '2025-08-30 10:52:49'),
(131, 8, '2025-08-29', 172, 41, '63.98', 190, '2025-08-30 10:52:49'),
(132, 7, '2025-08-28', 78, 39, '21.57', 171, '2025-08-30 10:52:49'),
(133, 8, '2025-08-28', 71, 44, '50.55', 115, '2025-08-30 10:52:49'),
(134, 7, '2025-08-27', 133, 20, '26.25', 74, '2025-08-30 10:52:49'),
(135, 8, '2025-08-27', 152, 54, '44.58', 94, '2025-08-30 10:52:49'),
(136, 7, '2025-08-26', 98, 76, '50.94', 186, '2025-08-30 10:52:49'),
(137, 8, '2025-08-26', 155, 56, '46.97', 121, '2025-08-30 10:52:49'),
(138, 7, '2025-08-25', 178, 68, '18.11', 202, '2025-08-30 10:52:49'),
(139, 8, '2025-08-25', 92, 59, '41.16', 159, '2025-08-30 10:52:49'),
(140, 7, '2025-08-24', 124, 68, '37.12', 185, '2025-08-30 10:52:49'),
(141, 8, '2025-08-24', 131, 57, '31.59', 186, '2025-08-30 10:52:49'),
(142, 7, '2025-08-23', 173, 42, '15.29', 207, '2025-08-30 10:52:49'),
(143, 8, '2025-08-23', 130, 30, '21.51', 78, '2025-08-30 10:52:49'),
(144, 7, '2025-08-22', 81, 52, '24.74', 114, '2025-08-30 10:52:49'),
(145, 8, '2025-08-22', 72, 50, '28.18', 183, '2025-08-30 10:52:49'),
(146, 7, '2025-08-21', 66, 77, '47.91', 123, '2025-08-30 10:52:49'),
(147, 8, '2025-08-21', 62, 73, '36.46', 132, '2025-08-30 10:52:49'),
(148, 7, '2025-08-20', 178, 53, '58.09', 158, '2025-08-30 10:52:49'),
(149, 8, '2025-08-20', 120, 60, '57.72', 99, '2025-08-30 10:52:49'),
(150, 7, '2025-08-19', 141, 59, '26.16', 85, '2025-08-30 10:52:49'),
(151, 8, '2025-08-19', 75, 28, '33.38', 122, '2025-08-30 10:52:49'),
(152, 7, '2025-08-18', 162, 26, '63.81', 148, '2025-08-30 10:52:49'),
(153, 8, '2025-08-18', 160, 51, '19.63', 204, '2025-08-30 10:52:49'),
(154, 7, '2025-08-17', 88, 48, '48.20', 203, '2025-08-30 10:52:49'),
(155, 8, '2025-08-17', 118, 64, '27.51', 64, '2025-08-30 10:52:49'),
(156, 7, '2025-08-16', 106, 70, '16.93', 168, '2025-08-30 10:52:49'),
(157, 8, '2025-08-16', 93, 42, '15.01', 203, '2025-08-30 10:52:49');

-- --------------------------------------------------------


--
-- Structure de la table `project_elements`
--

CREATE TABLE `project_elements` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `page` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `content` mediumtext,
  `css` mediumtext,
  `js` mediumtext,
  `meta` json DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `position` int(11) DEFAULT '0',
  `status` enum('active','hidden','deleted') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Structure de la table `project_stats`
--

CREATE TABLE `project_stats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `visitors` int(11) DEFAULT '0',
  `pageviews` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `quotes`
--

CREATE TABLE `quotes` (
  `id` int(11) NOT NULL,
  `quote_number` varchar(50) NOT NULL,
  `opportunity_id` int(11) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) NOT NULL,
  `status` enum('draft','sent','accepted','rejected','expired') DEFAULT 'draft',
  `total_amount` decimal(15,2) DEFAULT '0.00',
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `valid_until` date DEFAULT NULL,
  `notes` text,
  `terms_conditions` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `quote_items`
--

CREATE TABLE `quote_items` (
  `id` int(11) NOT NULL,
  `quote_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `quantity` decimal(10,2) DEFAULT '1.00',
  `unit_price` decimal(10,2) DEFAULT '0.00',
  `discount_percent` decimal(5,2) DEFAULT '0.00',
  `total_price` decimal(15,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `rt_events`
--

CREATE TABLE `rt_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `sales_dashboard`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `sales_dashboard` (
`date` date
,`total_opportunities` bigint(21)
,`revenue` decimal(37,2)
,`deals_won` decimal(23,0)
,`deals_lost` decimal(23,0)
,`avg_deal_size` decimal(19,6)
,`first_name` varchar(50)
,`last_name` varchar(50)
);

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Structure de la table `statuses`
--

CREATE TABLE `statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` enum('mission','dossier','global') NOT NULL DEFAULT 'global'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `statuses`
--

INSERT INTO `statuses` (`id`, `name`, `type`) VALUES
(1, 'Devis en cours', 'global'),
(2, 'Devis accepté', 'global'),
(3, 'Ouverte', 'mission'),
(4, 'En cours', 'mission'),
(5, 'Terminée', 'mission'),
(6, 'Facturée', 'global'),
(7, 'Payée', 'global'),
(8, 'Annulée', 'global');

-- --------------------------------------------------------



-- --------------------------------------------------------

--
-- Structure de la table `support_messages`
--

CREATE TABLE `support_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('open','in_progress','resolved','closed') DEFAULT 'open',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `category` varchar(100) DEFAULT NULL,
  `admin_response` text,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Structure de la table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text,
  `is_public` tinyint(1) DEFAULT '0',
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Structure de la table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `targets`
--

CREATE TABLE `targets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('revenue','deals','calls','meetings') NOT NULL,
  `period` enum('monthly','quarterly','yearly') NOT NULL,
  `target_value` decimal(15,2) NOT NULL,
  `achieved_value` decimal(15,2) DEFAULT '0.00',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT '',
  `last_name` varchar(50) DEFAULT '',
  `role` enum('admin','manager','sales','support') DEFAULT 'sales',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email_verified` tinyint(1) DEFAULT '0',
  `email_verify_token` varchar(64) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `twofa_enabled` tinyint(1) DEFAULT '0',
  `twofa_secret` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




-- --------------------------------------------------------

--
-- Structure de la table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_notifications` tinyint(1) DEFAULT '1',
  `security_alerts` tinyint(1) DEFAULT '1',
  `newsletter` tinyint(1) DEFAULT '0',
  `theme` enum('light','dark','auto') COLLATE utf8mb4_unicode_ci DEFAULT 'light',
  `language` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'fr',
  `timezone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Europe/Paris',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Structure de la table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Structure de la vue `sales_dashboard`
--
DROP TABLE IF EXISTS `sales_dashboard`;


--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `opportunity_id` (`opportunity_id`),
  ADD KEY `idx_activities_assigned_to` (`assigned_to`),
  ADD KEY `idx_activities_due_date` (`due_date`),
  ADD KEY `idx_activities_status` (`status`);

--
-- Index pour la table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_logs_user_id` (`user_id`),
  ADD KEY `idx_activity_logs_created_at` (`created_at`),
  ADD KEY `idx_activity_customer` (`customer_id`);

--
-- Index pour la table `analytics_metrics`
--
ALTER TABLE `analytics_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `opportunity_id` (`opportunity_id`),
  ADD KEY `idx_analytics_metric_date` (`metric_date`);

--
-- Index pour la table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Index pour la table `automations`
--
ALTER TABLE `automations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `automation_logs`
--
ALTER TABLE `automation_logs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `automation_steps`
--
ALTER TABLE `automation_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `automation_id` (`automation_id`);

--
-- Index pour la table `cahier_des_charges`
--
ALTER TABLE `cahier_des_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Index pour la table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_automation_id` (`automation_id`);

--
-- Index pour la table `campaign_events`
--
ALTER TABLE `campaign_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaign_id` (`campaign_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `event_type` (`event_type`),
  ADD KEY `identifier` (`identifier`);

--
-- Index pour la table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `response_to` (`response_to`),
  ADD KEY `fk_conversation_id` (`conversation_id`);

--
-- Index pour la table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_companies_status` (`status`),
  ADD KEY `idx_companies_assigned_to` (`assigned_to`),
  ADD KEY `fk_customer` (`customer_id`),
  ADD KEY `idx_companies_segment` (`segment`);

--
-- Index pour la table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contacts_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_contacts_company_id` (`company_id`),
  ADD KEY `fk_contacts_assigned_to` (`assigned_to`);

--
-- Index pour la table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `request_id` (`request_id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Index pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `crm_contacts`
--
ALTER TABLE `crm_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Index pour la table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);


--
-- Index pour la table `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`);

--
-- Index pour la table `erp_companies`
--
ALTER TABLE `erp_companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_erp_companies_customer_id` (`customer_id`);

--
-- Index pour la table `erp_employees`
--
ALTER TABLE `erp_employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_emp_company` (`company_id`),
  ADD KEY `idx_erp_employees_customer_id` (`customer_id`);

--
-- Index pour la table `erp_inventory`
--
ALTER TABLE `erp_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inventory_customer` (`customer_id`);

--
-- Index pour la table `erp_payrolls`
--
ALTER TABLE `erp_payrolls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_emp_period` (`employee_id`,`period`),
  ADD KEY `idx_erp_payrolls_customer_id` (`customer_id`);

--
-- Index pour la table `erp_sales`
--
ALTER TABLE `erp_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sales_product` (`product_id`),
  ADD KEY `idx_sales_customer` (`customer_id`),
  ADD KEY `idx_sales_employee` (`employee_id`);

--
-- Index pour la table `erp_shifts`
--
ALTER TABLE `erp_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_shift_employee` (`employee_id`),
  ADD KEY `idx_shift_customer` (`customer_id`),
  ADD KEY `idx_shift_company` (`company_id`);

--
-- Index pour la table `erp_stock`
--
ALTER TABLE `erp_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_stock_customer` (`customer_id`);

--
-- Index pour la table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `fk_folder_status` (`status_id`);

--
-- Index pour la table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_id` (`site_id`);

--
-- Index pour la table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder_id` (`folder_id`);

--
-- Index pour la table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_time` (`email`,`attempted_at`),
  ADD KEY `idx_ip_time` (`ip_address`,`attempted_at`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Index pour la table `missions`
--
ALTER TABLE `missions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder_id` (`folder_id`),
  ADD KEY `fk_mission_status` (`status_id`),
  ADD KEY `fk_missions_assigned_to` (`assigned_to`);

--
-- Index pour la table `msg_conversations`
--
ALTER TABLE `msg_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer` (`customer_id`);

--
-- Index pour la table `msg_messages`
--
ALTER TABLE `msg_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conv_id` (`conversation_id`,`id`);

--
-- Index pour la table `msg_participants`
--
ALTER TABLE `msg_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_participant` (`conversation_id`,`participant_type`,`participant_id`),
  ADD KEY `idx_conv` (`conversation_id`);

--
-- Index pour la table `msg_receipts`
--
ALTER TABLE `msg_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_msg` (`message_id`);

--
-- Index pour la table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user_id` (`user_id`),
  ADD KEY `idx_notifications_is_read` (`is_read`);

--
-- Index pour la table `opportunities`
--
ALTER TABLE `opportunities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `idx_opportunities_stage` (`stage`),
  ADD KEY `idx_opportunities_assigned_to` (`assigned_to`),
  ADD KEY `idx_opportunities_close_date` (`expected_close_date`),
  ADD KEY `idx_opportunities_customer_id` (`customer_id`),
  ADD KEY `idx_opportunities_status_id` (`status_id`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `store_id` (`store_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);



--
-- Index pour la table `pipeline_stages`
--
ALTER TABLE `pipeline_stages`
  ADD PRIMARY KEY (`id`);


--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Index pour la table `powerbi_reports`
--
ALTER TABLE `powerbi_reports`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `store_id` (`store_id`);


--
-- Index pour la table `project_analytics`
--
ALTER TABLE `project_analytics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_project_date` (`project_id`,`date`),
  ADD KEY `idx_project_id` (`project_id`),
  ADD KEY `idx_date` (`date`);



--
-- Index pour la table `project_stats`
--
ALTER TABLE `project_stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_date` (`user_id`,`date`),
  ADD KEY `idx_project_date` (`project_id`,`date`);

--
-- Index pour la table `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quote_number` (`quote_number`),
  ADD KEY `opportunity_id` (`opportunity_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Index pour la table `quote_items`
--
ALTER TABLE `quote_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quote_id` (`quote_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Index pour la table `rt_events`
--
ALTER TABLE `rt_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_topic_id` (`customer_id`,`topic`,`id`),
  ADD KEY `idx_created_at` (`created_at`);


--
-- Index pour la table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Index pour la table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Index pour la table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `targets`
--
ALTER TABLE `targets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_customer` (`customer_id`);

--
-- Index pour la table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Index pour la table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`);



--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `analytics_metrics`
--
ALTER TABLE `analytics_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `automations`
--
ALTER TABLE `automations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `automation_logs`
--
ALTER TABLE `automation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `automation_steps`
--
ALTER TABLE `automation_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cahier_des_charges`
--
ALTER TABLE `cahier_des_charges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `campaign_events`
--
ALTER TABLE `campaign_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT pour la table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `contact_requests`
--
ALTER TABLE `contact_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT pour la table `crm_contacts`
--
ALTER TABLE `crm_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;


--
-- AUTO_INCREMENT pour la table `emails`
--
ALTER TABLE `emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `erp_companies`
--
ALTER TABLE `erp_companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `erp_employees`
--
ALTER TABLE `erp_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `erp_inventory`
--
ALTER TABLE `erp_inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `erp_payrolls`
--
ALTER TABLE `erp_payrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `erp_sales`
--
ALTER TABLE `erp_sales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `erp_shifts`
--
ALTER TABLE `erp_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `erp_stock`
--
ALTER TABLE `erp_stock`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `missions`
--
ALTER TABLE `missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `msg_conversations`
--
ALTER TABLE `msg_conversations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `msg_messages`
--
ALTER TABLE `msg_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `msg_participants`
--
ALTER TABLE `msg_participants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `msg_receipts`
--
ALTER TABLE `msg_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `opportunities`
--
ALTER TABLE `opportunities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `pipeline_stages`
--
ALTER TABLE `pipeline_stages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT pour la table `powerbi_reports`
--
ALTER TABLE `powerbi_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- AUTO_INCREMENT pour la table `project_analytics`
--
ALTER TABLE `project_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;



--
-- AUTO_INCREMENT pour la table `project_stats`
--
ALTER TABLE `project_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `quotes`
--
ALTER TABLE `quotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `quote_items`
--
ALTER TABLE `quote_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rt_events`
--
ALTER TABLE `rt_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


--
-- AUTO_INCREMENT pour la table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `targets`
--
ALTER TABLE `targets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `vcard_contacts`
--
ALTER TABLE `vcard_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `winner`
--
ALTER TABLE `winner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `activities_ibfk_3` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `activities_ibfk_4` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_activity_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `analytics_metrics`
--
ALTER TABLE `analytics_metrics`
  ADD CONSTRAINT `analytics_metrics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `analytics_metrics_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `analytics_metrics_ibfk_3` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunities` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `automation_steps`
--
ALTER TABLE `automation_steps`
  ADD CONSTRAINT `automation_steps_ibfk_1` FOREIGN KEY (`automation_id`) REFERENCES `automations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `cahier_des_charges`
--
ALTER TABLE `cahier_des_charges`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `campaigns`
--
ALTER TABLE `campaigns`
  ADD CONSTRAINT `fk_campaigns_automation` FOREIGN KEY (`automation_id`) REFERENCES `automations` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`response_to`) REFERENCES `chat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conversation_id` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`);

--
-- Contraintes pour la table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `fk_contacts_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_contacts_company_id` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD CONSTRAINT `contact_requests_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `crm_contacts`
--
ALTER TABLE `crm_contacts`
  ADD CONSTRAINT `crm_contacts_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crm_contacts_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;


--
-- Contraintes pour la table `emails`
--
ALTER TABLE `emails`
  ADD CONSTRAINT `emails_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `erp_employees`
--
ALTER TABLE `erp_employees`
  ADD CONSTRAINT `fk_emp_company` FOREIGN KEY (`company_id`) REFERENCES `erp_companies` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `erp_inventory`
--
ALTER TABLE `erp_inventory`
  ADD CONSTRAINT `fk_inventory_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `erp_payrolls`
--
ALTER TABLE `erp_payrolls`
  ADD CONSTRAINT `fk_payroll_employee` FOREIGN KEY (`employee_id`) REFERENCES `erp_employees` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `erp_sales`
--
ALTER TABLE `erp_sales`
  ADD CONSTRAINT `fk_sales_customer_uniq` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sales_employee_uniq` FOREIGN KEY (`employee_id`) REFERENCES `erp_employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sales_product_uniq` FOREIGN KEY (`product_id`) REFERENCES `erp_stock` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `erp_shifts`
--
ALTER TABLE `erp_shifts`
  ADD CONSTRAINT `fk_shifts_company_uniq` FOREIGN KEY (`company_id`) REFERENCES `erp_companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_shifts_customer_uniq` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_shifts_employee_uniq` FOREIGN KEY (`employee_id`) REFERENCES `erp_employees` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `erp_stock`
--
ALTER TABLE `erp_stock`
  ADD CONSTRAINT `fk_stock_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `fk_folder_status` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  ADD CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`);

--

--
-- Contraintes pour la table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `missions`
--
ALTER TABLE `missions`
  ADD CONSTRAINT `fk_mission_status` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  ADD CONSTRAINT `fk_missions_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `missions_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `msg_messages`
--
ALTER TABLE `msg_messages`
  ADD CONSTRAINT `fk_mm_conv` FOREIGN KEY (`conversation_id`) REFERENCES `msg_conversations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `msg_participants`
--
ALTER TABLE `msg_participants`
  ADD CONSTRAINT `fk_mp_conv` FOREIGN KEY (`conversation_id`) REFERENCES `msg_conversations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `msg_receipts`
--
ALTER TABLE `msg_receipts`
  ADD CONSTRAINT `fk_mr_msg` FOREIGN KEY (`message_id`) REFERENCES `msg_messages` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `opportunities`
--
ALTER TABLE `opportunities`
  ADD CONSTRAINT `opportunities_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `opportunities_ibfk_2` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `opportunities_ibfk_3` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Contraintes pour la table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`);


--
-- Contraintes pour la table `project_analytics`
--
ALTER TABLE `project_analytics`
  ADD CONSTRAINT `project_analytics_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;



--
-- Contraintes pour la table `project_stats`
--
ALTER TABLE `project_stats`
  ADD CONSTRAINT `project_stats_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_stats_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quotes`
--
ALTER TABLE `quotes`
  ADD CONSTRAINT `quotes_ibfk_1` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotes_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quotes_ibfk_3` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotes_ibfk_4` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quote_items`
--
ALTER TABLE `quote_items`
  ADD CONSTRAINT `quote_items_ibfk_1` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quote_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;


--
-- Contraintes pour la table `stores`
--
ALTER TABLE `stores`
  ADD CONSTRAINT `stores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `support_messages`
--
ALTER TABLE `support_messages`
  ADD CONSTRAINT `support_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `targets`
--
ALTER TABLE `targets`
  ADD CONSTRAINT `targets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

















ALTER TABLE erp_employees MODIFY id INT NOT NULL AUTO_INCREMENT;
ALTER TABLE erp_companies MODIFY id INT NOT NULL AUTO_INCREMENT, ADD UNIQUE (id);
ALTER TABLE erp_companies MODIFY id INT NOT NULL AUTO_INCREMENT;