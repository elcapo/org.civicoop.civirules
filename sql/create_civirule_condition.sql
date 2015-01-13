CREATE TABLE IF NOT EXISTS `civirule_condition` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NULL,
  `label` VARCHAR(128) NULL,
  `function_name` VARCHAR(256) NULL,
  `civicrm_form_class` VARCHAR(128) NULL,
  `is_active` TINYINT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
