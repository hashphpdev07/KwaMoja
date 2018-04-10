<?php
include ('includes/session.php');
$Title = _('Sales People Maintenance');
$ViewTopic = 'SalesPeople';
$BookMark = 'SalesPeople';
if (isset($_GET['SelectedSalesPerson'])) {
	$BookMark = 'SalespeopleEdit';
} // For Edit's screen.
if (isset($_GET['delete'])) {
	$BookMark = 'SalespeopleDelete';
} // For Delete's ERROR Message Report.
include ('includes/header.php');

if (isset($_GET['SelectedSalesPerson'])) {
	$SelectedSalesPerson = mb_strtoupper($_GET['SelectedSalesPerson']);
} elseif (isset($_POST['SelectedSalesPerson'])) {
	$SelectedSalesPerson = mb_strtoupper($_POST['SelectedSalesPerson']);
}

echo '<p class="page_title_text">
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/maintenance.png" title="', _('Search'), '" alt="" />', ' ', $Title, '
	</p>';

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */
	$i = 1;

	//first off validate inputs sensible
	if (mb_strlen(stripslashes($_POST['SalesmanCode'])) > 3) {
		$InputError = 1;
		prnMsg(_('The salesperson code must be three characters or less long'), 'error');
	} elseif (mb_strlen($_POST['SalesmanCode']) == 0 or $_POST['SalesmanCode'] == '') {
		$InputError = 1;
		prnMsg(_('The salesperson code cannot be empty'), 'error');
	} elseif (mb_strlen($_POST['SalesmanName']) > 30) {
		$InputError = 1;
		prnMsg(_('The salesperson name must be thirty characters or less long'), 'error');
	} elseif (mb_strlen($_POST['SManTel']) > 20) {
		$InputError = 1;
		prnMsg(_('The salesperson telephone number must be twenty characters or less long'), 'error');
	} elseif (mb_strlen($_POST['SManFax']) > 20) {
		$InputError = 1;
		prnMsg(_('The salesperson telephone number must be twenty characters or less long'), 'error');
	} elseif (!is_numeric(filter_number_format($_POST['CommissionRate1'])) or !is_numeric(filter_number_format($_POST['CommissionRate2']))) {
		$InputError = 1;
		prnMsg(_('The commission rates must be a floating point number'), 'error');
	} elseif (!is_numeric(filter_number_format($_POST['Breakpoint']))) {
		$InputError = 1;
		prnMsg(_('The breakpoint should be a floating point number'), 'error');
	}

	if (isset($SelectedSalesPerson) and $InputError != 1) {

		/*SelectedSalesPerson could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/

		$SQL = "UPDATE salesman SET salesmanname='" . $_POST['SalesmanName'] . "',
									salesarea='" . $_POST['SalesArea'] . "',
									commissionrate1='" . filter_number_format($_POST['CommissionRate1']) . "',
									smantel='" . $_POST['SManTel'] . "',
									smanfax='" . $_POST['SManFax'] . "',
									breakpoint='" . filter_number_format($_POST['Breakpoint']) . "',
									commissionrate2='" . filter_number_format($_POST['CommissionRate2']) . "',
									manager='" . $_POST['Manager'] . "',
									current='" . $_POST['Current'] . "'
								WHERE salesmancode = '" . stripslashes($SelectedSalesPerson) . "'";

		$Msg = _('Salesperson record for') . ' ' . stripslashes($_POST['SalesmanName']) . ' ' . _('has been updated');
	} elseif ($InputError != 1) {

		/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */

		$SQL = "INSERT INTO salesman (salesmancode,
						salesmanname,
						salesarea,
						manager,
						commissionrate1,
						commissionrate2,
						breakpoint,
						smantel,
						smanfax,
						current)
				VALUES ('" . $_POST['SalesmanCode'] . "',
						'" . $_POST['SalesmanName'] . "',
						'" . $_POST['SalesArea'] . "',
						'" . $_POST['Manager'] . "',
						'" . filter_number_format($_POST['CommissionRate1']) . "',
						'" . filter_number_format($_POST['CommissionRate2']) . "',
						'" . filter_number_format($_POST['Breakpoint']) . "',
						'" . $_POST['SManTel'] . "',
						'" . $_POST['SManFax'] . "',
						'" . $_POST['Current'] . "'
					)";

		$Msg = _('A new salesperson record has been added for') . ' ' . stripslashes($_POST['SalesmanName']);
	}
	if ($InputError != 1) {

		/* if the sales person is a manager, ensure that there is no other manager for this area */
		if ($_POST['Manager'] == 1 and $_POST['Current'] == 1) {
			$ErrMsg = _('The update of the manager field failed because');
			$DbgMsg = _('The SQL that was used and failed was');
			$ManagerSQL = "UPDATE salesman SET manager=0 WHERE salesarea='" . $_POST['SalesArea'] . "'";
			$Result = DB_query($ManagerSQL, $ErrMsg, $DbgMsg);
		}

		//run the SQL from either of the above possibilites
		$ErrMsg = _('The insert or update of the salesperson failed because');
		$DbgMsg = _('The SQL that was used and failed was');
		$Result = DB_query($SQL, $ErrMsg, $DbgMsg);

		prnMsg($Msg, 'success');

		unset($SelectedSalesPerson);
		unset($_POST['SalesmanCode']);
		unset($_POST['SalesmanName']);
		unset($_POST['SalesArea']);
		unset($_POST['Manager']);
		unset($_POST['CommissionRate1']);
		unset($_POST['CommissionRate2']);
		unset($_POST['Breakpoint']);
		unset($_POST['SManFax']);
		unset($_POST['SManTel']);
		unset($_POST['Current']);
	}

} elseif (isset($_GET['delete'])) {
	//the link to delete a selected record was clicked instead of the submit button
	// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorsMaster'
	$SQL = "SELECT COUNT(*) FROM custbranch WHERE  custbranch.salesman='" . $SelectedSalesPerson . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_row($Result);
	if ($MyRow[0] > 0) {
		prnMsg(_('Cannot delete this salesperson because branches are set up referring to them') . ' - ' . _('first alter the branches concerned') . '<br />' . _('There are') . ' ' . $MyRow[0] . ' ' . _('branches that refer to this salesperson'), 'error');

	} else {
		$SQL = "SELECT COUNT(*) FROM salesanalysis WHERE salesanalysis.salesperson='" . $SelectedSalesPerson . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_row($Result);
		if ($MyRow[0] > 0) {
			prnMsg(_('Cannot delete this salesperson because sales analysis records refer to them'), '<br />' . _('There are') . ' ' . $MyRow[0] . ' ' . _('sales analysis records that refer to this salesperson'), 'error');
		} else {
			$SQL = "SELECT COUNT(*) FROM www_users WHERE salesman='" . $SelectedSalesPerson . "'";
			$Result = DB_query($SQL);
			$MyRow = DB_fetch_row($Result);
			if ($MyRow[0] > 0) {
				prnMsg(_('Cannot delete this salesperson because'), '<br />' . _('There are') . ' ' . $MyRow[0] . ' ' . _('user records that refer to this salesperson') . '.' . _('First delete any users that refer to this sales person'), 'error');
			} else {

				$SQL = "DELETE FROM salesman WHERE salesmancode='" . $SelectedSalesPerson . "'";
				$ErrMsg = _('The salesperson could not be deleted because');
				$Result = DB_query($SQL, $ErrMsg);

				prnMsg(_('Salesperson') . ' ' . stripslashes($SelectedSalesPerson) . ' ' . _('has been deleted from the database'), 'success');
				unset($SelectedSalesPerson);
				unset($delete);
			}
		}
	} //end if Sales-person used in GL accounts
	
}

