<?php
require_once CORE;
$auth = Auth::validate();
$p = $_SERVER['REQUEST_URI'];

if ($auth) {
?>
<a class="admin" href="/edit"><img src="<?php echo THEME.'/images/edit.png'; ?>" width="13" height="13" title="Editor" alt='Editor' /></a>
&nbsp;&nbsp;
<a class="admin" href="/settings"><img src="<?php echo THEME.'/images/settings.png'; ?>" width="13" height="13" title="Settings" alt='Settings' /></a>
&nbsp;&nbsp;
<a class="admin" href="/logout?ret=<?php echo $p ?>"><img src="<?php echo THEME.'/images/unlock.png'; ?>" width="13" height="13" title="Logout" alt='Log out ' />
<?php echo Auth::userName(); ?></a>

<?php 
}
else {
?>
<nobr>
<a id="login" class="admin" href="/login"><img src="<?php echo THEME . '/images/lock.png'; ?>" width="13" height="13" title="Login" alt='Login' /></a>
&nbsp;
<span id="loginForm"></span></nobr>

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

