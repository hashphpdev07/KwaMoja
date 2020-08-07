<?php
/* Selection of patient - from where all patient related maintenance, transactions and inquiries start */

include ('includes/session.php');
$Title = _('Medical Laboratory');
$ViewTopic = '';
$BookMark = '';
include ('includes/header.php');

echo '<p class="page_title_text">
		<img alt="" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images-medical/labtest.png" title="', _('Medical Laboratory'), '" /> ', _('Medical Laboratory'), '
	</p>';

include ('includes/SQL_CommonFunctions.php');
include ('includes/HospitalFunctions.php');

echo '<div class="page_help_text">', _('Select a menu option to operate using this patient'), '.</div>';

echo '<fieldset style="text-align:center">';
// Customer inquiries options:
echo '<fieldset class="MenuList">
		<legend><img alt="" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/reports.png" data-title="', _('Inquiries and Reports'), '" />', _('Patient Inquiries'), '</legend>
		<ul>
			<li class="MenuItem">
				<a href="', $RootPath, '/KCMCPendingLaboratoryTests.php">', _('Pending Laboratory Tests'), '</a>
			</li>
		</ul>
	</fieldset>';

echo '<fieldset class="MenuList">
		<legend><img alt="" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/transactions.png" data-title="', _('Patient Transactions'), '" />', _('Patient Transactions'), '</legend>
		<ul>
			<li class="MenuItem">
				<a href="', $RootPath, '/KCMCRequestLaboratoryTest.php?New=Yes">', _('Request a Laboratory Test'), '</a>
			</li>
		</ul>
	</fieldset>';

echo '<fieldset class="MenuList">
		<legend><img alt="" src="', $RootPath, '/css/', $_SESSION['Theme'], '/images/maintenance.png" data-title="', _('Patient Maintenance'), '" />', _('Patient Maintenance'), '</legend>
		<ul>
		</ul>
	</fieldset>';

echo '</fieldset>';

include ('includes/footer.php');
?>