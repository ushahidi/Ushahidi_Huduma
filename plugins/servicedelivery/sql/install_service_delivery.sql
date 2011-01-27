/**
 * Creates the DB tables for the service delivery plugin
 */

/**
 * Table boundary_type
 */
CREATE TABLE IF NOT EXISTS `boundary_type` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `boundary_type_name` VARCHAR(45) NOT NULL ,
  `parent_id` INT NOT NULL DEFAULT 0 ,
  `creation_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT = 'Types of administrative boundaries e.g. province, district, ward, constituency etc';

--
-- Table adminstrative_boundary
--
CREATE TABLE IF NOT EXISTS `boundary` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `boundary_name` VARCHAR(45) NOT NULL ,
  `boundary_type_id` INT NOT NULL ,
  `creation_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`) ,
  INDEX `boundary_fk1` (`boundary_type_id` ASC) ,
  CONSTRAINT `boundary_fk1` FOREIGN KEY (`boundary_type_id` ) REFERENCES `boundary_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) COMMENT = 'List of administrative boundaries (specific names of the various admin boundaries)';

--
-- Table service_provider
--
CREATE  TABLE IF NOT EXISTS `service_provider` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `provider_name` VARCHAR(50) NOT NULL ,
  `description` VARCHAR(200) NULL ,
  `category_id` INT NOT NULL ,
  `parent_id` INT NOT NULL DEFAULT 0 ,
  `boundary_id` INT NULL ,
  `creation_date` DATETIME NOT NULL ,
  PRIMARY KEY (`id`)
) COMMENT = 'Groups for the monitors responsible for attending to tickets';

--
-- Table service_provider_officer
--
CREATE  TABLE IF NOT EXISTS `service_provider_officer` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL ,
  `full_name` VARCHAR(100) NOT NULL ,
  `email_address` VARCHAR(45) NOT NULL ,
  `phone_number` VARCHAR(45) NOT NULL ,
  `service_provider_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `service_provider_officer_fk1` FOREIGN KEY (`service_provider_id` )
    REFERENCES `service_provider` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
);

--
-- Table static_entity_type
--
CREATE  TABLE IF NOT EXISTS `static_entity_type` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category_id` INT NOT NULL ,
  `type_name` VARCHAR(50) NOT NULL ,
  `entity_type_color` VARCHAR(15) NOT NULL ,
  `entity_type_image` VARCHAR(100) NULL ,
  `entity_type_image_thumb` VARCHAR(100) NULL ,
  `metadata` TEXT NULL ,
  PRIMARY KEY (`id`)
) COMMENT = 'Types of static entities e.g Dispensary, School, Hospital etc';

--
-- Table static_entity
--
CREATE  TABLE IF NOT EXISTS `static_entity` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `static_entity_type_id` INT NOT NULL ,
  `boundary_id` INT NOT NULL ,
  `entity_name` VARCHAR(45) NOT NULL ,
  `latitude` DOUBLE NOT NULL ,
  `longitude` DOUBLE NOT NULL ,
  `metadata` TEXT NULL ,
  `creation_date` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `static_entity_fk1` (`static_entity_type_id` ASC) ,
  INDEX `static_entity_fk2` (`boundary_id` ASC) ,
  CONSTRAINT `static_entity_fk1`
    FOREIGN KEY (`static_entity_type_id` )
    REFERENCES `static_entity_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `static_entity_fk2`
    FOREIGN KEY (`boundary_id` )
    REFERENCES `boundary` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) COMMENT = 'List of static entities';