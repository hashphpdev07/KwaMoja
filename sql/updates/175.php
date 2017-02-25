<?php

AddIndex(array('tabcode', 'date', 'codeexpense', 'counterindex'), 'pcashdetails', 'tabcodedate');

UpdateDBNo(basename(__FILE__, '.php'));

?>