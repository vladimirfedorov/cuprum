<?php
include_once CORE;
if (!Auth::validate()) {
	echo "";
	exit;
}
?>

<a href="/edit?id=<?php echo $params[1]; ?>">
<img src="<?php echo THEME.'/images/edit.png'; ?>" width="13" height="13" ?></a>
