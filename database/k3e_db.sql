-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema k3e_db
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema k3e_db
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `k3e_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `k3e_db` ;

-- -----------------------------------------------------
-- Table `k3e_db`.`province`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`province` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`district`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`district` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `province_id` INT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `province_id_idx` (`province_id` ASC) VISIBLE,
  CONSTRAINT `fk_district_province`
    FOREIGN KEY (`province_id`)
    REFERENCES `k3e_db`.`province` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`transporter`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`transporter` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `token` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`paymethod`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`paymethod` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `token` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`maincategory`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`maincategory` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `link` VARCHAR(1024) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`subcategory`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`subcategory` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `link` VARCHAR(1024) NOT NULL,
  `maincategory_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_subcategory_maincategory_idx` (`maincategory_id` ASC) VISIBLE,
  CONSTRAINT `fk_subcategory_maincategory`
    FOREIGN KEY (`maincategory_id`)
    REFERENCES `k3e_db`.`maincategory` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(200) NOT NULL,
  `firstname` VARCHAR(50) NOT NULL,
  `lastname` VARCHAR(100) NOT NULL,
  `password` VARCHAR(32) NOT NULL,
  `email` VARCHAR(254) NOT NULL,
  `phone` VARCHAR(11) NOT NULL,
  `address` VARCHAR(200) NULL,
  `district_id` INT NULL,
  `created_date` DATETIME NOT NULL DEFAULT now(),
  `locked` INT NOT NULL DEFAULT 0,
  `birthday` DATE NOT NULL,
  `gender` INT NOT NULL,
  `money` DECIMAL(18,3) NOT NULL DEFAULT 0,
  `role` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_user_district_idx` (`district_id` ASC) VISIBLE,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) VISIBLE,
  CONSTRAINT `fk_user_district`
    FOREIGN KEY (`district_id`)
    REFERENCES `k3e_db`.`district` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`shop`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`shop` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `created` DATETIME NOT NULL,
  `owner_id` INT NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `address` VARCHAR(200) NOT NULL,
  `district_id` INT NOT NULL,
  `locked` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_shop_district_idx` (`district_id` ASC) VISIBLE,
  INDEX `fk_shop_user_idx` (`owner_id` ASC) VISIBLE,
  UNIQUE INDEX `owner_id_UNIQUE` (`owner_id` ASC) VISIBLE,
  CONSTRAINT `fk_shop_district`
    FOREIGN KEY (`district_id`)
    REFERENCES `k3e_db`.`district` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_shop_user`
    FOREIGN KEY (`owner_id`)
    REFERENCES `k3e_db`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`product` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `description` TEXT NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `shop_id` INT NOT NULL,
  `stock_price` DECIMAL(18,3) UNSIGNED NOT NULL,
  `price` DECIMAL(18,3) UNSIGNED NOT NULL,
  `created_date` DATETIME NOT NULL,
  `subcategory_id` INT NOT NULL,
  `weight` INT UNSIGNED NOT NULL,
  `height` INT UNSIGNED NOT NULL,
  `width` INT UNSIGNED NOT NULL,
  `depth` INT UNSIGNED NOT NULL,
  `locked` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_product_subcategory_idx` (`subcategory_id` ASC) VISIBLE,
  INDEX `fk_product_shop_idx` (`shop_id` ASC) VISIBLE,
  CONSTRAINT `fk_product_subcategory`
    FOREIGN KEY (`subcategory_id`)
    REFERENCES `k3e_db`.`subcategory` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_product_shop`
    FOREIGN KEY (`shop_id`)
    REFERENCES `k3e_db`.`shop` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`cartitem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`cartitem` (
  `product_id` INT NOT NULL,
  `client_id` INT NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`product_id`, `client_id`),
  INDEX `fk_cartitem_product_idx` (`product_id` ASC) VISIBLE,
  INDEX `fk_cartitem_user_idx` (`client_id` ASC) VISIBLE,
  CONSTRAINT `fk_cartitem_product`
    FOREIGN KEY (`product_id`)
    REFERENCES `k3e_db`.`product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_cartitem_user`
    FOREIGN KEY (`client_id`)
    REFERENCES `k3e_db`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`productimage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`productimage` (
  `product_id` INT NOT NULL,
  `norder` INT UNSIGNED NOT NULL,
  `link` VARCHAR(512) NOT NULL,
  PRIMARY KEY (`product_id`, `norder`),
  CONSTRAINT `fk_productimage_product`
    FOREIGN KEY (`product_id`)
    REFERENCES `k3e_db`.`product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`productattribute`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`productattribute` (
  `product_id` INT NOT NULL,
  `norder` INT UNSIGNED NOT NULL,
  `attribute` VARCHAR(100) NOT NULL,
  `value` VARCHAR(1024) NOT NULL,
  INDEX `fk_productattribute_product_idx` (`product_id` ASC) VISIBLE,
  PRIMARY KEY (`norder`, `product_id`),
  CONSTRAINT `fk_productattribute_product`
    FOREIGN KEY (`product_id`)
    REFERENCES `k3e_db`.`product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`deliveryaddress`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`deliveryaddress` (
  `norder` INT NOT NULL,
  `user_id` INT NOT NULL,
  `default` INT NOT NULL,
  `address` VARCHAR(200) NOT NULL,
  `district_id` INT NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`norder`, `user_id`),
  INDEX `fk_deliveryaddress_user_idx` (`user_id` ASC) VISIBLE,
  INDEX `fk_deliveryaddress_district_idx` (`district_id` ASC) VISIBLE,
  CONSTRAINT `fk_deliveryaddress_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `k3e_db`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_deliveryaddress_district`
    FOREIGN KEY (`district_id`)
    REFERENCES `k3e_db`.`district` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`order`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`order` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `shop_id` INT NOT NULL,
  `client_id` INT NOT NULL,
  `address` VARCHAR(200) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `distinct_id` INT NOT NULL,
  `status` INT NOT NULL,
  `paymethod_id` INT NOT NULL,
  `paid` INT NOT NULL,
  `created_date` DATETIME NOT NULL,
  `verified_date` DATETIME NULL,
  `transporter_id` INT NOT NULL,
  `parcel_id` INT NOT NULL,
  `ship_fee` INT UNSIGNED NOT NULL,
  `note` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_order_paymethod_idx` (`paymethod_id` ASC) VISIBLE,
  INDEX `fk_order_user_idx` (`client_id` ASC) VISIBLE,
  INDEX `fk_order_shop_idx` (`shop_id` ASC) VISIBLE,
  INDEX `fk_order_transporter_idx` (`transporter_id` ASC) VISIBLE,
  INDEX `fk_order_district_idx` (`distinct_id` ASC) VISIBLE,
  CONSTRAINT `fk_order_paymethod`
    FOREIGN KEY (`paymethod_id`)
    REFERENCES `k3e_db`.`paymethod` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_order_shop`
    FOREIGN KEY (`shop_id`)
    REFERENCES `k3e_db`.`shop` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_order_user`
    FOREIGN KEY (`client_id`)
    REFERENCES `k3e_db`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_order_transporter`
    FOREIGN KEY (`transporter_id`)
    REFERENCES `k3e_db`.`transporter` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_order_district`
    FOREIGN KEY (`distinct_id`)
    REFERENCES `k3e_db`.`district` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`assessment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`assessment` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `client_id` INT NOT NULL,
  `comment` TEXT NOT NULL,
  `star` INT UNSIGNED NOT NULL,
  `created_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `order_id`, `product_id`),
  INDEX `fk_assessment_user_idx` (`client_id` ASC) VISIBLE,
  INDEX `fk_assessment_order_idx` (`order_id` ASC) VISIBLE,
  INDEX `fk_assessment_product_idx` (`product_id` ASC) VISIBLE,
  CONSTRAINT `fk_assessment_user`
    FOREIGN KEY (`client_id`)
    REFERENCES `k3e_db`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_assessment_order`
    FOREIGN KEY (`order_id`)
    REFERENCES `k3e_db`.`order` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_assessment_product`
    FOREIGN KEY (`product_id`)
    REFERENCES `k3e_db`.`product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`orderitem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`orderitem` (
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `pay_price` DECIMAL(18,3) UNSIGNED NOT NULL,
  `warranty_time` DATETIME NOT NULL,
  PRIMARY KEY (`order_id`, `product_id`),
  INDEX `fk_orderitem_product_idx` (`product_id` ASC) VISIBLE,
  CONSTRAINT `fk_orderitem_order`
    FOREIGN KEY (`order_id`)
    REFERENCES `k3e_db`.`order` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_orderitem_product`
    FOREIGN KEY (`product_id`)
    REFERENCES `k3e_db`.`product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`orderimage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`orderimage` (
  `order_id` INT NOT NULL,
  `norder` INT UNSIGNED NOT NULL,
  `link` VARCHAR(1024) NOT NULL,
  `created_date` DATETIME NOT NULL,
  PRIMARY KEY (`order_id`, `norder`),
  CONSTRAINT `fk_orderimage_order`
    FOREIGN KEY (`order_id`)
    REFERENCES `k3e_db`.`order` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `k3e_db`.`assessmentimage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `k3e_db`.`assessmentimage` (
  `assessment_id` INT NOT NULL,
  `norder` INT UNSIGNED NOT NULL,
  `link` VARCHAR(1024) NULL,
  PRIMARY KEY (`assessment_id`, `norder`),
  CONSTRAINT `fk_assessmentimage_assessment`
    FOREIGN KEY (`assessment_id`)
    REFERENCES `k3e_db`.`assessment` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
