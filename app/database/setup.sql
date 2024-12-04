-- libraries table
CREATE TABLE libraries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    user_id INT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- books table
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    library_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    genre VARCHAR(100) NULL,
    rating DECIMAL(2, 1) NULL, 
    review TEXT NULL,
    cover_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (library_id) REFERENCES libraries(id) ON DELETE CASCADE
);

CREATE TABLE book_library (
    book_id INT NOT NULL,
    library_id INT NOT NULL,
    PRIMARY KEY (book_id, library_id),
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (library_id) REFERENCES libraries(id)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
