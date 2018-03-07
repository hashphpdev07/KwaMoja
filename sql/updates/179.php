<?php

AddColumn('freighttaxcatid', 'taxprovinces', 'TINYINT(4)', 'NOT NULL', '0', 'taxprovincename');

$SQL = "SELECT taxcatid FROM taxcategories WHERE taxcatname='Freight'";
$TaxCatResult = DB_query($SQL);

if (DB_num_rows($TaxCatResult) > 0) {
	$TaxCatRow = DB_fetch_array($TaxCatResult);
	$UpdateSQL = "UPDATE taxprovinces SET freighttaxcatid='" . $TaxCatRow['taxcatid'] . "'";
	$UpdateResult = DB_query($UpdateSQL);
}

UpdateDBNo(basename(__FILE__, '.php'));

?>