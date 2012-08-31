<?php
// CODE ///////////////////////////////////////////////////////////////////////
require_once 'core.php';
if (Auth::validate()) {
	if (isset($_POST['fileupload'])) {
	    $userfile_name = $_FILES["file"]["name"];  
	    $userfile_tmp = $_FILES["file"]["tmp_name"];  
	    $userfile_size = $_FILES["file"]["size"];  
	    $filename = basename($_FILES["file"]["name"]);  
	    $file_ext = substr($filename, strrpos($filename, ".") + 1);  		
		$id = $_POST['id'];
		
		// is image
		if (false !== strpos("|jpg|jpeg|png|gif|", "|$file_ext|")) {
			$fpath = ROOTDIR . "/images/$id/";
			if (!file_exists($fpath)) mkdir($fpath);
			$imgfull = $fpath . "full/";
			if (!file_exists($imgfull)) mkdir($imgfull);
			$imgthmb = $fpath . "thmb/";
			if (!file_exists($imgthmb)) mkdir($imgthmb);
			
			$imgfullfile = $imgfull . $filename;
			
			move_uploaded_file($userfile_tmp, $imgfullfile);
			chmod($imgfullfile, 0777);
			
			list($width, $height, $imgtype) = getimagesize($imgfullfile);
			
			
			
			$image = null;
			if ($imgtype == IMAGETYPE_GIF)
				$image = imagecreatefromgif($imgfullfile);
			if ($imgtype == IMAGETYPE_JPEG)
				$image = imagecreatefromjpeg($imgfullfile);
			if ($imgtype == IMAGETYPE_PNG)
				$image = imagecreatefrompng($imgfullfile);
			
			$smallratio = MAXIMAGEWIDTH/$width;
			$smallw = MAXIMAGEWIDTH;
			$smallh = $smallratio * $height;
			if ((MAXIMAGEHEIGHT != 0) && ($msallh > MAXIMAGEHEIGHT)) {
				$smallh = MAXIMAGEHEIGHT;
				$smallratio = MAXIMAGEHEIGHT/$smallh;
				$smallw = $smallratio * $smallh;
			}
			
			$thmbratio = 60/$height;
			$thmbw = $thmbratio * $width;
			$thmbh = 60;

			$small = imagecreatetruecolor($smallw, $smallh);
			imagecopyresampled($small, $image, 0, 0, 0, 0, $smallw, $smallh, $width, $height);
			if ($imgtype == IMAGETYPE_GIF)
				imagegif($small, $fpath . $filename);
			if ($imgtype == IMAGETYPE_JPEG)
				imagejpeg($small, $fpath . $filename, 90);
			if ($imgtype == IMAGETYPE_PNG)
				imagepng($small, $fpath . $filename);

			$thmb = imagecreatetruecolor($thmbw, $thmbh);
			imagecopyresampled($thmb, $image, 0, 0, 0, 0, $thmbw, $thmbh, $width, $height);
			if ($imgtype == IMAGETYPE_GIF)
				imagegif($thmb, $imgthmb . $filename);
			if ($imgtype == IMAGETYPE_JPEG)
				imagejpeg($thmb, $imgthmb . $filename, 90);
			if ($imgtype == IMAGETYPE_PNG)
				imagepng($thmb, $imgthmb . $filename);


			echo $fpath;
		}
		// else is file
		else {
			$fpath = ROOTDIR . "/files/$id/";
			if (!file_exists($fpath)) 
				mkdir($fpath);
			move_uploaded_file($userfile_tmp, $fpath . $filename);
			
		}
	}
}
?>