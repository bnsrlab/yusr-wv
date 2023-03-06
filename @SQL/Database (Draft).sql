CREATE DATABASE yusr;
USE yusr;

CREATE TABLE `cx_advancedSettings` (
  `idAdSet` tinyint(4) PRIMARY KEY COMMENT 'ID pengaturan lanjutan (tunggal)',
  `host` char(150) NOT NULL COMMENT 'Host atau domain',
  `SMTPAuth` tinyint(1) DEFAULT '1' COMMENT 'SMTP Authentication',
  `SMTPSecure` char(50) NOT NULL COMMENT 'SSL',
  `username` char(150) NOT NULL COMMENT 'Alamat email',
  `password` char(150) NOT NULL COMMENT 'Kata sandi email',
  `port` int(11) NOT NULL COMMENT 'TCP Port',
  `sender` char(150) NOT NULL COMMENT 'Nama pengirim',
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu data diubah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Pengaturan lanjutan.';

INSERT INTO `cx_advancedSettings` (`idAdSet`, `host`, `SMTPAuth`, `SMTPSecure`, `username`, `password`, `port`, `sender`, `modified`) VALUES (1, 'mail.conary.id', 1, 'ssl', 'random@conary.id', 'Maumasuk246!', 465, 'Yusr.', '2021-12-23 07:09:52');

CREATE TABLE `cx_settings` (
  `idSet` tinyint(3) UNSIGNED NOT NULL COMMENT 'ID pengaturan (tunggal)',
  `img` char(55) NOT NULL COMMENT 'Logo utama situs web',
  `tagline` char(150) NOT NULL COMMENT 'Judul utama sitsu web',
  `trademark` char(60) NOT NULL COMMENT 'Singkatan atau merek dagang',
  `maintenance` enum('y','n') DEFAULT 'n' COMMENT 'Status pemeliharan',
  `address` char(250) DEFAULT NULL COMMENT 'Alamat lengkap',
  `email` char(150) NOT NULL COMMENT 'Alamat email',
  `phone` char(100) NOT NULL COMMENT 'Nomor telepon',
  `whatsapp` char(150) DEFAULT NULL COMMENT 'Whatsapp',
  `youtube` char(150) DEFAULT NULL COMMENT 'Tautan Youtube',
  `facebook` char(150) DEFAULT NULL COMMENT 'Tautan Facebook',
  `instagram` char(150) DEFAULT NULL COMMENT 'Tautan Instagram',
  `desc` text COMMENT 'Deskripsi atau keterangan',
  `created` datetime NOT NULL COMMENT 'Waktu data dibuat',
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu data diubah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Pengaturan dasar.';

INSERT INTO `cx_settings` (`idSet`, `img`, `tagline`, `trademark`, `maintenance`, `address`, `email`, `phone`, `whatsapp`, `youtube`, `facebook`, `instagram`, `desc`, `created`, `modified`) VALUES (1, 'logo.png', 'Make Quran More Accessible', 'Yusr.', 'n', '', 'info@yusr.conary.id', '087871894990', '087871894990', '', '', '', 'Quran app with many features for every user including people with special need.', NOW(), NOW());

CREATE TABLE `tb_users` (
  `idUser` char(50) NOT NULL COMMENT 'ID pengguna (ex: user-(jnygis)-00001)',
  `img` char(55) DEFAULT NULL COMMENT 'Foto profil',
  `name` char(150) NOT NULL COMMENT 'Nama pengguna',
  `nationality` char(150) NOT NULL COMMENT 'Negara asal',
  `phone` char(30) NOT NULL COMMENT 'Nomor telepon',
  `email` char(150) NOT NULL COMMENT 'Alamat email',
  `password` char(100) NOT NULL COMMENT 'Kata sandi',
  `status` enum('granted','blocked') DEFAULT 'granted' COMMENT 'Status pengguna',
  `lgnToken` char(15) DEFAULT NULL COMMENT 'Kode akses sementara',
  `fgtToken` char(100) DEFAULT NULL COMMENT 'Token untuk ubah kata sandi',
  `fgtDate` datetime DEFAULT NULL COMMENT 'Waktu terakhir ubah kata sandi',
  `created` datetime NOT NULL COMMENT 'Waktu data dibuat',
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu data diubah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='[CNRYFLEX] pengguna / administrator.';