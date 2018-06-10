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
				<div class="CanvasTitle">', _('Work Orders'), '
					<img title="', _('Remove From Your Dashboard'), '" class="menu_exit_icon" src="css/new/images/cross.png" onclick="RemoveApplet(', $MyRow['id'], ', \'', $Title, '\'); return false;" />
				</div>
			</th>
		</tr>';

$SQL = "SELECT workorders.wo,
				woitems.stockid,
				stockmaster.
				description,
				stockmaster.decimalplaces,
				woitems.qtyreqd,
				woitems.qtyrecd,
				workorders.requiredby,
				workorders.startdate
			FROM workorders
			INNER JOIN woitems
				ON workorders.wo = woitems.wo
			INNER JOIN stockmaster
				ON woitems.stockid = stockmaster.stockid
			ORDER BY workorders.wo LIMIT 5";
$WorkOrdersResult = DB_query($SQL);

echo '<tr>
		<th class="dashboard_column_head">', _('Item'), '</th>
		<th class="dashboard_column_head">', _('Quantity Required'), '</th>
		<th class="dashboard_column_head">', _('Quantity Outstanding'), '</th>
	</tr>';

while ($MyRow = DB_fetch_array($WorkOrdersResult)) {
	$StockId = $MyRow['stockid'];
	$FormatedRequiredByDate = ConvertSQLDate($MyRow['requiredby']);
	$FormatedStartDate = ConvertSQLDate($MyRow['startdate']);
	$QuantityRequired = locale_number_format($MyRow['qtyreqd'], $MyRow['decimalplaces']);
	$QuantityReceived = locale_number_format($MyRow['qtyreqd'] - $MyRow['qtyrecd'], $MyRow['decimalplaces']);

	echo '<tr class="dashboard_striped_row">
			<td><a href="#" onclick="Show(1, \'StockStatus.php?StockID=', urlencode($StockId), '\', \'', _('Stock Status Inquiry'), '\'); return false;">', $MyRow['stockid'], '</td>
			<td class="number">', $QuantityRequired, '</td>
			<td class="number">', $QuantityReceived, '</td>
		</tr>';

}

echo '</table>';

?>