if (!isset($SelectedSalesPerson)) {

	/* It could still be the second time the page has been run and a record has been selected for modification - SelectedSalesPerson will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
	then none of the above are true and the list of Sales-persons will be displayed with
	links to delete or edit each. These will call the same page again and allow update/input
	or deletion of the records*/

	$SQL = "SELECT salesmancode,
				salesmanname,
				salesarea,
				manager,
				smantel,
				smanfax,
				commissionrate1,
				breakpoint,
				commissionrate2,
				current
			FROM salesman";
	$Result = DB_query($SQL);

	echo '<table>
			<thead>
				<tr>
					<th class="SortedColumn">', _('Code'), '</th>
					<th class="SortedColumn">', _('Name'), '</th>
					<th class="SortedColumn">', _('SalesArea'), '</th>
					<th class="SortedColumn">' . _('Manager'), '</th>
					<th>', _('Telephone'), '</th>
					<th>', _('Facsimile'), '</th>
					<th>', _('Comm Rate 1'), '</th>
					<th>', _('Break'), '</th>
					<th>', _('Comm Rate 2'), '</th>
					<th class="SortedColumn">', _('Current'), '</th>
					<th colspan="2"></th>
				</tr>
			</thead>';
	$k = 0;
	while ($MyRow = DB_fetch_array($Result)) {
		if ($MyRow['current'] == 1) {
			$ActiveText = _('Yes');
		} else {
			$ActiveText = _('No');
		}
		if ($MyRow['manager'] == 1) {
			$ManagerText = _('Yes');
		} else {
			$ManagerText = _('No');
		}

		$SQL = "SELECT areadescription FROM areas WHERE areacode='" . $MyRow['salesarea'] . "'";
		$AreaResult = DB_query($SQL);
		$AreaRow = DB_fetch_array($AreaResult);

		echo '<tr class="striped_row">
				<td>', $MyRow['salesmancode'], '</td>
				<td>', $MyRow['salesmanname'], '</td>
				<td>', $AreaRow['areadescription'], '</td>
				<td>', $ManagerText, '</td>
				<td>', $MyRow['smantel'], '</td>
				<td>', $MyRow['smanfax'], '</td>
				<td class="number">', locale_number_format($MyRow['commissionrate1'], 2), '</td>
				<td class="number">', locale_number_format($MyRow['breakpoint'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td class="number">', locale_number_format($MyRow['commissionrate2'], 2), '</td>
				<td>', $ActiveText, '</td>
				<td><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?SelectedSalesPerson=', urlencode($MyRow['salesmancode']), '">', _('Edit'), '</a></td>
				<td><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?SelectedSalesPerson=', urlencode($MyRow['salesmancode']), '&amp;delete=1" onclick="return MakeConfirm(\'', _('Are you sure you wish to delete this sales person?'), '\', \'Confirm Delete\', this);">', _('Delete'), '</a></td>
			</tr>';

	} //END WHILE LIST LOOP
	echo '</table>';
} //end of ifs and buts!
if (isset($SelectedSalesPerson)) {
	echo '<div class="centre">
			<a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">', _('Show All Sales People'), '</a>
		</div>';
}

echo '<form method="post" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">';
echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

if (isset($SelectedSalesPerson)) {
	//editing an existing Sales-person
	$SQL = "SELECT salesmancode,
					salesmanname,
					salesarea,
					manager,
					smantel,
					smanfax,
					commissionrate1,
					breakpoint,
					commissionrate2,
					current
				FROM salesman
				WHERE salesmancode='" . $SelectedSalesPerson . "'";

	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);

	$_POST['SalesmanCode'] = $MyRow['salesmancode'];
	$_POST['SalesmanName'] = $MyRow['salesmanname'];
	$_POST['SalesArea'] = $MyRow['salesarea'];
	$_POST['Manager'] = $MyRow['manager'];
	$_POST['SManTel'] = $MyRow['smantel'];
	$_POST['SManFax'] = $MyRow['smanfax'];
	$_POST['CommissionRate1'] = locale_number_format($MyRow['commissionrate1'], 'Variable');
	$_POST['Breakpoint'] = locale_number_format($MyRow['breakpoint'], $_SESSION['CompanyRecord']['decimalplaces']);
	$_POST['CommissionRate2'] = locale_number_format($MyRow['commissionrate2'], 'Variable');
	$_POST['Current'] = $MyRow['current'];

	echo '<input type="hidden" name="SelectedSalesPerson" value="' . $SelectedSalesPerson . '" />';
	echo '<input type="hidden" name="SalesmanCode" value="' . $_POST['SalesmanCode'] . '" />';
	echo '<fieldset>
				<legend>', _('Edit the details for'), ' ', $_POST['SalesmanCode'], ' - ', $_POST['SalesmanName'], '</legend>
				<field>
					<label for="SalesmanCode">', _('Salesperson code'), ':</label>
					<div class="fieldtext">', $_POST['SalesmanCode'], '</div>
				</field>';

} else { //end of if $SelectedSalesPerson only do the else when a new record is being entered
	$_POST['SalesmanName'] = '';
	$_POST['SalesArea'] = '';
	$_POST['SManTel'] = '';
	$_POST['SManFax'] = '';
	$_POST['CommissionRate1'] = 0;
	$_POST['Breakpoint'] = 0;
	$_POST['CommissionRate2'] = 0;
	$_POST['Manager'] = 0;
	$_POST['Current'] = 1;

	echo '<fieldset>
				<legend>', _('Create a new sales person record'), '</legend>
				<field>
					<label for="SalesmanCode">', _('Salesperson code'), ':</label>
					<input type="text" name="SalesmanCode" size="3" autofocus="autofocus" required="required" maxlength="3" />
					<fieldhelp>', _('Enter a three character code for this sales person.'), '</fieldhelp>
				</field>';
}

