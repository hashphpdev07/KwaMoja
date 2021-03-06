<?php
include ('includes/session.php');
$Title = _('Maintain Stock Types');
include ('includes/header.php');

if (isset($_POST['SelectedType'])) {
	$SelectedType = mb_strtoupper($_POST['SelectedType']);
} elseif (isset($_GET['SelectedType'])) {
	$SelectedType = mb_strtoupper($_GET['SelectedType']);
}

echo '<p class="page_title_text">
		<img src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/maintenance.png" title="', _('Search'), '" alt="" />', ' ', $Title, '
	</p>';

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
	} elseif ($_POST['TypeAbbrev'] == '' or $_POST['TypeAbbrev'] == ' ' or $_POST['TypeAbbrev'] == '  ') {
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

		$SQL = "UPDATE stocktypes
				SET name = '" . $_POST['StockType'] . "',
					physicalitem =  '" . $PhysicalItem . "'
			WHERE type = '" . $SelectedType . "'";

		$Msg = _('The stock type') . ' ' . $SelectedType . ' ' . _('has been updated');
	} elseif ($InputError != 1) {

		// First check the type is not being duplicated
		$checkSql = "SELECT count(*)
				 FROM stocktypes
				 WHERE type = '" . $_POST['TypeAbbrev'] . "'";

		$CheckResult = DB_query($checkSql);
		$CheckRow = DB_fetch_row($CheckResult);

		if ($CheckRow[0] > 0) {
			$InputError = 1;
			prnMsg(_('The stock type ') . $_POST['TypeAbbrev'] . _(' already exists.'), 'error');
		} else {

			// Add new record on submit
			$SQL = "INSERT INTO stocktypes (type,
											name,
											physicalitem
										) VALUES (
											'" . str_replace(' ', '', $_POST['TypeAbbrev']) . "',
											'" . $_POST['StockType'] . "',
											'" . $PhysicalItem . "'
										)";

			$Msg = _('Stock type') . ' ' . $_POST['StockType'] . ' ' . _('has been created');
		}
	}

	if ($InputError != 1) {
		//run the SQL from either of the above possibilites
		$Result = DB_query($SQL);

		prnMsg($Msg, 'success');

		unset($SelectedType);
		unset($_POST['TypeAbbrev']);
		unset($_POST['StockType']);
	}

} elseif (isset($_GET['delete'])) {

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'stockcategories'
	$SQL = "SELECT COUNT(categoryid)
		   FROM stockcategory
		   WHERE stocktype='" . $SelectedType . "'";

	$ErrMsg = _('The number of stock categories using this stock type could not be retrieved');
	$Result = DB_query($SQL, $ErrMsg);

	$MyRow = DB_fetch_row($Result);
	if ($MyRow[0] > 0) {
		prnMsg(_('Cannot delete this stock type because stock categories exist using it') . '<br />' . _('There are') . ' ' . $MyRow[0] . ' ' . _('categories using this type'), 'error');

	} else {

		$SQL = "DELETE FROM stocktypes WHERE type='" . $SelectedType . "'";
		$ErrMsg = _('The Stock Type record could not be deleted because');
		$Result = DB_query($SQL, $ErrMsg);
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

	$SQL = "SELECT type,
					name,
					physicalitem
				FROM stocktypes";
	$Result = DB_query($SQL);

	echo '<table>
			<thead>
				<tr>
					<th class="SortedColumn">', _('Type Code'), '</th>
					<th class="SortedColumn">', _('Type Name'), '</th>
					<th>', _('Physical Items?'), '</th>
					<th colspan="2"></th>
				</tr>
			</thead>';
	echo '<tbody>';
	while ($MyRow = DB_fetch_array($Result)) {

		if ($MyRow['physicalitem'] == 0) {
			$PhysicalItem = _('No');
		} else {
			$PhysicalItem = _('Yes');
		}

		echo '<tr class="striped_row">
				<td>', $MyRow['type'], '</td>
				<td>', $MyRow['name'], '</td>
				<td>', $PhysicalItem, '</td>
				<td><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?SelectedType=', urlencode($MyRow['type']), '">', _('Edit'), '</a></td>
				<td><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '?SelectedType=', urlencode($MyRow['type']), '&amp;delete=yes" onclick="return MakeConfirm(\'', _('Are you sure you wish to delete this price list and all the prices it may have set up?'), '\', \'Confirm Delete\', this);">', _('Delete'), '</a></td>
			</tr>';
	}
	//END WHILE LIST LOOP
	echo '</tbody>
		</table>';
}

//end of ifs and buts!
if (isset($SelectedType)) {

	echo '<div class="centre">
			<a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '">', _('Show All Stock Types Defined'), '</a>
		</div>';
}
if (!isset($_GET['delete'])) {

	echo '<form method="post" action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" >';
	echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

	// The user wish to EDIT an existing type
	if (isset($SelectedType) and $SelectedType != '') {

		$SQL = "SELECT type,
						name,
						physicalitem
				FROM stocktypes
				WHERE type='" . $SelectedType . "'";

		$Result = DB_query($SQL);
		$MyRow = DB_fetch_array($Result);

		$_POST['TypeAbbrev'] = $MyRow['type'];
		$_POST['StockType'] = $MyRow['name'];
		$_POST['PhysicalItem'] = $MyRow['physicalitem'];

		echo '<input type="hidden" name="SelectedType" value="', $SelectedType, '" />';
		echo '<input type="hidden" name="TypeAbbrev" value="', $_POST['TypeAbbrev'], '" />';
		echo '<fieldset>
				<legend>', _('Edit Stock Type Setup'), '</legend>
				<field>
					<label for="TypeAbbrev">', _('Type Code'), ':</label>
					<div class="fieldtext">', $_POST['TypeAbbrev'], '</div>
				</field>';

	} else {
		$_POST['StockType'] = '';
		// This is a new type so the user may volunteer a type code
		echo '<fieldset>
				<legend>', _('New Stock Type Setup'), '</legend>
				<field>
					<label for="TypeAbbrev">', _('Type Code'), ':</label>
					<input type="text" size="3" required="required" autofocus="autofocus" maxlength="1" name="TypeAbbrev" />
					<fieldhelp>', _('Enter a one character code used to identify this stock type.'), '<fieldhelp>
				</field>';
	}

	if (isset($_POST['PhysicalItem']) and $_POST['PhysicalItem'] == 1) {
		$PhysicalItem = ' checked="checked" ';
	} else {
		$PhysicalItem = '';
	}
	echo '<field>
			<label for="StockType">', _('Stock Type Name'), ':</label>
			<input type="text" required="required" autofocus="autofocus" maxlength="40" name="StockType" value="', $_POST['StockType'], '" />
			<fieldhelp>', _('The name by which this stock type will be known.'), '</fieldhelp>
		</field>
		<field>
			<label for="PhysicalItem">', _('Physical Items'), '</label>
			<input type="checkbox" name="PhysicalItem"', $PhysicalItem, '/>
			<fieldhelp>', _('Tick this box if items of this type are physical objects, leave unticked if they are service/labour etc. items'), '</fieldhelp>
		</field>';

	echo '</fieldset>'; // close main table
	echo '<div class="centre">
			<input type="submit" name="submit" value="', _('Accept'), '" />
		</div>';

	echo '</form>';

} // end if user wish to delete
include ('includes/footer.php');
?>