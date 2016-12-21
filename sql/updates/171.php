<?php

NewScript('PurchasesReport.php', '2');

NewMenuItem('PO', 'Reports', _('Purchases from Suppliers'), '/PurchasesReport.php', 4);

UpdateDBNo(basename(__FILE__, '.php'));

?>