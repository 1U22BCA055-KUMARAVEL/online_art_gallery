-- Database: art_gallery

CREATE DATABASE art_gallery;
USE art_gallery;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
select * from users;
-- Artists Table
CREATE TABLE artists (
    artist_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255)
);

-- Artwork Table
CREATE TABLE artwork (
    artwork_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    artist_id INT,
    FOREIGN KEY (artist_id) REFERENCES artists(artist_id) ON DELETE CASCADE
);
ALTER TABLE artwork ADD COLUMN user_id INT NOT NULL;

-- Orders Table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    artwork_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (artwork_id) REFERENCES artwork(artwork_id) ON DELETE CASCADE
);

delete from artwork where artwork_id=1;
select * from artwork;
ALTER TABLE users ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0;
-- Add a payment status column in the orders table
ALTER TABLE orders ADD COLUMN payment_status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending';

-- Prevent users from buying their own artwork (Handled in PHP)
ALTER TABLE orders ADD COLUMN currency VARCHAR(10);
ALTER TABLE orders ADD COLUMN amount_in_usd DECIMAL(10,2);
ALTER TABLE orders ADD COLUMN address VARCHAR(255) NOT NULL;
ALTER TABLE orders ADD COLUMN txn_id VARCHAR(50) NOT NULL;
drop database art_gallery;
