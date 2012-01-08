<?php

define('ROOTDIR', $_SERVER['DOCUMENT_ROOT'].'/');

$config = Config::read();

// Paths //////////////////////////////////////////////////////////////////////

// web path
//define('ROOT', '/');

// Root path
define('ROOTDIR', $_SERVER['DOCUMENT_ROOT'].'/');


// Database settings //////////////////////////////////////////////////////////

// Databasse host
//define('DBHOST', 'localhost');

// Database name
//define('DBNAME', 'cuprum');

// Database user name
//define('DBUSER', 'cuprum_dba');

// Password for the database
//define('DBPWD', 'T#tra_ah7yuw&b2=');


// site address
//define('SITEADDRESS', 'http://cuprum.vladimirfedorov.net');

// system core file
define('CORE', ROOTDIR.'/system/core.php');

// Theme to use
//define('THEME', 'default');

// Template to use when a .php file is shown 
// (default is 'system', used basically for system files)
define('FILETEMPLATE','system');

// Misc settings //////////////////////////////////////////////////////////////

// Maimum number of iterations for template/variable/plugin processor 
// 1000 is more than enough 
define ('MAXITERATIONS', 100);

// Remove or not plugins, variables and templates that was not found
define ('REMOVENONEXISTENT', true);

class Config {
	function read() {
		eval(file_get_contents(ROOTDIR.'/system/site.cfg'));
		foreach ($config as $key => $value) {
			define($key, $value);
		}
		
		return $config;
	}
	
	function write($config) {
		file_put_contents(ROOTDIR.'/system/site.cfg', '$config = '.var_export($config, true).';');
	}
}

?>