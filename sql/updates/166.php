<?php

NewScript('PrintInvoice.php', 3);
NewScript('PrintCredit.php', 3);
$DefaultDatabase = 'default';
$DirHandle = dir($PathPrefix . 'companies/');
while (false !== ($CompanyEntry = $DirHandle->read())) {
	if (is_dir($PathPrefix . 'companies/' . $CompanyEntry) and $CompanyEntry != '..' and $CompanyEntry != '' and $CompanyEntry != '.') {
		if (!copy($PathPrefix . 'companies/' . $DefaultDatabase . '/FormDesigns/invoice.css', $PathPrefix . 'companies/' . $CompanyEntry . '/FormDesigns/invoice.css')) {
			$errors= error_get_last();
			echo "COPY ERROR: ".$errors['type'];
			echo "<br />\n".$errors['message'];
		}
		if (!copy($PathPrefix . 'companies/' . $DefaultDatabase . '/FormDesigns/credit.css', $PathPrefix . 'companies/' . $CompanyEntry . '/FormDesigns/credit.css')) {
			$errors= error_get_last();
			echo "COPY ERROR: ".$errors['type'];
			echo "<br />\n".$errors['message'];
		}
	}
}
$DirHandle->close();

UpdateDBNo(basename(__FILE__, '.php'));

?>