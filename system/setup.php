<?php
require_once 'core.php';
if (!Auth::validate()) header('Location: /');

header('Content-type: text/html; charset=utf-8');

if (isset($_POST['SaveButton'])) {
	Config::write(Config::formToConfig());
}

$menu = '<li><a href="/settings">Settings</a> </li>' ;

Template::assign('menu', $menu);



?>

<style>
	label {display:inline-block; width:20%;}
	label.chklabel {width:auto;}
	.settings div {margin-bottom:9px; width:100%;}
	.settings input.fld {width:50%;}
	.submit {padding:20px 40px; margin-left:20%;}
</style>

<script>
	function generatePwd() {
		var s = new Array();
		s[0] = 'abcdefghijklmnopqrstuvwxyz';
		s[1] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		s[2] = '1234567890';
		s[3] = '!@#$%^&*()-_=+';
		
		var p = "";
		
		for (var i=0; i<16; i++) {
			var sn = Math.floor(Math.random()*4);
			var slen = s[sn].length;
			
			p += s[sn][Math.floor(Math.random()*slen)];
		}
		
		$('#DBPWD').val(p);	
		
		return false;	
	}
</script>

<form method="POST" action="/settings">
<div class="settings">	

	<h4>Site settings:</h4>
<div><label for="SITEADDRESS">Site address:</label><input id="SITEADDRESS" name="cfg_SITEADDRESS" class="fld" type="text" value='[[=S.SITEADDRESS]]' /></div>	
<div><label for="SITENAME">Site name:</label><input id="SITENAME" name="cfg_SITENAME" class="fld" type="text" value='[[=S.SITENAME]]' /></div>	
<div><label for="DESCRIPTION">Description:</label><input id="DESCRIPTION" name="cfg_DESCRIPTION" class="fld" type="text" value='[[=S.DESCRIPTION]]' /></div>
<div><label for="AUTHOR">Author:</label><input id="AUTHOR" name="cfg_AUTHOR" class="fld" type="text" value='[[=S.AUTHOR]]' /></div>
<div><label for="KEYWORDS">Keywords:</label><input id="KEYWORDS" name="cfg_KEYWORDS" class="fld" type="text" value='[[=S.KEYWORDS]]' /></div>
<div><label for="ROOT">Root directory:</label><input id="ROOT" name="cfg_ROOT" class="fld" type="text" value='[[=S.ROOT]]' /></div>	
<div><label for="THEME">Theme:</label><input id="THEME" name="cfg_THEME" class="fld" type="text" value='[[=S.THEME]]' /></div>	
	<br />


	<h4>Database settings:</h4>
<div><label for="DBHOST">Host:</label><input id="DBHOST" name="cfg_DBHOST" class="fld" type="text" value='[[=S.DBHOST]]' /></div>	
<div><label for="DBNAME">Name:</label><input id="DBNAME" name="cfg_DBNAME" class="fld" type="text" value='[[=S.DBNAME]]' /></div>	
<div><label for="DBUSER">Username:</label><input id="DBUSER" name="cfg_DBUSER" class="fld" type="text" value='[[=S.DBUSER]]' /></div>
<div><label for="DBPWD">Password:</label><input id="DBPWD" name="cfg_DBPWD" class="fld" type="text" value='[[=S.DBPWD]]' /></div>	
<div><label for="TABPREFIX">Table prefix:</label><input id="TABPREFIX" name="cfg_TABPREFIX" class="fld" type="text" value='[[=S.TABPREFIX]]' /></div>	
	<br />

</div>

<div><input name="SaveButton" class="submit" type="submit" value="Save settings" /></div>
</form>
