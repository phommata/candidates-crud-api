CREATE DATABASE IF NOT EXISTS interview;
USE interview;

/*
 The table below is missing keys.
 Look at user-stories.md and determine what keys this table should have.
 Alter the table and add the appropriate keys.
 */

DROP TABLE IF EXISTS candidates;

CREATE TABLE candidates (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX(created_at)
);

INSERT INTO candidates (id, first_name, last_name, email, created_at) VALUES
(1, 'John', 'Jameson', 'john@jameson.com', '2020-07-01T12:33:04'),
(2, 'Samuel', 'Adams', 'sam@samadams.com', '2020-07-03T02:33:04'),
(3, 'Gerard', 'Heineken', 'gerry@heiny.com', '2020-07-05T22:02:45'),
(4, 'Jose', 'Cuervo', 'jose@cuervo.com', '2020-07-05T22:12:45'),
(5, 'Jacob', 'Leinenkugel', 'jake@line-en-ku-gul.co', '2020-07-06T00:31:00'),
(6, 'David', 'Yuengling', 'dave@yuengling.drink', '2020-07-06T05:33:10');
