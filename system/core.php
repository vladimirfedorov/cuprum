<?php
require_once 'config.php';

// classes

// Database ///////////////////////////////////////////////////////////////////

class DB {
	/// Execute query
	function execQuery($query) {
		$result = mysql_query($query);
		return $result;		
	}
	
	/// Get row collection
	function getRowsQ($query) {
		$rows = Array(); 
		$result = mysql_query($query);
		if ($result === false)
			return null;
		while($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			$rows[] = $row;
		}
		return $rows;
	}
	
	/// Get row collection
	function getRows($table, $cond) {
		$rows = Array(); 
		$result = mysql_query("Select * From `$table`" . 
			($cond == "" ? "" : " Where $cond"));
		if ($result === false)
			return null;
		while($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			$rows[] = $row;
		}
		return $rows;
	}

	/// Get one row
	function getRowQ($query) {
		$result = mysql_query($query);
		if ($result === false)
			return null;
		if($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			return $row;
		}
		return null;	
	}

	/// Get one row
	function getRow($table, $cond) {
		$result = mysql_query("Select * From `$table` " . 
			($cond == "" ? "" : "Where $cond"));
		if ($result === false)
			return null;
		if($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
			return $row;
		}
		return null;	
	}

    /// Save form data
    public function saveFormData($table) {
        if (isset($_POST["db_id"])) {
            $id = $_POST["db_id"];
            if ((is_numeric($id)) && ($id > 0))
                return self::updateFormData($table);
            else
                return self::createFormData($table);
        }
    }
    
    /// Update record
    public function updateFormData($table) {
        $id = 0;
        $fields = "";
        foreach ($_POST as $key=>$value) {
            if (substr($key,0,3) == "db_") {
                if ($key == "db_id") 
                	$id = $value;
				else {
	                $fldname = substr($key,3);
	                $fldvalue = mysql_real_escape_string($value);
					$fields .= " `$fldname`='$fldvalue',";	
				}
            }
        }
        $fields = substr($fields,0,-1);
        
        if ($fields != "") {
            $query = "Update $table Set $fields Where `id`=$id";
			//echo $query;
            $res = mysql_query($query);
            if ($res)
                return $id;
        }
        
        return false;
    }
    
	/// Create record
    public function createFormData($table) {
        $fields = "";
        $values = "";
        foreach ($_POST as $key=>$value) {
            if (substr($key,0,3) == "db_") {
                if ($key != 'db_id') {
	                $fldname = substr($key,3);
	                $fldvalue = mysql_real_escape_string($value);
	                $fields .= "`$fldname`,";
	                $values .= "'$fldvalue',";
				}
            }
        }
        $fields = substr($fields,0,-1);
        $values = substr($values,0,-1);
        
        if ($fields != "") {
            $query = "Insert Into $table ($fields) Values ($values);";
            //echo $query;
            $res = mysql_query($query);
            if ($res)
                return mysql_insert_id();
        }
        
        return false;
    }
}


// Authorization //////////////////////////////////////////////////////////////

class Auth {
	
	function login($username, $password) {
		$pwdhash = self::pwdHash($username, $password);
		$username = mysql_real_escape_string($username);
		$role = mysql_real_escape_string($role);
		$r = DB::getRows('users', "`name`='$username' And `pwd`='$pwdhash'");
		if (count($r) > 0) {
			session_start();
			$_SESSION['status'] = 'authorized';
			return $r;
		}
		return null;
	}

	function loginRole($username, $password, $role) {
		$pwdhash = self::pwdHash($username, $password);
		$username = mysql_real_escape_string($username);
		$role = mysql_real_escape_string($role);
		$r = DB::getRows('users', "`name`='$username' And `pwd`='$pwdhash' And `role`='$role'");
		if (count($r) > 0) {
			session_start();
			$_SESSION['status'] = 'authorized';
			return $r;
		}
		return null;
	}
	
	function validate() {
		session_start();
		if ($_SESSION['status'] == 'authorized')
			return true;
		else
			return false;
	}

	function logout() {
		session_start();
		unset($_SESSION['status']);
		session_unset();
		session_destroy();
		setcookie("status", "",time()-60*60*24*100, "/");
	}
	
	function pwdHash($username, $password) {
		return hash('sha512',$password) ;
	}
}


// Templates //////////////////////////////////////////////////////////////////

class Template {
	/// Template variables	
	var $vars = array();
	
	/// Process template
	function process($templateName)	{
		if ($templateName == '')
			$templateName = 'default';
		
		$templateName = THEME . "/$templateName.php";

		if (!file_exists($templateName)) 
			$templateName = THEME. '/default.php';
		
		if (!file_exists($templateName))
			return "Template not found: $templateName";
			
		$template = $this->readFile($templateName);
		$template = $this->processVariables($template);
		$template = $this->processPlugins($template);
		
		$template = $this->processTemplates($template);
				
		return $template;
	}
	
	/// Assing template variable
	function assign($name, $value) {
		$this->vars[$name] = &$value;
	}
	
	/// Delete template variable
	function clear($name) {
		if (isset($this->vars[$name]))
			unset($this->vars[$name]);
	}