echo '<field>
			<label for="SalesmanName">', _('Salesperson Name'), ':</label>
			<input type="text" name="SalesmanName" autofocus="autofocus" size="30" required="required" maxlength="30" value="', $_POST['SalesmanName'], '" />
			<fieldhelp>', _('Enter the name by which this sales person will be known.'), '</fieldhelp>
		</field>';
echo '<field>
			<label for="SalesArea">', _('Sales Area'), ':</label>
			<select name="SalesArea">';
$SQL = "SELECT areacode, areadescription FROM areas ORDER BY areadescription";
$ErrMsg = _('An error occurred in retrieving the areas from the database');
$DbgMsg = _('The SQL that was used to retrieve the area information and that failed in the process was');
$AreaResult = DB_query($SQL, $ErrMsg, $DbgMsg);
echo '<option value=""></option>';
while ($AreaRow = DB_fetch_array($AreaResult)) {
	if ($_POST['SalesArea'] == $AreaRow['areacode']) {
		echo '<option selected="selected" value="', $AreaRow['areacode'], '">', $AreaRow['areadescription'], ' (', $AreaRow['areacode'], ')</option>';
	} else {
		echo '<option value="', $AreaRow['areacode'], '">', $AreaRow['areadescription'], ' (', $AreaRow['areacode'], ')</option>';
	}
}
echo '</select>
	<fieldhelp>', _('Select the sales area this person covers. If they cover more than one area then leave it blank.'), '</fieldhelp>
