<?php
$page = ((isset($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0);
$ppp = 2;
$offset = $page * $ppp;

$cond = 'type=1 And draft<>1 And deleted<>1 And Now()>=publishdate And publishdate>0';

$numrecs = DB::GetRowsCount('content', $cond);
$div = floor($numrecs/$ppp) - 1;	
$maxpage = ($numrecs<$ppp ? 0 : (($numrecs % $ppp) == 0 ? $div : $div+1)); 
 
$res = DB::GetRows('content', "$cond Order by publishdate desc Limit $offset, $ppp", "date_format(publishdate,'%Y%m%d-%H%i') as ref");

Template::assign('Blog', $res)

?>




<?php
// Newer posts
if ($page > 0) {
?>
	<div class="blog-new"><a href="/blog?p=<?php echo ($page-1); ?>">&larr;</a></div>
<?php
}
?>

<?php 
// Older posts
if ($page < $maxpage) {
?>
	<div class="blog-old"><a href="/blog?p=<?php echo ($page+1); ?>">&rarr;</a></div>

<?php	
}
?>
