<?php
/* Session started in session.php for password checking and authorisation level check
 config.php is in turn included in session.php*/
include ('includes/session.php');
$Title = _('List of Items without picture');
include ('includes/header.php');
$SQL = "SELECT stockmaster.stockid,
				stockmaster.description,
				stockcategory.categorydescription
			FROM stockmaster
			INNER JOIN stockcategory
				ON stockmaster.categoryid = stockcategory.categoryid
			WHERE stockmaster.discontinued = 0
				AND stockcategory.stocktype != 'D'
			ORDER BY stockcategory.categorydescription,
					stockmaster.stockid";
$Result = DB_query($SQL);
$PrintHeader = true;
if (DB_num_rows($Result) != 0) {
	echo '<p class="page_title_text"  align="center"><strong>', _('Current Items without picture'), '</strong></p>';
	echo '<table>';

	$i = 1;
	$SupportedImgExt = array('png', 'jpg', 'jpeg');
	while ($MyRow = DB_fetch_array($Result)) {
		$ImageFileArray = glob($_SESSION['part_pics_dir'] . '/' . $MyRow['stockid'] . '.{' . implode(",", $SupportedImgExt) . '}', GLOB_BRACE);
		$ImageFile = reset($ImageFileArray);
		if (!file_exists($ImageFile)) {
			if ($PrintHeader) {
				echo '<tr>
						<th>', '#', '</th>
						<th>', _('Category'), '</th>
						<th>', _('Item Code'), '</th>
						<th>', _('Description'), '</th>
					</tr>';
				$PrintHeader = false;
			}
			$CodeLink = '<a href="' . $RootPath . '/SelectProduct.php?StockID=' . urlencode($MyRow['stockid']) . '" target="_blank">' . $MyRow['stockid'] . '</a>';
			echo '<tr class="striped_row">
					<td class="number">', $i, '</td>
					<td>', $MyRow['categorydescription'], '</td>
					<td>', $CodeLink, '</td>
					<td>', $MyRow['description'], '</td>
				</tr>';
			++$i;
		}
	}
	echo '</table>';
}
include ('includes/footer.php');
?>