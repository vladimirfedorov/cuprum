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

// Root path
define('ROOTDIR', $_SERVER['DOCUMENT_ROOT'].'/');

// web path
define('ROOT', '/');

// system core file
define('CORE', ROOTDIR.'/system/core.php');

// Theme to use
define('THEME', ROOT.'system/themes/default');

// Path to user plugins
define('USERPLUGINS', ROOT.'user/plugins/');

// Path to system plugins
define('SYSTEMPLUGINS', ROOT.'system/plugins/');

// Template to use when a .php file is shown 
// (default is 'system', used basically for system files)
define('FILETEMPLATE','system');

// Misc settings //////////////////////////////////////////////////////////////

// Maimum number of iterations for template/variable/plugin processor 
// 1000 is more than enough 
define ('MAXITERATIONS', 100);

// Remove or not plugins, variables and templates that was not found
define ('REMOVENONEXISTENT', true);

?>