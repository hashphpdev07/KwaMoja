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

$SQL = "SELECT purchorders.*,
			suppliers.suppname,
			suppliers.currcode,
			www_users.realname,
			www_users.email,
			currencies.decimalplaces AS currdecimalplaces
		FROM purchorders INNER JOIN suppliers
			ON suppliers.supplierid=purchorders.supplierno
		INNER JOIN currencies
			ON suppliers.currcode=currencies.currabrev
		INNER JOIN www_users
			ON www_users.userid=purchorders.initiator
		WHERE status='Pending' LIMIT 5";
$Result = DB_query($SQL);
echo '<tr>
		<th class="dashboard_column_head">', _('Supplier'), '</th>
		<th class="dashboard_column_head">', _('Email'), '</th>
		<th class="dashboard_column_head">', _('Order Date'), '</th>
		<th class="dashboard_column_head">', _('Delivery Date'), '</th>
		<th class="dashboard_column_head">', _('Total Amount'), '</th>
		<th class="dashboard_column_head">', _('Status'), '</th>
	</tr>';

while ($MyRow = DB_fetch_array($Result)) {
	$AuthSQL = "SELECT authlevel
					FROM purchorderauth
					WHERE currabrev='" . $MyRow['currcode'] . "'
						AND userid='" . $_SESSION['UserID'] . "'";

	$AuthResult = DB_query($AuthSQL);
	$myauthrow = DB_fetch_array($AuthResult);
	$AuthLevel = $myauthrow['authlevel'];

	$OrderValueSQL = "SELECT sum(unitprice*quantityord) as ordervalue,
							sum(unitprice*quantityord) as total
						FROM purchorderdetails
						GROUP BY orderno";

	$OrderValueResult = DB_query($OrderValueSQL);
	$MyOrderValueRow = DB_fetch_array($OrderValueResult);
	$OrderValue = $MyOrderValueRow['ordervalue'];
	$TotalOV = $MyOrderValueRow['total'];

	$FormatedOrderDate2 = ConvertSQLDate($MyRow['orddate']);
	$FormatedDelDate2 = ConvertSQLDate($MyRow['deliverydate']);

	echo '<tr class="dashboard_striped_row">
			<td>', $MyRow['suppname'], '</td>
			<td>', $MyRow['email'], '</td>
			<td>', $FormatedOrderDate2, '</td>
			<td>', $FormatedDelDate2, '</td>
			<td class="number">', locale_number_format($TotalOV, $MyRow['currdecimalplaces']), '</td>
			<td>', $MyRow['status'], '</td>
		</tr>';

}
echo '</table>';

?>