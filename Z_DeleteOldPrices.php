<?php
include ('includes/session.php');
$Title = _('UTILITY PAGE To Delete All Old Prices');
include ('includes/header.php');

if (isset($_GET['DeleteOldPrices'])) {
	DB_Txn_Begin();
	$Result = DB_query("DELETE FROM prices WHERE enddate<CURRENT_DATE AND enddate <>'9999-12-31'", '', '', true);
	$Result = DB_query("SELECT stockid,
							typeabbrev,
							currabrev,
							debtorno,
							branchcode,
							MAX(startdate) as lateststart
					FROM prices
					WHERE startdate<=CURRENT_DATE
					AND enddate ='9999-12-31'
					GROUP BY stockid,
							typeabbrev,
							currabrev,
							debtorno,
							branchcode");

	while ($MyRow = DB_fetch_array($Result)) {
		$DelResult = DB_query("DELETE FROM prices WHERE stockid='" . $MyRow['stockid'] . "'
													AND debtorno='" . $MyRow['debtorno'] . "'
													AND branchcode='" . $MyRow['branchcode'] . "'
													AND currabrev='" . $MyRow['currabrev'] . "'
													AND typeabbrev='" . $MyRow['typeabbrev'] . "'
													AND enddate='9999-12-31'
													AND startdate<'" . $MyRow['lateststart'] . "'", '', '', true);
	}
	prnMsg(_('All old prices have been deleted'), 'success');
	DB_Txn_Commit();
}

echo '<form action="' . htmlspecialchars(basename(__FILE__), ENT_QUOTES, 'UTF-8') . '" method="post">
		<div class="centre">
			<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />
			<input type="submit" name="DeleteOldPrices" value="' . _('Purge Old Prices') . '" onclick="return confirm(\'' . _('Are You Sure you wish to delete all old prices?') . '\');" />
		</div>
	</form>';

include ('includes/footer.php');
?>