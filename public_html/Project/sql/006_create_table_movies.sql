CREATE TABLE IF NOT EXISTS `Movies` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `title` VARCHAR(255) NOT NULL,
    `year` YEAR NOT NULL,
    `imdb_id` VARCHAR(50) NOT NULL,
    `source` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE (`title`)
)