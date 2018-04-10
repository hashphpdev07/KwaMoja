<?php
include ('includes/session.php');
$Title = _('Shipping Company Maintenance');
include ('includes/header.php');

if (isset($_GET['SelectedShipper'])) {
	$SelectedShipper = $_GET['SelectedShipper'];
} elseif (isset($_POST['SelectedShipper'])) {
	$SelectedShipper = $_POST['SelectedShipper'];
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	 ie the page has called itself with some user input */

	//first off validate inputs sensible
	$i = 1;

	if (mb_strlen($_POST['ShipperName']) > 40) {
		$InputError = 1;
		prnMsg(_('The shipper\'s name must be forty characters or less long'), 'error');
	} elseif (trim($_POST['ShipperName']) == '') {
		$InputError = 1;
		prnMsg(_('The shipper\'s name may not be empty'), 'error');
	}

	if (isset($SelectedShipper) and $InputError != 1) {

		/*SelectedShipper could also exist if submit had not been clicked this code
		would not run in this case cos submit is false of course  see the
		delete code below*/

		$SQL = "UPDATE shippers SET shippername='" . $_POST['ShipperName'] . "',
									mincharge='" . filter_number_format($_POST['MinimumCharge']) . "'
				WHERE shipper_id = '" . $SelectedShipper . "'";
		$Msg = _('The shipper record has been updated');
	} elseif ($InputError != 1) {

		/*SelectedShipper is null cos no item selected on first time round so must be adding a	record must be submitting new entries in the new Shipper form */

		$SQL = "INSERT INTO shippers (shippername,
									  mincharge
									 ) VALUES (
										'" . $_POST['ShipperName'] . "',
										'" . filter_number_format($_POST['MinimumCharge']) . "'
									)";
		$Msg = _('The shipper record has been added');
	}

	//run the SQL from either of the above possibilites
	if ($InputError != 1) {
		$Result = DB_query($SQL);
		echo '<br />';
		prnMsg($Msg, 'success');
		unset($SelectedShipper);
		unset($_POST['ShipperName']);
		unset($_POST['Shipper_ID']);
	}

} elseif (isset($_GET['delete'])) {
	//the link to delete a selected record was clicked instead of the submit button
	// PREVENT DELETES IF DEPENDENT RECORDS IN 'SalesOrders'
	$SQL = "SELECT COUNT(*) FROM salesorders WHERE salesorders.shipvia='" . $SelectedShipper . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_row($Result);
	if ($MyRow[0] > 0) {
		$CancelDelete = 1;
		echo '<br />';
		prnMsg(_('Cannot delete this shipper because sales orders have been created using this shipper') . '. ' . _('There are') . ' ' . $MyRow[0] . ' ' . _('sales orders using this shipper code'), 'error');

	} else {
		// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorTrans'
		$SQL = "SELECT COUNT(*) FROM debtortrans WHERE debtortrans.shipvia='" . $SelectedShipper . "'";
		$Result = DB_query($SQL);
		$MyRow = DB_fetch_row($Result);
		if ($MyRow[0] > 0) {
			$CancelDelete = 1;
			echo '<br />';
			prnMsg(_('Cannot delete this shipper because invoices have been created using this shipping company') . '. ' . _('There are') . ' ' . $MyRow[0] . ' ' . _('invoices created using this shipping company'), 'error');
		} else {
			// Prevent deletion if the selected shipping company is the current default shipping company in config.php !!
			if ($_SESSION['Default_Shipper'] == $SelectedShipper) {

				$CancelDelete = 1;
				echo '<br />';
				prnMsg(_('Cannot delete this shipper because it is defined as the default shipping company in the configuration file'), 'error');

			} else {

				$SQL = "DELETE FROM shippers WHERE shipper_id='" . $SelectedShipper . "'";
				$Result = DB_query($SQL);
				echo '<br />';
				prnMsg(_('The shipper record has been deleted'), 'success');
			}
		}
	}
	unset($SelectedShipper);
	unset($_GET['delete']);
}

