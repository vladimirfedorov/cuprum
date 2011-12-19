<?php
ob_start();
chdir('..');

header('Content-type: text/html; charset=utf-8');
require_once 'core.php';

if(isset($_POST['db_id'])) {
	$id = DB::saveFormData('content');
	
	if ($_POST['db_id'] == ''){
		//echo "Location: edit.php?id=$id";
		header("Location: edit.php?id=$id");
		exit;
	};
}




if ( !(isset($_GET['id']) || isset($_GET['type'])) || 
		(isset($_GET['id']) && ! is_numeric($_GET['id'])) ||
		(isset($_GET['type']) && ! is_numeric($_GET['type'])) ) {
	header('Location: /');
	exit;
}


$id = $_GET['id'];
$type = $_GET['type'];

$q = (is_null($id) ? '' : "`id`=$id") .
	( (is_null($id) || is_null($type)) ? '' : ' And ') .
	(is_null($type) ? '' : "`type`=$type");

$r = DB::getRow('content', $q);

if (is_null($type))
	$type = $r['type'];

$l = DB::getRows('content',"`type`=$type");


// FORM ///////////////////////////////////////////////////////////////////////
?>

<script src="js/jquery-1.7.1.min.js" language="JavaScript"></script>
<script src="js/jquery.cookie.js" language="JavaScript"></script>

<script>
	$(document).ready(function(){
		
		$("#more").click(function(){
			$("#panel_more").slideToggle();
			$("#more").text(
				$("#more").text() == 'More' ? 'Less' : 'More'
			);
		})
	})
	
	function updVal() {
		$("input:checkbox").each(function(i,obj){
			$("#db_"+obj.id).val(
				obj.checked ? 1 : 0
			);
		})
	}
</script>

<style>
	#list { float:left;width:15%;height:400px; overflow:auto; border-right:1px dotted black;}
	#list a {display:block; font-size:90%;}
	a.active{color:black; font-weight:bold;}
	#panel { width:80%; float:left; margin-left:1%; text-align:left;}
	.bk {display: block;}
	.bkfull {display: block; width:100%;}
	textarea {font-family: sans-serif;}
	#panel label {margin:0;padding-top:8px;color:#444; font-size:80%;}
	#panel_options {padding-top:8px; padding-bottom:8px; background-color: #eef;}
	#panel_options label {margin-top:8px;color:#444; font-size:100%; margin-right:16px;}
	
	#title{font-size:130%;}
	#excerpt {height:40px;}
	#text {height:200px;}
	
	#panel_more{float:none; }
	#panel_more_right {float:right; width:45%; margin-top:2px;}
	#panel_more_left { width:45%; margin-right:5%;}
	#btnSave {padding:4px 20px;margin:-3px 8px 0 0; float:right;}
	.createLinks{ padding-left:16%; margin-bottom: 8px;}
	.createLinks a{color:black;margin-right:8px;}
</style>

<div class="createLinks">
	<a href="/system/edit.php?type=0">Pages</a>
	<a href="/system/edit.php?type=1">Posts</a>
	<a href="/system/edit.php?id=0&type=<?php echo $type ?>">Create new</a>
</div>

<form name="editor" id="editor" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
	<div id="list">  

		<?php
			foreach ($l as $v) {
				 
				echo "<a " . ($v['id'] == $id ? 'class="active"' : '' ) . " href='/system/edit.php?id={$v['id']}'>" . 
					($v['deleted']==1 ? '<s>' : '') .
					"{$v['title']}" . 
					($v['deleted']==1 ? '</s>' : '') .
					($v['draft']==1 ? '*' :'') .
					"</a>" 
					
					;
			}
		?>
	</div>
	<div id="panel">
		<label class="bk">Options</label>
		<div id="panel_options">
			<nobr><input id="draft" type="checkbox" <?php echo $r['draft']==1 ? 'checked' : ''?> />
			<input id="db_draft" name="db_draft" type="hidden" value="" />
			<label for="draft">Draft</label></nobr>

			<nobr><input id="comments" type="checkbox" <?php echo $r['comments']==1 ? 'checked' : ''?> />
			<input id="db_comments" name="db_comments" type="hidden" value="" />
			<label for="comments">Enable comments</label></nobr>

			<nobr><input id="requireauth" type="checkbox" <?php echo $r['requireauth']==1 ? 'checked' : ''?> />
			<input id="db_requireauth" name="db_requireauth" type="hidden" value="" />
			<label for="requireauth">Require authorization</label></nobr>

			<nobr><input id="deleted" type="checkbox" <?php echo $r['deleted']==1 ? 'checked' : ''?> />
			<input id="db_deleted" name="db_deleted" type="hidden" value="" />
			<label for="deleted">Deleted</label></nobr>

			<input id="btnSave" type="submit" value="Save" onclick="updVal();">	
		</div><!-- panel_options -->
		<input id="type" name="db_type" type="hidden" value="<?php echo $type; ?>" />
		<input id="id"   name="db_id"   type="hidden" value="<?php echo $r['id']; ?>" />
		<label for="title" class="bk">Title</label>
		<input id="title" name="db_title" class="bkfull" type="text" value="<?php echo $r['title'] ?>" />
		<label for="excerpt" class="bk">Excerpt</label>
		<textarea id="excerpt" name="db_excerpt" class="bkfull"><?php echo $r['excerpt']; ?></textarea>
		<label for="text" class="bk">Text</label>
		<textarea id="text" name="db_text" class="bkfull"><?php echo $r['text']; ?></textarea>
		<div style="margin-top:8px;">
			<a id="more" href="javascript:void();">More</a>
		</div>
		<div id="panel_more" style="display:none;">
			<div id="panel_more_right">
				<label for="publishdate" class="bk">Publish date</label>
				<input id="publishdate" name="db_publishdate" class="bkfull" type="text" value="<?php echo $r['publishdate'] ?>" />

				<label for="keywords" class="bk">Keywords</label>
				<input id="keywords" name="db_keywords" class="bkfull" type="text" value="<?php echo $r['keywords'] ?>" />
				
				<label for="parentid" class="bk">Parent</label>
				<select id="parentid" name="db_parentid" class="bkfull">
					<option value="0"></option>
					<?php
						foreach ($l as $v) {
							if ($v['id'] != $id && $v['deleted'] != 1)
								echo "<option value='{$v['id']}'>{$v['title']}</option>";
						}
					?>
				</select>
			</div>
			<div id="panel_more_left">
				<label for="permalink" class="bk">Permalink</label>
				<input id="permalink" name="db_permalink" class="bkfull" type="text" value="<?php echo $r['permalink'] ?>" />

				<label for="tags" class="bk">Tags</label>
				<input id="tags" name="db_tags" class="bkfull" type="text" value="<?php echo $r['tags'] ?>" />
				
				<label for="template" class="bk">Template</label>
				<input id="template" name="db_template" class="bkfull" type="text" value="<?php echo $r['template'] ?>" />
			</div>
		</div>

			
		
	</div>
</form>



<?php
	$p = ob_get_contents();
	ob_end_clean();
	
	$t = new Template();
	$t->assign('P', $p);
	$content = $t->process('system');
	
	echo $content;
?>