<?php
include ('includes/session.php');
$Title = _('Reset stock costs table');
include ('includes/header.php');

// Fetch all stock codes from staockmaster
$SQL = "SELECT stockid FROM stockmaster";
$Result = DB_query($SQL);

while ($MyStockCodesRow = DB_fetch_array($Result)) {
	$CheckSQL = "SELECT stockid FROM stockcosts WHERE stockid='" . $MyStockCodesRow['stockid'] . "'";
	$CheckResult = DB_query($CheckSQL);
	if (DB_num_rows($CheckResult) == 0) {
		//Ensure every item has an entry in stockcosts
		$InsertItemSQL = "INSERT INTO stockcosts VALUES ('" . $MyStockCodesRow['stockid'] . "',
														 '" . 0 . "',
														 '" . 0 . "',
														 '" . 0 . "',
														 NOW,
														 '" . 0 . "'";
		$InsertItemResult = DB_query($InsertItemSQL);
	}
	//Set all succeeded flags to 1
	$UpdateFlagSQL = "UPDATE stockcosts SET succeeded=1 WHERE stockid='" . $MyStockCodesRow['stockid'] . "'";
	$UpdateFlagResult = DB_query($UpdateFlagSQL);

	//Find last entry
	$FindEntrySQL = "SELECT costfrom FROM stockcosts
								WHERE stockid='" . $MyStockCodesRow['stockid'] . "'
								ORDER BY costfrom DESC
								LIMIT 1";
	$FindEntryResult = DB_query($FindEntrySQL);
	$FindEntryRow = DB_fetch_array($FindEntryResult);

	//Set new flag
	$SetFlagSQL = "UPDATE stockcosts SET succeeded=0 WHERE stockid='" . $MyStockCodesRow['stockid'] . "' AND costfrom='" . $FindEntryRow['costfrom'] . "'";
	$SetFlagResult = DB_query($SetFlagSQL);
	prnMsg(_('The stockcosts table has been successfully updated'), 'success');
}

include ('includes/footer.php');

?>