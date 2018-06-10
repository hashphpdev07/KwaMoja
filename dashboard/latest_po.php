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
			<th colspan="5" class="dashboard_th">
				<div class="CanvasTitle">', $Title, '
					<img title="', _('Remove From Your Dashboard'), '" class="menu_exit_icon" src="css/new/images/cross.png" onclick="RemoveApplet(', $MyRow['id'], ', \'', $Title, '\'); return false;" />
				</div>
			</th>
		</tr>';

$SQL = "SELECT purchorders.orderno,
				suppliers.suppname,
				purchorders.orddate,
				purchorders.deliverydate,
				purchorders.initiator,
				purchorders.requisitionno,
				purchorders.allowprint,
				purchorders.status,
				suppliers.currcode,
				currencies.decimalplaces AS currdecimalplaces,
				SUM(purchorderdetails.unitprice*purchorderdetails.quantityord) AS ordervalue
			FROM purchorders
			INNER JOIN purchorderdetails
				ON purchorders.orderno = purchorderdetails.orderno
			INNER JOIN suppliers
				ON purchorders.supplierno = suppliers.supplierid
			INNER JOIN currencies
				ON suppliers.currcode=currencies.currabrev
			WHERE purchorders.orderno=purchorderdetails.orderno
			GROUP BY purchorders.orderno,
					suppliers.suppname,
					purchorders.orddate,
					purchorders.initiator,
					purchorders.requisitionno,
					purchorders.allowprint,
					purchorders.status,
					suppliers.currcode,
					currencies.decimalplaces
			ORDER BY orddate DESC LIMIT 5";
$SalesOrdersResult2 = DB_query($SQL);
$Total = 0;

echo '<tr>
		<th class="dashboard_column_head">', _('Supplier'), '</th>
		<th class="dashboard_column_head">', _('Order Date'), '</th>
		<th class="dashboard_column_head">', _('Delivery Date'), '</th>
		<th class="dashboard_column_head">', _('Order Total'), '</th>
		<th class="dashboard_column_head">', _('Status'), '</th>
	</tr>';

while ($MyRow = DB_fetch_array($SalesOrdersResult2)) {
	$FormatedOrderValue2 = locale_number_format($MyRow['ordervalue'], $MyRow['currdecimalplaces']);
	$Total+= $MyRow['ordervalue'];

	$FormatedOrderDate1 = ConvertSQLDate($MyRow['orddate']);
	$FormatedDelDate1 = ConvertSQLDate($MyRow['deliverydate']);

	echo '<tr class="dashboard_striped_row">
			<td> ' . $MyRow['suppname'] . ' </td>
			<td>' . $FormatedOrderDate1 . '</td>
			<td>' . $FormatedDelDate1 . '</td>
			<td class="number">' . $FormatedOrderValue2 . '</td>
			<td> ' . $MyRow['status'] . ' </td> ';

}
echo '</table>';

?>