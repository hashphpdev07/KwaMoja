<?php

CreateTable('pctags',
"CREATE TABLE `pctags` (
  `pccashdetail` int(11) NOT NULL,
  `tag` int(11) NOT NULL,
  PRIMARY KEY (`pccashdetail`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$SQL = "INSERT IGNORE INTO pctags (SELECT counterindex, tag FROM pcashdetails)";
$Result = DB_query($SQL);

DropColumn('tag', 'pcashdetails');

UpdateDBNo(basename(__FILE__, '.php'));

?>