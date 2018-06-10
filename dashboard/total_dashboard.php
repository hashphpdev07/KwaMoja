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
				<div class="CanvasTitle">', _('Total Sales/Purchase Orders'), '
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
			GROUP BY salesorders.orderno,
					debtorsmaster.name,
					currencies.decimalplaces,
					custbranch.brname,
					salesorders.customerref,
					salesorders.orddate ORDER BY salesorders.orderno";

$SalesOrdersResult = DB_query($SQL);

$TotalSalesOrders = 0;
while ($row = DB_fetch_array($SalesOrdersResult)) {
	$TotalSalesOrders+= $row['ordervalue'];
}
echo '<tr class="dashboard_striped_row">
		<td>', _('Total amount of sales orders'), '</td>
		<td class="number">', locale_number_format($TotalSalesOrders, $row['currdecimalplaces']), '</strong>
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
						currencies.decimalplaces LIMIT 5";
$SalesOrdersResult2 = DB_query($SQL);
$TotalPurchaseOrders = 0;
while ($row = DB_fetch_array($SalesOrdersResult2)) {

	$TotalPurchaseOrders+= $row['ordervalue'];
}
echo '<tr class="dashboard_striped_row">
		<td>', _('Total amount of Purchase orders'), '</td>
		<td class="number">', locale_number_format($TotalPurchaseOrders, $row['currdecimalplaces']), '</td>
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
				FROM salesorders INNER JOIN salesorderdetails
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
$TotalOutstanding = 0;
while ($row = DB_fetch_array($SalesOrdersResult1)) {
	$TotalOutstanding+= $row['ordervalue'];
}
echo '<tr class="dashboard_striped_row">
		<td>', _('Total amount of Outstanding to receive'), '</td>
		<td class="number">', locale_number_format($TotalOutstanding, $row['currdecimalplaces']), '</td>
	</tr>
</table>';

?>