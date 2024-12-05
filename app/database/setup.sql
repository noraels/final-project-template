-- libraries table
DROP TABLE IF EXISTS `libraries`;
CREATE TABLE libraries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    user_id INT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `genre` varchar(255) NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `review` text,
  `cover_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `published_date` date DEFAULT NULL,
  `synopsis` text,
  `library_name` varchar(255) NOT NULL
) 


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

-- Insert libraries
INSERT INTO libraries (name) VALUES 
('My First Library'),
('School Books'),
('Fun Books');

INSERT INTO `books` (`title`, `author`, `genre`, `rating`, `review`, `cover_url`, `created_at`, `published_date`, `synopsis`, `library_name`) VALUES
('Yo-Yo Ma', 'Richard Worth', 'Arts', NULL, NULL, 'http://books.google.com/books/content?id=KTjpP0j7DxQC&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', '2024-12-03 21:31:13', '2007-01-01', 'Born in Paris, to Chinese parents, Yo-Yo Ma began playing the cello when he was a child. At the age of five he was already playing Bach''s challenging cello suites. A short time later, Ma immigrated to the United States with his parents, who established a new home in New York City. This biography of Yo-Yo Ma is for musicians or music enthusiasts.', 'Library'),
('The Well', 'Mark Hall', 'Religion', NULL, NULL, 'http://books.google.com/books/content?id=4QKVl6ysrtQC&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', '2024-12-03 23:06:40', '2011-09-06', 'Why are so many so close to the Well and still so thirsty? Mark Hall takes the powerful story of the Woman at the Well and her encounter with Jesus to help readers understand that the “wells” we go to for life and sustenance, the “wells” of success, talent, control, favor, religion, etc., are keeping us from relying on Jesus and his abundant life, and we will never be truly satisfied until we realize that and go to Him for our needs.', 'Library'),
('The OK Book', 'Amy Krouse Rosenthal', 'Juvenile Fiction', NULL, NULL, 'http://books.google.com/books/content?id=RZQLdlTQa_kC&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', '2024-12-03 23:06:45', '2010-12-07', 'From the team that created the bestselling I Wish You More, this is a motivational picture book for exceptionally OK children! In this clever and visual play on words, OK is turned sideways, upside down, and right side up to show that being OK can really be quite great. With spare yet comforting illustrations and text, bestselling duo Amy Krouse Rosenthal and Tom Lichtenheld celebrate the real skills and talents children possess, encouraging and empowering them to discover their own individual strengths and personalities. Whether OK personifies an OK skipper, an OK climber, an OK lightning bug catcher, or an OK whatever there is to experience, OK is an OK place to be. And being OK just may lead to the discovery of what makes one great.', 'Library'),
('I Love You So...', 'Marianne Richmond', 'Juvenile Fiction', NULL, NULL, 'http://books.google.com/books/content?id=vQ9UEAAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', '2024-12-03 23:06:48', '2022-01-11', 'Celebrate 20 years of I LOVE YOU SO! This adorable classic puts into words the indescribable quality of boundless, steady, and unconditional love, a sweet story that has touched hundreds of thousands of lives. This comforting story embraces the reader like a warm hug and gently reassures a child that love is for always, despite the grouchy moods or physical separation. This is the perfect message of love to gift new mommies- and daddies-to-be, grandparents, and your special little ones at baby showers, Valentine''s Day, or birthdays. Embrace your loved ones from afar with this heartwarming reminder of your unconditional love.', 'Library'),
('The Seven Husbands of Evelyn Hugo', 'Taylor Jenkins Reid', 'FICTION', NULL, NULL, 'http://books.google.com/books/content?id=IdAmDwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', '2024-12-04 23:36:53', '2017-06-13', 'The epic adventures Evelyn creates over the course of a lifetime will leave every reader mesmerized. This wildly addictive journey of a reclusive Hollywood starlet and her tumultuous Tinseltown journey comes with unexpected twists and the most satisfying of drama.', 'Library'),
('Snoozie, Sunny, and So-So', 'Dafna Ben-Zvi', 'No category', NULL, NULL, 'http://books.google.com/books/content?id=fZ07uQEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', '2024-12-03 23:07:23', '2020-05-12', 'What do you do when you''re feeling so-so? Do you talk with a friend? What happens if you have no one to talk to?', 'Library'),
('Wuthering Heights', 'Brontë, Emily', 'Fiction', NULL, NULL, 'http://books.google.com/books/content?id=OxNHDwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', '2024-12-04 15:29:35', '2018-01-15', 'Wuthering Heights is Emily Brontë''s only novel. Written between October 1845 and June 1846, Wuthering Heights was published in 1847 under the pseudonym "Ellis Bell"; Brontë died the following year, aged 30. Wuthering Heights and Anne Brontë''s Agnes Grey were accepted by publisher Thomas Newby before the success of their sister Charlotte''s novel, Jane Eyre. After Emily''s death, Charlotte edited the manuscript of Wuthering Heights, and arranged for the edited version to be published as a posthumous second edition in 1850. Although Wuthering Heights is now widely regarded as a classic of English literature, contemporary reviews for the novel were deeply polarised; it was considered controversial because its depiction of mental and physical cruelty was unusually stark, and it challenged strict Victorian ideals of the day regarding religious hypocrisy, morality, social classes and gender inequality. The English poet and painter Dante Gabriel Rossetti, although an admirer of the book, referred to it as \"A fiend of a book – an incredible monster [...] The action is laid in hell, – only it seems places and people have English names there."', 'Library')
