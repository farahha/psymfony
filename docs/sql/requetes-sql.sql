CREATE TABLE advert (
    id INT AUTO_INCREMENT NOT NULL, 
    date DATETIME NOT NULL, 
    title VARCHAR(255) NOT NULL, 
    author VARCHAR(255) NOT NULL, 
    content LONGTEXT DEFAULT NULL, 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE advert ADD published TINYINT(1) NOT NULL;


CREATE TABLE images (
    id INT AUTO_INCREMENT NOT NULL, 
    url VARCHAR(255) NOT NULL, 
    alt VARCHAR(255) NOT NULL, 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE adverts ADD image_id INT DEFAULT NULL;
ALTER TABLE adverts ADD CONSTRAINT FK_8C88E7773DA5256D FOREIGN KEY (image_id) REFERENCES images (id);
CREATE UNIQUE INDEX UNIQ_8C88E7773DA5256D ON adverts (image_id);


CREATE TABLE applications (
    id INT AUTO_INCREMENT NOT NULL,
    advert_id INT NOT NULL,
    author VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    date DATETIME NOT NULL,
    INDEX IDX_F7C966F0D07ECCB6 (advert_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE applications ADD CONSTRAINT FK_F7C966F0D07ECCB6 FOREIGN KEY (advert_id) REFERENCES adverts (id);

CREATE TABLE advert_category (
	advert_id INT NOT NULL,
	category_id INT NOT NULL,
	INDEX IDX_84EEA340D07ECCB6 (advert_id),
	INDEX IDX_84EEA34012469DE2 (category_id),
	PRIMARY KEY(advert_id, category_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE categories (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE advert_category ADD CONSTRAINT FK_84EEA340D07ECCB6 FOREIGN KEY (advert_id) REFERENCES adverts (id) ON DELETE CASCADE;
ALTER TABLE advert_category ADD CONSTRAINT FK_84EEA34012469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE;