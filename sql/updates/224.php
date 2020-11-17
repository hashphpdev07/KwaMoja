<?php
CreateTable('gltotals', "CREATE TABLE IF NOT EXISTS `gltotals` (
  `account` varchar(20) NOT NULL DEFAULT '',
  `period` smallint(6) NOT NULL DEFAULT 0,
  `amount` double NOT NULL DEFAULT 0.0,
  PRIMARY KEY  (`account`, `period`)
)");

$SQL = "TRUNCATE gltotals";
$Result = DB_query($SQL);
$SQL = "INSERT INTO gltotals (account, period, amount)
		SELECT account, periodno, SUM(amount) as total FROM gltrans GROUP BY account,periodno";
$Result = DB_query($SQL);

UpdateDBNo(basename(__FILE__, '.php'));

?>