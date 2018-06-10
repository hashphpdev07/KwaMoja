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
				<div class="CanvasTitle">', _('Latest Unpaid Customer Invoices'), '
					<img title="', _('Remove From Your Dashboard'), '" class="menu_exit_icon" src="css/new/images/cross.png" onclick="RemoveApplet(', $MyRow['id'], ', \'', $Title, '\'); return false;" />
				</div>
			</th>
		</tr>';

$SQL = "SELECT salesorders.orderno,
				debtorsmaster.name,
				custbranch.brname,
				salesorders.customerref,
				salesorders.orddate,
				salesorders.deliverydate,
				salesorders.deliverto,
				salesorders.printedpackingslip,
				salesorders.poplaced,
				currencies.decimalplaces AS currdecimalplaces,
				SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)/currencies.rate) AS ordervalue
			FROM salesorders
			INNER JOIN salesorderdetails
				ON salesorders.orderno = salesorderdetails.orderno
			INNER JOIN debtorsmaster
				ON salesorders.debtorno = debtorsmaster.debtorno
			INNER JOIN custbranch
				ON debtorsmaster.debtorno = custbranch.debtorno
				AND salesorders.branchcode = custbranch.branchcode
			INNER JOIN currencies
				ON debtorsmaster.currcode = currencies.currabrev
			WHERE salesorderdetails.completed=0
			GROUP BY salesorders.orderno,
					debtorsmaster.name,
					custbranch.brname,
					salesorders.customerref,
					salesorders.orddate,
					salesorders.deliverydate,
					salesorders.deliverto,
					salesorders.printedpackingslip,
					salesorders.poplaced
			ORDER BY salesorders.orderno";
$SalesOrdersResult1 = DB_query($SQL);

echo '<tr>
		<th class="dashboard_column_head">', _('Customer'), '</th>
		<th class="dashboard_column_head">', _('Order Date'), '</th>
		<th class="dashboard_column_head">', _('Delivery Date'), '</th>
		<th class="dashboard_column_head">', _('Delivery To'), '</th>
		<th class="dashboard_column_head">', _('Order Total'), '</th>
	</tr> ';

$TotalOrderValue = 0;
while ($MyRow = DB_fetch_array($SalesOrdersResult1)) {
	$OrderValue = locale_number_format($MyRow['ordervalue'], $MyRow['currdecimalplaces']);
	$TotalOrderValue+= $MyRow['ordervalue'];

	$FormatedOrderDate = ConvertSQLDate($MyRow['orddate']);
	$FormatedDelDate = ConvertSQLDate($MyRow['deliverydate']);

	echo '<tr class="dashboard_striped_row">
			<td>', $MyRow['name'], '</td>
			<td>', $FormatedOrderDate, '</td>
			<td>', $FormatedDelDate, '</td>
			<td>', $MyRow['deliverto'], ' </td>
			<td class="number">', $OrderValue, '</td>
		</tr>';

}
echo '<tr>
		<td colspan="4">', _('Total'), '</td>
		<td colspan="2" class="number">', locale_number_format($TotalOrderValue, $MyRow['currdecimalplaces']), '</td>
	</tr>';

echo '</table>';

?>