<?php

NewScript('Z_ChangeSerialNumber.php', '15');

NewMenuItem('Utilities', 'Transactions', _('Change a serial number'), '/Z_ChangeSerialNumber.php', 6);

UpdateDBNo(basename(__FILE__, '.php'));

?>