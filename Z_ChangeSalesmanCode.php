<?php
/* $Id: Z_ChangeSalesmanCode.php 7751 2017-04-13 16:34:26Z rchacon $*/
/* This script is an utility to change a salesman code. */

include ('includes/session.php');
$Title = _('UTILITY PAGE To Change A Salesman Code In All Tables'); // Screen identificator.
$ViewTopic = 'SpecialUtilities'; // Filename's id in ManualContents.php's TOC.
$BookMark = 'Z_ChangeSalesmanCode'; // Anchor's id in the manual's html document.
include ('includes/header.php');
echo '<p class="page_title_text"><img alt="" src="' . $RootPath . '/css/' . $Theme . '/images/user.png" title="' . _('Change A Salesman Code') . '" /> ' . // Icon title.
_('Change A Salesman Code') . '</p>'; // Page title.
if (isset($_POST['ProcessSalesmanChange'])) {
	$_POST['NewSalesmanCode'] = mb_strtoupper($_POST['NewSalesmanCode']);

	/*First check salesman code exists */
	$Result = DB_query("SELECT salesmancode FROM salesman WHERE salesmancode='" . $_POST['OldSalesmanCode'] . "'");
	if (DB_num_rows($Result) == 0) {
		prnMsg(_('The salesman code') . ': ' . $_POST['OldSalesmanCode'] . ' ' . _('does not currently exist as a salesman code in the system'), 'error');
		include ('includes/footer.php');
		exit;
	}

	if (ContainsIllegalCharacters($_POST['NewSalesmanCode'])) {
		prnMsg(_('The new salesman code to change the old code to contains illegal characters - no changes will be made'), 'error');
		include ('includes/footer.php');
		exit;
	}

	if ($_POST['NewSalesmanCode'] == '') {
		prnMsg(_('The new salesman code to change the old code to must be entered as well'), 'error');
		include ('includes/footer.php');
		exit;
	}

	/*Now check that the new code doesn't already exist */
	$Result = DB_query("SELECT salesmancode FROM salesman WHERE salesmancode='" . $_POST['NewSalesmanCode'] . "'");

	if (DB_num_rows($Result) != 0) {
		echo '<br /><br />';
		prnMsg(_('The replacement salesman code') . ': ' . $_POST['NewSalesmanCode'] . ' ' . _('already exists as a salesman code in the system') . ' - ' . _('a unique salesman code must be entered for the new code'), 'error');
		include ('includes/footer.php');
		exit;
	}
	$Result = DB_Txn_Begin();

	prnMsg(_('Inserting new salesman master record'), 'info');
	$SQL = "INSERT INTO salesman (`salesmancode`,
								`salesmanname`,
								`commissionrate1`,
								`commissionrate2`,
								`breakpoint`,
								`smantel`,
								`smanfax`,
								`current`)
					SELECT '" . $_POST['NewSalesmanCode'] . "',
								`salesmanname`,
								`commissionrate1`,
								`commissionrate2`,
								`breakpoint`,
								`smantel`,
								`smanfax`,
								`current`
					FROM salesman
					WHERE salesmancode='" . $_POST['OldSalesmanCode'] . "'";

	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('The SQL to insert new salesman master record failed') . ', ' . _('the SQL statement was');
	$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
	echo ' ... ' . _('completed');

	prnMsg(_('Changing debtor transaction records'), 'info');
	$SQL = "UPDATE debtortrans SET salesperson='" . $_POST['NewSalesmanCode'] . "' WHERE salesperson='" . $_POST['OldSalesmanCode'] . "'";

	$ErrMsg = _('The SQL to update debtor transaction records failed');
	$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
	echo ' ... ' . _('completed');

	prnMsg(_('Changing debtor branch records'), 'info');
	$SQL = "UPDATE custbranch SET salesman='" . $_POST['NewSalesmanCode'] . "' WHERE salesman='" . $_POST['OldSalesmanCode'] . "'";

	$ErrMsg = _('The SQL to update debtor branch records failed');
	$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
	echo ' ... ' . _('completed');

	prnMsg(_('Changing sales analysis records'), 'info');
	$SQL = "UPDATE salesanalysis SET salesperson='" . $_POST['NewSalesmanCode'] . "' WHERE salesperson='" . $_POST['OldSalesmanCode'] . "'";

	$ErrMsg = _('The SQL to update Sales Analysis records failed');
	$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
	echo ' ... ' . _('completed');

	prnMsg(_('Changing sales orders records'), 'info');
	$SQL = "UPDATE salesorders SET salesperson='" . $_POST['NewSalesmanCode'] . "' WHERE salesperson='" . $_POST['OldSalesmanCode'] . "'";

	$ErrMsg = _('The SQL to update the sales order header records failed');
	$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
	echo ' ... ' . _('completed');

	prnMsg(_('Changing user salesman records'), 'info');
	$SQL = "UPDATE www_users SET salesman='" . $_POST['NewSalesmanCode'] . "' WHERE salesman='" . $_POST['OldSalesmanCode'] . "'";

	$ErrMsg = _('The SQL to update the user records failed');
	$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
	echo ' ... ' . _('completed');

	$Result = DB_IgnoreForeignKeys();

	prnMsg(_('Deleting old salesman master record'), 'info');
	$SQL = "DELETE FROM salesman WHERE salesmancode='" . $_POST['OldSalesmanCode'] . "'";

	$ErrMsg = _('The SQL to delete the old salesman record failed');
	$Result = DB_query($SQL, $ErrMsg, $DbgMsg, true);
	echo ' ... ' . _('completed');

	$Result = DB_Txn_Commit();
	$Result = DB_ReinstateForeignKeys();

}

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '" method="post">';
echo '<div class="centre">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<br />
	<table>
	<tr>
		<td>' . _('Existing Salesman Code') . ':</td>
		<td><input type="text" data-type="no-illegal-chars" name="OldSalesmanCode" size="4" maxlength="4" /></td>
	</tr>
	<tr>
		<td> ' . _('New Salesman Code') . ':</td>
		<td><input type="text" data-type="no-illegal-chars" name="NewSalesmanCode" size="4" maxlength="4" /></td>
	</tr>
	</table>

	<input type="submit" name="ProcessSalesmanChange" value="' . _('Process') . '" />
	</div>
	</form>';
include ('includes/footer.php');
?>
