<?php
$PageSecurity = 0;
$PathPrefix = '../';
include ('../includes/session.php');

$SQL = "SELECT id, description FROM dashboard_scripts WHERE scripts='" . basename(basename(__FILE__)) . "'";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);

$Title = $MyRow['description'];

echo '<table class="dashboard_table">
		<tr class="dashboard_row">
			<th colspan="6" class="dashboard_th">
				<div class="CanvasTitle">', _('Purchase Orders to Authorise'), '
					<img title="', _('Remove From Your Dashboard'), '" class="menu_exit_icon" src="css/new/images/cross.png" onclick="RemoveApplet(', $MyRow['id'], ', \'', $Title, '\'); return false;" />
				</div>
			</th>
		</tr>';

$SQL = "SELECT stockmaster.stockid,
				stockmaster.description,
				stockmaster.mbflag,
				SUM(locstock.quantity) AS qoh,
				stockmaster.units,
				stockmaster.decimalplaces
			FROM stockmaster,
				locstock
			WHERE stockmaster.stockid=locstock.stockid
			GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag,
					stockmaster.decimalplaces
			ORDER BY qoh DESC LIMIT 5";

$SearchResult = DB_query($SQL);

echo '<tr class="dashboard_row">
		<th class="dashboard_column_head">', _('Code'), '</th>
		<th class="dashboard_column_head">', _('Description'), '</th>
		<th class="dashboard_column_head">', _('Total QTY on Hand'), '</th>
		<th class="dashboard_column_head">', _('Units'), '</th>
	</tr>';

while ($MyRow = DB_fetch_array($SearchResult)) {
	$StockId = $MyRow['stockid'];
	$qoh = locale_number_format($MyRow['qoh'], $MyRow['decimalplaces']);

	echo '<tr class="dashboard_striped_row dashboard_row">
			<td><a href="#" onclick="Show(1, \'StockStatus.php?StockID=', urlencode($StockId), '\', \'', _('Stock Status Inquiry'), '\'); return false;">', $MyRow['stockid'], '</td>
			<td>', $MyRow['description'], '</td>
			<td class="number">', $qoh, '</td>
			<td>', $MyRow['units'], '</td>
		</tr>';
}

echo '</table>';

?>