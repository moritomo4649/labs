-- phpMyAdmin SQL Dump
-- version 3.3.10.3
-- http://www.phpmyadmin.net
--
-- 生成時間: 2012 年 7 月 29 日 19:43
-- サーバのバージョン: 5.5.15
-- PHP のバージョン: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- テーブルの構造 `cache_urlinfo`
--

CREATE TABLE IF NOT EXISTS `cache_urlinfo` (
  `exist_url` varchar(255) DEFAULT NULL,
  `max_level` int(11) DEFAULT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `exist_url` (`exist_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
