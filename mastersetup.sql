-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 07, 2018 at 10:22 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mastersetup`
--

-- --------------------------------------------------------

--
-- Table structure for table `activations`
--

CREATE TABLE `activations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `activations`
--

INSERT INTO `activations` (`id`, `user_id`, `code`, `completed`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'MLKvcmkwGZ3e6bUbjJGd7BhH5cL0S6yK', 1, '2016-04-25 07:50:44', '2016-04-25 07:50:44', '2016-04-25 07:50:44'),
(2, 2, 'CMMDG1WPHi44791mId6xPeFiRS6XwHS5', 1, '2017-04-14 05:49:59', '2017-04-14 05:49:59', '2017-04-14 05:49:59'),
(3, 3, 'CMMDG1WPHi44791mId6xPeFiRS6XwHS52312', 1, '2017-04-14 05:49:59', '2017-04-14 05:49:59', '2017-04-14 05:49:59'),
(4, 4, 'zDgTdRSN2efB7Eou3rGTeYIHP3cNPTFf', 1, '2017-04-18 00:19:09', '2017-04-18 00:19:09', '2017-04-18 00:19:09'),
(5, 5, 'Bxle0Rrpyz7OtDnIryI9k3FOivNgrO9O', 1, '2018-02-23 00:13:42', '2018-02-23 00:13:42', '2018-02-23 00:13:42'),
(6, 6, 'za5YcnXMH9XAVbXVRXzusrA9OgTZkUsl', 1, '2018-02-23 00:16:26', '2018-02-23 00:16:26', '2018-02-23 00:16:26'),
(7, 1, 'MLKvcmkwGZ3e6bUbjJGd7BhH5cL0S6yK', 1, NULL, NULL, NULL),
(8, 7, 'H6GD4hUrR8mufrW9Qd0vlRPvkY9PAE9m', 1, '2018-02-23 04:12:26', '2018-02-23 04:12:26', '2018-02-23 04:12:26'),
(9, 8, 'iXvlSwSjiVdRkTQaXcIHKuEeVRcWRjNg', 1, '2018-02-23 04:20:05', '2018-02-23 04:20:05', '2018-02-23 04:20:05'),
(10, 9, '49hwNbr7mwD3ZpldHDww1MPl4GC1DT9W', 1, '2018-02-23 04:22:16', '2018-02-23 04:22:16', '2018-02-23 04:22:16'),
(11, 10, '5ZpRy2IYZNMOUWo9LLNdP6HCwn2QneiB', 1, '2018-02-23 04:24:02', '2018-02-23 04:24:02', '2018-02-23 04:24:02'),
(12, 11, '3RWaR2wp07CKawzQO9y4XeNLqlN9FSew', 1, '2018-03-01 00:20:22', '2018-03-01 00:20:22', '2018-03-01 00:20:22'),
(13, 2, 'QVPEJYmKsYoHHl2JqP7iO6hWAvhvgqGO', 1, '2018-03-03 04:32:45', '2018-03-03 04:32:45', '2018-03-03 04:32:45');

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(255) NOT NULL,
  `module_title` varchar(255) NOT NULL,
  `module_action` enum('ADD','EDIT','REMOVED') NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `module_title`, `module_action`, `user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Account Settings', 'EDIT', 1, '2017-01-21 07:22:04', '2016-11-10 07:33:38', NULL),
(32, 'Account Settings', 'EDIT', 1, '2017-01-21 03:40:11', '2017-01-21 03:40:11', NULL),
(33, 'Admin Users', 'ADD', 1, '2017-01-21 03:45:00', '2017-01-21 03:45:00', NULL),
(35, 'Account Settings', 'EDIT', 1, '2017-01-21 04:23:51', '2017-01-21 04:23:51', NULL),
(36, 'Account Settings', 'EDIT', 1, '2017-01-21 04:24:14', '2017-01-21 04:24:14', NULL),
(37, 'Admin Users', 'ADD', 1, '2017-01-21 04:31:54', '2017-01-21 04:31:54', NULL),
(38, 'Admin Users', 'REMOVED', 1, '2017-01-21 04:32:02', '2017-01-21 04:32:02', NULL),
(39, 'Contact Enquiry', 'REMOVED', 1, '2017-01-21 04:44:05', '2017-01-21 04:44:05', NULL),
(40, 'Email Template', 'EDIT', 1, '2017-01-21 04:46:24', '2017-01-21 04:46:24', NULL),
(41, 'CMS', 'ADD', 1, '2017-01-21 04:53:35', '2017-01-21 04:53:35', NULL),
(42, 'CMS', 'EDIT', 1, '2017-01-21 04:54:12', '2017-01-21 04:54:12', NULL),
(43, 'CMS', 'EDIT', 1, '2017-01-21 04:56:02', '2017-01-21 04:56:02', NULL),
(44, 'CMS', 'REMOVED', 1, '2017-01-21 04:56:32', '2017-01-21 04:56:32', NULL),
(45, 'Site Settings', 'EDIT', 1, '2017-01-21 05:04:24', '2017-01-21 05:04:24', NULL),
(46, 'Site Settings', 'EDIT', 1, '2017-01-21 05:06:15', '2017-01-21 05:06:15', NULL),
(48, 'Users', 'EDIT', 1, '2017-01-21 06:11:47', '2017-01-21 06:11:47', NULL),
(49, 'Admin Users', 'EDIT', 1, '2017-01-21 06:31:59', '2017-01-21 06:31:59', NULL),
(50, 'CMS', 'ADD', 1, '2017-01-21 06:41:07', '2017-01-21 06:41:07', NULL),
(51, 'CMS', 'EDIT', 1, '2017-01-21 06:52:42', '2017-01-21 06:52:42', NULL),
(52, 'CMS', 'REMOVED', 1, '2017-01-21 06:57:18', '2017-01-21 06:57:18', NULL),
(53, 'Email Template', 'EDIT', 1, '2017-01-21 06:58:01', '2017-01-21 06:58:01', NULL),
(54, 'Users', 'ADD', 1, '2017-01-21 06:59:00', '2017-01-21 06:59:00', NULL),
(55, 'Users', 'EDIT', 1, '2017-01-21 06:59:22', '2017-01-21 06:59:22', NULL),
(56, 'Users', 'ADD', 1, '2017-01-21 07:00:04', '2017-01-21 07:00:04', NULL),
(57, 'Users', 'REMOVED', 1, '2017-01-21 07:00:18', '2017-01-21 07:00:18', NULL),
(58, 'Category/Sub-Category', 'ADD', 1, '2017-01-21 07:00:49', '2017-01-21 07:00:49', NULL),
(59, 'Category/Sub-Category', 'ADD', 1, '2017-01-21 07:01:15', '2017-01-21 07:01:15', NULL),
(60, 'Category/Sub-Category', 'EDIT', 1, '2017-01-21 07:01:32', '2017-01-21 07:01:32', NULL),
(61, 'Site Settings', 'EDIT', 1, '2017-01-21 07:17:14', '2017-01-21 07:17:14', NULL),
(62, 'Site Settings', 'EDIT', 1, '2017-01-21 07:18:52', '2017-01-21 07:18:52', NULL),
(63, 'Account Settings', 'EDIT', 1, '2017-01-21 07:21:29', '2017-01-21 07:21:29', NULL),
(64, 'Site Settings', 'EDIT', 1, '2017-01-21 07:21:38', '2017-01-21 07:21:38', NULL),
(65, 'Admin Users', 'REMOVED', 1, '2017-01-21 07:24:46', '2017-01-21 07:24:46', NULL),
(66, 'Admin Users', 'REMOVED', 1, '2017-01-21 07:24:46', '2017-01-21 07:24:46', NULL),
(67, 'Admin Users', 'REMOVED', 1, '2017-01-21 07:24:50', '2017-01-21 07:24:50', NULL),
(68, 'Admin Users', 'REMOVED', 1, '2017-01-21 07:24:50', '2017-01-21 07:24:50', NULL),
(69, 'Admin Users', 'ADD', 1, '2017-01-21 07:25:14', '2017-01-21 07:25:14', NULL),
(70, 'Site Settings', 'EDIT', 1, '2017-01-21 07:40:56', '2017-01-21 07:40:56', NULL),
(71, 'Site Settings', 'EDIT', 1, '2017-01-21 07:41:15', '2017-01-21 07:41:15', NULL),
(72, 'States', 'ADD', 10, '2017-01-23 00:16:23', '2017-01-23 00:16:23', NULL),
(73, 'States', 'ADD', 10, '2017-01-23 00:16:41', '2017-01-23 00:16:41', NULL),
(74, 'States', 'REMOVED', 10, '2017-01-23 00:16:52', '2017-01-23 00:16:52', NULL),
(75, 'States', 'EDIT', 10, '2017-01-23 00:17:20', '2017-01-23 00:17:20', NULL),
(76, 'States', 'EDIT', 10, '2017-01-23 00:17:37', '2017-01-23 00:17:37', NULL),
(77, 'States', 'ADD', 10, '2017-01-23 00:17:58', '2017-01-23 00:17:58', NULL),
(78, 'States', 'EDIT', 10, '2017-01-23 00:21:05', '2017-01-23 00:21:05', NULL),
(79, 'States', 'REMOVED', 10, '2017-01-23 00:21:24', '2017-01-23 00:21:24', NULL),
(80, 'States', 'REMOVED', 10, '2017-01-23 00:21:30', '2017-01-23 00:21:30', NULL),
(81, 'Category/Sub-Category', 'ADD', 1, '2017-01-23 00:25:42', '2017-01-23 00:25:42', NULL),
(82, 'Category/Sub-Category', 'ADD', 1, '2017-01-23 00:27:05', '2017-01-23 00:27:05', NULL),
(83, 'Category/Sub-Category', 'ADD', 1, '2017-01-23 00:28:18', '2017-01-23 00:28:18', NULL),
(84, 'Category/Sub-Category', 'ADD', 1, '2017-01-23 00:28:36', '2017-01-23 00:28:36', NULL),
(85, 'Email Template', 'EDIT', 1, '2017-01-23 04:09:23', '2017-01-23 04:09:23', NULL),
(86, 'FAQ\'s', 'ADD', 1, '2017-01-23 04:10:16', '2017-01-23 04:10:16', NULL),
(87, 'FAQ\'s', 'REMOVED', 1, '2017-01-23 04:10:42', '2017-01-23 04:10:42', NULL),
(88, 'Account Settings', 'EDIT', 1, '2017-01-23 04:20:52', '2017-01-23 04:20:52', NULL),
(89, 'Account Settings', 'EDIT', 1, '2017-01-23 04:29:19', '2017-01-23 04:29:19', NULL),
(90, 'Account Settings', 'EDIT', 1, '2017-01-23 04:50:09', '2017-01-23 04:50:09', NULL),
(91, 'Users', 'ADD', 1, '2017-01-23 04:58:07', '2017-01-23 04:58:07', NULL),
(92, 'Users', 'EDIT', 1, '2017-01-23 05:18:14', '2017-01-23 05:18:14', NULL),
(93, 'Users', 'EDIT', 1, '2017-01-23 05:18:51', '2017-01-23 05:18:51', NULL),
(94, 'Users', 'EDIT', 1, '2017-01-23 05:19:26', '2017-01-23 05:19:26', NULL),
(95, 'Users', 'EDIT', 1, '2017-01-23 05:19:35', '2017-01-23 05:19:35', NULL),
(96, 'Users', 'EDIT', 1, '2017-01-23 05:19:45', '2017-01-23 05:19:45', NULL),
(97, 'Users', 'EDIT', 1, '2017-01-23 05:19:54', '2017-01-23 05:19:54', NULL),
(98, 'Users', 'REMOVED', 1, '2017-01-23 05:20:08', '2017-01-23 05:20:08', NULL),
(99, 'Email Template', 'EDIT', 1, '2017-02-22 03:04:32', '2017-02-22 03:04:32', NULL),
(100, 'Account Settings', 'EDIT', 1, '2017-02-22 05:15:49', '2017-02-22 05:15:49', NULL),
(101, 'Category/Sub-Category', 'EDIT', 1, '2017-04-14 03:57:54', '2017-04-14 03:57:54', NULL),
(102, 'Category/Sub-Category', 'EDIT', 1, '2017-04-14 03:58:30', '2017-04-14 03:58:30', NULL),
(103, 'Category/Sub-Category', 'EDIT', 1, '2017-04-14 03:58:46', '2017-04-14 03:58:46', NULL),
(104, 'Category/Sub-Category', 'ADD', 1, '2017-04-14 03:59:25', '2017-04-14 03:59:25', NULL),
(105, 'Account Settings', 'EDIT', 1, '2017-04-14 04:25:12', '2017-04-14 04:25:12', NULL),
(106, 'Account Settings', 'EDIT', 1, '2017-04-14 04:25:18', '2017-04-14 04:25:18', NULL),
(107, 'Account Settings', 'EDIT', 1, '2017-04-14 04:25:25', '2017-04-14 04:25:25', NULL),
(108, 'Account Settings', 'EDIT', 1, '2017-04-14 04:27:10', '2017-04-14 04:27:10', NULL),
(109, 'Account Settings', 'EDIT', 1, '2017-04-14 04:27:32', '2017-04-14 04:27:32', NULL),
(110, 'Account Settings', 'EDIT', 1, '2017-04-14 04:28:35', '2017-04-14 04:28:35', NULL),
(111, 'Account Settings', 'EDIT', 1, '2017-04-14 04:52:14', '2017-04-14 04:52:14', NULL),
(112, 'Account Settings', 'EDIT', 1, '2017-04-14 04:52:44', '2017-04-14 04:52:44', NULL),
(113, 'Account Settings', 'EDIT', 1, '2017-04-14 04:54:18', '2017-04-14 04:54:18', NULL),
(114, 'Account Settings', 'EDIT', 1, '2017-04-14 04:54:22', '2017-04-14 04:54:22', NULL),
(115, 'Account Settings', 'EDIT', 1, '2017-04-14 04:54:36', '2017-04-14 04:54:36', NULL),
(116, 'Account Settings', 'EDIT', 1, '2017-04-14 04:55:55', '2017-04-14 04:55:55', NULL),
(117, 'Testimonial', 'REMOVED', 1, '2017-04-14 05:03:06', '2017-04-14 05:03:06', NULL),
(118, 'Testimonial', 'REMOVED', 1, '2017-04-14 05:03:19', '2017-04-14 05:03:19', NULL),
(119, 'Admin Users', 'ADD', 1, '2017-04-14 05:50:00', '2017-04-14 05:50:00', NULL),
(120, 'Admin Users', 'REMOVED', 1, '2017-04-14 05:50:15', '2017-04-14 05:50:15', NULL),
(121, 'Admin Users', 'REMOVED', 1, '2017-04-14 05:50:15', '2017-04-14 05:50:15', NULL),
(122, 'Testimonial', 'REMOVED', 1, '2017-04-14 06:06:13', '2017-04-14 06:06:13', NULL),
(123, 'Testimonial', 'REMOVED', 1, '2017-04-14 06:06:18', '2017-04-14 06:06:18', NULL),
(124, 'Site Settings', 'EDIT', 1, '2017-04-15 00:14:36', '2017-04-15 00:14:36', NULL),
(125, 'Site Settings', 'EDIT', 1, '2017-04-15 00:14:41', '2017-04-15 00:14:41', NULL),
(126, 'Site Settings', 'EDIT', 1, '2017-04-15 00:15:33', '2017-04-15 00:15:33', NULL),
(127, 'Site Settings', 'EDIT', 1, '2017-04-15 00:15:41', '2017-04-15 00:15:41', NULL),
(128, 'Site Settings', 'EDIT', 1, '2017-04-15 00:47:42', '2017-04-15 00:47:42', NULL),
(129, 'CMS', 'EDIT', 1, '2017-04-15 01:01:55', '2017-04-15 01:01:55', NULL),
(130, 'CMS', 'EDIT', 1, '2017-04-15 01:02:40', '2017-04-15 01:02:40', NULL),
(131, 'CMS', 'EDIT', 1, '2017-04-15 01:02:53', '2017-04-15 01:02:53', NULL),
(132, 'CMS', 'EDIT', 1, '2017-04-15 01:03:03', '2017-04-15 01:03:03', NULL),
(133, 'CMS', 'EDIT', 1, '2017-04-15 01:03:12', '2017-04-15 01:03:12', NULL),
(134, 'Subscribers ', 'REMOVED', 1, '2017-04-15 03:16:49', '2017-04-15 03:16:49', NULL),
(135, 'News', 'REMOVED', 1, '2017-04-15 03:25:19', '2017-04-15 03:25:19', NULL),
(136, 'News', 'REMOVED', 1, '2017-04-15 03:25:19', '2017-04-15 03:25:19', NULL),
(137, 'Category/Sub-Category', 'ADD', 1, '2017-04-15 06:07:02', '2017-04-15 06:07:02', NULL),
(138, 'Category/Sub-Category', 'ADD', 1, '2017-04-15 06:09:56', '2017-04-15 06:09:56', NULL),
(139, 'Category/Sub-Category', 'EDIT', 1, '2017-04-15 06:18:39', '2017-04-15 06:18:39', NULL),
(140, 'CMS', 'EDIT', 1, '2017-12-20 04:51:44', '2017-12-20 04:51:44', NULL),
(141, 'Admin Users', 'EDIT', 1, '2018-03-03 04:35:12', '2018-03-03 04:35:12', NULL),
(142, 'Payment Settings', 'EDIT', 1, '2018-03-03 05:05:53', '2018-03-03 05:05:53', NULL),
(143, 'Payment Settings', 'EDIT', 1, '2018-03-03 05:06:32', '2018-03-03 05:06:32', NULL),
(144, 'Payment Settings', 'EDIT', 1, '2018-03-03 07:32:05', '2018-03-03 07:32:05', NULL),
(145, 'Payment Settings', 'EDIT', 1, '2018-03-03 07:32:11', '2018-03-03 07:32:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contact_enquiry`
--

CREATE TABLE `contact_enquiry` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8 NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8 NOT NULL,
  `comments` text CHARACTER SET utf8 NOT NULL,
  `is_view` enum('1','0') CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contact_enquiry`
--

INSERT INTO `contact_enquiry` (`id`, `name`, `email`, `phone`, `subject`, `comments`, `is_view`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Prashant', 'prashantp@webwingtechnologies.com', '7897798798', 'my ac is block what i can do?', '', '1', '2017-11-22 01:45:42', '2017-11-22 02:52:56', NULL),
(6, 'Prashant Patil', 'prashantp@webwingtechnologies.com', '7878978798', 'Test', '', '1', '2017-11-22 03:52:12', '2017-11-22 03:52:57', NULL),
(8, 'Prashant', 'prashantp@webwingtechnologies.com', '787977', 'test faq', '', '0', '2017-11-22 05:59:14', '2017-11-22 05:59:14', NULL),
(9, 'suraj', 'surajs@webwingtechnologies.com', '78899877', 'test faq', '', '0', '2017-11-22 06:00:09', '2017-11-22 06:00:09', NULL),
(10, 'fgh', 'prashantp@webwingtechnologies.com', '35434543', 'fdgfg', '', '0', '2017-11-22 06:01:29', '2017-11-22 06:01:29', NULL),
(11, 'suraj', 'surajs@webwingtechnologies.com', '78989877987987', 'Why this is question. ', '', '0', '2017-11-23 01:28:07', '2017-11-23 01:28:07', NULL),
(12, 'dfgd', 'fgh@dfg.dfgq', '345353535435435353', '353453', '', '0', '2017-11-23 01:30:57', '2017-11-23 01:30:57', NULL),
(13, '543', 'testManufactureer@gmail.com', '35434543', 'etret', '', '0', '2017-11-23 01:31:27', '2017-11-23 01:31:27', NULL),
(14, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:32:13', '2017-11-23 01:32:13', NULL),
(15, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:32:42', '2017-11-23 01:32:42', NULL),
(16, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:33:16', '2017-11-23 01:33:16', NULL),
(17, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:34:59', '2017-11-23 01:34:59', NULL),
(18, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:35:10', '2017-11-23 01:35:10', NULL),
(19, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:35:33', '2017-11-23 01:35:33', NULL),
(20, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:35:39', '2017-11-23 01:35:39', NULL),
(21, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:35:51', '2017-11-23 01:35:51', NULL),
(22, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:36:01', '2017-11-23 01:36:01', NULL),
(23, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:36:47', '2017-11-23 01:36:47', NULL),
(24, 'sfsf', 'sf@sf.sdf', '23423424324234234', '242423424', '', '0', '2017-11-23 01:36:53', '2017-11-23 01:36:53', NULL),
(25, 'Prashant Patil', 'prashant@gmail.com', '78988', 'test', '', '0', '2017-11-23 01:58:09', '2017-11-23 01:58:09', NULL),
(26, 'Prashant Patil', 'prashantp@webwingtechnologies.com', '789789779', 'test', '', '0', '2017-11-23 01:59:30', '2017-11-23 01:59:30', NULL),
(27, 'test', 'prashantp@webwingtechnologies.com', '8596987456', 'test', '', '0', '2017-11-23 02:54:09', '2017-11-23 02:54:09', NULL),
(28, 'Test manufactureer', 'testManufactureer@gmail.com', '8596987456', 'sfdfsf', '', '0', '2017-11-23 02:55:16', '2017-11-23 02:55:16', NULL),
(29, 'Test', 'prashantp@webwingtechnologies.com', '353453', 'dgfdfg', '', '0', '2017-11-23 03:03:58', '2017-11-23 03:03:58', NULL),
(30, 'Prashant', 'prashantp@webwingtechnologies.com', '877898898', 'test', '', '0', '2017-11-23 03:05:50', '2017-11-23 03:05:50', NULL),
(31, 'Suraj', 'surajs@webwingtechnologies.com', '78998797', 'test', '', '0', '2017-11-23 03:07:00', '2017-11-23 03:07:00', NULL),
(32, 'Test manufactureer', 'testManufactureer@gmail.com', '8596987456', 'rtetet', '', '0', '2017-11-23 03:12:21', '2017-11-23 03:12:21', NULL),
(33, 'Test manufactureer', 'testManufactureer@gmail.com', '8596987456', 'fsdfsdf', '', '0', '2017-11-23 03:12:43', '2017-11-23 03:12:43', NULL),
(34, 'retailer Test', 'retailerTest@gmail.com', '8888899999', 'fghfghg', '', '0', '2017-11-23 03:13:54', '2017-11-23 03:13:54', NULL),
(35, 'dgd', 'dfg@dsfdsf.dsfsdf', '46456', 'dsgfdfgfdg', '', '0', '2017-11-23 03:15:32', '2017-11-23 03:15:32', NULL),
(37, 'dfgf', 'dfg@dsfdsf.dsfsdf', '464564', 'dcbgdf', '', '0', '2017-11-23 03:16:49', '2017-11-23 03:16:49', NULL),
(39, 'Prashant', 'prashantp@webwingtechnologies.com', '7879897897', 'test faq', '', '1', '2017-11-29 03:43:15', '2018-03-03 07:14:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_template`
--

