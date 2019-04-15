SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `si_bookmarks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `si_bookmarks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NOT NULL,
  `modified_at` DATETIME NOT NULL,
  `title` VARCHAR(128) NOT NULL,
  `url` VARCHAR(128) NOT NULL,
  `is_public` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `UQ_bookmarks_1` (`url` ASC))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `si_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `si_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `UQ_tags_1` (`name` ASC))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `si_bookmarks_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `si_bookmarks_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bookmark_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `FK_bookmarks_tags_1` (`bookmark_id` ASC),
  INDEX `FK_bookmarks_tags_2` (`tag_id` ASC),
  UNIQUE INDEX `UQ_bookmarks_tags_1` (`bookmark_id` ASC, `tag_id` ASC),
  CONSTRAINT `FK_bookmarks_tags_1`
    FOREIGN KEY (`bookmark_id`)
    REFERENCES `si_bookmarks` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_bookmarks_tags_2`
    FOREIGN KEY (`tag_id`)
    REFERENCES `si_tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;