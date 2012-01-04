<?php
require_once CORE;
$auth = Auth::validate();
$p = $_SERVER['REQUEST_URI'];

if ($auth) {
?>
 [[:sys.edit [[=P.id]]]]
<a href="/logout?ret=<?php echo $p ?>">
<img id="login" src="<?php echo THEME.'/images/unlock.png'; ?>" width="13" height="13" title="Logout" />
<?php echo Auth::userName(); ?>
</a>
<?php 
}
else {
?>
<a href="/login?ret=<?php echo $p ?>">
<img id="login" src="<?php echo THEME . '/images/lock.png'; ?>" width="13" height="13" title="Login" />
</a>
<?php
}
?>

