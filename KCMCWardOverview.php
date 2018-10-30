<?php
include ('includes/session.php');
$Title = _('Ward Overview');

include ('includes/header.php');
include ('includes/SQL_CommonFunctions.php');

if (!isset($_SESSION['WardID'])) {
	echo '<p class="page_title_text">
			<img class="page_title_icon" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" title="', _('Search'), '" alt="" />', _('Wards'), '
		</p>';
}

if (isset($_GET['SelectedWard'])) {
	$_SESSION['WardID'] = $_GET['SelectedWard'];
} elseif (isset($_POST['SelectedWard'])) {
	$_SESSION['WardID'] = $_POST['SelectedWard'];
}

if (isset($_SESSION['WardID'])) {
	$SQL = "SELECT nr,
					ward_id,
					name,
					status,
					description,
					dept_nr,
					room_nr_start,
					room_nr_end,
					roomprefix
				FROM care_ward
				WHERE nr='" . $_SESSION['WardID'] . "'";
	$Result = DB_query($SQL);
	$MyRow = DB_fetch_array($Result);

	$Title = _('Details for') . ' ' . $MyRow['name'];
	echo '<p class="page_title_text">
			<img class="page_title_icon" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" title="', _('Search'), '" alt="" />', $Title, '
		</p>';

	echo '<div class="page_help_text">' . _('Select a menu option to operate for this ward.') . '</div>';
	echo '<table width="90%" cellpadding="4">
			<tr>
				<th style="width:33%">
					<img class="page_title_icon" alt="" src="', $RootPath . '/css/', $_SESSION['Theme'], '/images/reports.png" title="', _('Ward Inquiries'), '" />', _('Ward Inquiries'), '</th>
				<th style="width:33%">
					<img class="page_title_icon" alt="" src="', $RootPath . '/css/', $_SESSION['Theme'], '/images/transactions.png" title="', _('Ward Transactions'), '" />', _('Ward Transactions'), '</th>
				<th style="width:33%">
					<img class="page_title_icon" alt="" src="', $RootPath . '/css/', $_SESSION['Theme'], '/images/maintenance.png" title="', _('Ward Maintenance'), '" />', _('Ward Maintenance'), '
				</th>
			</tr>
			<tr>
				<td style="vertical-align:top;">
				</td>
				<td style="vertical-align:top;">
					<a href="KCMCAllocatePatientsToBeds.php?SelectedWard=', $_SESSION['WardID'], '">', _('Allocate Patients to beds'), '</a>
				</td>
				<td style="vertical-align:top;">
					<a href="KCMCMaintainWards.php?SelectedWard=', $_SESSION['WardID'], '">', _('Maintain ward details'), '</a>
					<a href="KCMCMaintainWardRooms.php?SelectedWard=', $_SESSION['WardID'], '">', _('Maintain details of rooms in the ward'), '</a>
				</td>
			</tr>';
	echo '<tr>';

	echo '<td valign="top" class="select">';
	echo '</td>';

	echo '<td valign="top" class="select">';

	echo '</td>';

	echo '<td valign="top" class="select">';

	echo '</td>';

	echo '</tr>
		</table>';
}

echo '<p class="page_title_text">
		<img class="page_title_icon" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/magnifier.png" title="', _('Search'), '" alt="" />', _('Search For Ward'), '
	</p>';

echo '<form action="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8'), '" method="post">';
echo '<input type="hidden" name="FormID" value="', $_SESSION['FormID'], '" />';

if (!isset($_POST['WardIDSearch'])) {
	$_POST['WardIDSearch'] = '';
}
if (!isset($_POST['WardNameSearch'])) {
	$_POST['WardNameSearch'] = '';
}

echo '<fieldset>
		<legend class="search">', _('Search for ward details'), '</legend>
		<field>
			<label for="WardIDSearch">', _('Ward ID'), '</label>
			<input type="search" autofocus="autofocus" name="WardIDSearch" value="', $_POST['WardIDSearch'], '" />
			<fieldhelp>', _('Enter all or part of the ward ID if it is known'), '</fieldhelp>
		</field>
		<field>
			<label for="WardNameSearch">', _('Ward name'), '</label>
			<input type="search" autofocus="autofocus" name="WardNameSearch" value="', $_POST['WardNameSearch'], '" />
			<fieldhelp>', _('Enter all or part of the ward name if it is known'), '</fieldhelp>
		</field>
	</fieldset>';

echo '<div class="centre">
		<input type="submit" name="Search" value="', _('Search'), '" />
	</div>
</form>';

if (isset($_POST['Search'])) {
	$SQL = "SELECT nr,
					ward_id,
					name
				FROM care_ward
				WHERE name LIKE '%" . $_POST['WardNameSearch'] . "%'
					AND ward_id LIKE '%" . $_POST['WardIDSearch'] . "%'";
	$Result = DB_query($SQL);

	echo '<table>
			<tr>
				<th>', _('Ward Number'), '</th>
				<th>', _('Ward ID'), '</th>
				<th>', _('Ward Name'), '</th>
				<th></th>
			</tr>';

	while ($MyRow = DB_fetch_array($Result)) {
		echo '<tr class="striped_row">
				<td>', $MyRow['nr'], '</td>
				<td>', $MyRow['ward_id'], '</td>
				<td>', $MyRow['name'], '</td>
				<td><a href="', htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8') . '?SelectedWard=', urlencode($MyRow['nr']), '">', _('Select Ward'), '</a></td>
			</tr>';
	}

	echo '</table>';
}
include ('includes/footer.php');
exit;

include ('includes/footer.php');
?>