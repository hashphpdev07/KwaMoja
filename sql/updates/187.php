<?php

DropIndex('supptrans', 'TypeTransNo');
AddIndex(array('transno','type', 'supplierno'), 'supptrans', 'TypeTransNo');

UpdateDBNo(basename(__FILE__, '.php'));

?>