<?php
include_once CORE;
if (Auth::validate() && $params[1]!='') {
?>

<a href="/edit?id=<?php echo $params[1]; ?>">
<img src="[[=S.THEMEURL]]/images/edit.png" width="13" height="13" ?></a>

<?php 
}  
?>