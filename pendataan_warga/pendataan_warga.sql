-- Database: pendataan_warga
CREATE DATABASE IF NOT EXISTS pendataan_warga CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE pendataan_warga;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(100),
  role ENUM('admin','operator') DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- default admin (as requested)
INSERT INTO users (username, password, name, role) VALUES
('admin', '$2b$12$bG4mUA5kfMGG.NZmcIBuPOkPeRyQFlka./wxSw99OE6P5ll6Nq6ZO', 'Administrator', 'admin');

CREATE TABLE IF NOT EXISTS keluarga (
  id INT AUTO_INCREMENT PRIMARY KEY,
  no_kk VARCHAR(30) UNIQUE NOT NULL,
  kepala_keluarga VARCHAR(150) NOT NULL,
  alamat TEXT,
  rt VARCHAR(5),
  rw VARCHAR(5),
  desa_kelurahan VARCHAR(100),
  kecamatan VARCHAR(100),
  kabupaten VARCHAR(100),
  provinsi VARCHAR(100),
  kode_pos VARCHAR(10),
  file_scan VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS warga (
  id INT AUTO_INCREMENT PRIMARY KEY,
  keluarga_id INT,
  nik VARCHAR(20) UNIQUE,
  nama VARCHAR(150) NOT NULL,
  jenis_kelamin ENUM('L','P'),
  tempat_lahir VARCHAR(100),
  tanggal_lahir DATE,
  agama VARCHAR(50),
  pendidikan VARCHAR(50),
  pekerjaan VARCHAR(100),
  status_perkawinan VARCHAR(50),
  hubungan_keluarga VARCHAR(50),
  alamat_text TEXT,
  nomor_hp VARCHAR(30),
  foto VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (keluarga_id) REFERENCES keluarga(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  action VARCHAR(100),
  description TEXT,
  ip_address VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
