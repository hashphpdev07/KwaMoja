<?php
CreateTable('gltotals', "CREATE TABLE IF NOT EXISTS `gltotals` (
  `account` varchar(20) NOT NULL DEFAULT '',
  `period` smallint(6) NOT NULL DEFAULT 0,
  `amount` double NOT NULL DEFAULT 0.0,
  PRIMARY KEY  (`account`, `period`)
)");

$SQL = "TRUNCATE gltotals";
$Result = DB_query($SQL);

$PeriodsSQL = "SELECT periodno FROM periods";
$PeriodsResult = DB_query($PeriodSQL);
while ($PeriodRow = DB_fetch_array($PeriodsResult)) {
	$CreateEntriesSQL = "INSERT INTO gltotals (account, period, amount) SELECT accountcode, '" . $PeriodRow['periodno'] . "', 0 FROM chartmaster";
	$CreateEntriesResult = DB_query($CreateEntriesSQL);
}

$TotalsSQL = "SELECT account, period FROM gltotals";
$TotalsResult = DB_query($TotalsSQL);
while ($TotalsRow = DB_fetch_array($TotalsResult)) {
	$TotalSum = "SELECT SUM(amount) as total FROM gltrans WHERE account='" . $TotalsRow['account'] . "' AND periodno='" . $TotalsRow['periodno'] . "'";
	$TotalResult = DB_query($TotalSum);
	$TotalRow = DB_fetch_array($TotalResult);
	$UpdateSQL = "UPDATE gltotals SET amount='" . $TotalRow['total'] . "'
									WHERE account='" . $TotalsRow['account'] . "'
									AND periodno='" . $TotalsRow['periodno'] . "'";
	$UpdateResult = DB_query($UpdateSQL);
}

UpdateDBNo(basename(__FILE__, '.php'));

?>