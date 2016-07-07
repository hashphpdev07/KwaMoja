<?php

CreateTable('standardnotes',
"CREATE TABLE `standardnotes` (
  `id` INT(11) NOT NULL DEFAULT 0,
  `title` VARCHAR(50) NOT NULL DEFAULT '',
  `note` TEXT,
  PRIMARY KEY (`id`)
)");

CreateTable('patientrecord',
"CREATE TABLE `patientrecord` (
  `id` INT(11) NOT NULL DEFAULT 0,
  `debtorno` varchar(10) NOT NULL DEFAULT '0',
  `createdby` VARCHAR(20) NOT NULL DEFAULT '',
  `doctor` VARCHAR(100) NOT NULL DEFAULT '',
  `creationdate` DATETIME NOT NULL DEFAULT '0000-00-00',
  `record` TEXT,
  PRIMARY KEY (`id`),
  CONSTRAINT `patientrecord_ibfk_1` FOREIGN KEY (`createdby`) REFERENCES `www_users` (`userid`),
  CONSTRAINT `patientrecord_ibfk_2` FOREIGN KEY (`debtorno`) REFERENCES `debtorsmaster` (`debtorno`)
)");

CreateTable('insuranceco',
"CREATE TABLE `insuranceco` (
  `id` INT(11) NOT NULL DEFAULT 0,
  `name` VARCHAR(100) NOT NULL DEFAULT '',
  `address1` VARCHAR(40) NOT NULL DEFAULT '',
  `address2` VARCHAR(40) NOT NULL DEFAULT '',
  `address3` VARCHAR(40) NOT NULL DEFAULT '',
  `address4` VARCHAR(50) NOT NULL DEFAULT '',
  `address5` VARCHAR(20) NOT NULL DEFAULT '',
  `address6` VARCHAR(40) NOT NULL DEFAULT '',
  `currcode` char(3) NOT NULL DEFAULT '',
  `paymentterms` char(2) NOT NULL DEFAULT '',
  `billingfrequency` tinyint(2) NOT NULL DEFAULT 0,
  `insurancetype` tinyint(4) NOT NULL DEFAULT 0,
  `phoneno` varchar(20) NOT NULL DEFAULT '',
  `faxno` varchar(20) NOT NULL DEFAULT '',
  `contactname` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(55) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `Currency` (`currcode`),
  KEY `PaymentTerms` (`paymentterms`),
  KEY `InsuranceType` (`insurancetype`),
  CONSTRAINT `insuranceco_ibfk_1` FOREIGN KEY (`currcode`) REFERENCES `currencies` (`currabrev`),
  CONSTRAINT `insuranceco_ibfk_2` FOREIGN KEY (`paymentterms`) REFERENCES `paymentterms` (`termsindicator`),
  CONSTRAINT `insuranceco_ibfk_3` FOREIGN KEY (`insurancetype`) REFERENCES `insurancetypes` (`typeid`)
)");

CreateTable('insurancepolicy',
"CREATE TABLE `insurancepolicy` (
  `id` INT(11) NOT NULL DEFAULT 0,
  `companyid` INT(11) NOT NULL DEFAULT 0,
  `policyname` VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `CompanyID` (`companyid`),
  CONSTRAINT `insurancepolicy_ibfk_1` FOREIGN KEY (`companyid`) REFERENCES `insuranceco` (`id`)
)");

CreateTable('insurancetypes',
"CREATE TABLE `insurancetypes` (
  `typeid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `typename` varchar(100) NOT NULL,
  PRIMARY KEY (`typeid`)
)");

NewScript('StandardPatientNotes.php', '15');
NewScript('KCMCInsuranceTypes.php', '15');

NewMenuItem('system', 'Medical', _('Create Standard Notes For Inclusion In Patient Files'), '/StandardPatientNotes.php', 1);
NewMenuItem('system', 'Medical', _('Types of Insurance company'), '/KCMCInsuranceTypes.php', 2);

NewSysType(70, 'Standard Note');
NewSysType(520, 'Insurance Company');

UpdateDBNo(basename(__FILE__, '.php'));

?>