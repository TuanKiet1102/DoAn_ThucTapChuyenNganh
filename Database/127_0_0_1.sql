-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 21, 2025 at 01:02 PM
-- Server version: 9.1.0
-- PHP Version: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nhanvien`
--
CREATE DATABASE IF NOT EXISTS `nhanvien` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci;
USE `nhanvien`;

-- --------------------------------------------------------

--
-- Table structure for table `dangnhap`
--

DROP TABLE IF EXISTS `dangnhap`;
CREATE TABLE IF NOT EXISTS `dangnhap` (
  `TenDangNhap` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `MatKhau` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `CapQuyen` varchar(100) COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `ID` int DEFAULT NULL,
  PRIMARY KEY (`TenDangNhap`),
  KEY `fk_dn_nv` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `dangnhap`
--

INSERT INTO `dangnhap` (`TenDangNhap`, `MatKhau`, `CapQuyen`, `ID`) VALUES
('NguyenVanA', '123', 'Admin', 1),
('NguyenVanG', '223', 'Leader', 9),
('TranThiB', '123', 'TeamLeader', 2),
('TranThiH', '223', 'TeamLeader', 8),
('LeVanC', '123', 'User', 3),
('PhamThiJ', '223', 'User', 10),
('PhamThiD', '123', 'Quan Ly', 4),
('HoangVanE', '223', 'TeamLeader', 5),
('DoThiF', '123', 'User', 6),
('LeVanI', '223', 'Quan Ly', 9);

-- --------------------------------------------------------

--
-- Table structure for table `duan`
--

DROP TABLE IF EXISTS `duan`;
CREATE TABLE IF NOT EXISTS `duan` (
  `maDuAn` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `TenDuAn` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `kinhPhi` int DEFAULT NULL,
  `BatDau` datetime DEFAULT NULL,
  `KetThuc` datetime DEFAULT NULL,
  PRIMARY KEY (`maDuAn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `duan`
--

INSERT INTO `duan` (`maDuAn`, `TenDuAn`, `kinhPhi`, `BatDau`, `KetThuc`) VALUES
('DA1', 'DuAn_1', 50000000, '2025-11-06 16:57:27', '2026-02-04 16:57:27'),
('DA4', 'DuAn_4', 50000000, '2025-11-06 16:57:27', '2026-02-04 16:57:27'),
('DA7', 'DuAn_7', 50000000, '2025-11-06 16:57:27', '2026-02-04 16:57:27'),
('DA9', 'DuAn_9', 50000000, '2025-11-06 16:57:27', '2026-02-04 16:57:27');

-- --------------------------------------------------------

--
-- Table structure for table `nhanvien`
--

DROP TABLE IF EXISTS `nhanvien`;
CREATE TABLE IF NOT EXISTS `nhanvien` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `HoTen` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Tuoi` int DEFAULT NULL,
  `NgaySinh` date DEFAULT NULL,
  `PhongBan` varchar(50) COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `ChucVu` varchar(50) COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `Luong` decimal(10,2) DEFAULT NULL,
  `GioiTinh` tinyint(1) DEFAULT NULL,
  `tax` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `DiaChi` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `HonNhan` tinyint(1) DEFAULT NULL,
  `Email` varchar(100) COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `MaPhongBan` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_nv_pb` (`MaPhongBan`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `nhanvien`
--

INSERT INTO `nhanvien` (`ID`, `HoTen`, `Tuoi`, `NgaySinh`, `PhongBan`, `ChucVu`, `Luong`, `GioiTinh`, `tax`, `DiaChi`, `HonNhan`, `Email`, `MaPhongBan`) VALUES
(1, 'Nguyen Van A', 30, '1995-01-15', 'IT', 'Quản Lý', 25000000.00, 1, '574145516', 'HCM', 1, 'a.nguyen@company.com', 1),
(2, 'Tran Thi B', 28, '1997-03-20', 'IT', 'TeamLeader', 18000000.00, 0, '2398765435', 'HCM', 0, 'b.tran@company.com', 1),
(3, 'Le Van C', 26, '1999-07-05', 'IT', 'Nhân Viên', 15000000.00, 1, '3410293842', 'DaNang', 0, 'c.le@company.com', 1),
(4, 'Pham Thi D', 35, '1990-11-12', 'Design', 'Quan Ly', 24000000.00, 0, '4556473825', 'HCM', 0, 'd.pham@company.com', 2),
(5, 'Hoang Van E', 29, '1996-06-30', 'Design', 'TeamLeader', 17000000.00, 0, '5691827357', 'HCM', 0, 'e.hoang@company.com', 2),
(6, 'Do Thi F', 27, '1998-09-25', 'Design', 'Nhan Vien', 14000000.00, 0, '6724681352', 'Hue', 0, 'f.do@company.com', 2),
(7, 'Nguyen Van G', 40, '1985-02-18', 'Marketing', 'Quan Ly', 26000000.00, 1, '7813579234', 'HCM', 0, 'g.nguyen@company.com', 3),
(8, 'Tran Thi H', 25, '2000-12-01', 'Marketing', 'TeamLeader', 16000000.00, 0, '8965432098', 'NhaTrang', 0, 'h.tran@company.com', 3),
(9, 'Le Van I', 33, '1992-04-10', 'Tài chính', 'Quan Ly', 27000000.00, 1, '9011223334', 'HCM', 0, 'i.le@company.com', 4),
(10, 'Pham Thi J', 24, '2001-08-22', 'Tài chính', 'Nhan Vien', 13500000.00, 0, '1199887750', 'HaNoi', 0, 'j.pham@company.com', 4);

-- --------------------------------------------------------

--
-- Table structure for table `phongban`
--

DROP TABLE IF EXISTS `phongban`;
CREATE TABLE IF NOT EXISTS `phongban` (
  `MaPhongBan` int NOT NULL AUTO_INCREMENT,
  `TenPhongBan` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `SoLuongNhanVien` int DEFAULT '0',
  PRIMARY KEY (`MaPhongBan`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `phongban`
--

INSERT INTO `phongban` (`MaPhongBan`, `TenPhongBan`, `SoLuongNhanVien`) VALUES
(1, 'IT', 3),
(2, 'Design', 3),
(3, 'Marketing', 2),
(4, 'Tài chính', 2);

-- --------------------------------------------------------

--
-- Table structure for table `quanly`
--

DROP TABLE IF EXISTS `quanly`;
CREATE TABLE IF NOT EXISTS `quanly` (
  `ID` int NOT NULL,
  `MaDuAn` varchar(50) COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_ql_da` (`MaDuAn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `quanly`
--

INSERT INTO `quanly` (`ID`, `MaDuAn`) VALUES
(1, 'DA001'),
(4, 'DA001'),
(7, 'DA001'),
(9, 'DA001');

-- --------------------------------------------------------

--
-- Table structure for table `teamleader`
--

DROP TABLE IF EXISTS `teamleader`;
CREATE TABLE IF NOT EXISTS `teamleader` (
  `ID` int NOT NULL,
  `SoLuongNhanVien` int DEFAULT NULL,
  `SoLuongCongViec` int DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `teamleader`
--

INSERT INTO `teamleader` (`ID`, `SoLuongNhanVien`, `SoLuongCongViec`) VALUES
(2, 15, 3),
(5, 15, 2),
(8, 15, 5);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
