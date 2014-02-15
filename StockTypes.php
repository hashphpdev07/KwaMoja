<?php

include('includes/session.inc');
$Title = _('Maintain Stock Types');
include('includes/header.inc');

if (isset($_POST['SelectedType'])) {
	$SelectedType = mb_strtoupper($_POST['SelectedType']);
} elseif (isset($_GET['SelectedType'])) {
	$SelectedType = mb_strtoupper($_GET['SelectedType']);
}

echo '<p class="page_title_text noPrint" ><img src="' . $RootPath . '/css/' . $Theme . '/images/maintenance.png" title="' . _('Search') . '" alt="" />' . ' ' . $Title . '</p>';

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	$i = 1;
	if (isset($_POST['PhysicalItem'])) {
		$PhysicalItem = 1;
	} else {
		$PhysicalItem = 0;
	}

	if (mb_strlen($_POST['TypeAbbrev']) != 1) {
		$InputError = 1;
		prnMsg(_('The stock type code must be a single character'), 'error');
	} elseif ($_POST['TypeAbbrev'] == '' OR $_POST['TypeAbbrev'] == ' ' OR $_POST['TypeAbbrev'] == '  ') {
		$InputError = 1;
		prnMsg(_('The stock type code cannot be an empty string or spaces'), 'error');
	} elseif (trim($_POST['StockType']) == '') {
		$InputError = 1;
		prnMsg(_('The stock type description cannot be empty'), 'error');
	} elseif (mb_strlen($_POST['StockType']) > 40) {
		$InputError = 1;
		echo prnMsg(_('The stock type description must be forty characters or less long'), 'error');
	}

	if (isset($SelectedType) and $InputError != 1) {

		$sql = "UPDATE stocktypes
				SET name = '" . $_POST['StockType'] . "',
					physicalitem =  '" . $PhysicalItem . "'
			WHERE type = '" . $SelectedType . "'";

		$msg = _('The stock type') . ' ' . $SelectedType . ' ' . _('has been updated');
	} elseif ($InputError != 1) {

		// First check the type is not being duplicated

		$checkSql = "SELECT count(*)
				 FROM stocktypes
				 WHERE type = '" . $_POST['TypeAbbrev'] . "'";

		$CheckResult = DB_query($checkSql, $db);
		$CheckRow = DB_fetch_row($CheckResult);

		if ($CheckRow[0] > 0) {
			$InputError = 1;
			prnMsg(_('The stock type ') . $_POST['TypeAbbrev'] . _(' already exists.'), 'error');
		} else {

			// Add new record on submit

			$sql = "INSERT INTO stocktypes (type,
											name,
											physicalitem
										) VALUES (
											'" . str_replace(' ', '', $_POST['TypeAbbrev']) . "',
											'" . $_POST['StockType'] . "',
											'" . $PhysicalItem . "'
										)";

			$msg = _('Stock type') . ' ' . $_POST['StockType'] . ' ' . _('has been created');
		}
	}

	if ($InputError != 1) {
		//run the SQL from either of the above possibilites
		$result = DB_query($sql, $db);

		prnMsg($msg, 'success');

		unset($SelectedType);
		unset($_POST['TypeAbbrev']);
		unset($_POST['StockType']);
	}

} elseif (isset($_GET['delete'])) {

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'stockcategories'

	$sql = "SELECT COUNT(categoryid)
		   FROM stockcategory
		   WHERE stocktype='" . $SelectedType . "'";

	$ErrMsg = _('The number of stock categories using this stock type could not be retrieved');
	$result = DB_query($sql, $db, $ErrMsg);

	$myrow = DB_fetch_row($result);
	if ($myrow[0] > 0) {
		prnMsg(_('Cannot delete this stock type because stock categories exist using it') . '<br />' . _('There are') . ' ' . $myrow[0] . ' ' . _('categories using this type'), 'error');

	} else {

		$sql = "DELETE FROM stocktypes WHERE type='" . $SelectedType . "'";
		$ErrMsg = _('The Stock Type record could not be deleted because');
		$result = DB_query($sql, $db, $ErrMsg);
		prnMsg(_('Stock type') . ' ' . $SelectedType . ' ' . _('has been deleted'), 'success');
		unset($SelectedType);
		unset($_GET['delete']);

	} //end if sales type used in debtor transactions or in customers set up
}


if (isset($_POST['Cancel'])) {
	unset($SelectedType);
	unset($_POST['TypeAbbrev']);
	unset($_POST['StockType']);
}

