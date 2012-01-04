<?php
require_once 'core.php';
Auth::logout();
$ret = (isset($_GET['ret']) ? $_GET['ret'] : '/');
header("Location: $ret");
?>
