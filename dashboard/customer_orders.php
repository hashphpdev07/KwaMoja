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
				<div class="CanvasTitle">', _('Latest Customer Orders'), '
					<img title="', _('Remove From Your Dashboard'), '" class="menu_exit_icon" src="css/new/images/cross.png" onclick="RemoveApplet(', $MyRow['id'], ', \'', $Title, '\'); return false;" />
				</div>
			</th>
		</tr>';

$SQL = "SELECT salesorders.orderno,
				debtorsmaster.name,
				debtorsmaster.currcode,
				salesorders.orddate,
				salesorders.deliverydate,
				currencies.decimalplaces AS currdecimalplaces,
				SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)) AS ordervalue
			FROM salesorders
			INNER JOIN salesorderdetails
				ON salesorders.orderno = salesorderdetails.orderno
			INNER JOIN debtorsmaster
				ON salesorders.debtorno = debtorsmaster.debtorno
			INNER JOIN custbranch
				ON salesorders.branchcode = custbranch.branchcode
				AND salesorders.debtorno = custbranch.debtorno
			INNER JOIN currencies
				ON debtorsmaster.currcode = currencies.currabrev
			WHERE salesorderdetails.completed = 0
			GROUP BY salesorders.orderno,
					debtorsmaster.name,
					currencies.decimalplaces,
					custbranch.brname,
					salesorders.customerref,
					salesorders.orddate
			ORDER BY salesorders.orderno LIMIT 5";

$SalesOrdersResult = DB_query($SQL);

$TotalSalesOrders = 0;
echo '<tr>
		<th class="dashboard_column_head">', _('Order number'), '</th>
		<th class="dashboard_column_head">', _('Customer'), '</th>
		<th class="dashboard_column_head">', _('Order Date'), '</th>
		<th class="dashboard_column_head">', _('Delivery Date'), '</th>
		<th class="dashboard_column_head">', _('Order Amount'), '</th>
		<th class="dashboard_column_head">', _('Currency'), '</th>
	</tr> ';

while ($MyRow = DB_fetch_array($SalesOrdersResult)) {

	$FormatedOrderValue = locale_number_format($MyRow['ordervalue'], $MyRow['currdecimalplaces']);
	$OrderDate = ConvertSQLDate($MyRow['orddate']);
	$DelDate = ConvertSQLDate($MyRow['deliverydate']);
	$TotalSalesOrders+= $MyRow['ordervalue'];
	echo '<tr class="dashboard_striped_row">
			<td>', $MyRow['orderno'], ' </td>
			<td>', $MyRow['name'], ' </td>
			<td>', $OrderDate, '</td>
			<td>', $DelDate, '</td>
			<td class="number">', $FormatedOrderValue, '</td>
			<td>', $MyRow['currcode'], '</td>
		</tr>';
}
echo '<tr>
		<td colspan=3>', _('Total'), '</td>
		<td colspan=3 class="number">', locale_number_format($TotalSalesOrders, $MyRow['currdecimalplaces']), '</td>
	</tr>';

echo '</table>';

?>