	/// Delete all template vaiables
	function clearAll() {
		$this->vars = array();
	}
	
	/// Process all variables ([[=varname]] or [[=array.element]])
	function processVariables($p) {
		$o = 0;
		$maxSteps = MAXITERATIONS;
		
		while (false !== ($pos = strpos($p, "[[=", $o)) && (--$maxSteps>0)) {
			$varNameStart = $pos + 3;
			$varNameEnd = strpos($p, ']]', $varNameStart);
			$varName = substr($p, $varNameStart, ($varNameEnd-$varNameStart));
			
			if (strpos($varName, '.')>0) {
				$varNameFull = $varName;
				$ex = explode('.', $varName);	
				$varName = $ex[0];
				$varDim = $ex[1];
				if (isset($this->vars[$varName][$varDim]))
					$p = str_replace("[[=$varNameFull]]", $this->vars[$varName][$varDim], $p);
			}
			else {
				if (isset($this->vars[$varName]))
					$p = str_replace("[[=$varName]]", $this->vars[$varName], $p);
			}
			
			$o = $varNameStart+1;
			if ($o > strlen($p))
				break;
		}
		return $p;
	}
	
	/// Process plugins ([[:pluginname]])
	function processPlugins($p) {
		$o = 0;
		$maxSteps = MAXITERATIONS;

		while (false !== ($pos = strpos($p, "[[:", $o)) && (--$maxSteps>0)) {
			$pluginNameStart = $pos + 3;
			$pluginNameEnd = strpos($p, ']]', $pluginNameStart);
			$pluginName = substr($p, $pluginNameStart, ($pluginNameEnd-$pluginNameStart));
			
			// params?
			$pluginNameFull = $pluginName;
			$ex = explode(' ', $pluginName);	
			$pluginName = $ex[0];
				
			$up = USERPLUGINS . $pluginName . '/index.php';
			$sp = SYSTEMPLUGINS . $pluginName . '/index.php';
			
			$pluginPath = (file_exists($up) ? $up : 
				(file_exists($sp) ? $sp : ''));

			if ($pluginPath == '')
				continue;
			
			$pluginOut = $this->readFile($pluginPath, $ex);
			
			$p = str_replace("[[:$pluginNameFull]]", $pluginOut, $p);
			
			$o = $pluginNameStart+1;
			if ($o > strlen($p))
				break;
		}
		return $p;
	}
	
	function processTemplates($p) {
		$o = 0;
		$maxSteps = MAXITERATIONS;

		while (false !== ($pos = strpos($p, "[[.", $o)) && (--$maxSteps>0)) {
			$templateNameStart = $pos + 3;
			$templateNameEnd = strpos($p, ']]', $templateNameStart);
			$templateName = substr($p, $templateNameStart, ($templateNameEnd-$templateNameStart));
				
			$templatePath = THEME . "/$templateName";
			
			$templatePath = (file_exists("$templatePath.php") ? "$templatePath.php" : 
				(file_exists("$templatepath.html") ? "$templatePath.html" : ''));

			if ($templatePath == '')
				continue;
			
			$template = $this->readFile($templatePath);
			$template = $this->processVariables($template);
			$template = $this->processPlugins($template);
			
			
			$p = str_replace("[[.$templateName]]", $template, $p);
			
			$o = $templateNameStart+1;
			if ($o > strlen($p))
				break;
		}
		return $p;
	}
	
	/// Read a file and return its contents/output
	function readFile($filename, $params=null) {
		ob_start();
		include $filename;
		$v = ob_get_contents();
		ob_end_clean();
		return $v;
	}
}


// Core site functions ////////////////////////////////////////////////////////

class Site {

	/// Constructor
	function Site() {
		// connect to the specified database
		// configure the connection
		$conn = mysql_connect(DBHOST, DBUSER, DBPWD)
			   or die ("Not connected: " . mysql_error());
		
		mysql_select_db(DBNAME, $conn) or die ("Can't use {DBNAME} " . mysql_error());
		
		DB::execQuery('SET NAMES utf8');
	}
		
	/// Show page
	function displayPage() {
		$p = strtolower($_SERVER['REQUEST_URI']);
		//echo ($pg == '/' ? 'main' : $pg) . "<br />";
		
		$content = "";
		
		if (substr($p, 0, 8) == '/system/')
			return;
		
		// predefined routes
		switch ($p) {
			case '/edit': 										// edit
				header('Location: /system/edit.php?type=0');
				exit;
			default:
				$content = self::processPage($p);
				break;
		}
		
		// output everything;
		header('Content-type: text/html; charset=utf-8');
		echo $content;
	}
	
	/// Get page, process templates
	function processPage($p) {
		if ($p == '/')
			$p = 'home';

		// process template
		$p = self::getPageRec($p);
		$t = new Template();
		$t->assign('P', $p);
		$content = $t->process($p['template']);
		
		return $content;
	}
	
	/// Find page or blog record
	function getPageRec($p) {
		$r = DB::GetRow('content', "`permalink`='$p'");
		if ($r != null)
			return $r;
	}
}


///////////////////////////////////////////////////////////////////////////////

$s = new Site();
$s->displayPage();


