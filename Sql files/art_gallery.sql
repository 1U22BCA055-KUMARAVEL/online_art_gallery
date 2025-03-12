-- Create Database
CREATE DATABASE art_gallery;
USE art_gallery;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Artists Table
CREATE TABLE artists (
    artist_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Artwork Table
CREATE TABLE artwork (
    artwork_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    artist_id INT NOT NULL,
    user_id INT NOT NULL,  -- Assuming tracking who owns the artwork
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES artists(artist_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    artwork_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
    currency VARCHAR(10) NOT NULL,
    amount_in_usd DECIMAL(10,2) NOT NULL,
    address VARCHAR(255) NOT NULL,
    txn_id VARCHAR(50) UNIQUE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (artwork_id) REFERENCES artwork(artwork_id) ON DELETE CASCADE
);

-- Indexes for Optimization
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_artist_email ON artists(email);
CREATE INDEX idx_artwork_artist ON artwork(artist_id);
CREATE INDEX idx_orders_user ON orders(user_id);

-- Application-Level Restriction: Prevent Users from Buying Their Own Artwork
-- This should be handled in application logic before inserting orders.
ALTER TABLE artwork MODIFY COLUMN artist_id INT NULL;
