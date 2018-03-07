<?php

AddColumn('taxcatid', 'pcexpenses', 'TINYINT(4)', 'NOT NULL', '1', 'tag');
AddColumn('taxgroupid', 'pctabs', 'TINYINT(4)', 'NOT NULL', '1', 'defaulttag');

CreateTable('pcashdetailtaxes', "CREATE TABLE `pcashdetailtaxes` (
	`counterindex` INT(20) NOT NULL AUTO_INCREMENT,
	`pccashdetail` INT(20) NOT NULL DEFAULT 0,
	`calculationorder` TINYINT(4) NOT NULL DEFAULT 0,
	`description` VARCHAR(40) NOT NULL DEFAULT '',
	`taxauthid` TINYINT(4) NOT NULL DEFAULT '0',
	`purchtaxglaccount` VARCHAR(20) NOT NULL DEFAULT '',
	`taxontax` TINYINT(4) NOT NULL DEFAULT 0,
	`taxrate` DOUBLE NOT NULL DEFAULT 0.0,
	`amount` DOUBLE NOT NULL DEFAULT 0.0,
	PRIMARY KEY(counterindex)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

UpdateDBNo(basename(__FILE__, '.php'));

?>