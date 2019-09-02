<?php
echo '<div id="mask">
				<div id="dialog" name="dialog"></div>
			</div>';

echo '<div id="MessageContainerFoot">';

if (isset($Messages) and count($Messages) > 0) {
	foreach ($Messages as $Message) {
		switch ($Message[1]) {
			case 'error':
				$Class = 'error';
				$Message[2] = $Message[2] ? $Message[2] : _('ERROR') . ' ' . _('Report');
				if (isset($_SESSION['LogSeverity']) and $_SESSION['LogSeverity'] > 3) {
					fwrite($LogFile, date('Y-m-d h-m-s') . ',' . $Type . ',' . $_SESSION['UserID'] . ',' . trim($Msg, ',') . "\n");
				}
			break;
			case 'warn':
			case 'warning':
				$Class = 'warn';
				$Message[2] = $Message[2] ? $Message[2] : _('WARNING') . ' ' . _('Report');
				if (isset($_SESSION['LogSeverity']) and $_SESSION['LogSeverity'] > 3) {
					fwrite($LogFile, date('Y-m-d h-m-s') . ',' . $Type . ',' . $_SESSION['UserID'] . ',' . trim($Msg, ',') . "\n");
				}
			break;
			case 'success':
				$Class = 'success';
				$Message[2] = $Message[2] ? $Message[2] : _('SUCCESS') . ' ' . _('Report');
				if (isset($_SESSION['LogSeverity']) and $_SESSION['LogSeverity'] > 3) {
					fwrite($LogFile, date('Y-m-d h-m-s') . ',' . $Type . ',' . $_SESSION['UserID'] . ',' . trim($Msg, ',') . "\n");
				}
			break;
			case 'info':
			default:
				$Message[2] = $Message[2] ? $Message[2] : _('INFORMATION') . ' ' . _('Message');
				$Class = 'info';
				if (isset($_SESSION['LogSeverity']) and $_SESSION['LogSeverity'] > 2) {
					fwrite($LogFile, date('Y-m-d h-m-s') . ',' . $Type . ',' . $_SESSION['UserID'] . ',' . trim($Msg, ',') . "\n");
				}
		}
		echo '<div class="Message ', $Class, ' noPrint">
				<span class="MessageCloseButton">&times;</span>
				<b>', $Message[2], '</b> : ', $Message[0], '</div>';
	}
}

echo '</div>
	</div>
</div>'; // BodyDiv
echo '<div id="FooterDiv" class="noPrint">
		<div id="FooterWrapDiv">
			<div id="FooterLogoDiv">
				<a href="http://www.kwamoja.com" target="_blank"><img src="', $RootPath, '/', $_SESSION['LogoFile'], '" width="120" alt="KwaMoja" title="KwaMoja" /></a>
			</div>
			<div id="FooterVersionDiv">KwaMoja ', _('version'), ' ', $_SESSION['VersionNumber'], '.', $_SESSION['DBUpdateNumber'], '</div>
			<div id="FooterTimeDiv">', DisplayDateTime(), '</div>
		</div>
	</div>'; // FooterDiv
echo '</div>'; // Canvas
echo '<script type="text/javascript" defer="defer" src = "', $RootPath, '/javascripts/MiscFunctions.js"></script>';
echo '</body>';
echo '</html>';

?>