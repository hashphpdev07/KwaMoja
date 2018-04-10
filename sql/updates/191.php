<?php
NewScript('GLAccountGraph.php', 8);

NewMenuItem('GL', 'Reports', _('Graph a specific GL code'), '/GLAccountGraph.php', 4);

UpdateDBNo(basename(__FILE__, '.php'));

?>