CREATE TABLE `email_template` (
  `id` int(11) NOT NULL,
  `template_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template_from` varchar(255) CHARACTER SET utf8 NOT NULL,
  `template_from_mail` varchar(255) CHARACTER SET utf8 NOT NULL,
  `template_variables` varchar(500) CHARACTER SET utf8 NOT NULL DEFAULT 'NA' COMMENT '~ Separated',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `email_template`
--

INSERT INTO `email_template` (`id`, `template_name`, `template_from`, `template_from_mail`, `template_variables`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Admin Forget Password', 'SUPER-ADMIN', 'info@911express.com', '##REMINDER_URL##~##PROJECT_NAME##', NULL, '2017-04-14 07:09:21', '2017-12-20 01:24:53'),
(2, 'Contact Enquiry Reply', 'SUPER-ADMIN', 'info@911express.com', '##EMAIL_DATA##', NULL, '2017-04-15 06:14:55', '2017-12-30 01:32:32'),
(3, 'Admin Staff Add', 'SUPER-ADMIN', 'info@911express.com', '##FIRST_NAME##~##LAST_NAME##~##EMAIL##~##PASSWORD##~##PROJECT_NAME##~##ADMIN_LOGIN_URL##', NULL, '2017-04-17 23:55:25', '2017-12-20 01:25:51'),
(4, 'User Account Verification', 'SUPER-ADMIN', 'info@911express.com', '##NAME##~##PROJECT_NAME##~##ACTIVATION_URL##', NULL, '2017-04-21 00:28:06', '2017-12-20 01:26:15'),
(5, 'User Forget Password', 'SUPER-ADMIN', 'info@911express.com', '##FIRST_NAME##~##PROJECT_NAME##~##REMINDER_URL##', NULL, '2017-04-21 03:17:19', '2017-12-20 01:26:36'),
(6, 'Admin Super Rider Add', 'SUPER-ADMIN', 'info@911express.com', '##PROJECT_NAME##~##FIRST_NAME##~##LAST_NAME##~##EMAIL##~##PASSWORD##', NULL, '2017-12-21 06:08:43', '2017-12-21 06:08:43'),
(7, 'Admin Normal Rider Add', 'SUPER-ADMIN', 'info@911express.com', '##FIRST_NAME##~##PROJECT_NAME##~##LAST_NAME##~##EMAIL##~##PASSWORD##', NULL, '2017-12-22 00:10:02', '2017-12-22 00:10:02'),
(8, 'Admin Driver Add', 'SUPER-ADMIN', 'info@911express.com', '##FIRST_NAME##~##FIRST_NAME##~##EMAIL##~##PROJECT_NAME##~##PASSWORD##', NULL, '2017-12-22 01:31:51', '2017-12-22 01:31:51'),
(9, 'Admin Sub-Admin Add', 'SUPER-ADMIN', 'info@911express.com', '##FIRST_NAME##~##PROJECT_NAME##~##LAST_NAME##~##EMAIL##~##PASSWORD##', NULL, '2017-12-24 23:33:27', '2017-12-24 23:33:27'),
(10, 'Rider Registration', 'SUPER-ADMIN', 'info@911express.com', '##FIRST_NAME##~##PROJECT_NAME##~##LAST_NAME##~##EMAIL##~##PASSWORD##', NULL, '2017-12-25 03:13:47', '2017-12-25 03:13:47'),
(11, 'Driver Registration', 'SUPER-ADMIN', 'info@911express.com', '##FIRST_NAME##~##PROJECT_NAME##~##LAST_NAME##~##EMAIL##~##PASSWORD##', NULL, '2017-12-25 03:29:40', '2017-12-25 03:29:40'),
(12, 'driver receipt not deposited from three days', 'SUPER-ADMIN', 'info@911express.com', '####FIRST_NAME####~####LAST_NAME####', NULL, '2018-01-02 00:13:38', '2018-01-02 00:13:38'),
(13, 'Add Family Member', 'SUPER-ADMIN', 'info@911express.com', '####LINK####', NULL, '2018-01-06 04:41:31', '2018-01-06 04:42:35'),
(14, 'Forget Password', 'SUPER-ADMIN', 'info@911express.com', '####FIRST_NAME####~####OTP####', NULL, '2018-01-06 05:06:57', '2018-01-06 05:36:43');

-- --------------------------------------------------------

--
-- Table structure for table `email_template_translation`
--

CREATE TABLE `email_template_translation` (
  `id` int(11) NOT NULL,
  `email_template_id` int(11) NOT NULL,
  `template_subject` text COLLATE utf8_unicode_ci NOT NULL,
  `template_html` text CHARACTER SET utf8 NOT NULL,
  `locale` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `email_template_translation`
--

INSERT INTO `email_template_translation` (`id`, `email_template_id`, `template_subject`, `template_html`, `locale`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, '911-Express : Admin Reset Password', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">Admin </span></p>\n<p>You recently requested for reset your password for ##PROJECT_NAME## admin account. Click below button to reset it.</p>\n<p>##REMINDER_URL##&nbsp;</p>', 'en', NULL, '2017-04-14 07:09:21', '2017-12-20 01:24:53'),
(2, 2, '911-Express : Contact Enquiry Reply', '<p>##EMAIL_DATA##</p>', 'en', NULL, '2017-04-15 06:14:55', '2017-12-30 01:32:32'),
(3, 3, '911-Express : Promotion Of 911-Express Staff', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! You have been added as \"Photoshoot Subadmin \" on ##PROJECT_NAME## .</p>\r\n<p>Login details:</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##FIRST_NAME## ##LAST_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Password : &nbsp;##PASSWORD##&nbsp;</p>\r\n<p>To login your account please click the below link,</p>\r\n<p><a>##ADMIN_LOGIN_URL##</a>&nbsp;&nbsp;</p>', 'en', NULL, '2017-04-17 23:55:25', '2017-12-20 01:25:51'),
(4, 4, '911-Express : Account Verification', '<p>Hello&nbsp;<span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##NAME##</span></p>\r\n<p>Welcome to&nbsp;&nbsp;<strong>##PROJECT_NAME##</strong>.&nbsp;To complete your account setup, please verify your email address by clicking below button.</p>\r\n<p>##ACTIVATION_URL##&nbsp;</p>', 'en', NULL, '2017-04-21 00:28:06', '2017-12-20 01:26:15'),
(5, 5, '911-Express : Reset Password', '<p>Hello&nbsp;<span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##</span></p>\r\n<p>You recently requested for reset your password for ##PROJECT_NAME## account. Click below button to reset it.</p>\r\n<p>##REMINDER_URL##&nbsp;</p>', 'en', NULL, '2017-04-21 03:17:20', '2017-12-20 01:26:36'),
(6, 6, '911-Express : Super Rider Add', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! You have been added as \"911-Express Super Rider\" on ##PROJECT_NAME## .</p>\r\n<p>Login details:</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##FIRST_NAME## ##LAST_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Password : &nbsp;##PASSWORD##&nbsp;</p>', 'en', NULL, '2017-12-21 06:08:44', '2017-12-21 06:08:44'),
(7, 7, '911-Express : Normal Rider Add', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! You have been added as \"911-Express Rider\" on ##PROJECT_NAME## .</p>\r\n<p>Login details:</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##FIRST_NAME## ##LAST_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Password : &nbsp;##PASSWORD##&nbsp;</p>', 'en', NULL, '2017-12-22 00:10:02', '2017-12-22 00:10:02'),
(8, 8, '911-Express : Driver Add', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! You have been added as \"911-Express Driver\" on ##PROJECT_NAME## .</p>\r\n<p>Login details:</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##FIRST_NAME## ##LAST_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Password : &nbsp;##PASSWORD##&nbsp;</p>', 'en', NULL, '2017-12-22 01:31:51', '2017-12-22 01:31:51'),
(9, 9, '911-Express : Sub-Admin Add', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! You have been added as \"911-Express Sub-Admin\" on ##PROJECT_NAME## .</p>\r\n<p>Login details:</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##FIRST_NAME## ##LAST_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Password : &nbsp;##PASSWORD##&nbsp;</p>', 'en', NULL, '2017-12-24 23:33:27', '2017-12-24 23:33:27'),
(10, 10, '911-Express : Rider Registration', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! You have been register as \"911-Express Rider\" on ##PROJECT_NAME## .</p>\r\n<p>Following are Login details:</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##FIRST_NAME## ##LAST_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Password : &nbsp;##PASSWORD##&nbsp;</p>', 'en', NULL, '2017-12-25 03:13:47', '2017-12-25 03:13:47'),
(11, 11, '911-Express : Driver Registration', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! You have been register as \"911-Express Driver\" on ##PROJECT_NAME## .</p>\r\n<p>Following are Login details:</p>\r\n<p>Name &nbsp; &nbsp; &nbsp; &nbsp;: ##FIRST_NAME## ##LAST_NAME##</p>\r\n<p>Email &nbsp; &nbsp; &nbsp; &nbsp;: ##EMAIL##</p>\r\n<p>Password : &nbsp;##PASSWORD##&nbsp;</p>', 'en', NULL, '2017-12-25 03:29:40', '2017-12-25 03:29:40'),
(12, 12, 'Receipt and amount not deposited', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME## ##LAST_NAME##,</span></p>\r\n<p>&nbsp; &nbsp; You have not deposited the receipt and amount&nbsp; from three days, so deposit it as soon as possible.</p>\r\n<p>Thank you,</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>', 'en', NULL, '2018-01-02 00:13:38', '2018-01-02 00:13:38'),
(13, 13, '911-Express : You\'re invited by your family member', '<p>Hello <span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Congratulations ! You are invited by your family member. Hurry up for registration and take pleasant experience of our 911-express app.</p>\r\n<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;##LINK##&nbsp;</p>\r\n<p>Thank you,</p>\r\n<p>911-express Admin.</p>', 'en', NULL, '2018-01-06 04:41:31', '2018-01-06 04:41:31'),
(14, 14, '911 express - Forget Password', '<p>Hello &nbsp;<span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##FIRST_NAME##</span><span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">,&nbsp;</span></p>\r\n<p>&nbsp; &nbsp; Use this OTP to change your password. Have a nice day!</p>\r\n<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span style=\"color: #034a7c; font-family: \'Latomedium\',sans-serif;\">##OTP##</span></p>\r\n<p>&nbsp;</p>\r\n<p>Thank you,</p>\r\n<p>911-express Admin.</p>', 'en', NULL, '2018-01-06 05:06:57', '2018-01-06 05:36:43');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `is_active` enum('0','1') CHARACTER SET utf8 NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Soft Delete AND Active/Block Maintained';

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(7, '0', '2016-10-14 05:46:17', '2017-03-15 12:45:12', NULL),
(8, '1', '2016-10-14 05:47:11', '2016-10-14 05:47:11', NULL),
(9, '1', '2016-10-14 05:47:50', '2016-10-14 05:47:50', NULL),
(10, '1', '2016-10-14 05:48:46', '2016-10-14 05:48:46', NULL),
(11, '1', '2016-10-14 05:49:24', '2016-10-14 05:49:24', NULL),
(12, '1', '2016-10-14 05:50:10', '2016-10-14 05:50:10', NULL),
(13, '1', '2016-10-14 05:50:53', '2016-10-14 05:50:53', NULL),
(14, '1', '2016-10-14 05:51:31', '2017-11-14 04:06:50', NULL),
(15, '1', '2016-10-14 05:52:15', '2017-11-14 04:06:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faq_translation`
--

CREATE TABLE `faq_translation` (
  `id` int(11) NOT NULL,
  `faq_id` int(11) NOT NULL,
  `question` varchar(500) CHARACTER SET utf8 NOT NULL,
  `answer` mediumtext CHARACTER SET utf8 NOT NULL,
  `locale` varchar(10) CHARACTER SET utf8 NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='No Soft Delete OR Active/Block Maintained';

--
-- Dumping data for table `faq_translation`
--

INSERT INTO `faq_translation` (`id`, `faq_id`, `question`, `answer`, `locale`, `created_at`, `updated_at`) VALUES
(7, 6, 'Is account registration required?', '<p>Account registration at <strong>PrepBootstrap</strong> is only required if you will be selling or buying themes. This ensures a valid communication channel for all parties involved in any transactions.</p>', 'en', '2016-10-14 05:43:44', '2016-10-14 05:43:44'),
(8, 7, 'Can I submit my own Bootstrap templates or themes?', '<p>A lot of the content of the site has been submitted by the community. Whether it is a commercial element/template/theme <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; or a free one, you are encouraged to contribute. All credits are published along with the resources</p>', 'en', '2016-10-14 05:46:17', '2017-11-15 04:53:07'),
(9, 8, 'What is the currency used for all transactions?', '<p>All prices for themes, templates and other items, including each seller\'s or buyer\'s account balance are in <strong>USD</strong></p>', 'en', '2016-10-14 05:47:11', '2016-10-14 05:47:11'),
(10, 9, 'Who cen sell items?', '<p>Any registed user, who presents a work, which is genuine and appealing, can post it on <strong>PrepBootstrap</strong>.</p>', 'en', '2016-10-14 05:47:50', '2016-10-14 05:47:50'),
(11, 10, 'I want to sell my items - what are the steps?', '<p>The steps involved in this process are really simple. All you need to do is:</p>\r\n<ul>\r\n<li>Register an account</li>\r\n<li>Activate your account</li>\r\n<li>Go to the <strong>Themes</strong> section and upload your theme</li>\r\n<li>The next step is the approval step, which usually takes about 72 hours.</li>\r\n</ul>', 'en', '2016-10-14 05:48:46', '2016-10-14 05:48:46'),
(12, 11, 'How much do I get from each sale?', '<p>Here, at <strong>PrepBootstrap</strong>, we offer a great, 70% rate for each seller, regardless of any restrictions, such as volume, date of entry, etc. </p>', 'en', '2016-10-14 05:49:24', '2016-10-14 05:49:24'),
(13, 12, 'Why sell my items here?', '<p>There are a number of reasons why you should join us:</p>\r\n<ul>\r\n<li>A great 70% flat rate for your items.</li>\r\n<li>Fast response/approval times. Many sites take weeks to process a theme or template. And if it gets rejected, there is another iteration. We have aliminated this, and made the process very fast. It only takes up to 72 hours for a template/theme to get reviewed.</li>\r\n<li>We are not an exclusive marketplace. This means that you can sell your items on <strong>PrepBootstrap</strong>, as well as on any other marketplate, and thus increase your earning potential.</li>\r\n</ul>', 'en', '2016-10-14 05:50:10', '2016-10-14 05:50:10'),
(14, 13, 'What are the payment options?', '<p>The best way to transfer funds is via Paypal. This secure platform ensures timely payments and a secure environment.</p>', 'en', '2016-10-14 05:50:53', '2016-10-14 05:50:53'),
(15, 14, 'When do I get paid?', '<p>Our standard payment plan provides for monthly payments. At the end of each month, all accumulated funds are transfered to your account. The minimum amount of your balance should be at least 70 USD.</p>', 'en', '2016-10-14 05:51:31', '2016-10-14 05:51:31'),
(16, 15, 'I want to buy a theme - what are the steps?', '<p>Buying a theme on <strong>PrepBootstrap</strong> is really simple. Each theme has a live preview. Once you have selected a theme or template, which is to your liking, you can quickly and securely pay via Paypal. <br /> Once the transaction is complete, you gain full access to the purchased product.</p>', 'en', '2016-10-14 05:52:15', '2016-10-14 05:52:15'),
(17, 16, 'Is this the latest version of an item', '<p>Each item in <strong>PrepBootstrap</strong> is maintained to its latest version. This ensures its smooth operation.</p>', 'en', '2016-10-14 05:53:00', '2016-10-14 05:53:00'),
(18, 17, 'Test..', '<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum..</p>', 'en', '2016-11-03 22:43:26', '2017-11-14 04:04:12'),
(19, 18, 'nbnmbnm', '<p>bnmbnmbnm</p>', 'en', '2017-01-24 03:49:44', '2017-04-19 07:00:47'),
(20, 7, 'هل يمكنني إرسال قوالب أو موضوعات بوتستراب الخاصة بي؟', '<p>م تقديم الكثير من محتوى الموقع من قبل المجتمع. سواء كان عنصرا تجاريا / قالب /موضوع<br />أو واحدة مجانا، يتم تشجيع لك للمساهمة. يتم نشر جميع الاعتمادات جنبا إلى جنب مع الموارد.</p>', 'ar', '2017-04-06 05:18:52', '2017-04-07 11:17:03'),
(21, 19, 'fghgfh', '<p>hfghfhfh</p>', 'en', '2017-04-19 07:01:32', '2017-04-19 07:01:32'),
(22, 20, 'hjghj', '<p>ghjghjghjhgfhf</p>', 'en', '2017-04-19 07:03:38', '2017-04-19 07:03:38'),
(23, 21, 'bvbcv', '<p>vnvbnv</p>', 'en', '2017-04-19 07:04:21', '2017-04-19 07:04:21');

-- --------------------------------------------------------

--
-- Table structure for table `keyword_translations`
--

CREATE TABLE `keyword_translations` (
  `id` int(11) NOT NULL,
  `keyword` varchar(256) NOT NULL,
  `title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `keyword_translations`
--

INSERT INTO `keyword_translations` (`id`, `keyword`, `title`, `locale`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'dashboard', 'Dashboard', 'en', '2016-12-10 04:33:19', '2017-01-21 00:16:59', NULL),
(2, 'dashboard', 'डैशबोर्ड', 'hi', '2016-12-10 04:33:19', '2017-01-21 00:17:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `title`, `locale`, `status`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 1, '2016-02-06 15:47:35', '2016-02-03 03:22:23'),
(2, 'Deutsch', 'de', 0, '2016-02-06 15:47:35', '2016-02-17 00:10:19'),
(3, 'Italiano', 'it', 0, '2016-02-06 15:47:35', '2016-02-19 03:14:03'),
(4, 'Fran&ccedil;ais', 'fr', 0, '2016-02-06 15:47:35', '2016-02-10 08:15:21'),
(5, 'Espa&ntilde;ol', 'eo', 0, '2016-02-06 15:47:35', '2016-02-10 08:15:22'),
(6, 'Portugu&ecirc;s (Brasil)', 'pt-BR', 0, '2016-02-06 15:47:35', '2016-02-04 08:10:24'),
(7, 'Croatian', 'hr', 0, '2016-02-06 15:47:35', '2016-02-04 08:10:25'),
(8, 'Nederlands', 'nl-NL', 0, '2016-02-06 15:47:35', '2016-02-03 07:22:19'),
(9, 'Norsk', 'nn-NO', 0, '2016-02-06 15:47:35', '2016-02-03 05:35:21'),
(10, 'Svenska', 'sv-SE', 0, '2016-02-06 15:47:35', '2016-02-03 05:35:22'),
(11, 'Hindi', 'hi', 0, '2016-12-10 04:52:25', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `language_phrases`
--

CREATE TABLE `language_phrases` (
  `id` int(11) NOT NULL,
  `phrase` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(3) CHARACTER SET utf8 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `language_phrases`
--

INSERT INTO `language_phrases` (`id`, `phrase`, `content`, `locale`, `created_at`, `updated_at`) VALUES
(1, 'test', 'test', 'en', '2016-11-10 13:04:13', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_07_02_230147_migration_cartalyst_sentinel', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2014_07_02_230147_migration_cartalyst_sentinel', 1),
('2014_10_12_100000_create_password_resets_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_active` enum('1','0') NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `title`, `slug`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Change Password', 'change_password', '1', '2017-04-18 09:11:59', NULL, NULL),
(2, 'Account Settings', 'account_settings', '1', '2017-04-18 09:12:51', NULL, NULL),
(3, 'Site Setting', 'site_settings', '1', '2017-04-18 09:13:22', NULL, NULL),
(5, 'Contact Enquiry', 'contact_enquiry', '1', '2017-04-18 09:14:48', NULL, NULL),
(6, 'Static Pages', 'static_pages', '1', '2017-04-18 09:14:48', NULL, NULL),
(7, 'Email Template', 'email_template', '1', '2017-04-18 09:16:17', NULL, NULL),
(18, 'Admin Commission', 'admin_commission', '1', '2018-01-01 07:11:44', NULL, NULL),
(19, 'Sub Admin', 'sub_admin', '1', '2018-01-01 08:40:38', NULL, NULL),
(23, 'Reports', 'reports', '1', '2018-02-23 04:47:46', NULL, NULL),
(24, 'faq', 'faq', '1', '2018-03-03 09:29:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Fk - User table',
  `is_read` enum('1','0') NOT NULL DEFAULT '0' COMMENT '1 - Read, 0 - Un-Read',
  `is_show` enum('1','0') NOT NULL DEFAULT '0' COMMENT '1 - notification show, 0 - notification not show',
  `user_type` enum('ADMIN','SUB_ADMIN','USER') NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'Notification Title',
  `view_url` varchar(500) NOT NULL COMMENT 'Notification View Details Url',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `is_read`, `is_show`, `user_type`, `notification_type`, `title`, `view_url`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '0', '0', 'ADMIN', 'Sub Admin Registration', 'Registration', 'admin_users', '2018-03-03 12:26:16', '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_setting`
--

CREATE TABLE `payment_setting` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `enable_wire_transfer` enum('0','1') NOT NULL COMMENT '0 = Disable 1 = Enable',
  `beneficiary_bank_name` varchar(255) NOT NULL,
  `beneficiary_bank_address` mediumtext NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `swift_address` mediumtext NOT NULL,
  `bank_code` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `enable_paypal` enum('0','1') NOT NULL COMMENT '0 = Disable 1 = Enable',
  `transfer_sort_order_of_display` varchar(255) NOT NULL,
  `paypal_sort_order_of_display` varchar(255) NOT NULL,
  `cheque_sort_order_of_display` varchar(255) NOT NULL,
  `email` varchar(500) NOT NULL,
  `debug_email` varchar(500) NOT NULL,
  `mid` varchar(255) NOT NULL,
  `merchant_key` varchar(255) NOT NULL,
  `enable_cheque_transfer` enum('0','1') NOT NULL,
  `cheque_payee_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment_setting`
--

INSERT INTO `payment_setting` (`id`, `user_id`, `enable_wire_transfer`, `beneficiary_bank_name`, `beneficiary_bank_address`, `account_name`, `account_number`, `swift_address`, `bank_code`, `comment`, `enable_paypal`, `transfer_sort_order_of_display`, `paypal_sort_order_of_display`, `cheque_sort_order_of_display`, `email`, `debug_email`, `mid`, `merchant_key`, `enable_cheque_transfer`, `cheque_payee_name`, `created_at`, `updated_at`) VALUES
(1, 1, '1', 'sdfhskdhdsjkfhksdhf', 'nashik road nashik1', 'Webwing', '78787878781', 'nashik1', '787871', '', '1', '1', '2', '3', '1prashantp@webwingtechnologies.com', '1prashant@gmail.com', 'MID123456', 'fjkdg78ry975846ijflkjr', '0', 'Pooja Kothawade', '2017-11-13 10:07:50', '2018-03-03 07:32:11');

-- --------------------------------------------------------

--
-- Table structure for table `persistences`
--

CREATE TABLE `persistences` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `persistences`
--

INSERT INTO `persistences` (`id`, `user_id`, `code`, `created_at`, `updated_at`) VALUES
(3, 1, 'YWEhdzyHy17o28NxgIwPocgKAZdsWR6m', '2016-04-25 09:11:50', '2016-04-25 09:11:50'),
(4, 1, 'ohjZeLW0IJcBzKoSOsVYYPq4pNGLwNW9', '2016-04-25 23:31:27', '2016-04-25 23:31:27'),
(5, 1, 'Kv0vgkcewQYBKGNTrSdKKaMaP2srjqL2', '2016-04-26 00:48:34', '2016-04-26 00:48:34'),
(6, 1, 'BtZFQI6mqYLkBds3viVMWxYFa6ntpjiA', '2016-04-27 06:13:48', '2016-04-27 06:13:48'),
(7, 1, 'kO6IY5kueVHDeCwiaByXUEurIryxIIsZ', '2016-04-30 04:40:09', '2016-04-30 04:40:09'),
(8, 1, 'ODbVCEd5GlK0x1t5YfYGijzITInKeB1w', '2016-04-30 07:01:36', '2016-04-30 07:01:36'),
(9, 1, 'lTdCjvFHEA0fQW27RKpfnL4KzKxNqgDM', '2016-05-01 22:38:20', '2016-05-01 22:38:20'),
(10, 1, 'Toevdm6KNeIiX164iMcyQwYjLmUxf3WX', '2016-05-02 02:55:21', '2016-05-02 02:55:21'),
(11, 1, '2tKCFPQxNKIcnvLNk1FNI8GqjRQtyTY6', '2016-05-02 04:26:53', '2016-05-02 04:26:53'),
(12, 1, 'ph9qUZpA2yWLbfkWXn4VLQ3MBvggZB5g', '2016-05-03 22:51:32', '2016-05-03 22:51:32'),
(13, 1, 'dzq5xfSAGsqhfyJzDXiPzqxCnBFvtrpf', '2016-05-04 22:20:59', '2016-05-04 22:20:59'),
(14, 1, 'KSUtIgDd1r3ut2eg43i0eZYoaMhPJQti', '2016-05-04 23:43:56', '2016-05-04 23:43:56'),
(15, 1, 'tTZF8nLGA4le3uSOIlKHkGTtPLVsxtSz', '2016-05-05 22:26:49', '2016-05-05 22:26:49'),
(17, 1, 'qW7plMaYUYLt53fXOJ40ffdyZVkUjitu', '2016-05-06 00:06:40', '2016-05-06 00:06:40'),
(18, 1, 'xWyvBYTdhyMzsnu3doHgjCFdMEX2Aqvq', '2016-05-06 03:09:00', '2016-05-06 03:09:00'),
(19, 1, 'e2MkxKOD1S14l1BCXva9KgIEoAIewQcC', '2016-05-06 03:18:34', '2016-05-06 03:18:34'),
(20, 1, '5VwxmQJG8HjVbf9RRxG8RtnEH7xpLLsB', '2016-05-06 03:53:32', '2016-05-06 03:53:32'),
(23, 1, 'l4RTliZuRS2bdHEFmiXkpxH1QxTDH196', '2016-05-06 05:34:10', '2016-05-06 05:34:10'),
(25, 1, 'qW2GgENX14F4CXdjRF6SSj0eL9ANNzTo', '2016-05-06 08:17:20', '2016-05-06 08:17:20'),
(26, 1, 'XGRFKQI0wGijkuNFMnaPhI296WVDhnIc', '2016-05-06 08:51:48', '2016-05-06 08:51:48'),
(29, 1, 'QuqQ4OGPVbmCO3M64DLwr8bP8j1UmpKm', '2016-05-06 09:02:00', '2016-05-06 09:02:00'),
(30, 1, 'yBeF82eCEgP9TpBxaiU4xvJnmIQkK1bk', '2016-05-06 09:42:55', '2016-05-06 09:42:55'),
(31, 1, 'u6lsmMYSUmpHzDASlYHn0sWlRw2A9lou', '2016-05-19 08:47:49', '2016-05-19 08:47:49'),
(32, 1, 'Mx3EwspZkZ7gQpYCKIXqcYIXXgMkJsSo', '2016-05-21 04:08:02', '2016-05-21 04:08:02'),
(33, 1, 'sN6jdZX71x8okxAcUnBXq6wBBkGeqspg', '2016-05-22 22:52:22', '2016-05-22 22:52:22'),
(34, 1, 'Xuj6I2ah7iJQZM1RAWyGwo45QUaQrdBK', '2016-06-11 06:26:10', '2016-06-11 06:26:10'),
(35, 1, '5b4nIGn89LO6rVDs7zw45krOKhejc6qW', '2016-07-15 00:53:28', '2016-07-15 00:53:28'),
(37, 1, 'N4iQXjTlnci6vSLgWZxYVfdxxuK5haZn', '2016-07-15 01:59:59', '2016-07-15 01:59:59'),
(38, 1, 'Yh2H8G0XzJLsyqgCKUoecqoXpKM4OfEA', '2016-08-06 04:39:56', '2016-08-06 04:39:56'),
(39, 1, 'aTlVcTPqszrlnmDFVNroSJYlqIGg0iz9', '2016-08-16 07:35:36', '2016-08-16 07:35:36'),
(40, 1, 'bi5UHzlPjARxFGFGDh06OV6bAjxJwt27', '2016-08-16 22:27:44', '2016-08-16 22:27:44'),
(42, 2, 'gdUYcFmBPfmKdQtJ1bvjrPeVtzZD1W9C', '2016-08-17 00:44:16', '2016-08-17 00:44:16'),
(43, 1, 'Hcy4RAVDTI9wRYDqDzXPtCFIXLl6Sv9k', '2016-08-17 01:08:04', '2016-08-17 01:08:04'),
(44, 1, 'X7A1lyoyoh6bT2wyC0f9Mif4nasQPzRX', '2016-08-17 03:43:02', '2016-08-17 03:43:02'),
(45, 2, 'Yf5hU5wX1VjfkdKcp2C1SkZICMAECJVt', '2016-08-20 06:22:56', '2016-08-20 06:22:56'),
(46, 1, 'eselAvWp1pSzx42SZ8AK99shvRGx2U9x', '2016-08-29 03:24:18', '2016-08-29 03:24:18'),
(48, 1, 'mlHBvcAAHdTgRmxIrxCn6Cn7yMHKcoJa', '2016-09-01 03:27:31', '2016-09-01 03:27:31'),
(52, 1, 'iQ6axEtp98AejaWLfACVvzAaHcF6gSgC', '2016-09-01 05:09:17', '2016-09-01 05:09:17'),
(53, 1, 'lGeO68klod1Q7tIZOldKCgtbWrYF4YEX', '2016-09-02 22:26:56', '2016-09-02 22:26:56'),
(54, 1, '6MAmtOfdDbKGjoJdnQY7iETYTpb8gnGR', '2016-09-03 05:07:33', '2016-09-03 05:07:33'),
(55, 1, '9fTeIZjjG5nVMH9PnWBMHp3WzXjx2Ysk', '2016-09-04 23:15:12', '2016-09-04 23:15:12'),
(57, 1, 'Cjpacmqk6ep1G3rk4C0ZWfIJwTTCscIt', '2016-09-06 07:40:25', '2016-09-06 07:40:25'),
(58, 1, 'zcGBqgeiTORN4YvaJXVGvQLGDaPWoBMS', '2016-09-07 01:56:19', '2016-09-07 01:56:19'),
(59, 1, 'ETWChHJZnCXG7q8oG1jrEBt16OQMGAuN', '2016-09-20 22:26:12', '2016-09-20 22:26:12'),
(60, 1, 'MQGg9qur9mxfKDZS6Q901pxiKOGov9ta', '2016-10-03 04:21:48', '2016-10-03 04:21:48'),
(61, 1, 'V4SGvqIP4DUtNO7rNdJMnWdHbojuIWOw', '2016-10-13 05:51:16', '2016-10-13 05:51:16'),
(62, 1, 'fxbR67uUSywpttSrXLMfIb50wXrv74Cm', '2016-10-24 23:05:33', '2016-10-24 23:05:33'),
(63, 1, 'uVWt2FJjD4lZh5QMt8kLYmB4kXvBeO80', '2016-10-26 23:05:55', '2016-10-26 23:05:55'),
(64, 1, 'uju4KRqzNoDfXlOGsoBYyYahEXNm9GCI', '2016-10-27 00:59:44', '2016-10-27 00:59:44'),
(65, 1, '1uVIQF9d1ydqU8ut2PD1NjbeNtvP9Kmq', '2016-10-27 01:58:11', '2016-10-27 01:58:11'),
(66, 1, 'ogq67U5aCKASyr98iS1YW9DFXwYuBMOD', '2016-10-28 00:52:17', '2016-10-28 00:52:17'),
(67, 1, 'NZQ3dl0KSWbscviGQB9uT7nmeHAmeyl1', '2016-11-05 01:01:53', '2016-11-05 01:01:53'),
(68, 1, '74ke7cZi59WdufyPVpSfQv2dQVrqYsfi', '2016-11-05 07:33:19', '2016-11-05 07:33:19'),
(69, 1, 'RQ1iseiyqxOxk32nmXKsUibRzlvZc2X4', '2016-11-07 01:11:54', '2016-11-07 01:11:54'),
(70, 1, 'XpkOtotduJF0rSEYdt6ZLtBo60dJNxpn', '2016-11-07 04:02:11', '2016-11-07 04:02:11'),
(71, 1, 'BW4QIoeGhRvxy8aFQpv9RCHbejpqP52o', '2016-11-08 07:49:45', '2016-11-08 07:49:45'),
(72, 1, 'mOhvFEptvooDA84tyKkT7hSlELFpBNHP', '2016-11-09 03:09:34', '2016-11-09 03:09:34'),
(75, 1, '1Ip1gVQpqbtZj1tkyYMRZwMkXfUUYsQp', '2016-11-10 00:28:45', '2016-11-10 00:28:45'),
(78, 1, 'm93X9L9kgbEydlQaWkHgVV9HdQ5UwKOO', '2016-11-10 02:57:00', '2016-11-10 02:57:00'),
(79, 1, 'OwmuqztoAVER1OKlKaaw6ipQPNL1G6YS', '2016-11-10 06:38:49', '2016-11-10 06:38:49'),
(80, 1, '5IBCSv0CL1uyepMSeOnfVhO1LERqw889', '2016-11-10 07:13:48', '2016-11-10 07:13:48'),
(81, 1, 'pHtb3JRNvyVhvpbRYHi3hp0CxhN5mlkN', '2016-11-10 07:33:30', '2016-11-10 07:33:30'),
(82, 1, 'RzIfGyMgMJvaHQR6oUavrUB392pc357j', '2016-11-10 22:30:04', '2016-11-10 22:30:04'),
(83, 1, 'PgfBy5RB94nY8ehVqA0YKA6a7jQx7Adp', '2016-11-10 23:00:12', '2016-11-10 23:00:12'),
(84, 1, 'NVgqzBRutXFBA4COdFWmuz5MgXNGrDNq', '2016-11-22 00:20:23', '2016-11-22 00:20:23'),
(85, 1, 'OuBhFPmnCUbKVFt7XkMPETz1MeoFoRmL', '2016-12-09 07:35:56', '2016-12-09 07:35:56'),
(86, 1, 'KekM39HwLb0xz6gKGDLs8egOSXmPoXh4', '2016-12-09 07:45:58', '2016-12-09 07:45:58'),
(87, 1, 'SfoisGhc2J8UQPkkuc1tAJjBw4Qbw8jq', '2016-12-27 01:05:43', '2016-12-27 01:05:43'),
(88, 1, 'lGOpLz60gkVnzcDLoEPQOxgob8mgLMAv', '2017-01-20 23:30:52', '2017-01-20 23:30:52'),
(89, 1, '6vKi8IVRJLjhKJhAPXwERQk0LKsFDGU4', '2017-01-20 23:34:39', '2017-01-20 23:34:39'),
(90, 1, 'dMh6GUVIZzx06Nx25d70aq4t3HfrpOY6', '2017-01-21 06:53:40', '2017-01-21 06:53:40'),
(91, 1, 'rsp4emdifk4xnHAAcTv560AUDhZqhm0B', '2017-01-22 22:51:05', '2017-01-22 22:51:05'),
(92, 10, 'oGzgqeGFJZuYGB6m443sy4yGvqxayFhg', '2017-01-23 00:01:15', '2017-01-23 00:01:15'),
(93, 1, 'ChizmJZId5VYfp9IsTFMEaB95dHdETP9', '2017-01-23 03:53:01', '2017-01-23 03:53:01'),
(94, 1, 'gIVasba4doIYkiUhw3ywgNWoU0rcDpFD', '2017-01-23 03:58:19', '2017-01-23 03:58:19'),
(95, 1, 'tDTgUZRswTOafqgwRDGUeSZNP7crmm2K', '2017-01-23 05:12:34', '2017-01-23 05:12:34'),
(96, 1, 'g34z5PFP7KpFOQ0wvbzf37naxzbuhd1O', '2017-02-22 00:18:05', '2017-02-22 00:18:05'),
(97, 1, 'R3vIElxuwrQZOjFUG1TxvDxkQlEuGcWy', '2017-02-22 03:04:16', '2017-02-22 03:04:16'),
(98, 1, 'DALessyPVBZtUhtmqPFomOzEleYJXZwy', '2017-02-22 04:57:44', '2017-02-22 04:57:44'),
(99, 1, '2x0fV2Z4V11EnIzIanGUUQyKuFd1xMbz', '2017-02-27 02:52:38', '2017-02-27 02:52:38'),
(101, 1, 'n4jEOW9Og4ulSENp4TIs9S7cjwJVSWLb', '2017-02-27 03:00:27', '2017-02-27 03:00:27'),
(102, 1, 'mM09PHt6oqJJl0aFwKtZNQISGLYYNpiu', '2017-03-13 23:24:26', '2017-03-13 23:24:26'),
(103, 1, '4S6TT3i2onouciszUSieizDDaTMN5Yzu', '2017-03-22 22:41:50', '2017-03-22 22:41:50'),
(104, 1, 'rg5Nqm0Erv26pIbwVj1p0TJcZjM9cwdC', '2017-04-05 22:27:07', '2017-04-05 22:27:07'),
(107, 1, 'eG4NymyFaIujdL5AHmzqGWAltOIwXuyg', '2017-04-14 03:35:37', '2017-04-14 03:35:37'),
(108, 1, '74VaCSQX9wSfk2RDvU6DkRpxuDX0LFj0', '2017-04-14 03:37:57', '2017-04-14 03:37:57'),
(111, 1, 'RCrRUN8TksRBdjH1aZNjqxmj2xtEv5gn', '2017-04-14 07:04:12', '2017-04-14 07:04:12'),
(113, 1, 'LYPzpDfvqfauDVaEB4T6HNeAw0QQuIVB', '2017-04-14 22:26:04', '2017-04-14 22:26:04'),
(117, 1, 'eo2andbL0qFfYiLu0tzGajAmZpnqsByD', '2017-04-14 23:33:30', '2017-04-14 23:33:30'),
(119, 1, '8OboJxyWRfAIAUIk2WauhTmwJ6ugYB0G', '2017-04-15 00:12:35', '2017-04-15 00:12:35'),
(120, 1, 'l99OjEbEHQGeN1GYLz9k6ylDDjjLKkYF', '2017-04-15 00:55:01', '2017-04-15 00:55:01'),
(121, 1, 'FRTCXagg7FHDIPtSyDUNzfhMrAZQwsqh', '2017-04-15 03:17:16', '2017-04-15 03:17:16'),
(122, 1, 'GDWOHXOHrYKmlUymda5QZ3oBVOk4BqYJ', '2017-04-15 04:29:54', '2017-04-15 04:29:54'),
(123, 1, '2G8k2BDfiP9MrRxIHVRUUfPWgbYRtlZQ', '2017-04-15 07:47:45', '2017-04-15 07:47:45'),
(124, 1, 'tdMKDlB8v8ywRKYzXYYLeFBsgOuhmTsX', '2017-04-16 22:23:44', '2017-04-16 22:23:44'),
(125, 1, 'VZ0prwbyDBBE5Vjec0YWyj1BG0Q70nHl', '2017-04-16 22:25:33', '2017-04-16 22:25:33'),
(127, 1, 'EIiqEAghPKsDtqnFx60RcjSSoLf78Tbh', '2017-04-16 23:16:17', '2017-04-16 23:16:17'),
(128, 1, 'vt6CIrobJ4MiYX8cKzot4dlZJhgvxJwm', '2017-04-17 00:27:24', '2017-04-17 00:27:24'),
(129, 1, '4mqMZ2qBqTnW3iVl0q1Z3geqwvNC1BxC', '2017-04-17 00:35:55', '2017-04-17 00:35:55'),
(130, 1, 'AC2g9isOx1CQApQTaCWRGzoE73ePY4n4', '2017-04-17 00:47:29', '2017-04-17 00:47:29'),
(131, 1, 'H5BgAvlwqqzX2TbRo8dIRg1JaD96CZyF', '2017-04-17 02:55:39', '2017-04-17 02:55:39'),
(132, 1, 'tUUMLcULUmpFdBMmqKvv7ewWo7w9lWPm', '2017-04-17 02:55:21', '2017-04-17 02:55:21'),
(133, 1, 'cSvXKytSjQaERYxQYWs5LN6a3h6tBJyL', '2017-04-17 05:03:36', '2017-04-17 05:03:36'),
(134, 1, 'XMsq7juEqDV412eagEX3Lo8rnwngUWbn', '2017-04-17 06:55:14', '2017-04-17 06:55:14'),
(135, 1, 'ptUngX4uxomL719WoG838t6xrnzjc0gf', '2017-04-17 06:55:28', '2017-04-17 06:55:28'),
(136, 1, 'Zz4x0gKNMogbtAsWxcMDYU9i1ozwfwBy', '2017-04-17 06:56:56', '2017-04-17 06:56:56'),
(137, 1, 'p7Ew1I0HlkB0qIs4um8dpKgFN0vNPS4R', '2017-04-17 22:25:14', '2017-04-17 22:25:14'),
(138, 1, 'prIQ6BAHkoC2GlUbbtzuq3VGGd62kiUb', '2017-04-17 22:24:45', '2017-04-17 22:24:45'),
(139, 1, 'b5RyqnR5VdBYeyovlI0jdTXuhGHvXfoV', '2017-04-17 23:18:45', '2017-04-17 23:18:45'),
(141, 1, 'UH52aVvuN9tTeZQ9ETjcJRCCrydO7dnb', '2017-04-17 23:48:37', '2017-04-17 23:48:37'),
(142, 1, 'RtDNnEzLp3tLRQ7j7n3sTciTqoSpeKOa', '2017-04-18 00:40:41', '2017-04-18 00:40:41'),
(143, 10, 'l9iBp8hCylvtp9mfRDJ7xbtWvi1AP2Uy', '2017-04-18 00:47:21', '2017-04-18 00:47:21'),
(145, 12, 'pzLnNRIPt5LeXzLbfqFYr1jkpaPJrJUA', '2017-04-18 01:18:51', '2017-04-18 01:18:51'),
(146, 12, 'pJqqV4OO20gdHVXdoMdDfC4EKJ0dy4dm', '2017-04-18 01:19:06', '2017-04-18 01:19:06'),
(147, 1, '11yi4DmIIEF06ZAepxKVbKzAZJufyrWt', '2017-04-18 01:19:39', '2017-04-18 01:19:39'),
(148, 12, 'nzYcfceZUcID63VA46ZEe806zIAzipkB', '2017-04-18 01:19:48', '2017-04-18 01:19:48'),
(149, 12, 'pwFHNJOm6YO5dyfchwLY8g4Y6SIEy7tL', '2017-04-18 01:20:10', '2017-04-18 01:20:10'),
(150, 12, 'ZrJmeoTDr3i2ttCFPZBffhEn6yTxe8jz', '2017-04-18 01:21:00', '2017-04-18 01:21:00'),
(151, 12, '0GxQJeFIs1O6UZ2FNv52QtgxJVpq982t', '2017-04-18 01:21:12', '2017-04-18 01:21:12'),
(152, 12, 'WloeyXgHgFM3tcJUHTdTkfdzo1erSSPM', '2017-04-18 01:22:10', '2017-04-18 01:22:10'),
(153, 12, 'limQTibfJy0cSIBuq2BRPUkNXDiJ3qtQ', '2017-04-18 01:23:43', '2017-04-18 01:23:43'),
(157, 1, 'liqAMqGqk3juIFlhtJ1p2WyRD8tPawZh', '2017-04-18 01:31:07', '2017-04-18 01:31:07'),
(167, 1, 'xEaV3SzqrR7LUCDyekejRcxn2CcpnkrR', '2017-04-18 01:42:46', '2017-04-18 01:42:46'),
(169, 1, 'Ejl6Gs9DeFrRoNEhhXP5tZACOKWmxygD', '2017-04-18 02:52:10', '2017-04-18 02:52:10'),
(170, 1, 'CJqM4F9NdA8oEdaHNxkE1AOabQ2QAU5m', '2017-04-18 03:14:47', '2017-04-18 03:14:47'),
(172, 1, 'oSZCUALL3Dpw2L7TS0th2o6PnFsrKUhI', '2017-04-18 04:02:04', '2017-04-18 04:02:04'),
(173, 1, 'kpGiEZAY2Yb9CvuXWHDV7C2NvTcy9PHk', '2017-04-18 04:05:27', '2017-04-18 04:05:27'),
(174, 1, 'Prarg3IFgG32gXFIy3gBPTt19QSmoxfX', '2017-04-18 04:08:02', '2017-04-18 04:08:02'),
(175, 1, 'LiH0Sx4hX1Z0VE0AcX1TXKM81uqcPhbL', '2017-04-18 04:23:31', '2017-04-18 04:23:31'),
(178, 1, 'wk2DHA5XnudgekkQsNip120pmyAfXGvG', '2017-04-18 05:17:23', '2017-04-18 05:17:23'),
(179, 1, '7HG1mGDpxenZdL6vQy0Uf8ECyaYsCHd5', '2017-04-18 05:28:15', '2017-04-18 05:28:15'),
(180, 1, 'Y61tmSE6VrBW4bjS5KssLvDqbU8rDeRS', '2017-04-18 05:53:43', '2017-04-18 05:53:43'),
(181, 1, 'dw9fkzmBb5thlflCkBwpnuUD9givckkY', '2017-04-18 06:36:49', '2017-04-18 06:36:49'),
(182, 12, '1CZELmQayYFQYBjTjJi261YcM5RSuIDa', '2017-04-18 06:59:30', '2017-04-18 06:59:30'),
(183, 1, 'qJ9gXvJomoNMCZTisdZ64JWAZaIF1QA7', '2017-04-18 07:00:09', '2017-04-18 07:00:09'),
(184, 1, 'RxyACBx53KyKLngOX3BmCu0xpgDAgvsu', '2017-04-18 07:00:57', '2017-04-18 07:00:57'),
(185, 1, 'u34SQYApdBN1iWKtPUQpm185O1IpdbY2', '2017-04-18 07:11:21', '2017-04-18 07:11:21'),
(186, 1, 'vPtg6tn1fW8Qf2sl93GZprr4LEItFRs8', '2017-04-18 07:24:38', '2017-04-18 07:24:38'),
(187, 1, 'tuebcTCHSQCniuNOzNYSXMXaqsXrF5Ga', '2017-04-18 07:44:37', '2017-04-18 07:44:37'),
(188, 1, 'uLN0UGDTngQdgqgKHMn0a8PaX2rfm1TT', '2017-04-18 07:45:37', '2017-04-18 07:45:37'),
(189, 12, 'XuOve3gSxqJ3qv0AfM8TQPR3CGNYfQmm', '2017-04-18 07:56:03', '2017-04-18 07:56:03'),
(190, 1, 'yDb1smxy6z3VkC6o5AnaY11iphV1SOGd', '2017-04-18 22:25:03', '2017-04-18 22:25:03'),
(192, 1, 'RhJapJkZoHHtYLRjgQvdgJefjfTQkQ6K', '2017-04-18 22:39:12', '2017-04-18 22:39:12'),
(193, 12, 'yWbWtVyZ3YBqcbnLDv5YBaLztY26nBNb', '2017-04-18 22:39:27', '2017-04-18 22:39:27'),
(194, 1, 'wRbPFOpN4fd6x5oQ2E7EVOwI00sno0FW', '2017-04-18 23:13:10', '2017-04-18 23:13:10'),
(195, 1, 'BTyg5JeqEgwjfEq17Gjb1ixMBVLO3NSy', '2017-04-18 23:52:56', '2017-04-18 23:52:56'),
(196, 1, 'zA3YpUFkjUPcO87HsJT9Th2rWPl9icgx', '2017-04-18 23:58:53', '2017-04-18 23:58:53'),
(197, 1, 'NLn39Pvra1ku5V0sewl5xHBb7DZLlPup', '2017-04-18 23:54:51', '2017-04-18 23:54:51'),
(198, 1, 'uRtuHPEm90mgc05aoY1JquyyzZ5sOuUA', '2017-04-19 01:52:03', '2017-04-19 01:52:03'),
(199, 1, 'irDD7pllEGTkBLatLUNum7E3s7qowyB1', '2017-04-19 02:59:15', '2017-04-19 02:59:15'),
(200, 12, 'gha31kQL8cGGpKHFal9uRUZsQbkY1uJi', '2017-04-19 03:09:23', '2017-04-19 03:09:23'),
(201, 1, 'LpqRJbPGRGBDHp5ZX0BN0H7NYAzIfFDR', '2017-04-19 03:42:09', '2017-04-19 03:42:09'),
(203, 1, 'OU0VLS0nfW2v4xbO9UZap7JNJILp8jDp', '2017-04-19 04:06:10', '2017-04-19 04:06:10'),
(205, 1, 'DmvSyxBE43vNtBl6JvgbRqacNYGeBZqP', '2017-04-19 05:15:15', '2017-04-19 05:15:15'),
(206, 1, 'VCBV7UuR9OG3tVvsb9DbS7uxKjEQVfLX', '2017-04-19 06:12:10', '2017-04-19 06:12:10'),
(207, 12, 'xDpVngGCmL3fg3uJoWFUs0yASKSUXpU3', '2017-04-19 06:13:13', '2017-04-19 06:13:13'),
(209, 12, 'ho4Z4ZZFrICiPEhb8B1kbZhrsuZfOCrK', '2017-04-19 06:13:33', '2017-04-19 06:13:33'),
(210, 1, 'Z5GTJBmUNO0Au6hVkGsYyXenK0xkgp4Y', '2017-04-19 06:27:08', '2017-04-19 06:27:08'),
(211, 1, 'HhPfyfTJwe6pumnqOhHWs15AkAaGWv3y', '2017-04-19 07:57:58', '2017-04-19 07:57:58'),
(212, 1, '7hveqJNaNhcSzcwBoJlBazJTnvuwuxnK', '2017-04-19 22:27:51', '2017-04-19 22:27:51'),
(213, 1, 'L53eOXtSxU4MwRlEEuJAaLuVcoohqix8', '2017-04-19 22:40:30', '2017-04-19 22:40:30'),
(214, 1, 'C6DXP04UZ9diQW20DivMXzJhhmHANPXj', '2017-04-19 22:48:58', '2017-04-19 22:48:58'),
(215, 1, 'bnamio2JCsVFUGsPs74LOq5pOK72I4ua', '2017-04-19 22:46:48', '2017-04-19 22:46:48'),
(217, 1, 'lQHRNqFNmaNAfoWcIYBM2keBzf2fV1dE', '2017-04-19 22:49:41', '2017-04-19 22:49:41'),
(219, 1, 'TblK1NR0gX7F7NwfCw22CdGrOjwUdTBT', '2017-04-19 23:44:22', '2017-04-19 23:44:22'),
(220, 12, 'j9sIxgN5DQA6dCtgO3TuiuTyUYw2QOek', '2017-04-19 23:46:33', '2017-04-19 23:46:33'),
(221, 1, 'YhXHH20HUnlWuPJLHGVSZKy71qrXBSmW', '2017-04-19 23:48:13', '2017-04-19 23:48:13'),
(222, 12, 'NJpbIfKUi4TUwH2lOoN9vouc64gFyjrx', '2017-04-19 23:48:36', '2017-04-19 23:48:36'),
(223, 1, '7IS4J7vdP9n1WHDU8R3FusL7sOrijwQE', '2017-04-19 23:50:43', '2017-04-19 23:50:43'),
(224, 1, 'CdWpgnZgL1iFrNazYvRXWpUMgNAlLzdH', '2017-04-20 02:56:06', '2017-04-20 02:56:06'),
(225, 1, 'i31QNzuiJajvArS6OsR1WEYEynoWE2Pf', '2017-04-20 03:39:31', '2017-04-20 03:39:31'),
(226, 1, '0skPK6vUbV7XdDf8UE99xjEOZuqc3zwT', '2017-04-20 03:40:28', '2017-04-20 03:40:28'),
(227, 1, 'xZ1RBI1LzI05k85rQeQwSvi1AF3qqK6X', '2017-04-20 04:44:32', '2017-04-20 04:44:32'),
(228, 1, 'MEoVSlOue1iR8NOcO7eysg9xjhvVJmY6', '2017-04-20 08:17:08', '2017-04-20 08:17:08'),
(229, 1, '6x41I6pS6YnLtQ2KowdcsUUSPmiU0xOz', '2017-04-20 22:37:25', '2017-04-20 22:37:25'),
(230, 1, 'I9M6dSm14P2q4DRZ6rsGix2t73BkQEMT', '2017-04-20 22:36:58', '2017-04-20 22:36:58'),
(231, 1, '8Ql003wWwOYebWptuGh1O1jd8m9sep6r', '2017-04-20 22:39:00', '2017-04-20 22:39:00'),
(233, 1, 'Z9RtreVy308qsjpRfLp09LThLYowT9Jj', '2017-04-20 22:49:43', '2017-04-20 22:49:43'),
(234, 1, 'EnUpCcxKG56aNVyZYmubDodTGr7f4nks', '2017-04-20 22:55:23', '2017-04-20 22:55:23'),
(235, 1, 'MlqG8rNmB1PdW5Zmrfb83xRqDB0UuBP1', '2017-04-20 22:51:35', '2017-04-20 22:51:35'),
(236, 12, 'wufxQ6uCInPc6lXDRK3KCKsMGFTQwNhZ', '2017-04-20 23:20:56', '2017-04-20 23:20:56'),
(237, 1, 'szdFTkpvLEBrSIBESblHY9ZmEe63Pzrb', '2017-04-20 23:44:56', '2017-04-20 23:44:56'),
(239, 1, 'OGnhH28Nbf5trnG1fYO6FdF0tn4KrpLo', '2017-04-21 03:38:18', '2017-04-21 03:38:18'),
(240, 1, 'dtlqhgyDARCL7pKxPsIdx9qKnclxzN7z', '2017-04-21 04:47:23', '2017-04-21 04:47:23'),
(241, 1, 'bv8G6Xz38dfCMhgstKnLHo6rprPY41eL', '2017-04-21 04:51:46', '2017-04-21 04:51:46'),
(242, 1, '9R8NU0s8y3qWstPdroUN9chP1QCiXyff', '2017-04-21 05:23:54', '2017-04-21 05:23:54'),
(243, 1, 'I2raL7PuBIQmz3T95xL4wTk48UoViPxE', '2017-04-21 05:47:22', '2017-04-21 05:47:22'),
(244, 1, 'TZchaPF0N6ifR7BRa6EMqJGnZrfhfcIK', '2017-04-21 07:02:14', '2017-04-21 07:02:14'),
(245, 1, 'm80uhT8DbGP4AeUFLfASBZIP804dZ2un', '2017-11-20 00:22:12', '2017-11-20 00:22:12'),
(246, 1, '1fPQN23h272FJ4zuTK5VWRo7QHztRBKc', '2017-12-20 00:10:00', '2017-12-20 00:10:00'),
(248, 1, 'B7XMZdv8FQqegM5BX4Gmik203X4FmKHM', '2017-12-20 01:27:01', '2017-12-20 01:27:01'),
(249, 1, 'RbD0oHysPGD7geJMJEWPeAinmYkETIg0', '2017-12-20 22:24:11', '2017-12-20 22:24:11'),
(251, 1, 'erJmivlodpEmaQi3fCLssPuqM0aFcaVW', '2017-12-21 00:53:59', '2017-12-21 00:53:59'),
(253, 1, 'UlRvckVH458blhFwzHPfKWq2dsmfgawo', '2017-12-21 01:16:21', '2017-12-21 01:16:21'),
(254, 1, '4QRwR9PE9M39RTSJ6yySEvwVlk7pYje6', '2017-12-21 02:52:03', '2017-12-21 02:52:03'),
(255, 1, 'VxFiiMekCkBusY3nWQippot5Iyr22aD2', '2017-12-21 22:28:46', '2017-12-21 22:28:46'),
(256, 1, 'KSKcGo3zxWccUoUNpqK7bkTgUz9ybUJG', '2017-12-21 22:56:04', '2017-12-21 22:56:04'),
(257, 1, 'EBGwX9jK3CEfHBk5XqchVYxkoqwg5qHf', '2017-12-22 01:37:52', '2017-12-22 01:37:52'),
(258, 1, '0x40YmsDGyrCBmotPdfxhyjMcXVvQDEM', '2017-12-24 22:33:56', '2017-12-24 22:33:56'),
(259, 1, 'K2tytXR4pqgSBO73sZNPnYd443Wtiznm', '2017-12-24 22:34:30', '2017-12-24 22:34:30'),
(260, 1, 'yd5dhn9L4d1fKSdqtFXmVnXkelCe2GWZ', '2017-12-25 00:26:05', '2017-12-25 00:26:05'),
(262, 1, 'VriyFjA82c1DtYK0oK2YDY0fg6YgU5O5', '2017-12-25 08:20:38', '2017-12-25 08:20:38'),
(263, 1, 'POyGWWckisTCBKrakFqGUKnFiEUVbdr1', '2017-12-25 22:59:11', '2017-12-25 22:59:11'),
(264, 1, '5D0f4ed5PNN2VD2Py7OmDOcnI5UYUqv1', '2017-12-25 23:08:24', '2017-12-25 23:08:24'),
(265, 1, 'HhhH9OS4o3tSjcp5pcKp3InW02L4cS4R', '2017-12-25 23:20:49', '2017-12-25 23:20:49'),
(266, 1, 'WxOGLREaemcJgLTh3F37vUmH0Y7XQTp6', '2017-12-25 23:23:27', '2017-12-25 23:23:27'),
(267, 1, 'mFKeP131OLY4cGPMu2fpd4J9CGLLGSh2', '2017-12-25 23:27:12', '2017-12-25 23:27:12'),
(268, 1, 'yKlZj7esdN5uTpv7Vo94NmrRqmmGCSaY', '2017-12-26 03:41:11', '2017-12-26 03:41:11'),
(269, 1, 'Xc17ukOBPlokC5PIIn9SMJXnaNmPU8fy', '2017-12-26 03:51:53', '2017-12-26 03:51:53'),
(270, 1, 'ikBH8taoqpGi4rFgkJyWU5BOdYEhxIjO', '2017-12-26 04:11:15', '2017-12-26 04:11:15'),
(271, 1, 'xU9necILIBwlob2g6s3b5b3ZbMETdF4G', '2017-12-26 04:24:31', '2017-12-26 04:24:31'),
(273, 1, 'NJnyDOskSITeUtqcvrPms5ju59zcrtqf', '2017-12-26 07:20:02', '2017-12-26 07:20:02'),
(274, 1, 'Mb7i81nr1viThXQdX5vdKjh837WyccEi', '2017-12-26 22:25:52', '2017-12-26 22:25:52'),
(275, 1, 'VrYWYHrXmm8rfG6nDUNktEoFULYLEkAV', '2017-12-26 22:26:06', '2017-12-26 22:26:06'),
(276, 1, 'UHmyBeu38AQYoTDUu6EQQMVGCJyLFsVY', '2017-12-26 22:43:37', '2017-12-26 22:43:37'),
(277, 1, '5g58BnNiOlz91tjTXXuyBBerZjnxV94T', '2017-12-26 23:05:07', '2017-12-26 23:05:07'),
(278, 1, 'DIGVyCAbscLi8keH3QSwOiblDwYxmVdr', '2017-12-26 23:14:46', '2017-12-26 23:14:46'),
(279, 1, 'IwrjZgdMoTxjK2T2EW4K5kZljXMa1pGv', '2017-12-26 23:24:21', '2017-12-26 23:24:21'),
(280, 1, 'Qvl1NGA10dovX1LvikPLRseVpd47v41A', '2017-12-26 23:32:51', '2017-12-26 23:32:51'),
(281, 1, 'aHoc4LEBNUCSZ2LUc3wQpiIlmty0VUM1', '2017-12-26 23:58:11', '2017-12-26 23:58:11'),
(282, 1, 'NeU2TBdMeOiuTYqP3HtOVGFhJPv0QdSe', '2017-12-27 00:49:45', '2017-12-27 00:49:45'),
(283, 1, 'YUX9RNaKl6BKePxynYCBiBBzmzPF3kt2', '2017-12-27 01:41:45', '2017-12-27 01:41:45'),
(285, 1, '51M4y4EDocDnHJUE406eYRHwSIgm62PT', '2017-12-27 02:54:35', '2017-12-27 02:54:35'),
(287, 1, '0rBVfvZWtyOxRgnjwZWR5WbhASkJJZXg', '2017-12-27 02:57:30', '2017-12-27 02:57:30'),
(289, 28, 'fw6XnCiBqHbyKza2mIcrxeIbw2B88tHv', '2017-12-27 03:05:03', '2017-12-27 03:05:03'),
(290, 1, 'LfGEnFT0uueQ5Myns88HsvjFhCVqSXuO', '2017-12-27 03:14:51', '2017-12-27 03:14:51'),
(292, 1, 'e170Q0gjijnvMQ4eiyGxeviFzTDl359F', '2017-12-27 03:35:34', '2017-12-27 03:35:34'),
(293, 1, 'X5mHXu5zLE53Vz2dX7pifxgmajbGsIgt', '2017-12-27 04:13:14', '2017-12-27 04:13:14'),
(294, 25, 'xyRDKHQHqnhjbob5AqM7i5rSl9obbVp7', '2017-12-27 06:15:46', '2017-12-27 06:15:46'),
(295, 25, 'PMr0LLgUyCsOHRfySsZbXZEi8yX2wgIp', '2017-12-27 06:16:16', '2017-12-27 06:16:16'),
(296, 25, 'W5Sv1xxbxiTezfINoav8KdgQNRFLn2ov', '2017-12-27 06:17:50', '2017-12-27 06:17:50'),
(297, 25, '55Xc6cf6NbNVoRrwIstY1pxCuKCTRv0i', '2017-12-27 06:18:06', '2017-12-27 06:18:06'),
(298, 25, '43OuGgM5UC5CmNsfT5Q73YDavNUgrfwW', '2017-12-27 06:48:15', '2017-12-27 06:48:15'),
(300, 1, 'JJJHq7dQ7uM6PnOFNpH5p2aEbDoTczNZ', '2017-12-27 06:56:37', '2017-12-27 06:56:37'),
(301, 1, 'xH19vFsMLweWGqJhCjpU2Gu7w1gtAq2c', '2017-12-27 06:57:00', '2017-12-27 06:57:00'),
(302, 1, '9dgpFPO9r5I8t1EzQegHcTvGe3GIuwkw', '2017-12-27 22:39:04', '2017-12-27 22:39:04'),
(303, 1, 'UANIm8IwECvwptK3t42gI5ly9IRe0YpE', '2017-12-27 22:43:34', '2017-12-27 22:43:34'),
(304, 1, 'WVB2VI6yKPxlEf0qAKpi3vAmXPS6orKY', '2017-12-27 22:44:19', '2017-12-27 22:44:19'),
(305, 1, 'xEWpFFIsAadS7yfTI6kjPFoLCqWqwdzC', '2017-12-27 22:58:43', '2017-12-27 22:58:43'),
(306, 1, 'QedOC3wnoOU0zQGlwFwbnGCe4StbMW2c', '2017-12-28 01:14:59', '2017-12-28 01:14:59'),
(308, 1, 'O0vZVdWZKfPj46DxgDVxzyrprhysYnlj', '2017-12-28 06:17:54', '2017-12-28 06:17:54'),
(309, 1, 'xvK5ATwyy22ULqquVb1Ze3tK1wRr1WmP', '2017-12-28 06:24:25', '2017-12-28 06:24:25'),
(311, 1, 'Vg3vHCMgAYDwajdzXKunFtKYkQU3JX7N', '2017-12-28 06:55:12', '2017-12-28 06:55:12'),
(312, 1, 'Eg6O23rtCoTq7JAlE8NvCSpqbMubPx8n', '2017-12-28 22:29:06', '2017-12-28 22:29:06'),
(313, 1, 'ktuzKWAznSFegu42VMZoV07owGFHLtbY', '2017-12-28 22:43:54', '2017-12-28 22:43:54'),
(314, 1, '3tlMqpSgbB957iSPaiy1maSfCrlprlOe', '2017-12-28 23:04:10', '2017-12-28 23:04:10'),
(316, 1, 'hp27qD7tbBBaYls9SQHWvTJ3s0S89cVo', '2017-12-29 03:17:45', '2017-12-29 03:17:45'),
(317, 1, 'KORD9D2vDSmxHnJIIqjOvTonfrj8OrUZ', '2017-12-29 04:22:54', '2017-12-29 04:22:54'),
(318, 1, '2RGCvh22gmpUdL6TbZJcH6ehfO4dtNKa', '2017-12-29 06:20:02', '2017-12-29 06:20:02'),
(319, 1, 'oYHF7YvzNBh9QMHkhGCSBTlwzdCTSuTC', '2017-12-29 08:07:20', '2017-12-29 08:07:20'),
(320, 1, 'mh9Cg1ZFyf0iB6FjBPm0i6638tCNLuxR', '2017-12-29 08:07:31', '2017-12-29 08:07:31'),
(321, 1, 'dg8C8RTMeIUPqbB0VgSoDht8QNro3jVN', '2017-12-29 08:21:40', '2017-12-29 08:21:40'),
(322, 1, 'gyM0tTtBGeue1toLfycAc4sKbIsfw0OI', '2017-12-29 22:46:40', '2017-12-29 22:46:40'),
(323, 1, 'n4Xdv5GYtw7jfNZdSgYSNu10fqfdJKfF', '2017-12-29 22:49:09', '2017-12-29 22:49:09'),
(324, 1, '1xUkJokBcf0wJPGcxfbNOo5rnPkG7CbJ', '2017-12-29 22:51:00', '2017-12-29 22:51:00'),
(325, 1, 'H9BPTX3MSjLaDZuzFyC1UrJFicNHjIXB', '2017-12-30 00:32:22', '2017-12-30 00:32:22'),
(326, 1, 'LN97f5BTPVljzRN924C2OxmQ0XjLHLrK', '2017-12-30 00:35:32', '2017-12-30 00:35:32'),
(327, 1, 'MOGfKLwkxsPRkYK4wIk0kNA4DmXq8kwf', '2017-12-30 01:37:05', '2017-12-30 01:37:05'),
(328, 1, 'ADVu7LrL1SC0VF6ABz07h6bvHmQmfw7M', '2017-12-30 01:38:25', '2017-12-30 01:38:25'),
(331, 25, 'Vjs6LO9k8MTwTyZtrEF7F7h6toKRHK3Z', '2017-12-30 01:50:33', '2017-12-30 01:50:33'),
(332, 1, 'yLZDwZ1RYC4rym4YcqZSbUfyCTAggWP7', '2017-12-30 04:05:17', '2017-12-30 04:05:17'),
(333, 1, 'o2nmbQG3Om9sKCiqSRgXmKMYtshahvt7', '2017-12-30 04:24:28', '2017-12-30 04:24:28'),
(334, 1, 'FM4JMyvOAoJrXaHIBoZrBeIpiPBIUbMc', '2017-12-31 22:26:44', '2017-12-31 22:26:44'),
(335, 1, 'h4xYkaP35lv4Hpf6h54wexMpTaecRoGR', '2017-12-31 22:31:27', '2017-12-31 22:31:27'),
(337, 1, 'TS4iADKfzdaQo5M8cuFpyG1HpRhuOKuw', '2018-01-01 00:10:21', '2018-01-01 00:10:21'),
(338, 1, 'v6zO1AVTtEWs3oPtS1Xa3jEOdRMAfyN5', '2018-01-01 03:13:44', '2018-01-01 03:13:44'),
(341, 25, 'dipsyrFkWzwEOMVX4d1zmyyaaokJ19EE', '2018-01-01 05:04:22', '2018-01-01 05:04:22'),
(342, 1, 'bz7xd1VQzyAP9rPezkuzbyNJQNFdEJx3', '2018-01-01 05:05:37', '2018-01-01 05:05:37'),
(344, 25, 'dG3FXUrww2rhv4VqDe74l0bvzsbnvqH9', '2018-01-01 05:05:45', '2018-01-01 05:05:45'),
(362, 1, 'mHe8wYkJPODP5D4uWltrOGXeDgGUpLgV', '2018-01-01 06:05:03', '2018-01-01 06:05:03'),
(364, 1, '3mesJONFIeuA2mgJ7DMTFG5Iy3R8OV56', '2018-01-01 07:08:40', '2018-01-01 07:08:40'),
(365, 1, 'UNyhBC7ZcoWr6AxLrZuYPUrHSQdb1wsb', '2018-01-01 07:08:46', '2018-01-01 07:08:46'),
(366, 1, 'XUjJY1MdWC9Yqjfs3WVkj11hz7Xydadn', '2018-01-01 07:18:29', '2018-01-01 07:18:29'),
(367, 1, 'YC3J3Y5M00o9VY4fr3CKHLmz37KcOSEp', '2018-01-01 07:23:26', '2018-01-01 07:23:26'),
(370, 1, 'KN0YKY8KCLD19j9Th0QwYhZ1CS35UBOW', '2018-01-01 07:27:41', '2018-01-01 07:27:41'),
(371, 1, '3WNBWAo8zVzni9kP5sEbMGGz6mIzi1cO', '2018-01-01 07:28:06', '2018-01-01 07:28:06'),
(372, 1, 'vCoTFtkepd1UHVbMydEELM55gSearPFY', '2018-01-01 07:30:11', '2018-01-01 07:30:11'),
(373, 1, 'X20bpS9F7nMpLOIT5VtPfEUjdCfG2UfG', '2018-01-01 07:30:27', '2018-01-01 07:30:27'),
(374, 1, '8wMBjCPLtqaPt5TZVU9Oi9abUbkMg77f', '2018-01-01 22:31:57', '2018-01-01 22:31:57'),
(375, 1, 'SOMgwygh1zJt5BEwF5iIWBh2SfkkgpT7', '2018-01-01 22:33:18', '2018-01-01 22:33:18'),
(382, 1, 'IcH3IvLos9hXhVBGQYACaaXCznHahqug', '2018-01-02 04:08:43', '2018-01-02 04:08:43'),
(389, 1, 'vEaxWD9nsHE6nJBXDScwobbja2FkHdFi', '2018-01-02 04:40:56', '2018-01-02 04:40:56'),
(391, 1, 'yFcdyH36IGJ24Il5gnHwcR8RIMTqqnV5', '2018-01-02 04:58:01', '2018-01-02 04:58:01'),
(393, 1, 'Bv1SW17lWZRHDnuE8hUjvwU5vsgOZiIB', '2018-01-02 05:19:50', '2018-01-02 05:19:50'),
(394, 1, 'GO1J0YLt3qFZz6aTxNAiNwChKs9ieqXD', '2018-01-02 05:43:01', '2018-01-02 05:43:01'),
(395, 1, 'Mvc301KIJtMkoNq6iijy7JKtB9CuyL6F', '2018-01-02 06:42:15', '2018-01-02 06:42:15'),
(396, 1, 'fUTn4moWwbu9TNuVvbloNYht3s3yRSWz', '2018-01-02 06:49:25', '2018-01-02 06:49:25'),
(397, 1, '0lF7NuVZPNA93UP66x1J6L3BWq9HWMvH', '2018-01-02 06:54:40', '2018-01-02 06:54:40'),
(398, 1, 'XCx9sT6XYC49mlVQHw0u57mEj6we9dqz', '2018-01-02 07:51:04', '2018-01-02 07:51:04'),
(399, 1, 'Gm98akbLzJvdpRxSsd9O7tGMU5BAL7DD', '2018-01-02 07:58:08', '2018-01-02 07:58:08'),
(400, 1, 'j4OMIHnE9CnyFyJHqWYgbetIqI3YHiQZ', '2018-01-02 08:03:44', '2018-01-02 08:03:44'),
(401, 1, 'sy6SSXbZxMXUumYPB33lpVCpfBW3VRYv', '2018-01-02 22:37:07', '2018-01-02 22:37:07'),
(402, 1, 'd2EvlLtDxABoS683k46xXeppeslHaWUE', '2018-01-02 22:38:12', '2018-01-02 22:38:12'),
(403, 1, 'UVOX34CLrVHLpua40SnzJ4l5tknozbWq', '2018-01-02 22:50:19', '2018-01-02 22:50:19'),
(404, 1, 'n2RQ8t5mcMzf3PHKj0WuCmeH69fsUVrT', '2018-01-02 23:41:49', '2018-01-02 23:41:49'),
(405, 1, 'blvtAsIktgRP7zA7QfuvwpOxIJsiWUJS', '2018-01-02 23:55:41', '2018-01-02 23:55:41'),
(406, 1, 'OXrXw0jkfUgwjTrjwCPp2MHr1EUPRfoU', '2018-01-03 01:49:27', '2018-01-03 01:49:27'),
(407, 1, 'E09rtVMqtTNcIdiRvv0nWxCz7EScfxWR', '2018-01-03 04:50:26', '2018-01-03 04:50:26'),
(408, 1, 'oe9ZdBfYloTdKYvFG6xuel8lEZWBnVjn', '2018-01-03 05:18:21', '2018-01-03 05:18:21'),
(409, 1, 'wJO9PAA48j8A26y6W8aWMwwF4SLdxV2Q', '2018-01-03 22:19:46', '2018-01-03 22:19:46'),
(410, 1, 'C4MWCtaQU2P7v2P3dfOmfukLUwUAlwry', '2018-01-03 22:24:05', '2018-01-03 22:24:05'),
(411, 1, 'W1B33IyfFCxM2xfrJ8ycKoW6XmBO1yLW', '2018-01-03 22:24:48', '2018-01-03 22:24:48'),
(413, 1, '2jK6NZV08ivwDoq4vy21XmtIq2h2IJi8', '2018-01-03 23:41:22', '2018-01-03 23:41:22'),
(417, 1, 'JOkTGsEm3WQujBAlKRfwYWVUNWCbQSHN', '2018-01-04 00:29:32', '2018-01-04 00:29:32'),
(418, 1, 'dGgVNFxVzGWASmI0CUhcI0D4N6WWpjYr', '2018-01-04 02:55:07', '2018-01-04 02:55:07'),
(419, 1, 'TbzP3PHRojGujvaguqllceDBmsYvgako', '2018-01-04 03:24:14', '2018-01-04 03:24:14'),
(420, 1, 'Bw0NlliPWD24GclqLof8W2FONdjMNFHr', '2018-01-04 03:35:54', '2018-01-04 03:35:54'),
(421, 1, 'mh22k8Ghzj1cVjwAbHnqPLNDWe1Wqp7q', '2018-01-04 05:44:42', '2018-01-04 05:44:42'),
(422, 1, 'JiVngADpDgzyIV0JcX4q1VzKhVfeqBBG', '2018-01-04 05:47:55', '2018-01-04 05:47:55'),
(423, 1, 'oyYFw5KYKG5eNDzVMUB7g08w9oJvYA1H', '2018-01-04 22:25:08', '2018-01-04 22:25:08'),
(424, 1, 'GxfipWzCE1aK7ATaDRrgwCYorQhYX4I1', '2018-01-04 22:26:08', '2018-01-04 22:26:08'),
(425, 1, 'Ts5Nr4t7GPtWXPVAKvGgrScbkCVsyjxH', '2018-01-04 22:57:14', '2018-01-04 22:57:14'),
(426, 1, 'utSISVysFijkOVmdttLnBLcL102dDkeZ', '2018-01-05 00:09:07', '2018-01-05 00:09:07'),
(427, 1, '0ez2SPyJqFuwY4VvohaKxajAp1AvixSW', '2018-01-05 00:19:05', '2018-01-05 00:19:05'),
(428, 1, 'aDcxwYInnhm9HdEZ9bRLmLaBZYJSg1yH', '2018-01-05 03:11:48', '2018-01-05 03:11:48'),
(429, 1, 'wwJRt7orOQX5lXIXUzXPDLuOj4IMWXwH', '2018-01-05 06:13:19', '2018-01-05 06:13:19'),
(430, 1, '8jtAWDBnTYGcBgMJmp8ntHncRu6PSzuv', '2018-01-05 06:14:25', '2018-01-05 06:14:25'),
(431, 1, 'CvVFxxa9aTOdVryzTVvlh5nFqxF4BrLR', '2018-01-05 06:21:02', '2018-01-05 06:21:02'),
(432, 1, '9D5GuFBl625eZ2mMU0LSqWBLPJ3QGNUI', '2018-01-05 07:25:58', '2018-01-05 07:25:58'),
(433, 1, 'EJpOFe1Q5JE2N0rwGoPNfP6tvQ1F7FIm', '2018-01-05 22:27:10', '2018-01-05 22:27:10'),
(434, 1, 'MMLqEaq3DtbxoIKLiC5Ok2tNlTn09RlK', '2018-01-05 22:27:10', '2018-01-05 22:27:10'),
(435, 1, 'JxQZPqegNTM4DJD18gWZFh34riy7puc9', '2018-01-05 22:27:59', '2018-01-05 22:27:59'),
(436, 1, 'wxdQYRYtR3lw6as5muAXM71rRULwm5Qh', '2018-01-05 22:28:42', '2018-01-05 22:28:42'),
(438, 1, '695iftcOv0OeNBcWIP9EYS1Q46rtQtoX', '2018-01-05 22:34:36', '2018-01-05 22:34:36'),
(439, 1, 'kKTtiMlGSv2sURnflWLQ81twTl85kVqL', '2018-01-06 04:08:03', '2018-01-06 04:08:03'),
(440, 1, 'KNnylzRh5NFYHWTen2YXSv2xaMd3Kl0N', '2018-01-06 04:21:29', '2018-01-06 04:21:29'),
(441, 1, 'k1HaDGcpJhAGSmCuX90p96oO0jL4MokP', '2018-01-06 04:23:04', '2018-01-06 04:23:04'),
(442, 1, 'b4jJhyL3vLTz8ablTgF0GqpfoLXyiubi', '2018-01-06 04:55:38', '2018-01-06 04:55:38'),
(443, 1, 'CEZZmOnrh89uhwa5nliz85Cwjli3P1Cm', '2018-01-06 04:57:19', '2018-01-06 04:57:19'),
(444, 1, 'lHPdiVIgCIbdmMTUBztnahHQj3fWrIEX', '2018-01-06 07:52:13', '2018-01-06 07:52:13'),
(445, 1, 'ypsLXiR4TDlUSCi37ErpjII2ipSNGGvr', '2018-01-06 07:52:57', '2018-01-06 07:52:57'),
(446, 1, 'oEO7r8wuatxDPfAgMfgNO0HufrcThGMV', '2018-01-06 08:00:25', '2018-01-06 08:00:25'),
(447, 1, 'r7oj1B89DuDLqTa0BMRSogYxShJsKk5G', '2018-01-07 22:28:12', '2018-01-07 22:28:12'),
(448, 1, 'nUHRQRaGl2TJARKBghbQslPacnWodBAA', '2018-01-07 22:47:14', '2018-01-07 22:47:14'),
(449, 1, 'Ybg8hELR6jiETF8D3WyCG3RFRfwTLMiZ', '2018-01-07 23:23:03', '2018-01-07 23:23:03'),
(450, 1, '5DpDdtshkkdGjETY80Ix7ahVj8eMJMQ5', '2018-01-08 05:06:16', '2018-01-08 05:06:16'),
(451, 1, 'cl5FN5hCgxQNhfa1qkJILJB2GEZciPCp', '2018-01-08 22:29:08', '2018-01-08 22:29:08'),
(452, 1, '0eresaIqd9rWi7hhOC6kMQaRUAzc6qMC', '2018-01-08 22:48:30', '2018-01-08 22:48:30'),
(453, 1, 'IBadAGD5KZFC2knlogk1TQYBEjHersUd', '2018-01-09 01:33:13', '2018-01-09 01:33:13'),
(454, 1, 'F8qgr2QYCk7pHjWCAM49n3vn0vM8IaN8', '2018-01-09 01:36:23', '2018-01-09 01:36:23'),
(455, 1, 'l7upd7sVqcZneUy9JWvaJgrddapllMid', '2018-01-09 03:02:54', '2018-01-09 03:02:54'),
(456, 1, '1DSWCmDefUtVTCuShl2e6FdY9315hncK', '2018-01-09 03:22:33', '2018-01-09 03:22:33'),
(459, 1, 'S5riBAwGIfjSR1hthNrYaFDwTYRwEOQd', '2018-01-09 03:27:07', '2018-01-09 03:27:07'),
(460, 1, 'HCvBGkXAoVqsnTIu23vIl95Kv6WokDJv', '2018-01-09 03:30:09', '2018-01-09 03:30:09'),
(461, 1, 'OkqzciHebNoteleG8huc80JcuHGcUhzo', '2018-01-09 03:34:27', '2018-01-09 03:34:27'),
(463, 1, 'nvpVO4crACc5Gc4QtfPqg4fB6PNQG0zj', '2018-01-09 03:35:59', '2018-01-09 03:35:59'),
(464, 1, 'SiiOM7PWJGG4691KDypsReACtMjcbRwi', '2018-01-09 03:41:23', '2018-01-09 03:41:23'),
(465, 1, 'ydQBYONoXqXxcWZkxKIagNXad5kNIxW2', '2018-01-09 03:48:45', '2018-01-09 03:48:45'),
(466, 1, 'CZMsRTNtl59gljL31b7MAsH6xjABbIxp', '2018-01-09 03:48:45', '2018-01-09 03:48:45'),
(467, 1, 'gENyXyBOKk0j8O29lOjhf9PoUp1NJ14d', '2018-01-09 04:23:49', '2018-01-09 04:23:49'),
(468, 1, 'x09lezKql5jDPRFwAb9Veyr0FSr4rljZ', '2018-01-09 22:42:14', '2018-01-09 22:42:14'),
(469, 1, 'w8UUOxJxaOVUJCLML6p2epN8EhXigUdk', '2018-01-10 00:31:02', '2018-01-10 00:31:02'),
(470, 1, 'hkBEEk2IYV1foUqQuah3uX8CZD7mSucW', '2018-01-10 01:17:38', '2018-01-10 01:17:38'),
(471, 1, 'SXhzxrw62pu6DEz0W8AV3AWb9bGUfRfX', '2018-01-10 06:48:13', '2018-01-10 06:48:13'),
(472, 1, 'oenAWrOWWWuR6CsFEHV5RybpQDpnkXb9', '2018-01-10 22:25:50', '2018-01-10 22:25:50'),
(473, 1, 'b7TgpjzzfFJskaGPjPWNJ7itqAbB4HME', '2018-01-10 23:26:17', '2018-01-10 23:26:17'),
(474, 1, 'GaI8sM6QrxqXHC0RLbXHPKK9EVz6KpJl', '2018-01-11 00:26:11', '2018-01-11 00:26:11'),
(475, 1, 'I9Ag95WqrqkNYB2Mbc2tgnac9S8qGUmX', '2018-01-11 01:23:24', '2018-01-11 01:23:24'),
(476, 1, '2jKc4xWt306etkdn90ruv6hbIRi0mhCk', '2018-01-11 03:08:40', '2018-01-11 03:08:40'),
(477, 1, 'rr15bXc2pG7nHBjvDz8HSQb8n6k5dU1W', '2018-01-11 04:43:09', '2018-01-11 04:43:09'),
(478, 1, 'FZH6kcuh5tJZhmJbHCjCM2xWPBSkBBO5', '2018-01-11 06:00:38', '2018-01-11 06:00:38'),
(479, 1, 'Y0OYBPJRP90w9Bbn2aUkKGQGlY67CIT7', '2018-01-11 22:27:52', '2018-01-11 22:27:52'),
(480, 1, 'NhRAWQvM0EuEiXDEvnDBG6idcXZdAGvo', '2018-01-11 22:27:52', '2018-01-11 22:27:52'),
(481, 1, 'pYWr7U4ddYMdlyl6xICjtjHjOWBIl8k8', '2018-01-11 22:28:57', '2018-01-11 22:28:57'),
(482, 1, 'eBupaPddFxEbmkijiBeJcPEYUknppadd', '2018-01-11 23:01:04', '2018-01-11 23:01:04'),
(483, 1, '0rRa4buVXwyVJy8apNJqEQauwnXoVv4N', '2018-01-12 01:01:12', '2018-01-12 01:01:12'),
(484, 1, 'RG6PJO9XPSvQfPOhqgeeN1UeA5RKiSwJ', '2018-01-12 01:35:37', '2018-01-12 01:35:37'),
(485, 1, 'hFqsg3GFw91qvdFHkuP6isJhoIUqDbXY', '2018-01-12 01:35:37', '2018-01-12 01:35:37'),
(486, 1, 'FBcZJxK4gRp2lHWk1HlVDOwy4smo8tWe', '2018-01-12 03:05:34', '2018-01-12 03:05:34'),
(487, 1, '4Wws4RMawL8xFhf3LSkDrS9DGgkFnVDZ', '2018-01-12 07:16:26', '2018-01-12 07:16:26'),
(488, 1, '3mZruJFliGAy3eiXODjUXZJ7ugCOjqJ1', '2018-01-12 07:47:23', '2018-01-12 07:47:23'),
(489, 1, 'L2Wgm6shJWOUxwzE6ihHGTJL7uFUIX1D', '2018-01-14 22:20:56', '2018-01-14 22:20:56'),
(493, 1, 'SSJwDEfEXeOrA3y3bXWedtEhyK7jE2GE', '2018-01-15 01:10:45', '2018-01-15 01:10:45'),
(494, 1, '2GvmBvWsWY4VZdutpZ5QugGGO3pUKbZg', '2018-01-15 03:14:01', '2018-01-15 03:14:01'),
(495, 1, 'LY10wONGf6No5ITDaCOqskbnBJo7dwuo', '2018-01-15 22:26:30', '2018-01-15 22:26:30'),
(496, 1, 'AgQJDHI3qdycJfZ0IM3ipMNTX8Wo9rAZ', '2018-01-15 22:28:06', '2018-01-15 22:28:06'),
(497, 1, 'WerAPFTy6PPHYrB7XqmNQvWyJclFOHtH', '2018-01-16 01:25:51', '2018-01-16 01:25:51'),
(498, 1, '74knbXdYGpDehx9snW5UGxuYpmgU1f2w', '2018-01-16 04:28:27', '2018-01-16 04:28:27'),
(499, 1, 'ajFOiGnS6ZM051zFwSmtjoWHED3k7xjN', '2018-01-16 05:24:13', '2018-01-16 05:24:13'),
(500, 1, '4bsENWBdlepKJIDgECzBWLoZU5gZmiCi', '2018-01-16 22:27:17', '2018-01-16 22:27:17'),
(501, 1, 'EOn6u8TiIGSufeKam1mqsgFQF1dw0oFi', '2018-01-16 22:29:21', '2018-01-16 22:29:21'),
(502, 1, 'BTGxjJEAHfhAS8lpqcrRBHpuLQCjJZAQ', '2018-01-16 22:48:53', '2018-01-16 22:48:53'),
(503, 1, 'xLe2NLoIhxPiUFhUm7CTY9Ya1yUpMUPW', '2018-01-16 22:48:53', '2018-01-16 22:48:53'),
(504, 1, 'wn8miTC1RqeXWIIDyZSOzlLRlTRpVsnE', '2018-01-17 00:16:14', '2018-01-17 00:16:14'),
(505, 1, '9UiS4oGV6w4WmWh5yZHJsjKQWaiqMkZD', '2018-01-17 05:11:55', '2018-01-17 05:11:55'),
(506, 1, 'yglOU88JEnS2pW8KoT5z8SA4lPvdZ21t', '2018-01-17 06:22:13', '2018-01-17 06:22:13'),
(507, 1, 'G9wib69EtmrPl23LovJhi7qQTbBsFMjT', '2018-01-17 07:56:23', '2018-01-17 07:56:23'),
(508, 1, '6gr6T1xGEQF23fzxD2B73AMNWVrfN38M', '2018-01-17 08:38:40', '2018-01-17 08:38:40'),
(509, 1, '3KZRMAYkTZfmNFwMQarWY23PObRBL0wr', '2018-01-17 22:21:19', '2018-01-17 22:21:19'),
(510, 1, 'eQmv9cIXxl5vDaWyw3s5k5hs27cN9eRG', '2018-01-17 22:33:36', '2018-01-17 22:33:36'),
(511, 1, 'BZz51ApdlHlqX5k9tjQCMltyEPk9183C', '2018-01-17 22:56:32', '2018-01-17 22:56:32'),
(512, 1, '8cJXpJ7Dyjx10fC2ylvX8lUlUswuMa4D', '2018-01-17 23:27:44', '2018-01-17 23:27:44'),
(514, 1, 'miSqm2bZqqzZA6XGP1fkhJy69N5doU1P', '2018-01-18 00:48:09', '2018-01-18 00:48:09'),
(515, 1, 'OVM70eCMYYDXheLz7Iq6YMtqOHMFdCXQ', '2018-01-18 00:49:05', '2018-01-18 00:49:05'),
(516, 1, 'bMKRdjo7mDZvhPABFUct3koeV4mPn9o3', '2018-01-18 00:50:51', '2018-01-18 00:50:51'),
(517, 1, 'P6uGAN8k8L9GVZQ5SjAiaZULmu3YPmty', '2018-01-18 00:52:59', '2018-01-18 00:52:59'),
(518, 1, 'lO7vp2KhcMSvaq52C1eW1UqBGT7yxtxq', '2018-01-18 00:53:46', '2018-01-18 00:53:46'),
(519, 1, 'be9pVI6dogMRH2d3LqRC1mTKfgYH9s0i', '2018-01-18 00:55:26', '2018-01-18 00:55:26'),
(520, 1, 'pDLTtOaFDUcq7z8TGqABesejGxw4pWek', '2018-01-18 00:56:29', '2018-01-18 00:56:29'),
(521, 1, 'jUQrjSvHsqwW2kPcZwf9c1Ph9xZuns6X', '2018-01-18 00:56:53', '2018-01-18 00:56:53'),
(522, 1, '0ULQ8e8Y18YHwp9P1Tfu1ppCcVgsUGiO', '2018-01-18 00:58:41', '2018-01-18 00:58:41'),
(523, 1, 'gZIYiiH8R8MxuGQQmzmZ30zrMa33CGPl', '2018-01-18 01:02:36', '2018-01-18 01:02:36'),
(524, 1, 'Lc9VxFoUCDHCtpuA4QvH1ujRxrBL4hkI', '2018-01-18 01:03:46', '2018-01-18 01:03:46'),
(525, 1, 'hUXQwLOBFzNbqHRS7lTW2FzVPhlzt8CM', '2018-01-18 01:05:21', '2018-01-18 01:05:21'),
(526, 1, '6Fwblo4wnGsYQswiEoeOk2UzQaeaXwjD', '2018-01-18 01:09:16', '2018-01-18 01:09:16'),
(527, 1, 'oFBau2w1J71VpBv6fXkx6ay5aG1JL0kk', '2018-01-18 01:35:21', '2018-01-18 01:35:21'),
(528, 1, '3MVVXa9h604SOFvcRKaJqRQ79jFBlzJd', '2018-01-18 05:24:37', '2018-01-18 05:24:37'),
(529, 1, 'E3OgGPURmF0P6G2z4xaIUvMxDdNGtOg0', '2018-01-18 22:29:50', '2018-01-18 22:29:50'),
(530, 1, 'IXrfT6EB8jjAduRZhn5W1BdiLv32Ai8y', '2018-01-18 22:35:23', '2018-01-18 22:35:23'),
(531, 1, 'wFJ4da4mKs4BSHD5mnBkg4m2v6DASii4', '2018-01-19 04:01:29', '2018-01-19 04:01:29'),
(532, 1, 'wEi8RVnlCNgXwMb3L0nBhmQi3IXpxLqw', '2018-01-19 04:43:07', '2018-01-19 04:43:07'),
(533, 1, 'tG30c3f0ozMLmY0JHxri0EVCUqzGogAC', '2018-01-19 06:34:07', '2018-01-19 06:34:07'),
(534, 1, '05wrNweVsZVSFWNYrv0MqEqAc3T92S8f', '2018-01-19 22:27:24', '2018-01-19 22:27:24'),
(536, 1, 'Gzj0v0YqeXdykknrWkO0or4vOYHLn20w', '2018-01-20 05:31:57', '2018-01-20 05:31:57'),
(537, 1, 'd643qpignUpkxTqGYxORCTpx0uZbDCOS', '2018-01-20 05:44:05', '2018-01-20 05:44:05'),
(538, 1, 'OvC1P5CF9rIH9JBcNe3CbKHLMjN3evA1', '2018-01-20 07:29:59', '2018-01-20 07:29:59'),
(539, 1, 'vrQQbAaAGRTktkerBeqEoPcRa3PIu2oh', '2018-01-21 22:19:54', '2018-01-21 22:19:54'),
(540, 1, 'efbQdnr44jjjHsTrQEKl8cGkLXIeK4bZ', '2018-01-21 22:28:12', '2018-01-21 22:28:12'),
(541, 1, 'N5swndquWyoqrocOPAn3VYBckK0Kpf3U', '2018-01-22 04:39:45', '2018-01-22 04:39:45'),
(542, 1, 'ZiS1SNnH0C4E5eonUszwZko7NA4BbjIG', '2018-01-22 04:45:49', '2018-01-22 04:45:49'),
(543, 1, 'HkUIfohhe49tEIgbqhunIzgvauusJjU9', '2018-01-22 06:33:28', '2018-01-22 06:33:28'),
(544, 1, 'aWv3VIzLWZUYZTDtAtGMtLQtyxoCWZQp', '2018-01-22 22:18:15', '2018-01-22 22:18:15'),
(545, 1, 'PXBQkkmGakCBsxbpVIEvjO39SgBIp7hw', '2018-01-22 22:43:11', '2018-01-22 22:43:11'),
(546, 1, 'eCTziAfY5Vb7ijQ4VYDvl4CbjqjaTVsG', '2018-01-22 22:43:11', '2018-01-22 22:43:11'),
(547, 1, 'II81sAlwk5VVgc9oQM5E6iJ9Mjh3wzSr', '2018-01-22 23:43:58', '2018-01-22 23:43:58'),
(548, 1, 'kmGjsKVYnhvcZXLOeRnk9o1aMVxj0KXe', '2018-01-23 22:17:37', '2018-01-23 22:17:37'),
(549, 1, '2cXmJzlNbgSxBwuGkQh4ufalRNrAnEgW', '2018-01-23 22:27:52', '2018-01-23 22:27:52'),
(550, 5, 'qjfQDHJ2VSnoRd4tXpi0tDk5ueI95zhz', '2018-01-23 22:45:14', '2018-01-23 22:45:14'),
(551, 1, '9Cw2mbTcAiXG6tHS4IoeNXrUJ8rCW0cn', '2018-01-24 02:52:43', '2018-01-24 02:52:43'),
(552, 1, 'lrzis5WZkub2lxeQ91gV0mXAZyHqmDt4', '2018-01-24 03:46:58', '2018-01-24 03:46:58'),
(553, 1, 'oYbHKigS7tXh2gHuajUglBk2nsXsgezW', '2018-01-24 22:23:47', '2018-01-24 22:23:47'),
(554, 1, 'uz1ty4yYGMEHdNZTp6qxEcd2PBCR2SXi', '2018-01-24 22:26:10', '2018-01-24 22:26:10'),
(555, 1, 'NkSZEiYKSlTbIDQ0QYdk2iHSfIkHfWXe', '2018-01-25 06:31:55', '2018-01-25 06:31:55'),
(556, 1, 'eu0CdE61PCHWNTWtHZ82ZJpmT79AyZ1I', '2018-01-28 22:29:31', '2018-01-28 22:29:31'),
(557, 1, 'xWQuosmRka7QVaB4dapnctruPRo7uZWV', '2018-01-28 22:54:47', '2018-01-28 22:54:47'),
(558, 1, 'f0wXFhdhyI1wZTFufwBJXts9enl0WDEU', '2018-01-29 22:28:54', '2018-01-29 22:28:54'),
(559, 1, 'EwFjyt3uvcmwZUnQLjKRMidqlFhjf2cE', '2018-01-29 22:52:13', '2018-01-29 22:52:13'),
(560, 1, 'bax6nbrYtI4dAtnbcGCoHN9DLFj2B8EO', '2018-01-29 22:52:46', '2018-01-29 22:52:46'),
(561, 1, 'EHasswSLOW5oUYN0pPdBI1mGknNs5qWp', '2018-01-29 22:53:11', '2018-01-29 22:53:11'),
(562, 1, 'Vd5RfhDwUNBEEaBC91ut6uYWepuORYMd', '2018-01-30 04:24:53', '2018-01-30 04:24:53'),
(563, 1, '1woFtxTuLCObjz3US4tl2LMDli5JaFev', '2018-01-30 04:33:11', '2018-01-30 04:33:11'),
(564, 1, 'gG8tiuXbm4Y9T153XfjEXmcHE9zqCRGi', '2018-01-30 08:20:46', '2018-01-30 08:20:46'),
(565, 1, '5WfrK4M6s7RoFWIxeq8d2Oe0zdV6AKfh', '2018-01-30 22:18:04', '2018-01-30 22:18:04'),
(566, 1, 'l4fd11V2HD710UAO3istmbl1tqZPLbDq', '2018-01-30 22:30:19', '2018-01-30 22:30:19'),
(567, 1, 'WAHbJq2QJzL9eHUQV3fgzkT3MNvxoJ1P', '2018-01-31 03:15:06', '2018-01-31 03:15:06'),
(568, 1, 'D3AnX6ZGeDqYMYs5KgQOACy7vM0ExClL', '2018-01-31 08:59:18', '2018-01-31 08:59:18'),
(569, 1, 'eELLnuCGyiLDqLfVshGF5kivP9tRrsD1', '2018-01-31 22:27:36', '2018-01-31 22:27:36'),
(570, 1, 'qCIl3oIkxwm1sjQ1LpzKyHRLjG4zjtn2', '2018-01-31 22:40:51', '2018-01-31 22:40:51'),
(571, 1, 'IvDqgixmcwf1YcWtfT4ZbmDqLWeZHOnW', '2018-01-31 23:02:54', '2018-01-31 23:02:54'),
(572, 1, 'BLrXKVhqZeWF8Ju2l8EMPPunZ3To2kzn', '2018-02-01 22:22:19', '2018-02-01 22:22:19'),
(573, 1, 'qbiZFGqScSXanzJvlJ7NWmmLwwTyuQ4b', '2018-02-01 22:29:15', '2018-02-01 22:29:15'),
(574, 1, '90mDF35hLw7L0bG9gGR8XDvKcol5U8kc', '2018-02-02 01:50:14', '2018-02-02 01:50:14'),
(575, 1, 'OtMD2r0kLboh5mfiARs59iCYGvpJFfxT', '2018-02-02 22:29:25', '2018-02-02 22:29:25'),
(576, 1, '2pKSXSXgnnNkQViALguZ3pKx85kYJrIS', '2018-02-02 23:22:48', '2018-02-02 23:22:48'),
(577, 1, 'AjfdvYcjAN6iXtPUIl97tB1NZp592EO9', '2018-02-03 00:41:43', '2018-02-03 00:41:43'),
(579, 1, 'RYxz3zimRuJnLX6A6rCYcmHzcwxKvHEC', '2018-02-03 05:07:41', '2018-02-03 05:07:41'),
(580, 1, 'kKDJkuKNTP4JTCmcf3s0ULH5gcQKpJZq', '2018-02-03 06:14:33', '2018-02-03 06:14:33'),
(581, 1, 'T6tDHwSm894Z72v2FXIncvyKlJ5wYUlD', '2018-02-03 06:41:48', '2018-02-03 06:41:48'),
(582, 1, 'ORyYoZDDukrDOv3K8b6iFDeV5zo7vFcB', '2018-02-03 07:04:12', '2018-02-03 07:04:12'),
(583, 1, 'b1TGfyDcuf4hkPE9MTXEQScMHYIRV2Dq', '2018-02-03 08:12:32', '2018-02-03 08:12:32'),
(584, 1, 'IL26vwRnFRhiBpSXcpGfNUBjlyoYITfL', '2018-02-04 22:25:23', '2018-02-04 22:25:23'),
(585, 1, 'UbKgpjQHmyr595XAOhet2v3CH7OHvCUb', '2018-02-04 22:35:04', '2018-02-04 22:35:04'),
(586, 1, 'vzSm2t62A1Fj6qSAR5CIrzR3ol0IHe1K', '2018-02-04 23:01:23', '2018-02-04 23:01:23'),
(587, 1, 'srGU3vGonJXatLxiSoPHBIrAcwycwAbC', '2018-02-04 23:09:31', '2018-02-04 23:09:31'),
(588, 1, 'AmDeMQVX0K0mUnQcwJaTjPAaYtqHPQIy', '2018-02-04 23:19:42', '2018-02-04 23:19:42'),
(589, 1, '2VI5l4zqFL9UPcjMJEYGNpOGaVWO0Mkd', '2018-02-04 23:50:09', '2018-02-04 23:50:09'),
(590, 1, '2Wq1gKZ8rjSvpsL3VGv02vfqwsTEL0vw', '2018-02-05 05:24:17', '2018-02-05 05:24:17'),
(591, 1, '5p8YlKwgy876lAOkO48etn4Lmgj7m0lg', '2018-02-05 06:42:08', '2018-02-05 06:42:08'),
(592, 1, 'NYiLnnwkCKaLEPI690Zkzt652d8yaoWQ', '2018-02-05 22:18:45', '2018-02-05 22:18:45'),
(594, 1, 'lCkSw8T97mkSFEceUCPozaOiMWH2PMAO', '2018-02-05 23:06:14', '2018-02-05 23:06:14'),
(595, 1, 'YO1m4XCyLyyztd2bDikWnDbkbD0M7NOh', '2018-02-06 04:42:32', '2018-02-06 04:42:32'),
(596, 1, 'vcfJNT6zc8AWfxSAigHDYiLmvNiLAVIf', '2018-02-06 06:46:05', '2018-02-06 06:46:05'),
(597, 1, 'apEvDAT77lwOAiWJVbxeBmmLaszd4gH3', '2018-02-06 22:19:47', '2018-02-06 22:19:47'),
(598, 1, 'Uvja3Gvfcvd96ZU8OEXHB2jkyduyNaiY', '2018-02-06 22:40:11', '2018-02-06 22:40:11'),
(599, 1, 'k8iBAlu9xpfuvJYR5AXMypHMseeAdcnW', '2018-02-07 03:27:50', '2018-02-07 03:27:50'),
(600, 1, 'a2CkVIkfIHjF1N4xFlofp4786LEVavqe', '2018-02-07 04:36:25', '2018-02-07 04:36:25'),
(601, 1, 'tmckiQHpE6s1pNPmAzQcx0q62R2PZBzj', '2018-02-07 22:35:04', '2018-02-07 22:35:04'),
(602, 1, 'AS8ieh1SXRKGvCQIgRM2bbMbmQjqXzhg', '2018-02-07 22:40:48', '2018-02-07 22:40:48'),
(603, 1, 'XhjZZ253RpWsYoMxntpqnJXiGPbW9pVi', '2018-02-08 00:31:39', '2018-02-08 00:31:39'),
(604, 1, '59lkgmVhC15GM6norw0BoZOaF736KGgv', '2018-02-08 03:28:20', '2018-02-08 03:28:20'),
(605, 1, 'WsWWiNd5uJhr771OiwQlOuzvvanaSmKF', '2018-02-08 22:24:06', '2018-02-08 22:24:06'),
(606, 1, 'KhnVE7nSKf81E1rIQAr7B94FjfeqAYqA', '2018-02-09 04:22:32', '2018-02-09 04:22:32'),
(607, 1, 'PGiJn9w44mLnyNhyQtisqTiCnt52phns', '2018-02-09 10:09:35', '2018-02-09 10:09:35'),
(608, 1, 'B5b9PJz3SEqaIWEtvwEQnOGfjWjJq3Tj', '2018-02-09 10:10:44', '2018-02-09 10:10:44'),
(609, 1, 'jMBYTWtR5q1rhSvGj9U5yDQ54lR9Kk7Z', '2018-02-11 22:21:05', '2018-02-11 22:21:05'),
(610, 1, '3iyHbnu25JKF4qfct86xg64P0OpaJdev', '2018-02-12 03:49:56', '2018-02-12 03:49:56'),
(611, 1, 'OFVt9k7O4mzWJvuSvKcmYtDQDafsT7dx', '2018-02-12 07:26:20', '2018-02-12 07:26:20'),
(612, 1, 'Npc2tCzUqzzkgGxSfL59WYRTEOZXu94g', '2018-02-12 22:48:39', '2018-02-12 22:48:39'),
(613, 1, 'pcM9jvvntbZ1nMcqgj4ser2VJ09n1H6v', '2018-02-12 23:14:51', '2018-02-12 23:14:51'),
(614, 1, 'aEACHJiRu0WaLgnGYjYIhwNMhtzwVkQk', '2018-02-12 23:16:44', '2018-02-12 23:16:44'),
(615, 1, 'ut2UbaQNLmBJ3u9PMkCn6vDHqyLCj3u4', '2018-02-13 01:09:55', '2018-02-13 01:09:55'),
(616, 1, '2f8rjnaDmpJcgGhz5DOn88BQe8cJEtwJ', '2018-02-13 03:39:35', '2018-02-13 03:39:35'),
(617, 1, 'J23a29KfA3LQ1O1DMHWcB1Z0n6fASUXF', '2018-02-13 06:06:41', '2018-02-13 06:06:41'),
(618, 1, 'jEwX5R7pW17DvSkgqEagEUxyZWJucjdB', '2018-02-13 06:08:12', '2018-02-13 06:08:12'),
(619, 1, 'EQj8sdTw7NjSv2BG9ZTZg7S6HShDVr4r', '2018-02-13 06:16:48', '2018-02-13 06:16:48'),
(620, 1, '75IYo0XuJP4Hzos3CraZvYsnSEz7FPB2', '2018-02-13 06:32:26', '2018-02-13 06:32:26'),
(621, 1, 'XkQ3O5Rapomf9ZInPYrh7z2yDKjZYuEH', '2018-02-13 06:47:29', '2018-02-13 06:47:29'),
(622, 1, '5kC3gRRwLP1bcfXfBcJ6VQL4mnxJbbRF', '2018-02-13 06:50:37', '2018-02-13 06:50:37'),
(623, 1, 'WSLIekxZBW5Iw4qdzWdeefpUSuPRjSDs', '2018-02-13 06:51:49', '2018-02-13 06:51:49'),
(624, 1, 'yLfVXkPXImZoz2Xsk8uKha7Hpifn1BOZ', '2018-02-13 22:25:12', '2018-02-13 22:25:12'),
(625, 1, 'mR5nM8oM2lWG4vec2yRMGB9CKXgYtzZe', '2018-02-14 06:03:43', '2018-02-14 06:03:43'),
(626, 1, 'pzFBP27Jdt8tKexCeDJRjAb2C8wCzyID', '2018-02-14 22:37:38', '2018-02-14 22:37:38'),
(627, 1, '9Oz5dHRAuWlOGbQcS0G25u557io428Kg', '2018-02-15 03:49:54', '2018-02-15 03:49:54'),
(628, 1, '56uJOfReKBBz8IwwdJ7Kj4pkjizCWFNU', '2018-02-15 23:07:24', '2018-02-15 23:07:24'),
(629, 1, 'QAZhPoUtcj7cBlXYTNxOpo7YCWwKzO8j', '2018-02-16 03:59:55', '2018-02-16 03:59:55'),
(630, 1, '3qJiakVCfbDS81WovUgEvKYEETAMTaFn', '2018-02-16 05:09:00', '2018-02-16 05:09:00'),
(631, 1, 'm1MpG1IFFrSvZMU2WGErKJaRE2NIWxl3', '2018-02-16 05:19:53', '2018-02-16 05:19:53'),
(632, 1, '8K9Uy69NK5xlB4zNXaga8mJo91MDxQ18', '2018-02-16 06:42:05', '2018-02-16 06:42:05'),
(633, 1, 'NANYE61oNewShGBXNhHpsmb8PB1AnwDv', '2018-02-16 22:31:44', '2018-02-16 22:31:44'),
(634, 1, 'Bg0AJQ7m4gvI2RnME3LiMoRRPziBMjHt', '2018-02-16 22:32:25', '2018-02-16 22:32:25'),
(635, 1, '1JhuQCGyvmYTH1EmOQvXlB8i43aBSmdB', '2018-02-16 22:34:31', '2018-02-16 22:34:31'),
(636, 1, 'XYfvwIsiWWtETjQZp54nPRzfLhc2wvr6', '2018-02-17 00:26:41', '2018-02-17 00:26:41'),
(637, 1, 'Fs9gpAyE0s8rGMmnApY76x6UVPHejJyV', '2018-02-17 01:13:09', '2018-02-17 01:13:09'),
(638, 1, '5Bn4qOdz3Z1OUmnTAKLYervsABypQpGc', '2018-02-17 01:26:39', '2018-02-17 01:26:39'),
(639, 1, 'yy6TPTSiRMitFcijnlhPtsnF6MEcEutH', '2018-02-17 03:48:25', '2018-02-17 03:48:25'),
(640, 1, 'fyisIeEQTpa16ifp2Ozk3keLnhBDTNGi', '2018-02-17 04:21:36', '2018-02-17 04:21:36'),
(641, 1, 'H7n0ThYpgpubpRLjms8M4qFM7bK8uLOh', '2018-02-17 07:53:15', '2018-02-17 07:53:15'),
(642, 1, 'ZSaRSDHxIge2Dq9wI8ZiOaDv5tw7bTC7', '2018-02-18 22:47:25', '2018-02-18 22:47:25'),
(643, 1, 'el4c2CSNnscy610pfSNncPhAwplr8APf', '2018-02-18 23:47:13', '2018-02-18 23:47:13'),
(644, 1, 'ABxvPydESbwXuATnWdQ008EwxmIzXuTM', '2018-02-19 00:12:55', '2018-02-19 00:12:55'),
(645, 1, 'YgcUg4BNxLn0VtO5lnQJyQA6FGpN42Id', '2018-02-19 04:12:37', '2018-02-19 04:12:37'),
(646, 1, 'Ng5p948NlAWuSRw0KouePZuvsWYPn6ZE', '2018-02-19 06:06:22', '2018-02-19 06:06:22'),
(647, 1, 'w4Ci0Dk9BDo5qOvhBdXe5VKkBNcCPhvm', '2018-02-19 06:09:48', '2018-02-19 06:09:48'),
(648, 1, 'sSvKgr5PyRVnBrc14xPFoj0T7TZoWEvd', '2018-02-19 22:26:23', '2018-02-19 22:26:23'),
(649, 1, '5DWnQWBkNNls484WbN8PAgRlZp2GdxV1', '2018-02-19 22:26:41', '2018-02-19 22:26:41'),
(650, 1, 'hqpTgjUxIphdwNDCKHmB1bK1zyw4rgCr', '2018-02-19 22:46:08', '2018-02-19 22:46:08'),
(651, 1, 'TlET41ik6bbKAqlGmr3e02MKSzhUTfE3', '2018-02-19 23:40:45', '2018-02-19 23:40:45'),
(652, 1, 'F6houH4CcL4TdNF5h0Ql7YdxBWqOW4O2', '2018-02-19 23:55:47', '2018-02-19 23:55:47'),
(653, 1, '7VEgswVe1wq2GxsYbesNIv8BpcNzzUBX', '2018-02-19 23:59:13', '2018-02-19 23:59:13'),
(654, 1, 'GQNjaVgDW6f2LK94JrXRBRo0qMbeOvMc', '2018-02-20 00:55:39', '2018-02-20 00:55:39'),
(655, 1, 'eNy4QuP8QdDrb2d1VuK9sXddhtaH8RgW', '2018-02-20 00:57:46', '2018-02-20 00:57:46'),
(656, 1, 'pI4BlilNYpP9mWmmYUBmayC5pagXARkq', '2018-02-20 01:27:43', '2018-02-20 01:27:43'),
(657, 1, '0ie38eD2MR5QVUSBwb1kiz5InnxQn0Im', '2018-02-20 03:22:13', '2018-02-20 03:22:13'),
(658, 1, 'OsRptq9xuEgRiXLjGUBAJRAEGWbJX1m5', '2018-02-20 03:30:39', '2018-02-20 03:30:39'),
(659, 1, '70e1qvPHwrYK3aXAxDwaPflDGFB5VliO', '2018-02-20 05:40:23', '2018-02-20 05:40:23'),
(660, 1, 'yxApTo19wldKzutlWWCXtuCWSaO7Tswt', '2018-02-20 05:41:38', '2018-02-20 05:41:38'),
(661, 1, 'RlTljzGZBb0GjapHBaUy4gXCyhEzA3Kp', '2018-02-20 05:47:57', '2018-02-20 05:47:57'),
(662, 1, 'x47JysjS8bgXNaPBEguiApi6OYeDAvK1', '2018-02-20 06:17:30', '2018-02-20 06:17:30'),
(663, 1, '56LV48cyaRLh0U5FPlBuej0rf4qdoab8', '2018-02-20 22:15:49', '2018-02-20 22:15:49'),
(664, 1, 'uCR5kjVhZ2AhmUFo8JZb6FbHrzIWYm39', '2018-02-20 22:24:08', '2018-02-20 22:24:08'),
(665, 1, 'Q6n6LbOaJ1viIjKfSpIwT3iZio0OoohZ', '2018-02-21 00:27:48', '2018-02-21 00:27:48'),
(666, 1, 'bDMER0C8igzmjfBtXHWyQYfaCqCLcy7U', '2018-02-21 04:44:10', '2018-02-21 04:44:10'),
(667, 1, 'FAgu9JoC2HazfprhJGPnMXs5UPvQBhWI', '2018-02-21 22:15:23', '2018-02-21 22:15:23'),
(668, 1, '87k1v5Dy0RvZwvZc9Xsj2yxUbb8hb4Oy', '2018-02-21 22:32:28', '2018-02-21 22:32:28'),
(669, 1, 'CWliDOCfdZj1w0BpNqtxvq35IUC7Ejxv', '2018-02-21 23:13:40', '2018-02-21 23:13:40'),
(670, 1, 'U1urC2NRwDWBAJhDcSxLDrohWmET7aIU', '2018-02-21 23:30:46', '2018-02-21 23:30:46'),
(671, 1, 'nrEXPMHfp67dE9j2xtBmadriBg6uJuKN', '2018-02-21 23:49:39', '2018-02-21 23:49:39'),
(672, 1, 'FNSdhKanE50OplzzrnVy3KQPYnEOFZxt', '2018-02-21 23:52:37', '2018-02-21 23:52:37'),
(673, 1, 'X5VMa8rwCDmlpoRpNjvJU3tDbMHJhkAN', '2018-02-21 23:58:26', '2018-02-21 23:58:26'),
(674, 1, 'sK4cNT1GLp9te9BzNJiDYzVs9kLTglje', '2018-02-22 03:09:13', '2018-02-22 03:09:13'),
(675, 1, 'wEChbawoJRiNO6t3zZaFsPj5FLc4HWin', '2018-02-22 03:58:54', '2018-02-22 03:58:54'),
(676, 1, 'RgljvWL4cdQJtikT13p3feZNbPVsdwKl', '2018-02-22 05:30:14', '2018-02-22 05:30:14'),
(677, 1, 'E61YbYlKP5ob8qfQTlBzdGAV3EsE26XG', '2018-02-22 05:57:47', '2018-02-22 05:57:47'),
(678, 1, 'L3IC5oxSlKy8Czc4YP4pMqQz3auNXHsX', '2018-02-22 06:58:23', '2018-02-22 06:58:23'),
(679, 1, 'SSJxlhRgpOjrCS3A5reY43CyXegKJzzl', '2018-02-22 08:24:00', '2018-02-22 08:24:00');
INSERT INTO `persistences` (`id`, `user_id`, `code`, `created_at`, `updated_at`) VALUES
(680, 1, 'n8EBj5mjUxWbcT7HjrMOk8fdt4S7Nmqy', '2018-02-22 22:28:08', '2018-02-22 22:28:08'),
(681, 1, 'LJ4TZ3YmAbIfEV1BWSoo9OwlxgbsFFb8', '2018-02-22 23:06:27', '2018-02-22 23:06:27'),
(682, 1, 'KRkbIBrPExC08WfVfwmdSfd6iMlodyHz', '2018-02-22 23:47:56', '2018-02-22 23:47:56'),
(683, 1, 'rE6BoLoxkx2Tvx7TirjCTleD5U4EKcoa', '2018-02-23 05:03:59', '2018-02-23 05:03:59'),
(684, 1, 'rdmqyzMIWWMIT4A8dJxvYdTB5wcrjTPk', '2018-02-23 05:20:25', '2018-02-23 05:20:25'),
(685, 1, '3ox8sxI1QMVsW3yHDqF7ploRZDNaN1wX', '2018-02-23 06:22:21', '2018-02-23 06:22:21'),
(686, 1, 'OaogVL8LaWPt296RxLea8tVDogT4qEB6', '2018-02-23 06:40:57', '2018-02-23 06:40:57'),
(687, 1, 'mt9Qk8bYCeeTvDvklVCUuAIDl9P9mjQy', '2018-02-23 06:50:17', '2018-02-23 06:50:17'),
(688, 1, 'RaAS0ZpEea7duvbyU1A1sz1FRoKRnLiE', '2018-02-25 22:16:08', '2018-02-25 22:16:08'),
(689, 1, 'XZ18NDhqQwGGLYV3DaXC1MQdUb4i1WRE', '2018-02-25 22:24:25', '2018-02-25 22:24:25'),
(690, 1, '2B1P3LlVkMA4gsxLT4NgJysPvSQP3zAP', '2018-02-26 07:03:31', '2018-02-26 07:03:31'),
(691, 1, 'DiZqPU1WpP2Cq1zdUiBbaw11EHwQfJUO', '2018-02-26 23:44:16', '2018-02-26 23:44:16'),
(692, 1, 'IKcYJu3L6jHAp6QNR33934uzL8ALFunr', '2018-02-27 00:12:47', '2018-02-27 00:12:47'),
(693, 1, 'hUZUcYypWT7ukRvBgnbl4VqIveS31j0T', '2018-02-27 00:43:41', '2018-02-27 00:43:41'),
(694, 1, 'jf13SAqPFe8zUZVlqPHZ4CAM3SbG7vaY', '2018-02-27 05:24:33', '2018-02-27 05:24:33'),
(695, 1, 'ze70ihgvujOcIxmNI9pFV7j2V58vxTuc', '2018-02-27 22:33:30', '2018-02-27 22:33:30'),
(696, 1, 'QneH5doraiS9PdJ7no3lJa1oaxGIKoUC', '2018-02-28 06:14:26', '2018-02-28 06:14:26'),
(697, 1, 'sNOfUldGYYaeae34bvCWH60fS1V8YHli', '2018-02-28 06:20:31', '2018-02-28 06:20:31'),
(698, 1, 'Rt1vdzLyvCQWv4QsfUn1U4NVuME3At7S', '2018-02-28 06:34:44', '2018-02-28 06:34:44'),
(699, 1, 'wy9UEmbUyrwmUlHDBzsQLfxb8LiecBS8', '2018-03-01 01:32:49', '2018-03-01 01:32:49'),
(700, 1, 'JvrLhM1iPfah6SpXub680cc8xvgjuHmd', '2018-03-02 03:29:41', '2018-03-02 03:29:41'),
(701, 1, 'it1SfqgUKhSnribFojs0R1yowsp4upMH', '2018-03-02 23:48:51', '2018-03-02 23:48:51'),
(702, 1, 'j56rvR1vIEJdwnJvlZ6r38dkQGT3F6yz', '2018-03-03 01:02:34', '2018-03-03 01:02:34'),
(703, 1, 'tR68SUcbrKWOdiI37OmRQgw7l0OjxRf9', '2018-03-03 01:04:01', '2018-03-03 01:04:01'),
(704, 1, 'kdpgnM9Wtqhanlj1Yegt8aNoAgjaM2Zq', '2018-03-03 01:31:32', '2018-03-03 01:31:32'),
(705, 1, '4vFNogtkhKmVaZCdkKksBxF5AwSt33ER', '2018-03-03 03:40:48', '2018-03-03 03:40:48'),
(709, 1, 'bNX37HewqzErrX0MoKdRuGiLhzMKML7Q', '2018-03-03 05:18:03', '2018-03-03 05:18:03'),
(710, 1, 'sYqCem2Fal3b54O6U7t7KMPllhApAwCB', '2018-03-03 05:47:11', '2018-03-03 05:47:11'),
(711, 1, 'MLN4DL9S79lBKUnpD0ZL93LsoZbfaGiF', '2018-03-03 06:45:13', '2018-03-03 06:45:13'),
(712, 1, 'Q27CWao98qXquLYtFpur0PGtAfkQDp24', '2018-03-03 07:23:36', '2018-03-03 07:23:36'),
(714, 1, 'ZMJgM5z5UTMjYdF2FmCe4xU36iJz8sNI', '2018-03-03 07:28:58', '2018-03-03 07:28:58'),
(715, 1, 'YfOGRht5B9f6JOx4h94YUVBcZwBvQNG9', '2018-03-03 07:31:17', '2018-03-03 07:31:17'),
(716, 1, 'uJJQwcJIsMBEFeVDAKN9vNekSVhrULsb', '2018-03-03 07:44:10', '2018-03-03 07:44:10'),
(717, 1, '5sLgu7hh9iMcCZcndLT9fzqXMP1omfxu', '2018-03-03 07:51:24', '2018-03-03 07:51:24'),
(718, 1, 'tZvESacCIF5IgXSL51lacbri5v4CrQE3', '2018-03-03 08:25:23', '2018-03-03 08:25:23'),
(719, 1, 'L7B11llMQNv2DEA5VNoJVkvs3mnF5eEe', '2018-03-04 22:30:54', '2018-03-04 22:30:54'),
(720, 1, 'PGRub1voVdj2rhBu26rmXYiO0urvjfsS', '2018-03-05 23:20:05', '2018-03-05 23:20:05'),
(723, 1, 'H7foxWTj9eQJ6gHauJqEznwEtpsOfXIn', '2018-03-06 06:31:40', '2018-03-06 06:31:40'),
(724, 1, 'bsHH7j3JzJXWIupyLeKXm8EKU96uR8Wz', '2018-03-06 23:18:05', '2018-03-06 23:18:05'),
(725, 1, 'ZrEyAWbWq9PFEyFiaxyG42jk5euTNIZw', '2018-03-06 23:22:27', '2018-03-06 23:22:27');

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`id`, `user_id`, `code`, `completed`, `completed_at`, `created_at`, `updated_at`) VALUES
(19, 1, '6RhbybyEJwpfJaeIvY6tZ87QS6L3Ltg5', 1, '2016-09-01 04:43:00', '2016-09-01 04:33:06', '2016-09-01 04:43:00'),
(26, 1, 'JUNUgyF9nR3eI4VDom5K9JrmcmtXyhwt', 1, '2017-04-14 23:33:13', '2017-04-14 23:32:34', '2017-04-14 23:33:13'),
(27, 1, 'faVdJy5sIYxSupF1UVmrc1p9VPNs1gWJ', 1, '2017-04-14 23:41:41', '2017-04-14 23:40:56', '2017-04-14 23:41:41'),
(46, 1, 'Lm3VObTyz74sf5lOWVnHVhZHOYxeXazC', 1, '2017-12-20 00:16:51', '2017-12-20 00:15:33', '2017-12-20 00:16:51');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'Admin', '{\"admin\":true}', '2016-04-25 07:50:54', '2016-05-06 06:05:21', NULL),
(5, 'sub_admin', 'Sub Admin', NULL, '2017-04-17 23:31:27', '2017-04-17 23:31:27', NULL),
(6, 'user', 'user', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

CREATE TABLE `role_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_users`
--

INSERT INTO `role_users` (`user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL),
(2, 5, NULL, NULL),
(3, 6, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `site_setting_id` int(11) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `site_address` varchar(255) NOT NULL,
  `site_contact_number` varchar(255) NOT NULL,
  `meta_desc` text NOT NULL,
  `meta_keyword` varchar(500) NOT NULL,
  `site_email_address` varchar(255) NOT NULL,
  `fb_url` varchar(255) NOT NULL,
  `twitter_url` varchar(255) NOT NULL,
  `google_plus_url` varchar(500) NOT NULL,
  `youtube_url` varchar(255) NOT NULL,
  `rss_feed_url` varchar(255) NOT NULL,
  `instagram_url` varchar(255) NOT NULL,
  `linked_in_url` varchar(255) NOT NULL,
  `site_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0 - Offline / 1- Online',
  `emergency_contact_one` varchar(50) NOT NULL,
  `emergency_contact_two` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `site_banner_image` varchar(288) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`site_setting_id`, `site_name`, `site_address`, `site_contact_number`, `meta_desc`, `meta_keyword`, `site_email_address`, `fb_url`, `twitter_url`, `google_plus_url`, `youtube_url`, `rss_feed_url`, `instagram_url`, `linked_in_url`, `site_status`, `emergency_contact_one`, `emergency_contact_two`, `created_at`, `updated_at`, `deleted_at`, `site_banner_image`) VALUES
(1, '911 Express', 'ABC, Pqr Street, India', '9876543210', '911 Express', '911 Express', 'info@911express.com', 'http://facebook.com', 'http://twitter.com', 'http://plus.google.com', 'http://youtube.com', 'http://rssfeed.com', 'http://www.instagram.com', 'https://in.linkedin.com', '0', '9876543210', '7845124578', '2016-05-30 22:59:12', '2018-03-03 07:31:48', '0000-00-00 00:00:00', '149242209358f48dcdb5f0f.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `static_pages`
--

CREATE TABLE `static_pages` (
  `id` int(11) NOT NULL,
  `page_slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `static_pages`
--

INSERT INTO `static_pages` (`id`, `page_slug`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(10, 'about-us', '1', '2016-05-26 06:54:01', '2018-03-03 05:19:03', NULL),
(11, 'contact-us', '1', '2016-05-26 06:54:01', '2018-01-15 07:15:46', NULL),
(13, 'faq', '0', '2016-05-26 06:54:01', '2018-01-05 05:48:45', NULL),
(14, 'terms-and-conditions', '0', '2016-05-26 06:54:01', '2018-01-05 05:48:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `static_pages_translation`
--

CREATE TABLE `static_pages_translation` (
  `id` int(11) NOT NULL,
  `static_page_id` int(11) NOT NULL,
  `page_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `page_desc` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `meta_keyword` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `static_pages_translation`
--

INSERT INTO `static_pages_translation` (`id`, `static_page_id`, `page_title`, `page_desc`, `locale`, `meta_keyword`, `meta_desc`) VALUES
(4, 10, 'About Us ', '<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s</p>', 'en', 'test Meta Keyword', 'This is the test meta description'),
(13, 13, 'faq', '<p>faq</p>', 'en', 'faq', 'faq'),
(16, 11, 'Contact Us', '<div class=\"heading\">Address</div>\r\n<div class=\"con-location\">900 Biscayne Boulevard, Miami, FL 33132, USA</div>\r\n<p>&nbsp;</p>\r\n<div class=\"heading\">Contact Details</div>\r\n<div class=\"contact-links\">\r\n<ul>\r\n<li><i class=\"fa fa-phone fa-2x\" aria-hidden=\"true\"></i>1-222-333-4444</li>\r\n<li><i class=\"fa fa-mobile fa-2x\" aria-hidden=\"true\"></i>1-234-456-7894</li>\r\n</ul>\r\n</div>\r\n<div class=\"contact-links\">\r\n<ul>\r\n<li><i class=\"fa fa-fax fa-2x\" aria-hidden=\"true\"></i>1-234-456-7894</li>\r\n<li><i class=\"fa fa-envelope-o fa-2x\" aria-hidden=\"true\"></i>info@rentalhouse.com</li>\r\n</ul>\r\n</div>', 'en', 'test', 'testss'),
(17, 14, 'Terms And Conditions', '<div class=\"pp-head\">Lorem Ipsum is simply</div>\r\n<div class=\"abt-head\">\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley &nbsp;desktop publ</p>\r\n</div>\r\n<div class=\"pp-head\">Contrary to popular belief</div>\r\n<div class=\"abt-head\">\r\n<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical.</p>\r\n</div>', 'en', 'Terms And Conditions', 'Terms And Conditions'),
(18, 15, 'test1', '<p>test1</p>', 'en', 'test1', 'test1'),
(19, 16, 'AA', '<p>AAA</p>', 'en', 'AA', 'AA'),
(24, 11, 'Contáctenos', '<div class=\"heading\">\n<pre class=\"tw-data-text vk_txt tw-ta tw-text-large\" data-placeholder=\"Translation\" id=\"tw-target-text\" data-fulltext=\"\" dir=\"ltr\"><span lang=\"es\">Direcci&oacute;n</span></pre>\n</div>\n<div class=\"con-location\"><span>900 Biscayne Boulevard, Miami , FL 33132 , EE.UU.</span></div>\n<p>&nbsp;</p>\n<div class=\"heading\">\n<pre class=\"tw-data-text vk_txt tw-ta tw-text-medium\" data-placeholder=\"Translation\" id=\"tw-target-text\" data-fulltext=\"\" dir=\"ltr\"><span lang=\"es\">Detalles de contacto</span></pre>\n</div>\n<div class=\"contact-links\">\n<ul>\n<li><i class=\"fa fa-phone fa-2x\" aria-hidden=\"true\"></i>1-222-333-4444</li>\n<li><i class=\"fa fa-mobile fa-2x\" aria-hidden=\"true\"></i>1-234-456-7894</li>\n</ul>\n</div>\n<div class=\"contact-links\">\n<ul>\n<li><i class=\"fa fa-fax fa-2x\" aria-hidden=\"true\"></i>1-234-456-7894</li>\n<li><i class=\"fa fa-envelope-o fa-2x\" aria-hidden=\"true\"></i>info@rentalhouse.com</li>\n</ul>\n</div>', 'es', '', ''),
(25, 10, 'Sobre nosotros', '<p><span lang=\"es\">Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industr</span></p>', 'es', '', ''),
(26, 13, 'Preguntas más frecuentes', '<div class=\"tw-ta-container tw-nfl\" id=\"tw-target-text-container\">Preguntas m&aacute;s frecuentes</div>', 'es', 'Preguntas más frecuentes', 'Preguntas más frecuentes'),
(27, 14, 'Términos y Condiciones', '<p><span lang=\"es\">Lorem Ipsum es simplemente Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto . Lorem Ipsum ha sido el texto de relleno est&aacute;ndar de la industria desde el a&ntilde;o 1500, cuando un desconocido tom&oacute; una impresora de escritorio publ galera Contrario a la creencia popular Contrariamente a la creencia popular , Lorem Ipsum no es simplemente texto aleatorio . Tiene sus ra&iacute;ces en una pieza de la literatura cl&aacute;sica latina de 45 aC , por lo que es m&aacute;s de 2000 a&ntilde;os de antig&uuml;edad. Richard McClintock , un profesor de lat&iacute;n en Hampden - Sydney College en Virginia, encontr&oacute; una de las palabras latinas m&aacute;s oscuros , Consectetur , a partir de un pasaje de Lorem Ipsum , y pasando por la cita de la palabra en el cl&aacute;sico.</span></p>', 'es', 'Términos y Condiciones', 'Términos y Condiciones'),
(28, 15, 'Política de privacidad', '<p><span lang=\"es\">Lorem Ipsum es simplemente Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto . Lorem Ipsum ha sido el texto de relleno est&aacute;ndar de la industria desde el a&ntilde;o 1500, cuando un desconocido tom&oacute; una impresora de cocina tipo y codificados para hacer un libro de textos especimen. Ha sobrevivido no s&oacute;lo cinco siglos , sino tambi&eacute;n el salto a la composici&oacute;n tipogr&aacute;fica electr&oacute;nica , quedando esencialmente sin cambios . Se populariz&oacute; en la d&eacute;cada de 1960 con el lanzamiento de las hojas de Letraset que contienen pasajes de Lorem Ipsum, y m&aacute;s recientemente con software de autoedici&oacute;n , como Aldus PageMaker incluidas las versiones de Lorem Ipsum .</span></p>', 'es', 'Política de privacidad', 'Política de privacidad'),
(29, 16, 'Blogs', '<p>Blogs</p>', 'es', 'Blogs', 'Blogs'),
(30, 17, 'test', '<p>test</p>', 'en', 'test', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `throttle`
--

CREATE TABLE `throttle` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `throttle`
--

INSERT INTO `throttle` (`id`, `user_id`, `type`, `ip`, `created_at`, `updated_at`) VALUES
(1, NULL, 'global', NULL, '2018-02-28 06:20:23', '2018-02-28 06:20:23'),
(2, NULL, 'ip', '192.168.1.24', '2018-02-28 06:20:23', '2018-02-28 06:20:23'),
(3, NULL, 'global', NULL, '2018-03-02 03:29:31', '2018-03-02 03:29:31'),
(4, NULL, 'ip', '192.168.1.24', '2018-03-02 03:29:31', '2018-03-02 03:29:31'),
(5, NULL, 'global', NULL, '2018-03-03 00:45:53', '2018-03-03 00:45:53'),
(6, NULL, 'ip', '192.168.1.115', '2018-03-03 00:45:53', '2018-03-03 00:45:53'),
(7, NULL, 'global', NULL, '2018-03-03 00:46:10', '2018-03-03 00:46:10'),
(8, NULL, 'ip', '192.168.1.115', '2018-03-03 00:46:10', '2018-03-03 00:46:10'),
(9, NULL, 'global', NULL, '2018-03-03 01:31:14', '2018-03-03 01:31:14'),
(10, NULL, 'ip', '192.168.1.82', '2018-03-03 01:31:14', '2018-03-03 01:31:14'),
(11, NULL, 'global', NULL, '2018-03-03 07:41:45', '2018-03-03 07:41:45'),
(12, NULL, 'ip', '192.168.1.64', '2018-03-03 07:41:45', '2018-03-03 07:41:45'),
(13, NULL, 'global', NULL, '2018-03-03 07:42:08', '2018-03-03 07:42:08'),
(14, NULL, 'ip', '192.168.1.64', '2018-03-03 07:42:08', '2018-03-03 07:42:08'),
(15, NULL, 'global', NULL, '2018-03-03 07:43:23', '2018-03-03 07:43:23'),
(16, NULL, 'ip', '192.168.1.64', '2018-03-03 07:43:23', '2018-03-03 07:43:23'),
(17, NULL, 'global', NULL, '2018-03-06 23:17:36', '2018-03-06 23:17:36'),
(18, NULL, 'ip', '192.168.1.82', '2018-03-06 23:17:36', '2018-03-06 23:17:36'),
(19, 1, 'user', NULL, '2018-03-06 23:17:36', '2018-03-06 23:17:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` enum('M','F') COLLATE utf8_unicode_ci NOT NULL,
  `profile_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `is_active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `is_user_block_by_admin` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT 'if the drivers receipt last entry within 3 days then status automatically by block.',
  `mobile_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `latitude` text COLLATE utf8_unicode_ci NOT NULL,
  `longitude` text COLLATE utf8_unicode_ci NOT NULL,
  `post_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `driving_license` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `via_social` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `otp` int(6) DEFAULT NULL,
  `is_otp_verified` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `otp_type` enum('SIGNUP','RESEND_OTP','FORGET_PASSWORD') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `gender`, `profile_image`, `permissions`, `is_active`, `is_user_block_by_admin`, `mobile_no`, `country_id`, `state_id`, `city`, `address`, `latitude`, `longitude`, `post_code`, `driving_license`, `via_social`, `last_login`, `otp`, `is_otp_verified`, `otp_type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin@maicarwash.com', '$2y$10$Z1uYu.qLnqDEHSLe/4srS.caJbYVw6oFvazLbXOjQchUI1JBFWHFO', 'Admin', 'Admin', 'M', '', '{\"change_password.list\":true,\"change_password.create\":true,\"change_password.update\":true,\"change_password.delete\":true,\"contact_enquiry.list\":true,\"contact_enquiry.create\":true,\"contact_enquiry.update\":true,\"contact_enquiry.delete\":true,\"users.list\":true,\"users.create\":true,\"users.update\":true,\"users.delete\":true,\"email_template.list\":true,\"email_template.create\":true,\"email_template.update\":true,\"email_template.delete\":true,\"site_settings.list\":true,\"site_settings.create\":true,\"site_settings.update\":true,\"site_settings.delete\":true,\"static_pages.list\":true,\"static_pages.create\":true,\"static_pages.update\":true,\"static_pages.delete\":true,\"sub_admin.list\":true,\"sub_admin.create\":true,\"sub_admin.update\":true,\"sub_admin.delete\":true,\"faq.list\":true,\"faq.create\":true,\"faq.update\":true,\"faq.delete\":true,\"admin_users.list\":true,\"admin_users.create\":true,\"admin_users.update\":true,\"admin_users.delete\":true,\"payment_settings.list\":true,\"payment_settings.create\":true,\"payment_settings.update\":true,\"payment_settings.delete\":true, \"activity_log.list\":true,\"activity_log.create\":true,\"activity_log.update\":true,\"activity_log.delete\":true}', '1', '0', '12312312312', 0, 0, '0', 'asdsadasd', '', '', '', '', '0', '2018-03-07 03:49:49', NULL, '1', 'SIGNUP', '2016-04-25 07:50:44', '2018-03-07 03:49:49', NULL),
(2, 'poojak@webwing.com', '$2y$10$Z1uYu.qLnqDEHSLe/4srS.caJbYVw6oFvazLbXOjQchUI1JBFWHFO', 'Pooja', 'Kothawade', 'M', '', '', '1', '0', '', NULL, NULL, NULL, '', '', '', '', '', '0', NULL, NULL, '0', 'SIGNUP', '2018-03-03 04:32:44', '2018-03-03 06:57:20', NULL),
(3, 'sayalib@webwing.com', '$2y$10$Z1uYu.qLnqDEHSLe/4srS.caJbYVw6oFvazLbXOjQchUI1JBFWHFO', 'Sayali', 'Bhirud', 'F', '', NULL, '1', '0', '', NULL, NULL, 'nashik', '73.898989', '21.232323', '', '422521', '', '0', NULL, NULL, '1', 'SIGNUP', NULL, '2018-03-03 06:19:56', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activations`
--
ALTER TABLE `activations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_enquiry`
--
ALTER TABLE `contact_enquiry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_template`
--
ALTER TABLE `email_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_template_translation`
--
ALTER TABLE `email_template_translation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_template_id` (`email_template_id`),
  ADD KEY `email_template_id_2` (`email_template_id`),
  ADD KEY `locale` (`locale`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_translation`
--
ALTER TABLE `faq_translation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faq_id` (`faq_id`),
  ADD KEY `locale` (`locale`);

--
-- Indexes for table `keyword_translations`
--
ALTER TABLE `keyword_translations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language_phrases`
--
ALTER TABLE `language_phrases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`),
  ADD KEY `password_resets_token_index` (`token`);

--
-- Indexes for table `payment_setting`
--
ALTER TABLE `payment_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `persistences`
--
ALTER TABLE `persistences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `persistences_code_unique` (`code`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_users`
--
ALTER TABLE `role_users`
  ADD PRIMARY KEY (`user_id`,`role_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`site_setting_id`);

--
-- Indexes for table `static_pages`
--
ALTER TABLE `static_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `static_pages_translation`
--
ALTER TABLE `static_pages_translation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `throttle`
--
ALTER TABLE `throttle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `throttle_user_id_index` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activations`
--
ALTER TABLE `activations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `contact_enquiry`
--
ALTER TABLE `contact_enquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `email_template`
--
ALTER TABLE `email_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `email_template_translation`
--
ALTER TABLE `email_template_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `faq_translation`
--
ALTER TABLE `faq_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `keyword_translations`
--
ALTER TABLE `keyword_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `language_phrases`
--
ALTER TABLE `language_phrases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_setting`
--
ALTER TABLE `payment_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `persistences`
--
ALTER TABLE `persistences`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=731;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `site_setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `static_pages`
--
ALTER TABLE `static_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `static_pages_translation`
--
ALTER TABLE `static_pages_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `throttle`
--
ALTER TABLE `throttle`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
