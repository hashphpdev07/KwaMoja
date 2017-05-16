<?php

AddColumn('showpagehelp', 'www_users', 'TINYINT(1)', 'NOT NULL', '1', 'defaulttag');
AddColumn('showfieldhelp', 'www_users', 'TINYINT(1)', 'NOT NULL', '1', 'showpagehelp');

UpdateDBNo(basename(__FILE__, '.php'));

?>