if (!isset($SelectedType)) {

	/* It could still be the second time the page has been run and a record has been selected for modification -
	 * SelectedType will exist because it was sent with the new call. If its the first time the page has been
	 * displayed with no parameters then none of the above are true and the list of stock types will be displayed
	 * with links to delete or edit each. These will call the same page again and allow update/input or deletion
	 * of the records
	 */

	$sql = "SELECT type,
					name,
					physicalitem
				FROM stocktypes";
	$result = DB_query($sql, $db);

	echo '<table class="selection">
			<tr>
				<th>' . _('Type Code') . '</th>
				<th>' . _('Type Name') . '</th>
				<th>' . _('Physical Items?') . '</th>
			</tr>';

	$k = 0; //row colour counter

	while ($myrow = DB_fetch_array($result)) {
		if ($k == 1) {
			echo '<tr class="EvenTableRows">';
			$k = 0;
		} else {
			echo '<tr class="OddTableRows">';
			$k = 1;
		}

		if ($myrow['physicalitem'] == 0) {
			$PhysicalItem = _('No');
		} else {
			$PhysicalItem = _('Yes');
		}

		printf('<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td><a href="%sSelectedType=%s">' . _('Edit') . '</a></td>
				<td><a href="%sSelectedType=%s&amp;delete=yes" onclick="return MakeConfirm(\'' . _('Are you sure you wish to delete this price list and all the prices it may have set up?') . '\', \'Confirm Delete\', this);">' . _('Delete') . '</a></td>
			</tr>',
				$myrow['type'],
				$myrow['name'],
				$PhysicalItem,
				htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?', $myrow['type'],
				htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?', $myrow['type']);
	}
	//END WHILE LIST LOOP
	echo '</table>';
}

//end of ifs and buts!
if (isset($SelectedType)) {

	echo '<div class="centre"><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '">' . _('Show All Stock Types Defined') . '</a></div>';
}
if (!isset($_GET['delete'])) {

	echo '<form onSubmit="return VerifyForm(this);" method="post" class="noPrint" action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '" >';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	// The user wish to EDIT an existing type
	if (isset($SelectedType) and $SelectedType != '') {

		$sql = "SELECT type,
						name,
						physicalitem
				FROM stocktypes
				WHERE type='" . $SelectedType . "'";

		$result = DB_query($sql, $db);
		$myrow = DB_fetch_array($result);

		$_POST['TypeAbbrev'] = $myrow['type'];
		$_POST['StockType'] = $myrow['name'];
		$_POST['PhysicalItem'] = $myrow['physicalitem'];

		echo '<input type="hidden" name="SelectedType" value="' . $SelectedType . '" />';
		echo '<input type="hidden" name="TypeAbbrev" value="' . $_POST['TypeAbbrev'] . '" />';
		echo '<table class="selection">
				<tr>
					<th colspan="4"><b>' . _('Stock Type Setup') . '</b></th>
				</tr>';
		echo '<tr>
				<td>' . _('Type Code') . ':</td>
				<td>' . $_POST['TypeAbbrev'] . '</td>
			</tr>';

	} else {

		// This is a new type so the user may volunteer a type code

		echo '<table class="selection">
				<tr>
					<th colspan="4"><b>' . _('Stock Type Setup') . '</b></th>
				</tr>
				<tr>
					<td>' . _('Type Code') . ':</td>
					<td><input type="text" size="3" required="required" minlength="1" maxlength="2" name="TypeAbbrev" /></td>
				</tr>';
	}

	if (!isset($_POST['StockType'])) {
		$_POST['StockType'] = '';
	}
	if (isset($_POST['PhysicalItem']) and $_POST['PhysicalItem'] == 1) {
		$PhysicalItem = ' checked="checked" ';
	} else {
		$PhysicalItem = '';
	}
	echo '<tr>
			<td>' . _('Stock Type Name') . ':</td>
			<td><input type="text" required="required" minlength="1" maxlength="40" name="StockType" value="' . $_POST['StockType'] . '" /></td>
		</tr>
		<tr>
			<td>' . _('Physical Items') . '</td>
			<td><input type="checkbox" name="PhysicalItem"' . $PhysicalItem . '/></td>
		</tr>';

	echo '</table>'; // close main table

	echo '<div class="centre"><input type="submit" name="submit" value="' . _('Accept') . '" /></div>';

	echo '</form>';

} // end if user wish to delete

include('includes/footer.inc');
?>