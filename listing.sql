CREATE DATABASE rental_db;

USE rental_db;

CREATE TABLE listings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('apartment', 'house'),
  title VARCHAR(255),
  rooms INT,
  bathrooms INT,
  pool BOOLEAN,
  furnished BOOLEAN,
  parking BOOLEAN,
  braai BOOLEAN,
  laundry BOOLEAN,
  rental INT,
  value INT
);

-- Sample data
INSERT INTO listings (type, title, rooms, bathrooms, pool, furnished, parking, braai, laundry, rental, value)
VALUES
('apartment', 'Sea View Apartment', 2, 1, TRUE, TRUE, TRUE, FALSE, TRUE, 8500, 1200000),
('house', 'Family Home in Stellenbosch', 3, 2, TRUE, FALSE, TRUE, TRUE, TRUE, 12000, 2500000);