<?php
NewScript('Z_UpdateSystypes.php', 15);

NewMenuItem('Utilities', 'Transactions', _('Ensure systypes table is not corrupted'), '/Z_UpdateSystypes.php', 11);

UpdateDBNo(basename(__FILE__, '.php'));

?>