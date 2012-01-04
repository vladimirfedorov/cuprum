<?php
require_once 'core.php';
$ret = (isset($_GET['ret']) ? $_GET['ret'] : '/');

if (Auth::validate()) header("Location: $ret");

if ( isset($_POST['username']) && isset($_POST['password']) ) {
	$u = Auth::login($_POST['username'], $_POST['password']);
	if ($u != null) header("Location: $ret");;
}

?>
<form action="/login?ret=<?php echo $ret?>" method="POST">
	<label for="username">Username:</label>
	<input id="username" name="username" type="text" />
	<label for="password">Password:</label>
	<input id="password" name="password" type="password" />
	<input type="submit" value="Login" />
	
</form>
