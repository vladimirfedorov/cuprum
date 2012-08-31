<?php
// CODE ///////////////////////////////////////////////////////////////////////
require_once 'core.php';
if (!Auth::validate()) header('Location: /');

header('Content-type: text/html; charset=utf-8');

if(isset($_POST['db_id'])) {
	$id = DB::saveFormData('content');

	if ($_POST['db_id'] == 0){
		header("Location: /edit?id=$id");
		exit;
	};
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

$menu = '<li><a href="?type=0">Pages</a> </li>' .
	    '<li><a href="?type=1">Posts</a> </li>' ;

Template::assign('menu', $menu);


// FORM ///////////////////////////////////////////////////////////////////////
?>

		<div class="imageadd" >
			<form id="frmUpload" name="uploadform" action="/system/upload.php" enctype="multipart/form-data" method="POST" target="uploadTarget">
				<input name="id" type="hidden" value="<?php echo $id; ?>" />
				<input id="btnOpenFile" name="file" size="30" type="file" />  
				<input id="btnSendFile" name="fileupload" type="submit" value="Add" />  
				<iframe id="uploadTarget" name="uploadTarget"></iframe>
			</form>
		</div>

		
<form name="editor" id="editor" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
	
	<div id="filters">
		Sort by 
		<input type='radio' name='Orderby' id='OrderById' value="Id" checked /><label for="OrderById">creation order</label>
		&nbsp;
		<input type='radio' name='Orderby' id='OrderByPub' value="PubDate" /><label for="OrderByPub">publication date</label>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='checkbox' name='ShowDeleted' id="ShowDeleted" /><label for="ShowDeleted">Show deleted</label>
		&nbsp;&nbsp;&nbsp;&nbsp;
		Set filter:
		<input type='text' value="" />
		<img class="info" src="[[=S.THEMEURL]]/images/info.png" width="15" height="15" title="Filter formats" />
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="Submit" class="submit" value="Apply" />
	</div>
	
	<div id="list">  <ul>
		<li class="addnew"><span style="display:inline-block; width:27px">&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="?id=0&type=<?php echo $type; ?>">Add new</a></li>
		<?php
			foreach ($l as $v) {
				echo '<li><nobr>';
				echo '<img src="[[=S.THEMEURL]]/images/lock_'.$v['requireauth'].'.png" width="9" height="9" title="Locked" />&nbsp';
				echo '<img src="[[=S.THEMEURL]]/images/comm_'.$v['comments'].'.png" width="9" height="9" title="Comments enabled" />&nbsp';
				echo '<img src="[[=S.THEMEURL]]/images/draft_'.$v['draft'].'.png" width="9" height="9" title="Draft" />&nbsp;&nbsp;';
				echo "<a " . ($v['id'] == $id ? 'class="active"' : '' ) . " href='/edit?id={$v['id']}'>" . 
					($v['deleted']==1 ? '<s>' : '') .
					"{$v['title']}" . 
					($v['deleted']==1 ? '</s>' : '') .
					"</a></nobr></li>" 
					;
			}
		?>
		</ul>
	</div>
	<div id="panel">
		<label class="bk">Options</label>
		<div id="panel_options">
			<nobr><input id="draft" type="checkbox" <?php echo ($r['draft']==1 || $r['id']==0) ? 'checked' : ''?> />
			<input id="db_draft" name="db_draft" type="hidden" value="" />
			<label for="draft">Draft</label></nobr>

			<nobr><input id="comments" type="checkbox" <?php echo ($r['comments']==1 || ($r['id']==0 && $type>0))? 'checked' : ''?> />
			<input id="db_comments" name="db_comments" type="hidden" value="" />
			<label for="comments">Enable comments</label></nobr>

			<nobr><input id="requireauth" type="checkbox" <?php echo $r['requireauth']==1 ? 'checked' : ''?> />
			<input id="db_requireauth" name="db_requireauth" type="hidden" value="" />
			<label for="requireauth">Require authorization</label></nobr>


			<input id="btnSave" type="submit" value="Save" onclick="updVal();" />	
			<?php if ($id != 0) { ?>
			<input id="btnDelete" type="submit" value="<?php echo ($r['deleted']==1 ? 'Undelete' : 'Delete')?>" onclick="toggleDelete();updVal();" />
			<?php } ?>

		</div><!-- panel_options -->
		<input id="type" name="db_type" type="hidden" value="<?php echo $type; ?>" />
		<input id="id"   name="db_id"   type="hidden" value="<?php echo $id; ?>" />
		<input id="db_deleted" name="db_deleted" type="hidden" value="<?php echo $r['deleted']; ?>" />

		<label for="title" class="bk">Title</label>
		<input id="title" name="db_title" class="bkfull" type="text" value="<?php echo $r['title'] ?>" />
		<label for="excerpt" class="bk">Excerpt</label>
		<textarea id="excerpt" name="db_excerpt" class="bkfull"><?php echo $r['excerpt']; ?></textarea>
		<label for="text" class="bk">Text</label>
		<textarea id="text" name="db_text" class="bkfull"><?php echo $r['text']; ?></textarea>

		<div class="addfile">
			<a href="#AddFile" onclick="return addFile();"><img src="[[=S.THEMEURL]]/images/attach.png" width="15" height="15" /> Add file/image</a>
			<img id="snake" src="[[=S.THEMEURL]]/images/snake.gif" />
			&nbsp;
			<span id="pnlImageOptions">
				<a id="btnDeleteFile" href="#DeleteFile" onclick="return deleteFile();"><img src="[[=S.THEMEURL]]/images/attach.png" width="15" height="15" /> Delete file/image</a>
				&nbsp;
				<a href="#InsertFile" onclick="return insertFileRef();"><img src="[[=S.THEMEURL]]/images/attach.png" width="15" height="15" /> Insert into text</a>
			</span>
		</div>
		
		
		<div id="imgList" class="imagelist"></div>
		
		<div id="panel_more" class="table">
			<div class="trow">
				<div class="panel_more_col tcell">
					<label for="permalink" class="bk">Permalink</label>
					<input id="permalink" name="db_permalink" class="bkfull" type="text" value="<?php echo $r['permalink'] ?>" />
	
					<label for="tags" class="bk">Tags</label>
					<input id="tags" name="db_tags" class="bkfull" type="text" value="<?php echo $r['tags'] ?>" />
					
					<label for="template" class="bk">Template</label>
					<input id="template" name="db_template" class="bkfull" type="text" value="<?php echo $r['template'] ?>" />
				</div>
				<div class="tcell">&nbsp;</div>
				<div class="panel_more_col tcell">
					<?php if ($type>0) { ?>
					<label for="publishdate" class="bk">Publish date</label>
					<input id="publishdate" name="db_publishdate" class="bkfull" type="text" value="<?php echo $r['publishdate'] ?>" />
					<?php } ?>
					<label for="keywords" class="bk">Keywords</label>
					<input id="keywords" name="db_keywords" class="bkfull" type="text" value="<?php echo $r['keywords'] ?>" />
					<?php if ($type==0) { ?>				
					<label for="parentid" class="bk">Parent</label>
					<select id="parentid" name="db_parentid" class="bkfull">
						<option value="0"></option>
						<?php
							foreach ($l as $v) {
								$sel = ($r['parentid'] == $v['id'] ? ' selected' : ''); 
								if ($v['id'] != $id && $v['deleted'] != 1)
									echo "<option value='{$v['id']}' $sel>{$v['title']}</option>";
							}
						?>
					</select>
					<?php } ?>
				</div>
			</div>
		</div>
		
	</div>
</form>

<script>
	$(document).ready(function(){
		$('#list').height(
			$('#panel').height()
		);
	})
	
	function updVal() {
		$("input:checkbox").each(function(i,obj){
			$("#db_"+obj.id).val(
				obj.checked ? 1 : 0
			);
		})
	}
	
	function toggleDelete() {
		//alert('#db_deleted: '+$('#db_deleted').val());
		$('#db_deleted').val(function(i,val) { 
			return (val == '1' ? '0' : '1'); 
		});
	}
</script>
