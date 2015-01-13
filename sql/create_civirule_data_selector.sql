CREATE TABLE IF NOT EXISTS `civirule_data_selector` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity` VARCHAR(64) NULL,
  `column` VARCHAR(128) NULL,
  `label` VARCHAR(68) NULL,
  `description` TEXT NULL,
  `is_active` TINYINT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
