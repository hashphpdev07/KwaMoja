<?php

AddColumn('security', 'emailsettings', 'CHAR(4)', 'NOT NULL', '', 'auth');

UpdateDBNo(basename(__FILE__, '.php'));

?>