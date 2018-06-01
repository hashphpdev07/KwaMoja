<?php
AddColumn('changepassword', 'www_users', 'TINYINT(1)', 'NOT NULL', 0, 'blocked');

UpdateDBNo(basename(__FILE__, '.php'));

?>