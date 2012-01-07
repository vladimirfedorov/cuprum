<?php
require_once 'config.php';
require_once ROOTDIR.'/system/plugins/markdown/markdown.php';

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
		$r = DB::getRows('users', "`name`='$username' And `pwd`='$pwdhash'");
		if (count($r) == 1) {
			session_start();
			$_SESSION['status'] = 'authorized';
			$_SESSION['authname'] = $r[0]['name'];
			$_SESSION['authrole'] = $r[0]['role'];
			$r[0]['pwd'] = '';
			return $r[0];
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
		unset($_SESSION['authname']);
		unset($_SESSION['authrole']);
		session_unset();
		session_destroy();
		setcookie("status", "", time()-60*60*24*100, "/");
		setcookie("authname", "", time()-60*60*24*100, "/");
		setcookie("authrole", "", time()-60*60*24*100, "/");
	}
	
	function pwdHash($username, $password) {
		return hash('sha512', $password) ;
	}
	
	function getAuthForm() {
		$ret = (isset($_GET['ret']) ? $_GET['ret'] : '/');
		$f = "<form action='/login?ret=$ret' method='POST'>" .
			"<input class='loginFormInput' id='username' name='username' type='text' />&nbsp;" .
			"<input class='loginFormInput' id='password' name='password' type='password' />&nbsp;" .
			"<input type='submit' value='Login' />" .
			"</form>";
		return $f;
	}
	
	function userName() {
		return (self::validate() ? $_SESSION['authname'] : '');
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
		
		$templateName = ROOTDIR.THEME."/$templateName.php";

		if (!file_exists($templateName)) 
			$templateName = ROOTDIR.THEME.'/default.php';
		
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
				if (isset($this->vars[$varName][$varDim])) {
					if ($varName == 'P' && ($varDim == 'text' || $varDim == 'excerpt'))
						$p = str_replace("[[=$varNameFull]]", Markdown($this->vars[$varName][$varDim]), $p);
					else
						$p = str_replace("[[=$varNameFull]]", $this->vars[$varName][$varDim], $p);
				}
				else {
					if (REMOVENONEXISTENT)
						$p = str_replace("[[=$varNameFull]]", '', $p);
				}
			}
			else {
				if (isset($this->vars[$varName])) {
					$p = str_replace("[[=$varName]]", $this->vars[$varName], $p);
				}
				else {
					if (REMOVENONEXISTENT)
						$p = str_replace("[[=$varName]]", '', $p);
				}
			}
			
			$o = $pos+1;
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
				
			$up = ROOTDIR.USERPLUGINS.$pluginName . '/main.php';
			$sp = ROOTDIR.SYSTEMPLUGINS.$pluginName . '/main.php';
			
			$pluginPath = (file_exists($up) ? $up : 
				(file_exists($sp) ? $sp : ''));

			if ($pluginPath == '') {
				if (REMOVENONEXISTENT)
					$p = str_replace("[[:$pluginNameFull]]", '', $p);
				continue;
			}
			
			$pluginOut = $this->readFile($pluginPath, $ex);
			$pluginOut = $this->processVariables($pluginOut);
			
			$p = str_replace("[[:$pluginNameFull]]", $pluginOut, $p);
			
			$o = $pos+1;
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
				
			$templatePath = ROOTDIR.THEME."/$templateName";
			
			$templatePath = (file_exists("$templatePath.php") ? "$templatePath.php" : 
				(file_exists("$templatepath.html") ? "$templatePath.html" : ''));

			if ($templatePath == '') {
				if (REMOVENONEXISTENT)
					$p = str_replace("[[.$templateName]]", '', $p);
				continue;
			}
			
			$template = $this->readFile($templatePath);
			$template = $this->processVariables($template);
			$template = $this->processPlugins($template);
			
			
			$p = str_replace("[[.$templateName]]", $template, $p);
			
			$o = $pos+1;
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
		$p = explode('?', $p);
		$p = $p[0];
		
		$content = "";
		
		// predefined routes
		switch ($p) {
			case '/edit': 
				$content = self::processFile(ROOTDIR.'/system/edit.php');
				break;
			case '/settings':
				break;
			case '/login': 
				$ret = (isset($_GET['ret']) ? $_GET['ret'] : '/');
				
				// user is logged in
				if (Auth::validate()) {
					header("Location: $ret");
					break;
				}
				
				// user has sent valid name and password
				if ( isset($_POST['username']) && isset($_POST['password']) ) {
					$u = Auth::login($_POST['username'], $_POST['password']);
					header("Location: $ret");
					break;
				}
				
				// show the form
				$content = Auth::getAuthForm();
				break;
			case '/logout':
				Auth::logout();
				$ret = (isset($_GET['ret']) ? $_GET['ret'] : '/');
				header("Location: $ret");
				break;
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
	
	/// Display a file using default file template
	function processFile($f) {
		$t = new Template();
		$c = $t->readFile($f);
		$t->assign('P', $c);
		$content = $t->process(FILETEMPLATE);
		
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


