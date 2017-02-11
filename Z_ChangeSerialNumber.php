<?php

/*     This script is an utility to change the serial number of an.
 *     inventory item
 */

include('includes/session.php');
$Title = _('UTILITY PAGE Change A Serial Number');// Screen identificator.
$ViewTopic = 'SpecialUtilities'; // Filename in ManualContents.php's TOC.
$BookMark = ''; // Anchor's id in the manual's html document.
include('includes/header.php');

echo '<p class="page_title_text">
		<img alt="" src="' . $RootPath . '/css/' . $_SESSION['Theme'] . '/images/maintenance.png" title="' . _('Change An Items Serial Number') . '" />' . ' ' . _('Change An Items Serial Number') . '
	</p>';

if (isset($_POST['SubmitNewNo'])) {
	$SQL = "UPDATE stockserialitems SET serialno='" . $_POST['NewSerialNo'] . "'
								WHERE serialno='" . $_POST['OldSerialNo'] . "'
									AND stockid='" . $_POST['StockID'] . "'";
	$Result = DB_query($SQL);

	$SQL = "UPDATE stockserialmoves SET serialno='" . $_POST['NewSerialNo'] . "'
								WHERE serialno='" . $_POST['OldSerialNo'] . "'
									AND stockid='" . $_POST['StockID'] . "'";
	$Result = DB_query($SQL);

	prnMsg(_('The serial number was correctly changed'), 'success');
	include('includes/footer.php');
	exit;
}

if (isset($_GET['SerialNo'])) {
	echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '" method="post">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<input type="hidden" name="OldSerialNo" value="' . $_GET['SerialNo'] . '" />';
	echo '<input type="hidden" name="StockID" value="' . $_GET['StockID'] . '" />';

	echo '<div class="centre">',
			_('New Serial Number'), '<input type="text" name="NewSerialNo" value="', $_GET['SerialNo'], '" />
			<input type="submit" name="SubmitNewNo" value="', _('Change Serial Number'), '" />
		</div>';

	echo '</form>';
	include('includes/footer.php');
	exit;
}

if (isset($_POST['SubmitItemCode'])) {
	$SQL = "SELECT serialno,
					quantity
				FROM stockserialitems
				WHERE stockid='" . $_POST['StockCode'] . "'";
	$Result = DB_query($SQL);

	if (DB_num_rows($Result) == 0) {
		prnMsg(_('There are no serial numbers for this item'), 'info');
		include('includes/footer.php');
		exit;
	}
	echo '<table>
			<thead>
				<tr>
					<th colspan="4">', _('Select a serial number'), '</th>
				</tr>
				<tr>
					<th>', _('Stock Code'), '</th>
					<th>', _('Serial Number'), '</th>
					<th>', _('Quantity'), '</th>
				</tr>
			</thead>';
	echo '<tbody>';

	while ($MyRow = DB_fetch_array($Result)) {
		echo '<tr>
				<td>', $_POST['StockCode'], '</td>
				<td>', $MyRow['serialno'], '</td>
				<td>', $MyRow['quantity'], '</td>
				<td><a href="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '?SerialNo=', $MyRow['serialno'], '&StockID=', $_POST['StockCode'], '">', _('Change'), '</a></td>
			</tr>';
	}
	echo '</tbody>
		</table>';
	include('includes/footer.php');
	exit;
}

echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . '" method="post">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<table>
		<thead>
			<tr>
				<th colspan="2">', _('Select an item'), '</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>', _('Item Code'), '</td>
				<td><input type="text" name="StockCode" value="" /></td>
			</tr>
		</tbody>
	</table>';

echo '<div class="centre">
		<input type="submit" name="SubmitItemCode" value="', _('Find Serial Numbers'), '" />
	</div>';
echo '</form>';
include('includes/footer.php');

?>