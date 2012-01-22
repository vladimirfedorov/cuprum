<?php

// Root path
define('ROOTDIR', $_SERVER['DOCUMENT_ROOT'].'/');

// system core file
define('CORE', ROOTDIR.'/system/core.php');

// system config file
define('CONFIG', ROOTDIR.'/system/site.cfg');

// Template to use when a .php file is shown 
// (default is 'system', used basically for system files)
define('FILETEMPLATE','system');

// Maimum number of iterations for template/variable/plugin processor 
// In most cases 100 is OK and 1000 is more than enough 
define ('MAXITERATIONS', 100);

// Remove or not plugins, variables and templates that was not found
define ('REMOVENONEXISTENT', true);

// Global config
$config = Config::read();


class Config {
	
	/// Check if site.cfg exists, if not - create valid config file
	function check() {
		if (!file_exists(CONFIG)) {
			$f = fopen(CONFIG, 'w');
			fwrite($f, 'array()');
			fclose($f);
			return false;
		}
		else {
			$fc = file_get_contents(CONFIG);
			if($fc == 'array()')
				return false;
		}
		return true;
	}
	
	/// Read configuration from file
	function read() {
		self::check();
		eval('$config = '.file_get_contents(CONFIG).';');
		foreach ($config as $key => $value) {
			define($key, $value);
		}
		return $config;
	}
	
	/// Write configuration
	function write($config) {
		self::check();
		file_put_contents(CONFIG, var_export($config, true));
	}
	
	function update() {
		global $config;
		self::write($config);
		$config = self::read();
	}
	
	/// Get configuration from posted data
	function formToConfig() {
		global $config;
		foreach ($_POST as $key => $value) {
			if (substr($key, 0, 4) == 'cfg_') {
				$cfgName = substr($key, 4);
				$config[$cfgName] = $value;
			}
		}
		return $config;
	}
}

?>