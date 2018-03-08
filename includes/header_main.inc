<?php

// Titles and screen header
// Needs the file config.php loaded where the variables are defined for
//  $RootPath
//  $Title - should be defined in the page this file is included with
if (!isset($RootPath)) {
	$RootPath = dirname(htmlspecialchars($_SERVER['PHP_SELF']));
	if ($RootPath == '/' or $RootPath == "\\") {
		$RootPath = '';
	}
}

$ViewTopic = isset($ViewTopic) ? '?ViewTopic=' . $ViewTopic : '';
$BookMark = isset($BookMark) ? '#' . $BookMark : '';

if (isset($Title) and $Title == _('Copy a BOM to New Item Code')) { //solve the cannot modify heaer information in CopyBOM.php scritps
	ob_start();
}

echo '<!DOCTYPE html>';
echo '';

echo '<html moznomarginboxes mozdisallowselectionprint>
		<head>
			<meta http-equiv="Content-Type" content="application/html; charset=utf-8" />
			<title>', $Title, '</title>
			<link rel="icon" href="', $RootPath, '/favicon.ico" />
			<link href="', $RootPath, '/css/', $_SESSION['Theme'], '/default.css" rel="stylesheet" type="text/css" media="screen" />
			<link href="', $RootPath, '/css/', $_SESSION['Theme'], '/forms.css" rel="stylesheet" type="text/css" media="screen" />
			<link href="', $RootPath, '/css/', $_SESSION['Theme'], '/tables.css" rel="stylesheet" type="text/css" media="screen" />
			<link href="', $RootPath, '/css/', $_SESSION['Theme'], '/main.css" rel="stylesheet" type="text/css" media="screen" />
			<link href="', $RootPath, '/css/', $_SESSION['Theme'], '/footer.css" rel="stylesheet" type="text/css" media="screen" />
			<link href="', $RootPath, '/css/', $_SESSION['Theme'], '/ModalWindow.css" rel="stylesheet" type="text/css" media="screen" />
			<link href="', $RootPath, '/css/print.css" rel="stylesheet" type="text/css" media="print" />
			<link href="', $RootPath, '/css/hint.css" rel="stylesheet" type="text/css" media="screen" /
			<link href="http://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet" type="text/css">
			<script type="text/javascript" src = "', $RootPath, '/javascripts/MiscFunctions.js"></script>
			<script type="text/javascript" src = "', $RootPath, '/javascripts/ModalWindow.js"></script>';

if ($Debug === 0) {
	echo '</head>';
	echo '<body onload="initial()">';
} else {
	echo '<link href="', $RootPath, '/css/holmes.css" rel="stylesheet" type="text/css" />';
	echo '</head>';
	echo '<body class="holmes-debug" onload="initial()">';
}

if ($_SESSION['ShowPageHelp'] == 0) {
	echo '<link href="', $RootPath, '/css/', $_SESSION['Theme'], '/page_help_off.css" rel="stylesheet" type="text/css" media="screen" />';
} else {
	echo '<link href="', $RootPath, '/css/', $_SESSION['Theme'], '/page_help_on.css" rel="stylesheet" type="text/css" media="screen" />';
}

if ($_SESSION['ShowFieldHelp'] == 0) {
	echo '<link href="', $RootPath, '/css/', $_SESSION['Theme'], '/field_help_off.css" rel="stylesheet" type="text/css" media="screen" />';
} else {
	echo '<link href="', $RootPath, '/css/', $_SESSION['Theme'], '/field_help_on.css" rel="stylesheet" type="text/css" media="screen" />';
}

if (isset($_GET['FontSize'])) {
	$SQL = "UPDATE www_users
				SET fontsize='" . $_GET['FontSize'] . "'
				WHERE userid = '" . $_SESSION['UserID'] . "'";
	$Result = DB_query($SQL);
	switch ($_GET['FontSize']) {
		case 0:
			$_SESSION['ScreenFontSize'] = '8pt';
			break;
		case 1:
			$_SESSION['ScreenFontSize'] = '10pt';
			break;
		case 2:
			$_SESSION['ScreenFontSize'] = '12pt';
			break;
		default:
			$_SESSION['ScreenFontSize'] = '10pt';
	}
}
echo '<style>
			body {
					font-size: ', $_SESSION['ScreenFontSize'], ';
				}
			</style>';

echo '<div id="FooterVersionDiv" style="display:none;">
			', $_SESSION['VersionNumber'], '.', $_SESSION['DBUpdateNumber'],
	'</div>';

?>