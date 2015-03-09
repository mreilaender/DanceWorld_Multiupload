<?php
error_reporting ( E_ALL );
include '../Thumbnails/Thumbnail.php';
$target_dir = ""; // Picture directory
$thum_dir = ""; // Thumbnail directory
$temp_dir = ""; // Temporary directory, where files will be uploaded in full size (e.g. 4k), but will be cleared after finishing convertation
makeDirs();
$nextNumber = false; // Jakub Kopec file number system, foto 1 (1.png) has a thumbnail with the exact same name (1.png) directories listed above
$supported_extensions = array (
		1 => "jpeg",
		2 => "jpg",
		3 => "png",
		4 => "gif" 
);
if (isset ( $_POST ['upload'] )) {
	checkNextNumber ();
	$tmp = "LOG:<br/>";
	$tmp = checkFile ();
	$tmp = uploadFile ();
	echo "<br/>" . $tmp;
}
function makeDirs() {
	$tmp = __DIR__;
	$tmp2 = explode ( "/", $tmp);
	$tmp3 = "";
	// 	echo "<br/>";
	// 	echo $tmp2[1];
	for($i = 0; $i < (count ( $tmp2 )-1); ++ $i) {
		$tmp3 .= $tmp2[$i] . "/";
	}
	$GLOBALS['target_dir'] = $tmp3 . "Bilder/Fotos/";
	$GLOBALS['thum_dir'] = $tmp3 . "Bilder/Thumbnails/";
	$GLOBALS['temp_dir'] = $tmp3 . "Bilder/Temporaer/";
	return true;
}
function checkNextNumber() {
	$dir = opendir ( $GLOBALS ["target_dir"] );
	while ( $datei = readdir ( $dir ) ) {
		$tmp = explode ( '.', $datei );
		if ($GLOBALS ['nextNumber'] < $tmp [0])
			$GLOBALS ['nextNumber'] = $tmp [0];
	}
	closedir ( $dir );
	if ($GLOBALS ['nextNumber'] == false)
		$GLOBALS ['nextNumber'] = 1;
	else
		++ $GLOBALS ['nextNumber'];
}
function checkFile() {
	$func_info = "";
	if ($GLOBALS ['nextNumber'] == false) {
		$func_info .= "nextNumber hasn't been set yet<br/>\n";
		return $func_info;
	}
	
	$filename = explode ( '.', basename ( $_FILES ['file_upload'] ['name'] ) );
	$tmp = end ( $filename );
	$target_ext = strtolower ( $tmp );
	$GLOBALS ["target_dir"] .= $GLOBALS ['nextNumber'] . ".$target_ext"; // Adding Filenamne to path = /path/to/file.asd
	$GLOBALS ["thum_dir"] .= $GLOBALS ['nextNumber'] . ".$target_ext"; // Adding Filenamne to path = /path/to/file.asd
	                                                                   
	// Check if supported file extension, listed above as a global
	$supported = false;
	$func_info .= "Checking if file extension is supported<br/>\n";
	foreach ( $GLOBALS ["supported_extensions"] as $key => $value ) {
		if ($target_ext == $value) {
			$func_info .= "Supported file extension<br/>\n";
			$supported = true;
		}
	}
	if (! $supported) {
		$func_info .= "Unsupported File extension: $target_ext<br/>\n";
		return $func_info;
	}
	return true;
}
/**
 * checkFile has to be called first or $GLOBALS['target_dir'] has to be set to a path like that: path/to/file.jpg (JPG, JPEG, GIF or PNG)
 * 
 * @return boolean|string
 */
function uploadFile() {
	$func_info = "";
	
	// Uploading file
	$func_info .= "Uploading file from <b>" . $_FILES ["file_upload"] ["tmp_name"] . "</b> to <b>" . $GLOBALS ['temp_dir'] . basename ( $_FILES ['file_upload'] ['name'] ) . "</b><br/>\n";
	
	if (move_uploaded_file ( $_FILES ["file_upload"] ["tmp_name"], $GLOBALS ['temp_dir'] . basename ( $_FILES ['file_upload'] ['name'] ) )) {
		$func_info .= "File has been successfully uploaded<br/>\n";
	} else {
		$func_info .= "Some error occurred<br/>\n";
		return $func_info;
	}
	
	// Converting to FullHD
	$func_info .= "Converting to FullHD<br/>\n";
	$tmp = resizePropotional ( $GLOBALS ['temp_dir'] . basename ( $_FILES ['file_upload'] ['name'] ), "1920", "1080", $GLOBALS ['target_dir'] );
	if ($tmp === true) {
		$func_info .= "Convertation to FullHD successful<br/>\n";
	} else {
		$func_info .= "Convertation to FullHD failed, function (resizePropotional) log:<br/>\n------------------ LOG ----------------------<br/>\n" . $tmp . "<br/>\n--------------- END LOG ------------------";
		return $func_info;
	}
	
	// Creating Thumbnail
	$func_info .= "Making Thumbnail<br/>\n";
	// TODO Adjust thumbnail measures
	$tmp = resizePropotional ( $GLOBALS ['temp_dir'] . basename ( $_FILES ['file_upload'] ['name'] ), "500", "300", $GLOBALS ['thum_dir'] );
	if ($tmp === true) {
		$func_info .= "Thumbnail successfully created";
	} else {
		$func_info .= "Couldn't create Thumbnail, function log (resizePropotional) log:<br/>\n------------------ LOG ----------------------<br/>\n" . $tmp . "<br/>\n--------------- END LOG ------------------";
		return $func_info;
	}
	
	// Temp ordner leeren
	$dir = opendir ( $GLOBALS ['temp_dir'] );
	while ( $file = readdir ( $dir ) ) {
		if ($file != "." && $file != "..") {
			unlink ( $GLOBALS ['temp_dir'] . $file );
		}
	}
	closedir ( $dir );
	return true;
}
?>