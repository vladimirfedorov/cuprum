<?php
require_once CORE;
$auth = Auth::validate();
$p = $_SERVER['REQUEST_URI'];

if ($auth) {
?>
<a class="admin" href="/edit">
<img src="<?php echo THEME.'/images/edit.png'; ?>" width="13" height="13" title="Editor" />
</a>
&nbsp;&nbsp;
<a class="admin" href="/settings">
<img src="<?php echo THEME.'/images/settings.png'; ?>" width="13" height="13" title="Settings" />
</a>
&nbsp;&nbsp;
<a class="admin" href="/logout?ret=<?php echo $p ?>">
<img src="<?php echo THEME.'/images/unlock.png'; ?>" width="13" height="13" title="Logout" />
<?php echo Auth::userName(); ?>
</a>
<?php 
}
else {
?>
<!--<a id="login" class="admin" href="/login?ret=<?php echo $p ?>">-->
<nobr>
<a id="login" class="admin" href="/login">
<img src="<?php echo THEME . '/images/lock.png'; ?>" width="13" height="13" title="Login" />
</a>
&nbsp;
<span id="loginForm"></span>
</nobr>

<script>
	$('#login').click(function(){
		$.ajax({
			url: '/login?ret=<?php echo $p; ?>',
			success: function(data){
				$('#loginForm').html(data).toggle();
				$('#username').focus();
			}
		});
		return false;
	});
</script>


<?php
}
?>

