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
				<div class="CanvasTitle">', _('Latest Bank Transactions'), '
					<img title="', _('Remove From Your Dashboard'), '" class="menu_exit_icon" src="css/new/images/cross.png" onclick="RemoveApplet(', $MyRow['id'], ', \'', $Title, '\'); return false;" />
				</div>
			</th>
		</tr>';

$SQL = "SELECT banktrans.currcode,
				banktrans.amount,
				banktrans.functionalexrate,
				banktrans.exrate,
				banktrans.banktranstype,
				banktrans.transdate,
				bankaccounts.bankaccountname,
				systypes.typename,
				currencies.decimalplaces
			FROM banktrans
			INNER JOIN bankaccounts
				ON banktrans.bankact=bankaccounts.accountcode
			INNER JOIN systypes
				ON banktrans.type=systypes.typeid
			INNER JOIN currencies
				ON banktrans.currcode=currencies.currabrev
			ORDER BY banktrans.transdate DESC LIMIT 5";

$Result = DB_query($SQL);
$AccountCurrTotal = 0;
$LocalCurrTotal = 0;

echo '<tr class="dashboard_header_row">
		<th class="dashboard_column_head">', _('Amount'), '</th>
		<th class="dashboard_column_head">', _('Trans Type'), '</th>
		<th class="dashboard_column_head">', _('Trans Date'), '</th>
		<th class="dashboard_column_head">', _('Account Name'), '</th>
	</tr>';

while ($MyRow = DB_fetch_array($Result)) {

	$AccountCurrTotal+= $MyRow['amount'];
	$LocalCurrTotal+= $MyRow['amount'] / $MyRow['functionalexrate'] / $MyRow['exrate'];
	echo '<tr class="dashboard_striped_row">
			<td class="number">', locale_number_format(abs($MyRow['amount']), $MyRow['decimalplaces']), ' ', $MyRow['currcode'], '</td>
			<td>', $MyRow['typename'], '</td>
			<td>', ConvertSQLDate($MyRow['transdate']), '</td>
			<td>', $MyRow['bankaccountname'], '</td>
		</tr>';
}
echo '</table>';

?>