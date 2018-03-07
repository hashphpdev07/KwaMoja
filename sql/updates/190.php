<?php
NewScript('Z_ChangeSalesmanCode.php', 15);

NewMenuItem('Utilities', 'Transactions', _('Change a sales person code'), '/Z_ChangeSalesmanCode.php', 7);

UpdateDBNo(basename(__FILE__, '.php'));

?>