if (!isset($SelectedShipper)) {

	/* It could still be the second time the page has been run and a record has been selected for modification - SelectedShipper will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
	then none of the above are true and the list of Shippers will be displayed with
	links to delete or edit each. These will call the same page again and allow update/input
	or deletion of the records*/
	echo '<p class="page_title_text" >
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/supplier.png" title="', _('Search'), '" alt="" />', ' ', $Title, '
		</p>';

	$SQL = "SELECT shipper_id,
					shippername,
					mincharge
				FROM shippers
				ORDER BY shipper_id";
	$Result = DB_query($SQL);

	echo '<table>
			<thead>
				<tr>
					<th class="SortedColumn">', _('Shipper ID'), '</th>
					<th class="SortedColumn">', _('Shipper Name'), '</th>
					<th>', _('Minimum charge'), '</th>
					<th colspan="2"></th>
				</tr>
			</thead>';
	echo '<tbody>';

	while ($MyRow = DB_fetch_array($Result)) {
		echo '<tr class="striped_row">
				<td>', $MyRow['shipper_id'], '</td>
				<td>', $MyRow['shippername'], '</td>
				<td class="number">', locale_number_format($MyRow['mincharge'], $_SESSION['CompanyRecord']['decimalplaces']), '</td>
				<td><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?SelectedShipper=', urlencode($MyRow['shipper_id']), '">', _('Edit'), '</a></td>
				<td><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?SelectedShipper=', urlencode($MyRow['shipper_id']), '&amp;delete=1" onclick="return MakeConfirm(\'', _('Are you sure you wish to delete this shipper?'), '\', \'Confirm Delete\', this);">', _('Delete'), '</a></td>
			</tr>';
	}
	//END WHILE LIST LOOP
	echo '</tbody>
		</table>';
}

if (isset($SelectedShipper)) {
	echo '<div class="toplink">
			<a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">', _('Review Records'), '</a>
		</div>';

	echo '<p class="page_title_text" >
			<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/supplier.png" title="', _('Search'), '" alt="" />', ' ', $Title, '
		</p>';
}

if (!isset($_GET['delete'])) {

	echo '<form method="post" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

	if (isset($SelectedShipper)) {
		//editing an existing Shipper
		$SQL = "SELECT shipper_id,
						shippername,
						mincharge
					FROM shippers
					WHERE shipper_id='" . $SelectedShipper . "'";

		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);

		$_POST['Shipper_ID'] = $MyRow['shipper_id'];
		$_POST['ShipperName'] = $MyRow['shippername'];
		$_POST['MinimumCharge'] = $MyRow['mincharge'];

		echo '<input type="hidden" name="SelectedShipper" value="', $SelectedShipper, '" />';
		echo '<input type="hidden" name="Shipper_ID" value="', $_POST['Shipper_ID'], '" />';
		echo '<fieldset>
				<legend>', _('Edit details for'), ' ', $MyRow['shippername'], '</legend>
					<field>
						<label for="Shipper_ID">', _('Shipper Code'), ':</label>
						<div class="fieldtext">', $_POST['Shipper_ID'], '</div>
					</field>';
	} else {
		echo '<fieldset>
				<legend>', _('Create new shipper details'), '</legend>';
	}
	if (!isset($_POST['ShipperName'])) {
		$_POST['ShipperName'] = '';
		$_POST['MinimumCharge'] = 0;
	}

	echo '<field>
			<label for="ShipperName">', _('Shipper Name'), ':</label>
			<input type="text" name="ShipperName" value="', $_POST['ShipperName'], '" size="35" required="required" autofocus="autofocus" maxlength="40" />
			<fieldhelp>', _('Enter a name by which this shipper is known.'), '</fieldhelp>
		</field>

		<field>
			<label for="MinimumCharge">', _('Minimum Charge'), ':</label>
			<input type="text" name="MinimumCharge" class="number" value="', $_POST['MinimumCharge'], '" size="10" required="required" maxlength="10" />
			<fieldhelp>', _('If this shipper has a minimum charge per shipment, then enter that amount in here.'), '</fieldhelp>
		</field>

	</fieldset>

	<div class="centre">
		<input type="submit" name="submit" value="', _('Enter Information'), '" />
	</div>
</form>';

} //end if record deleted no point displaying form to add record
include ('includes/footer.php');
?>