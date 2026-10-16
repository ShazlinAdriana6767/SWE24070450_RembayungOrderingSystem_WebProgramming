-- Run ONCE in phpMyAdmin (database: rembayung_db) before using the upgraded site.
-- Makes room for secure password hashes. Existing plain-text passwords keep working
-- and are converted to hashes automatically the next time each user logs in.
ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL;
