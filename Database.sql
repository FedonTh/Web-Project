DROP DATABASE IF EXISTS project2026;
CREATE DATABASE project2026;
USE project2026;

CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') NOT NULL,
    credits INT DEFAULT 5
);

CREATE TABLE aggelia(
    id INT AUTO_INCREMENT PRIMARY KEY,
    chef_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    photo VARCHAR(255),
    merides_total INT NOT NULL,
    merides_left INT NOT NULL,
    location VARCHAR(255) NOT NULL,
    pickup_time DATETIME NOT NULL,
    created_at DATETIME NOT NULL,
    expires_at DATETIME NOT NULL,
    status ENUM('active','inactive','deleted') DEFAULT 'active',
    allergens SET(
        'Cereals containing gluten',
        'Crustaceans',
        'Eggs',
        'Fish',
        'Peanuts',
        'Soybeans',
        'Milk',
        'Nuts',
        'Celery',
        'Mustard',
        'Sesame seeds',
        'Sulphur dioxide and sulphites',
        'Lupin',
        'Molluscs'
    ),

    FOREIGN KEY(chef_id)
        REFERENCES users(id)
);

CREATE TABLE aitima(
    id INT AUTO_INCREMENT PRIMARY KEY,
    aggelia_id INT NOT NULL,
    user_id INT NOT NULL,
    aitima_date DATETIME NOT NULL,
    status ENUM(
        'pending',
        'approved',
        'rejected'
    ) DEFAULT 'pending',
    picked_up BOOLEAN DEFAULT FALSE,
    pickup_date DATETIME NULL,

    FOREIGN KEY(aggelia_id)
        REFERENCES aggelia(id),
    FOREIGN KEY(user_id)
        REFERENCES users(id)
);

CREATE TABLE rating(
    id INT AUTO_INCREMENT PRIMARY KEY,
    aitima_id INT UNIQUE NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT,
    rating_date DATETIME,
    CHECK (rating BETWEEN 1 AND 5),

    FOREIGN KEY(aitima_id)
        REFERENCES aitima(id)
);

SET GLOBAL event_scheduler = ON;

CREATE EVENT delete_expired_aggelies
ON SCHEDULE EVERY 1 HOUR
DO
    UPDATE aggelia
    SET status = 'deleted'
    WHERE status = 'active'
      AND expires_at <= NOW();
      

INSERT INTO users(username, password, role) VALUES ('username1', '123', 'user');
INSERT INTO users(username, password, role) VALUES ('username2', '123', 'user');
INSERT INTO users(username, password, role) VALUES ('username3', '123', 'admin');

