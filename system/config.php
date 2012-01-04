<?php

// Database settings //////////////////////////////////////////////////////////

// Databasse host
define('DBHOST', 'localhost');
// Database name
define('DBNAME', 'cuprum');
// Database user name
define('DBUSER', 'cuprum_dba');
// Password for the database
define('DBPWD', 'T#tra_ah7yuw&b2=');

// Paths //////////////////////////////////////////////////////////////////////

// Theme to use
define('THEME', 'system/themes/default');
// Path to user plugins
define('USERPLUGINS', 'user/plugins/');
// Path to system plugins
define('SYSTEMPLUGINS', 'system/plugins/');
// Template to use when a .php file is shown 
// (default is 'system', used basically for system files)
define('FILETEMPLATE','system');

// Misc settings //////////////////////////////////////////////////////////////

// Maimum number of iterations for template/variable/plugin processor 
// 1000 is more than enough 
define ('MAXITERATIONS', 1000);

?>