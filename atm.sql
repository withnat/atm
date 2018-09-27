-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 27, 2018 at 05:05 PM
-- Server version: 5.7.23-0ubuntu0.18.04.1
-- PHP Version: 7.0.31-1+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `atm`
--

-- --------------------------------------------------------

--
-- Table structure for table `vd_Config`
--

DROP TABLE IF EXISTS `vd_Config`;
CREATE TABLE `vd_Config` (
  `id` int(11) NOT NULL,
  `key` varchar(250) CHARACTER SET utf8 NOT NULL,
  `value` mediumtext CHARACTER SET utf8,
  `package` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `ordering` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vd_Config`
--

INSERT INTO `vd_Config` (`id`, `key`, `value`, `package`, `ordering`) VALUES
(1, 'pagesize', '5', NULL, 0),
(2, 'editor', 'wysiwyg', NULL, 0),
(3, 'offline', '0', NULL, 0),
(4, 'offlineMessage_bak', NULL, NULL, 0),
(5, 'lifetime', '60', NULL, 0),
(6, 'offset', '0', NULL, 0),
(7, 'secretkey', NULL, NULL, 0),
(8, 'backendpath', '/web', NULL, 0),
(9, 'sef', '1', NULL, 0),
(10, 'sitename', 'ATM', NULL, 0),
(11, 'frontendtemplate', 'none', NULL, 0),
(12, 'backendtemplate', 'vanda', NULL, 0),
(13, 'timezone', 'Asia/Bangkok', NULL, 0),
(14, 'metadesc', '', NULL, 0),
(15, 'metakey', NULL, NULL, 0),
(16, 'sev', '1', NULL, 0),
(17, 'numberOfPages', '10', NULL, 0),
(18, 'bn20', '10', NULL, 0),
(19, 'bn50', '10', NULL, 0),
(20, 'bn100', '9', NULL, 0),
(21, 'bn500', '10', NULL, 0),
(22, 'bn1000', '10', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `vd_Language`
--

DROP TABLE IF EXISTS `vd_Language`;
CREATE TABLE `vd_Language` (
  `id` int(10) NOT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `folder` char(8) CHARACTER SET utf8mb4 NOT NULL,
  `sef` varchar(50) CHARACTER SET utf8mb4 NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `description` varchar(250) CHARACTER SET utf8mb4 DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `options_bak` mediumtext NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creator` int(10) DEFAULT '0',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updater` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vd_Language`
--

INSERT INTO `vd_Language` (`id`, `title`, `folder`, `sef`, `icon`, `description`, `status`, `default`, `ordering`, `options_bak`, `created`, `creator`, `updated`, `updater`) VALUES
(1, 'English', '', '', NULL, '', 1, 1, 1, 'a:3:{s:9:\"metatitle\";s:0:\"\";s:7:\"metakey\";s:0:\"\";s:8:\"metadesc\";s:0:\"\";}', '2011-06-07 13:14:03', NULL, '2013-03-01 16:07:51', 1),
(9, 'Thai', '', 'th', NULL, 'Main site', 1, 0, 2, 'Array', '2011-06-21 10:28:40', NULL, '2013-10-27 21:19:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vd_Package`
--

DROP TABLE IF EXISTS `vd_Package`;
CREATE TABLE `vd_Package` (
  `id` int(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `folder` varchar(250) NOT NULL,
  `side` char(8) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `params` mediumtext,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creator` int(10) NOT NULL DEFAULT '0',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updater` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `vd_Page`
--

DROP TABLE IF EXISTS `vd_Page`;
CREATE TABLE `vd_Page` (
  `id` int(10) NOT NULL,
  `languageId` int(10) NOT NULL,
  `type_bak` enum('Page','ErrorPage','RedirectorPage','VirtualPage') NOT NULL,
  `package` varchar(100) DEFAULT NULL,
  `subpackage` varchar(100) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `params` varchar(250) DEFAULT NULL,
  `title` varchar(250) NOT NULL,
  `alias` varchar(250) NOT NULL,
  `content` mediumtext,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creator` int(10) NOT NULL DEFAULT '0',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updater` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vd_Page`
--

INSERT INTO `vd_Page` (`id`, `languageId`, `type_bak`, `package`, `subpackage`, `action`, `params`, `title`, `alias`, `content`, `status`, `default`, `ordering`, `created`, `creator`, `updated`, `updater`) VALUES
(1, 0, 'Page', NULL, NULL, NULL, NULL, 'About', 'about', 'About our company', 1, 1, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0),
(2, 0, 'Page', 'contact', NULL, NULL, NULL, '', 'contact', NULL, 1, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vd_Session`
--

DROP TABLE IF EXISTS `vd_Session`;
CREATE TABLE `vd_Session` (
  `id` varchar(32) NOT NULL,
  `access` int(10) UNSIGNED DEFAULT NULL,
  `data` text,
  `userId` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vd_Session`
--

INSERT INTO `vd_Session` (`id`, `access`, `data`, `userId`) VALUES
('00ifavd1cvq2sddbia6jbfek95', 1521024043, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1521024043;}', 0),
('0b56frgph3ma4ug9ncqovncff4', 1520957788, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520957788;s:12:\"__vandaToken\";s:32:\"411ec470b4905a8cc0fef1a562db467f\";}', 0),
('0dam2j19gmlptt257dpp21gtu5', 1515491156, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1515491156;s:12:\"__vandaToken\";s:32:\"ac1b1d168106dc2b1e262b9657fed2b3\";}', 0),
('0elp9klpq2v2ft2d62d1t1qsl0', 1519370823, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519370823;s:12:\"__vandaToken\";s:32:\"fa70627bc41d059458e177a3b5524d91\";}', 0),
('0fbroh3m2om8c5k6p1j9r86jp0', 1521199837, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1521199837;s:12:\"__vandaToken\";s:32:\"e81b55056db4766658ed3c828878e404\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('19bp7gbhh7r5907iabanb4u8s0', 1516745297, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1516745297;s:12:\"__vandaToken\";s:32:\"7a2d0760895a62ef34df500f01c4d6e2\";}', 0),
('1i69f4ih9bse18v2bjea68bg52', 1511174582, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1511174582;s:12:\"__vandaToken\";s:32:\"570fb01cb8f33a468862d10354257e89\";}', 0),
('20sk1dbhipj26fvdb94cscq041', 1520781869, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520781869;s:12:\"__vandaToken\";s:32:\"8459d5cac9b3649a8226348197b1a312\";}', 0),
('25t7nrbcs3vastkc0cvqfikan0', 1519716249, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1519716249;s:12:\"__vandaToken\";s:32:\"6644ede683430566b1363e06a3c3cd9d\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('2dndigc04g1eilusm6ue1dvdc0', 1511665505, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1511665505;}', 0),
('2ghd6e5vgbqau53rptov5hl0l6', 1520675475, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520675475;s:12:\"__vandaToken\";s:32:\"3f6ea25f6bf77ec21e5d5e91cb897de3\";}', 0),
('2tpjo1o3mkrk4is4ohadqtu3j4', 1538042558, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1538042558;s:12:\"__vandaToken\";s:32:\"31e60ee09783a3c3dc098da7ad81b00b\";}', 0),
('3c74o4dlouvgj14tb5d0hll3f7', 1511172606, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1511172606;s:12:\"__vandaToken\";s:32:\"7a4917e5240d511fcf6e59a4d01c73c9\";}', 0),
('3fotsf1rvhv73m6nh70uajedp3', 1521693299, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1521693299;}', 0),
('3msdjmu1q0vjml4v3psnd3d333', 1519371330, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519371330;s:12:\"__vandaToken\";s:32:\"58e446849851cb41e4db83104f418214\";}', 0),
('4odfv9g754eljvbk4a0u4fah32', 1521085207, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1521085207;s:12:\"__vandaToken\";s:32:\"a028e30b735607997b23bace3a968898\";}', 0),
('4tpt6uoiosarsc1cqmef5vutb0', 1520963270, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520963270;s:12:\"__vandaToken\";s:32:\"c4635553b5f87055151659514a681208\";}', 0),
('5gb90gl5u3s0hf3q3h4214b883', 1519298237, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519298237;s:12:\"__vandaToken\";s:32:\"b67826e125a852791ec75cda8bd6ae37\";}', 0),
('5gf4bgvpnj119rptqn2v70ni45', 1524544343, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1524544343;s:12:\"__vandaToken\";s:32:\"420dd01dac7d362390c7a6d2572e5aaf\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('5otv46h6k0s7o1lc18rhb4hbv3', 1521034806, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1521034806;}', 0),
('60tepci1n6ss96d8acftuh63h0', 1511837161, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1511837161;s:12:\"__vandaToken\";s:32:\"a1f5122eff07f9355f6c02cda8bd9e15\";}', 0),
('6dnkeurgdgms4g73e3jmak79o6', 1522248616, '__vandaSession|a:2:{s:12:\"__vandaToken\";s:32:\"fb8bb4a8afd023caea0d7d2a2bb07384\";s:23:\"__vandaLastActivityTime\";i:1522248616;}', 0),
('70mora1ms6a2i4knbp1ian4o73', 1515491909, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1515491908;s:12:\"__vandaToken\";s:32:\"81b2a1064dc8aa101877bb7aa3370b2f\";}', 0),
('78ljs4qkn3o25hf046f6t4ler5', 1511946449, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1511946449;s:12:\"__vandaToken\";s:32:\"dc6e971432d4a03c11f5ae3ba6cff9d3\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('7od7g5p02ro1n1ip7el486q3e1', 1520437328, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520437328;s:12:\"__vandaToken\";s:32:\"a71bfea54d354c494505e0217484a59c\";}', 0),
('8a230o1aj8tcnnhd9k9gcmjvf2', 1520592616, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520592616;s:12:\"__vandaToken\";s:32:\"119ecffb3f45a5171c656d1551b01ff4\";}', 0),
('95eifrtir93ujqfk86bgcmdm00', 1519535135, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1519535135;s:12:\"__vandaToken\";s:32:\"02c4a3aa361385a4d326553ce4df48c5\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('990vtajdnjjd724nkk7govfmf3', 1511687137, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1511687137;s:12:\"__vandaToken\";s:32:\"59653b2ca843428d6ac6801cd774e8b4\";}', 0),
('9b93hj2q5n37kmfg17jn84j0o2', 1511336791, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1511336791;s:12:\"__vandaToken\";s:32:\"161563cb33beb9c8cb21a1d468c17aa4\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('9fj7rj2cmhmlav28u80ih69fc1', 1521048168, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1521048168;}', 0),
('9qivbtsge45cpogpgu01v7j4p6', 1512765317, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1512765317;s:12:\"__vandaToken\";s:32:\"e727c3ca077ec190c864452ae1366cd9\";}', 0),
('9rl9g3f31agt49akmkkcgae4c0', 1511172606, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1511172606;}', 0),
('a912jlc9jo3bqpafoh2ucvmth0', 1513936044, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1513936044;s:12:\"__vandaToken\";s:32:\"7f87c504bf105213c0125ab28f036dfd\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('alljs29oq3qr4205db8nh3ppt0', 1522654265, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1522654262;s:12:\"__vandaToken\";s:32:\"8d483b28a1e35582376b53618175fef3\";}', 0),
('am508tpog1udihdflaci31t741', 1520540512, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520540512;s:12:\"__vandaToken\";s:32:\"23c50e7d881bbdfd470318be32532dfb\";}', 0),
('amgchkg5qoga9sklt7ue1ce0k6', 1511853429, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1511853429;s:12:\"__vandaToken\";s:32:\"8f280a162e194076e1df627682d1a6ac\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('b75aibmabliglf5jdhefmind47', 1519294400, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519294400;s:12:\"__vandaToken\";s:32:\"4fe45e0bebe95f631baad5f38c7e09b8\";}', 0),
('c425vmlu21k8hkbdmbvv3217f6', 1520843026, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520843026;s:12:\"__vandaToken\";s:32:\"88ea89a9fba0ccd88eabce9ea8d3481c\";}', 0),
('ddt021oi1dvd5cjqn9kod83om0', 1521034806, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1521034806;s:12:\"__vandaToken\";s:32:\"4fe8d010149427ea44d9467222eeefbb\";}', 0),
('dg18h59sip0ecabjb94s89ehr2', 1511176780, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1511176780;s:12:\"__vandaToken\";s:32:\"881bc2ee975f7c00883c2a5f792dad69\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:9;}', 1),
('djrsfije8i3bf9thhcouu6ni85', 1519370822, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1519370822;}', 0),
('dqrrfdgfv7aceo9aqtnmephgh4', 1518160833, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1518160833;s:12:\"__vandaToken\";s:32:\"1b7a6b91a5391029382bb69f69848098\";}', 0),
('dv7kgp7j221cim7r33ddqrn3q5', 1519366231, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1519366231;}', 0),
('e5630tatqdtkibtsuir4vs8234', 1521219714, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1521219714;s:12:\"__vandaToken\";s:32:\"ede1f71d9b7c7e9b0e2255d8a701e40d\";}', 0),
('e6s4k1hp5sma5t8k8fgj36ldh6', 1520568034, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520568034;s:12:\"__vandaToken\";s:32:\"ac9a1592ae00fb2f7eae42b6f3afd0ad\";}', 0),
('ei5j3pu1faghcits4cnda6cee6', 1520848204, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520848204;s:12:\"__vandaToken\";s:32:\"2db761ca1baaa7c106b68252f0bc200a\";}', 0),
('ermuaq294semn84m2ajbnb9gg3', 1519371330, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519371330;s:12:\"__vandaToken\";s:32:\"b5da784ed155e695023f7e8b133b4896\";}', 0),
('f3v97lni3uthtov2t99omik0v1', 1511172606, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1511172606;}', 0),
('f4m40svq1ejk584b2makjmb706', 1515235205, '__vandaSession|a:1:{s:12:\"__vandaToken\";s:32:\"e768c0dcd110889273a3602008170c7b\";}', 0),
('f5moikrkqb47a4urmusjim4hv1', 1515423131, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1515423131;s:12:\"__vandaToken\";s:32:\"da2d9c75b1bf5738c8e7d551bb608cb7\";}', 0),
('fse0r5u6qff9uc3k1s734vebk3', 1521048168, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1521048168;s:12:\"__vandaToken\";s:32:\"4d2fa3e64d0dba3a9ecf4a7a170e2b6f\";}', 0),
('g3j5jqd0hnto3e4urg58thhom7', 1520685590, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520685590;s:12:\"__vandaToken\";s:32:\"0799579b3f7d13a7f7f200e13676b0fa\";}', 0),
('g8vvtr4d3fiid31gv7t1a7em40', 1516347605, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1516347605;}', 0),
('gdgej9gj4g8mc5kre1ltaqjub3', 1520675479, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520675479;s:12:\"__vandaToken\";s:32:\"f13b3319c1fa947a93d1c5d3daf6f28f\";}', 0),
('givj9optge7upl0s4d50thd7i3', 1520957788, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1520957788;}', 0),
('gqqh2d8tjs4tvo6vgil1151765', 1519369899, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1519369899;}', 0),
('h3324uv4itndfajr9hiu5g0p92', 1511777411, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1511777411;s:12:\"__vandaToken\";s:32:\"158b8c0e1a66dc9e28c61d34094121ff\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('h4vg2i23aa22ohgfm1ue2ehr94', 1516745297, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1516745297;}', 0),
('h73ijk7kdmshrrquno35coa7h4', 1516347606, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1516347606;s:12:\"__vandaToken\";s:32:\"9d8d13927b011d6ff11dcefd91b33058\";}', 0),
('hv0rqm6orj93h56eba7t38caj5', 1514795463, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1514795463;s:12:\"__vandaToken\";s:32:\"5e976dbd2d5c80d043bfe397b6fdc7ad\";}', 0),
('ige5qkh8q08ej0lbkfnm87ent0', 1519370822, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519370822;s:12:\"__vandaToken\";s:32:\"03b865867eaa03287dcc21f12792ccc1\";}', 0),
('j0lsjquqnm5gt81ju9hb6m5b13', 1520592615, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1520592615;}', 0),
('k9tmd0130utae15aq72e6cj8h4', 1511242743, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1511242743;s:12:\"__vandaToken\";s:32:\"a4534175ac4e870cf3a03d5243e318b1\";s:13:\"__vandaAuthId\";s:1:\"1\";s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";s:1:\"1\";}', 1),
('l5tis1jgjnkkbialet5o9stb66', 1517988946, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1517988946;s:12:\"__vandaToken\";s:32:\"f82fbab07a114df30e4f99100a674d5f\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('lei2jhf920qceuj741rm64cf01', 1520511089, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520511089;s:12:\"__vandaToken\";s:32:\"938776ba716006f7912309319a0dd954\";}', 0),
('lf39t6q64j9ersql1t6fq9i233', 1511665830, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1511665830;s:12:\"__vandaToken\";s:32:\"5d4f49372f7e7f005f7093dc034e6b57\";}', 0),
('lico9mfdvq79bjb2l46mjmksh0', 1520685589, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1520685589;}', 0),
('lkvil8gemff7obabpuhuhu1ho2', 1521048180, '__vandaSession|a:1:{s:12:\"__vandaToken\";s:32:\"95d70152f5360a0966a57143ebeea1b8\";}', 0),
('mgjotlsj8pvf8kkumak4g9u4f4', 1512297700, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1512297700;s:12:\"__vandaToken\";s:32:\"2d74022a94fa136a570f4f0af1b5aecf\";}', 0),
('n12lvgcrqq8223cfeerqh755g7', 1519366232, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519366232;s:12:\"__vandaToken\";s:32:\"1459aa81a7f5b46cc17d35e81df8c758\";}', 0),
('n4u028idv7pmm2h5vgf2iuqen4', 1511404281, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1511404281;s:12:\"__vandaToken\";s:32:\"aa14619237d7abb3f3babe8fcec37f4f\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('n6jn7crggnj8m5qe562o996016', 1513419481, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1513419481;s:12:\"__vandaToken\";s:32:\"c16f43718aa4a2a75371b01b7d744b41\";}', 0),
('n84doaac8u56u3k8kjjgnaunf1', 1511665505, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1511665505;s:12:\"__vandaToken\";s:32:\"fa31e432f299fc3837094ead31aa709d\";}', 0),
('o434apocqnn151pvsgor4ee1j3', 1520338775, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1520338775;s:12:\"__vandaToken\";s:32:\"0dbd0440be5ece08291c6c74ff203e98\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('ocvg2q23ko02udmuhlb71uifb0', 1518160067, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1518160067;s:12:\"__vandaToken\";s:32:\"1366cc5ec891f2f179d4a0d1826ea4ea\";}', 0),
('ou2bkdiikou55cjh0vo5gm2457', 1522164652, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1522164652;s:12:\"__vandaToken\";s:32:\"99cc34de6028764287e02c04e1b88aa3\";}', 0),
('phja92871bse4k0mlik3as94g0', 1519371330, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519371330;s:12:\"__vandaToken\";s:32:\"98fcab4c938e9f5607184ea875ff80ba\";}', 0),
('pnjobj20pgb8f70goapsmineg2', 1515135785, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1515135785;s:12:\"__vandaToken\";s:32:\"fac189a701a8867480d27d380c61f6b0\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('poalm37bsfsnsbhp21a716kst2', 1520962799, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520962799;s:12:\"__vandaToken\";s:32:\"0e37dd280219c60ba2808c0ef119acc2\";}', 0),
('pvj97259ullno03f7u6aoegtv6', 1519649947, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519649947;s:12:\"__vandaToken\";s:32:\"b311d9d02c4eedbf388650eaf379f34b\";}', 0),
('qjvsf2frnhnpekce7ebnc3me50', 1515260226, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1515260226;s:12:\"__vandaToken\";s:32:\"77a891d2cee1a329106a1a83f8bce40b\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('r26nhgbdenau4rk4l17sl2gqe2', 1519293732, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519293732;s:12:\"__vandaToken\";s:32:\"2fc0c3c824b8073d36438185ca943eb1\";}', 0),
('rbii127hi7s5ram9o9t5au0r93', 1519366230, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519366230;s:12:\"__vandaToken\";s:32:\"1544706ff685caadad2809f6e8bea6ec\";}', 0),
('rf5ri5je9sd11a9hh0881monj2', 1520861561, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520861561;s:12:\"__vandaToken\";s:32:\"846f998c525292259b583d87ec4d37f9\";}', 0),
('sakl3g1e759om1flrqeskpa1u6', 1519366230, '__vandaSession|a:1:{s:23:\"__vandaLastActivityTime\";i:1519366230;}', 0),
('smf508dotr4ts1a1cbeilkv611', 1521690693, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1521690693;s:12:\"__vandaToken\";s:32:\"547e3e615cd4e97c053979b1a956a8e0\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('snsdtkh6peji0l3blu1lm5jdh2', 1520725338, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520725338;s:12:\"__vandaToken\";s:32:\"78b1c812e9bb6208ab747d9b5547256a\";}', 0),
('t51dlcgti0ccdo7tgmufapd022', 1519275752, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1519275752;s:12:\"__vandaToken\";s:32:\"ffe226be68ff7d8a0eb8f6ac896a40f7\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('t7uiitvvbnm6o3fduui1i5lnn2', 1520861086, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1520861086;s:12:\"__vandaToken\";s:32:\"d7c59ddbf026d3d885dee6edcc7acd88\";}', 0),
('tjdmmaqpa6j0jvv3teg1737mn5', 1522683642, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1522683642;s:12:\"__vandaToken\";s:32:\"87f6ff15ebb471d681cd45e19621e0b9\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('tmf3dnbc84i2n5hatpsvbd1s76', 1522723112, '__vandaSession|a:8:{s:23:\"__vandaLastActivityTime\";i:1522723112;s:12:\"__vandaToken\";s:32:\"b350e0bab321990fe713f0adbc2dfa6c\";s:13:\"__vandaAuthId\";i:1;s:22:\"__vandaAuthUserGroupId\";i:1;s:23:\"__vandaAuthDepartmentId\";i:0;s:15:\"__vandaAuthName\";s:5:\"Admin\";s:19:\"__vandaAuthUserName\";s:5:\"admin\";s:21:\"__vandaAuthLanguageId\";i:1;}', 1),
('uc9a72han85bv0rlrg19d3csa3', 1511177640, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1511177640;s:12:\"__vandaToken\";s:32:\"b3c1207088e12a35e35ca90422c02b86\";}', 0),
('v91mquoakafe3ph2igu5d6nuq3', 1521166394, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1521166394;s:12:\"__vandaToken\";s:32:\"f80cca4496ad50b85d1d4769234b7774\";}', 0),
('vtrf37d50qsb6ktbl7ljckd4e3', 1519371330, '__vandaSession|a:2:{s:23:\"__vandaLastActivityTime\";i:1519371330;s:12:\"__vandaToken\";s:32:\"bb51e0046b527a45435937c774f9f7bd\";}', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vd_User`
--

DROP TABLE IF EXISTS `vd_User`;
CREATE TABLE `vd_User` (
  `id` int(10) NOT NULL,
  `userGroupId` int(10) DEFAULT '0',
  `departmentId` int(10) DEFAULT '0',
  `name` varchar(250) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `languageId` int(10) NOT NULL DEFAULT '0',
  `imageAvatar` mediumtext,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `creator` int(10) NOT NULL DEFAULT '0',
  `updated` datetime DEFAULT NULL,
  `updater` int(10) NOT NULL DEFAULT '0',
  `visited` datetime DEFAULT NULL,
  `tmpKickout` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vd_User`
--

INSERT INTO `vd_User` (`id`, `userGroupId`, `departmentId`, `name`, `username`, `password`, `email`, `languageId`, `imageAvatar`, `status`, `created`, `creator`, `updated`, `updater`, `visited`, `tmpKickout`) VALUES
(1, 1, 0, 'Admin', 'admin', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'nat@withnat.com', 1, 'data:image/jpeg;base64,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', 1, '2016-05-08 23:20:38', 1, '2017-11-21 12:35:04', 1, '2018-04-24 11:32:20', 0);
INSERT INTO `vd_User` (`id`, `userGroupId`, `departmentId`, `name`, `username`, `password`, `email`, `languageId`, `imageAvatar`, `status`, `created`, `creator`, `updated`, `updater`, `visited`, `tmpKickout`) VALUES
(2, 2, 0, 'Sukanya', 'sukanya', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Sukanya@domain.com', 9, '', 1, '2016-05-29 23:09:37', 1, NULL, 0, NULL, 0),
(3, 3, 1, 'Somsak', 'somsaky', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Somsak@domain.com', 1, NULL, 1, '2016-05-29 23:11:52', 1, '2016-10-31 12:33:47', 3, '2017-11-20 17:25:08', 0),
(4, 3, 1, 'Unn', 'unn', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Unn@domain.com', 9, '', 1, '2016-05-29 23:12:11', 1, NULL, 0, NULL, 0),
(5, 3, 1, 'Jeab', 'jeab', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Jeab@domain.com', 9, '', 1, '2016-05-29 23:12:44', 1, NULL, 0, '2017-02-07 17:01:22', 0),
(6, 3, 1, 'Natty', 'natty', '$2y$10$AqvX15ROBCNxr3re8UP5/.yPnLMe4MBQAP7rklS53Yu10JjrT3MvC', 'Natty@domain.com', 1, NULL, 1, '2016-05-29 23:40:18', 1, '2017-11-20 18:02:10', 6, '2017-11-20 18:02:17', 0),
(7, 3, 1, 'AWN', 'awn', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'AWN@domain.com', 9, NULL, 1, '2016-06-06 11:47:34', 1, NULL, 0, NULL, 0),
(8, 3, 1, 'Atsakorn', 'atsakorn', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Atsakorn@domain.com', 1, NULL, -2, '2016-06-11 09:53:32', 1, '2016-09-30 00:43:50', 8, '2016-10-31 08:38:23', 0),
(9, 3, 1, 'Pongnarin', 'pongnarin', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Pongnarin@domain.com', 9, NULL, 1, '2016-06-11 09:53:58', 1, NULL, 0, NULL, 0),
(10, 3, 2, 'Anucha', 'anucha', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Anucha@domain.com', 9, NULL, 1, '2016-06-11 09:54:20', 1, '2017-11-15 05:56:29', 1, NULL, 0),
(11, 3, 1, 'Sutham', 'sutham', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Sutham@domain.com', 9, NULL, 1, '2016-06-11 09:54:43', 1, NULL, 0, NULL, 0),
(12, 3, 1, 'Savake', 'savake', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Savake@domain.com', 9, NULL, 1, '2016-06-11 09:55:11', 1, NULL, 0, NULL, 0),
(13, 3, 1, 'Phattapee', 'phattapee', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Phattapee@domain.com', 9, NULL, 1, '2016-06-11 09:55:43', 1, NULL, 0, NULL, 0),
(14, 3, 1, 'Sittichai', 'sittichai', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Sittichai@domain.com', 9, NULL, 1, '2016-06-11 09:56:06', 1, NULL, 0, NULL, 0),
(15, 3, 1, 'Mahintra', 'mahintra', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Mahintra@domain.com', 9, NULL, -2, '2016-06-17 14:34:25', 1, NULL, 0, '2016-10-22 18:13:05', 0),
(16, 3, 1, 'Jatuporn', 'jatuporn', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Jatuporn@domain.com', 9, NULL, 1, '2016-07-02 15:28:06', 1, NULL, 0, '2016-12-16 14:09:38', 0),
(17, 3, 1, 'Manat', 'manatn', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Manat@domain.com', 9, NULL, 1, '2016-07-06 07:46:16', 1, NULL, 0, '2016-12-31 21:46:23', 0),
(18, 3, 1, 'Varinton', 'varintonc', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Varinton@domain.com', 9, NULL, 1, '2016-07-06 07:46:16', 1, NULL, 0, '2016-11-07 09:10:52', 0),
(19, 3, 1, 'Jakrapong', 'jakrapongb', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Jakrapong@domain.com', 9, NULL, 1, '2016-07-06 07:46:16', 1, NULL, 0, '2017-01-05 12:35:41', 0),
(20, 3, 1, 'Ittikorn', 'ittikornw', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Ittikorn@domain.com', 9, NULL, 1, '2016-07-06 07:46:16', 1, NULL, 0, '2016-12-29 21:57:23', 0),
(21, 3, 1, 'Suriyawut', 'suriyawutc', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Suriyawut@domain.com', 9, NULL, 1, '2016-07-06 07:46:16', 1, NULL, 0, '2017-01-05 13:35:23', 0),
(22, 3, 1, 'Channarong', 'channarongl', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Channarong@domain.com', 9, NULL, 1, '2016-07-06 07:46:16', 1, NULL, 0, '2017-01-05 11:12:57', 0),
(23, 3, 2, 'Anurak', 'anurakp', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Anurak@domain.com', 9, NULL, 1, '2016-07-06 07:46:16', 1, '2017-11-15 05:30:48', 1, '2017-01-05 13:28:36', 0),
(24, 3, 1, 'Kittipong', 'kittipongw', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Kittipong@domain.com', 9, NULL, 1, '2016-07-06 07:46:16', 1, NULL, 0, '2017-01-03 17:15:14', 0),
(25, 3, 1, 'Prayoon', 'prayoonk', '$2y$10$0pGZtMpNWCXt2jfko4.WCOEHQ3CldPooDWRgk9Lx/MNw5XB900lVO', 'Prayoon@domain.com', 9, NULL, 1, '2016-07-06 07:46:16', 1, NULL, 0, '2017-01-05 13:09:07', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vd_UserGroup`
--

DROP TABLE IF EXISTS `vd_UserGroup`;
CREATE TABLE `vd_UserGroup` (
  `id` int(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `permission` mediumtext NOT NULL,
  `fixPermission` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `creator` int(10) NOT NULL DEFAULT '0',
  `updated` datetime DEFAULT NULL,
  `updater` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vd_UserGroup`
--

INSERT INTO `vd_UserGroup` (`id`, `name`, `permission`, `fixPermission`, `ordering`, `status`, `created`, `creator`, `updated`, `updater`) VALUES
(1, 'Super User', ',evaluate:d,report:r,user:d,master:d,config:w,', 1, 1, 1, '2016-04-27 01:18:18', 0, '2017-11-15 05:24:40', 1),
(2, 'Administrator', ',evaluate:d,report:r,user:d,master:d,config:n,', 0, 2, 1, '2016-04-27 01:19:03', 0, '2017-11-15 05:25:50', 1),
(3, 'Employee', ',evaluate:d,report:r,user:n,master:n,config:n,', 0, 3, 1, '2016-04-27 01:21:20', 1, '2017-11-15 05:26:25', 1),
(4, 'Read Only', ',evaluate:r,report:r,user:r,master:r,config:n,', 0, 4, 1, '2016-05-27 04:27:15', 1, '2017-11-15 05:26:41', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vd_Widget`
--

DROP TABLE IF EXISTS `vd_Widget`;
CREATE TABLE `vd_Widget` (
  `id` int(10) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` mediumtext,
  `folder` varchar(250) NOT NULL,
  `side` char(8) NOT NULL,
  `position` varchar(100) NOT NULL,
  `ordering` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `params` mediumtext,
  `pages` mediumtext,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creator` int(10) NOT NULL DEFAULT '0',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updater` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vd_Widget`
--

INSERT INTO `vd_Widget` (`id`, `title`, `content`, `folder`, `side`, `position`, `ordering`, `status`, `params`, `pages`, `created`, `creator`, `updated`, `updater`) VALUES
(3, 'Main Menu', NULL, 'menu.main', 'backend', '{{mainmenu}}', 1, 1, 'showtitle=1&classsuffix=', NULL, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vd_Config`
--
ALTER TABLE `vd_Config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vd_Language`
--
ALTER TABLE `vd_Language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vd_Package`
--
ALTER TABLE `vd_Package`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vd_Page`
--
ALTER TABLE `vd_Page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vd_Session`
--
ALTER TABLE `vd_Session`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vd_User`
--
ALTER TABLE `vd_User`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userGroupId` (`userGroupId`),
  ADD KEY `subcontractorId` (`departmentId`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `vd_UserGroup`
--
ALTER TABLE `vd_UserGroup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vd_Widget`
--
ALTER TABLE `vd_Widget`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vd_Config`
--
ALTER TABLE `vd_Config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `vd_Language`
--
ALTER TABLE `vd_Language`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `vd_Package`
--
ALTER TABLE `vd_Package`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vd_Page`
--
ALTER TABLE `vd_Page`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vd_User`
--
ALTER TABLE `vd_User`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `vd_UserGroup`
--
ALTER TABLE `vd_UserGroup`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vd_Widget`
--
ALTER TABLE `vd_Widget`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
