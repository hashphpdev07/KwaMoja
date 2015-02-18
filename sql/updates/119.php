<?php

CreateTable('care_address1',
"CREATE TABLE care_address1 (
  `country` CHAR(2) NOT NULL DEFAULT '',
  `address_code` INT(11) NOT NULL DEFAULT 0,
  `address_name` VARCHAR(100) NOT NULL DEFAULT '',
  `form_label` VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`country`, `address_code`),
  KEY (`address_code`)
)");

CreateTable('care_address2',
"CREATE TABLE care_address2 (
  `country` CHAR(2) NOT NULL DEFAULT '',
  `address1` INT(11) NOT NULL DEFAULT 0,
  `address_code` INT(11) NOT NULL DEFAULT 0,
  `address_name` VARCHAR(100) NOT NULL DEFAULT '',
  `form_label` VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`country`, `address_code`),
  KEY (`address_code`),
  CONSTRAINT `careaddr2_ibfk_1` FOREIGN KEY (`address1`) REFERENCES `care_address1` (`address_code`)
)");

CreateTable('care_address3',
"CREATE TABLE care_address3 (
  `country` CHAR(2) NOT NULL DEFAULT '',
  `address2` INT(11) NOT NULL DEFAULT 0,
  `address_code` INT(11) NOT NULL DEFAULT 0,
  `address_name` VARCHAR(100) NOT NULL DEFAULT '',
  `form_label` VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`country`, `address_code`),
  KEY (`address_code`),
  CONSTRAINT `careaddr3_ibfk_1` FOREIGN KEY (`address2`) REFERENCES `care_address2` (`address_code`)
)");

CreateTable('care_address4',
"CREATE TABLE care_address4 (
  `country` CHAR(2) NOT NULL DEFAULT '',
  `address3` INT(11) NOT NULL DEFAULT 0,
  `address_code` INT(11) NOT NULL DEFAULT 0,
  `address_name` VARCHAR(100) NOT NULL DEFAULT '',
  `form_label` VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`country`, `address_code`),
  KEY (`address_code`),
  CONSTRAINT `careaddr4_ibfk_1` FOREIGN KEY (`address3`) REFERENCES `care_address3` (`address_code`)
)");

CreateTable('care_address5',
"CREATE TABLE care_address5 (
  `country` CHAR(2) NOT NULL DEFAULT '',
  `address4` INT(11) NOT NULL DEFAULT 0,
  `address_code` INT(11) NOT NULL DEFAULT 0,
  `address_name` VARCHAR(100) NOT NULL DEFAULT '',
  `form_label` VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`country`, `address_code`),
  KEY (`address_code`),
  CONSTRAINT `careaddr5_ibfk_1` FOREIGN KEY (`address4`) REFERENCES `care_address4` (`address_code`)
)");

CreateTable('care_tribes',
"CREATE TABLE `care_tribes` (
  `country` CHAR(2) NOT NULL DEFAULT '',
  `tribe_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tribe_code` varchar(10) NOT NULL DEFAULT '',
  `tribe_name` varchar(20) NOT NULL DEFAULT '',
  `is_additional` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tribe_id`),
  KEY `tribe_id` (`tribe_id`)
)");

executeSQL("TRUNCATE care_address1");
executeSQL("INSERT INTO care_address1 SELECT 'UG', id, district_name, '" . _('District') . "' FROM care_ug_districts");

executeSQL("TRUNCATE care_address2");
executeSQL("INSERT INTO care_address2 SELECT 'UG', district_id, id, county, '" . _('County') . "' FROM care_ug_county");

executeSQL("TRUNCATE care_address3");
executeSQL("INSERT INTO care_address3 SELECT 'UG', county_id, id, subcounty, '" . _('Sub-County') . "' FROM care_ug_subcounty");

executeSQL("TRUNCATE care_address4");
executeSQL("INSERT INTO care_address4 SELECT 'UG', subcounty_id, id, parish, '" . _('Parish') . "' FROM care_ug_parish");

executeSQL("TRUNCATE care_address5");
executeSQL("INSERT INTO care_address5 SELECT 'UG', parish_id, id, village, '" . _('Village') . "' FROM care_ug_village");

UpdateDBNo(basename(__FILE__, '.php'));

?>