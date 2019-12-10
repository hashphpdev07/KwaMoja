<?php
/**********************************************************/
$PathPrefix = '../';

if (basename($_SERVER['SCRIPT_NAME']) != 'Dashboard.php') {
	require_once ($PathPrefix . 'includes/session.php');
	$DashBoardURL = $RootPath . '/Dashboard.php';
}

$ScriptTitle = _('Latest bank transactions');

$SQL = "SELECT id FROM dashboard_scripts WHERE scripts='" . basename(basename(__FILE__)) . "'";
$DashboardResult = DB_query($SQL);
$DashboardRow = DB_fetch_array($DashboardResult);

echo '<div class="container">
		<table class="DashboardTable">
			<tr>
				<th colspan="5">
					<div class="CanvasTitle">', $ScriptTitle, '
						<a class="CloseButton" href="', $DashBoardURL, '?Remove=', urlencode($DashboardRow['id']), '" target="_parent" id="CloseButton">X</a>
					</div>
				</th>
			</tr>';
/* The section above must be left as is, apart from changing the script title.
 * Making other changes could stop the dashboard from functioning
*/

/**********************************************************************/
$SQL = "";
$DashboardResult = DB_query($SQL);
/* Create an SQL SELECT query to produce the data you want to show
 * and store the result in $DashboardResult
*/

/**********************************************************************/
echo '<thead>
		<tr>
			<th>Column 1</th>
			<th>Column 2</th>
			.................
			.................
			<th>Final Column</th>
		</tr>
	</thead>
	<tbody>';
/* Create the table/column headings for the output that you want to show
*/

while ($row = DB_fetch_array($DashboardResult)) {

	$AccountCurrTotal+= $row['amount'];
	$LocalCurrTotal+= $row['amount'] / $row['functionalexrate'] / $row['exrate'];
	echo '<tr class="striped_row">
			<td>', $row['currcode'], '</td>
			<td class="number">', locale_number_format($row['amount'], $row['decimalplaces']), '</td>
			<td>', $row['banktranstype'], '</td>
			<td>', ConvertSQLDate($row['transdate']), '</td>
			<td class="number">', $row['bankaccountname'], '</td>
		</tr>';
}
echo '</tbody>
	</table>';

?>