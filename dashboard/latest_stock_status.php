<?php
$PageSecurity = 0;
$PathPrefix = '../';
include ('../includes/session.php');

$SQL = "SELECT id, description FROM dashboard_scripts WHERE scripts='" . basename(basename(__FILE__)) . "'";
$Result = DB_query($SQL);
$MyRow = DB_fetch_array($Result);

$Title = $MyRow['description'];

$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.longdescription,
						stockmaster.mbflag,
						stockmaster.discontinued,
						SUM(locstock.quantity) AS qoh,
						stockmaster.units,
						stockmaster.decimalplaces
					FROM stockmaster
					LEFT JOIN stockcategory
					ON stockmaster.categoryid=stockcategory.categoryid,
						locstock
					WHERE stockmaster.stockid=locstock.stockid
					GROUP BY stockmaster.stockid,
						stockmaster.description,
						stockmaster.longdescription,
						stockmaster.units,
						stockmaster.mbflag,
						stockmaster.discontinued,
						stockmaster.decimalplaces
					ORDER BY stockmaster.discontinued, stockmaster.stockid LIMIT 5";
$SearchResult = DB_query($SQL);

echo '<table class="dashboard_table">
		<tr class="dashboard_row">
			<th colspan="4" class="dashboard_th">
				<div class="CanvasTitle">', _('Latest Stock Status'), '
					<img title="', _('Remove From Your Dashboard'), '" class="menu_exit_icon" src="css/new/images/cross.png" onclick="RemoveApplet(', $MyRow['id'], ', \'', $Title, '\'); return false;" />
				</div>
			</th>
		</tr>';

echo '<tr>
		<th class="dashboard_column_head">', _('Code'), '</th>
		<th class="dashboard_column_head">', _('Description'), '</th>
		<th class="dashboard_column_head">', _('Total Quantity on Hand'), '</th>
		<th class="dashboard_column_head">', _('Units'), '</th>
	</tr>
</thead>';

echo '<tbody>';
while ($row = DB_fetch_array($SearchResult)) {
	$qoh = locale_number_format($row['qoh'], $row['decimalplaces']);

	echo '<tr class="dashboard_striped_row">
			<td>' . $row['stockid'] . '</td>
			<td>' . $row['description'] . '</td>
			<td class="number">' . $qoh . '</td>
			<td> ' . $row['units'] . '</td>
		</tr>';

}

echo '</tbody>
	</table>';

?>