</field>';

echo '<field>
		<label for="SManTel">', _('Telephone No'), ':</label>
		<input type="tel" name="SManTel" size="20" maxlength="20" value="', $_POST['SManTel'], '" />
		<fieldhelp>', _('Contact telephone number for this sales person.'), '</fieldhelp>
	</field>';
echo '<field>
		<label for="SManFax">', _('Facsimile No'), ':</label>
		<input type="tel" name="SManFax" size="20" maxlength="20" value="', $_POST['SManFax'], '" />
		<fieldhelp>', _('Contact fax number for this sales person.'), '</fieldhelp>
	</field>';
echo '<field>
		<label for="CommissionRate1">', _('Commission Rate 1'), ':</label>
		<input type="text" class="number" name="CommissionRate1" size="5" required="required" maxlength="5" value="', $_POST['CommissionRate1'], '" />
		<fieldhelp>', _('The initial rate of commission applied to sales for this sales person.'), '</fieldhelp>
	</field>';
echo '<field>
		<label for="Breakpoint">', _('Breakpoint'), ':</label>
		<input type="text" class="number" name="Breakpoint" size="6" maxlength="6" value="', $_POST['Breakpoint'], '" />
		<fieldhelp>', _('A breakpoint after which the commission rate goes up.'), '</fieldhelp>
	</field>';
echo '<field>
		<label for="CommissionRate2">', _('Commission Rate 2'), ':</label>
		<input type="text" class="number" name="CommissionRate2" size="5" required="required" maxlength="5" value="', $_POST['CommissionRate2'], '" />
		<fieldhelp>', _('The new rate of commission applied to sales over the breakpoint limit for this sales person.'), '</fieldhelp>
	</field>';

echo '<field>
		<label for="Manager">', _('Area Manager?'), ':</label>
		<select required="required" name="Manager">';
if ($_POST['Manager'] == 1) {
	echo '<option selected="selected" value="1">', _('Yes'), '</option>';
} else {
	echo '<option value="1">', _('Yes'), '</option>';
}
if ($_POST['Manager'] == 0) {
	echo '<option selected="selected" value="0">', _('No'), '</option>';
} else {
	echo '<option value="0">', _('No'), '</option>';
}
echo '</select>
	<fieldhelp>', _('If this sales person is the manager for this area, select Yes, otherwise select No.'), '</fieldhelp>
</field>';

echo '<field>
		<label for="Current">', _('Current?'), ':</label>
		<select required="required" name="Current">';
if ($_POST['Current'] == 1) {
	echo '<option selected="selected" value="1">', _('Yes'), '</option>';
} else {
	echo '<option value="1">', _('Yes'), '</option>';
}
if ($_POST['Current'] == 0) {
	echo '<option selected="selected" value="0">', _('No'), '</option>';
} else {
	echo '<option value="0">', _('No'), '</option>';
}
echo '</select>
	<fieldhelp>', _('If this sales person is currently employed, select Yes, otherwise select No.'), '</fieldhelp>
</field>';

echo '</fieldset>';

echo '<div class="centre">
		<input type="submit" name="submit" value="', _('Enter Information'), '" />
	</div>
</form>';

include ('includes/footer.php');
?>