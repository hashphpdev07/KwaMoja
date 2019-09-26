<?php
NewScript('GLStatements.php', 8);
NewMenuItem('GL', 'Reports', _('Print GL Report Set'), '/GLStatements.php', 1);

UpdateDBNo(basename(__FILE__, '.php'));

?>