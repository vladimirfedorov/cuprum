<?php

// Root path
define('ROOTDIR', $_SERVER['DOCUMENT_ROOT'].'/');

// Global config
$config = Config::read();

// system core file
define('CORE', ROOTDIR.'/system/core.php');

// Template to use when a .php file is shown 
// (default is 'system', used basically for system files)
define('FILETEMPLATE','system');


// Maimum number of iterations for template/variable/plugin processor 
// In most cases 100 is OK and 1000 is more than enough 
define ('MAXITERATIONS', 100);

// Remove or not plugins, variables and templates that was not found
define ('REMOVENONEXISTENT', true);

class Config {
	function read() {
		eval('$config = '.file_get_contents(ROOTDIR.'/system/site.cfg').';');
		foreach ($config as $key => $value) {
			define($key, $value);
		}
		
		return $config;
	}
	
	function write($config) {
		file_put_contents(ROOTDIR.'/system/site.cfg', var_export($config, true));
	}
}

?>