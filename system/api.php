<?php
// API functions //////////////////////////////////////////////////////////////
require_once 'core.php';
if (Auth::validate()) {
	
	// Image list for post or page

	if (isset($_GET['getImagesFor']))
	{
		$id = $_GET['getImagesFor'];
		if (is_numeric($id)) {
			$thmbpath = "/images/$id/thmb";
			$dh = opendir(ROOTDIR . $thmbpath);
			$files = array();
			while($file = readdir($dh)) {
				if (($file == '.') || ($file == '..')) continue;
				$files[] = $file;
			}
			closedir($dh);
			echo json_encode($files);
		}
	}
	
	// Delete image
	
	if (isset($_GET['deleteImage']))
	{
		$f = $_GET['deleteImage'];
		
		if (substr($f, 0, 8) == "/images/" && file_exists(ROOTDIR.$f)) {
			$p = pathinfo($f);
			$path = ROOTDIR.$p['dirname'];
			$file = $p['basename'];
			
			if (file_exists("$path/$file")) unlink("$path/$file"); 
			if (file_exists("$path/full/$file")) unlink("$path/full/$file");
			if (file_exists("$path/thmb/$file")) unlink("$path/thmb/$file");
		}
	}
	
	
	// Test function
	
	if (isset($_GET['hello'])) {
		echo 'hello ' . $_GET['hello'];
	}
}

?>