CREATE TABLE IF NOT EXISTS `user_access_token` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `user_id` BIGINT(20) UNSIGNED NULL COMMENT '',
  `access_token` VARCHAR(255) NOT NULL COMMENT '',
  `device` TEXT NOT NULL COMMENT '',
  `created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  `updated` DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  UNIQUE INDEX `access_token_UNIQUE` (`access_token` ASC)  COMMENT '',
  INDEX `FK_access_token_user_idx` (`user_id` ASC)  COMMENT '',
  CONSTRAINT `FK_access_token_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;
