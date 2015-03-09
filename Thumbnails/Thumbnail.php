<?php
/**
 * Creates a Thumbnail from a given Image (GIF, JPEG and PNG) others are not supported yet. This function requires GD-PHP-LIB to be installed (apt-get install php5-gd)
 * @author manuel
 * @param $file String Path to the Image
 * @param $width int Width of the Thumbnail
 * @param $height int Height of the Thumbnail
 * @param $filename String Path where the Thumbnail will be created (e.g. "path\to\the\image.jpeg")
 */
function resizePropotional($file, $width, $height, $filename) {
	$image;
	$func_info = "";
		
	// File exists
	if (! file_exists ( $file ))
		return $func_info;
	
	$image_info = getimagesize ( $file );
	/*
	 * [0] -> width
	 * [1] -> height
	 * [2] -> image extension 
	 */
	//Prevent memory exhausted
// 	$func_info .= "Checking resolution<br/>\n";
// 	if(($image_info[0] * $image_info[1]) > (1920*1080)) {
// 		$func_info .= "More than (1920*1080) Pixels are not allowed";
// 		return $func_info;
// 	}
	//Check file format
	$func_info .= "Checking Image-Type ...\n";
	echo "Usage before picture creating: " . memory_get_usage(true)/1000000 . "MB";
	switch($image_info[2]) {
		case 1:
		//Catch exhausted memory error and prints the memory usage AFTER this error TODO
// 			try {
			
// 			} catch() {
				
// 			}
			$image = imagecreatefromgif($file);
			$func_info .= "Image Type is GIF\n";
			break;
		case 2:
			$image = imagecreatefromjpeg($file);
			$func_info .= "Image Type is JPEG\n";
			break;
		case 3:
			$image = imagecreatefrompng($file);
			$func_info .= "Image Type is PNG\n";
			break;
		default:
			$func_info .= "Unsupported Image-Type, Following Image-Types are supported: GIF, JPEG, PNG";
			return $func_info;
	}
	echo "Usage after picture creating: " . memory_get_usage(true)/1000000 . "MB";	
	//Create propotional width or height
	$func_info .= "Detecting propotional width or height";
	if($width && ($image_info[0] < $image_info[1])) {
		$func_info .= "Width greater than Height, making Width propotional";
		$width = ($height / $image_info[1]) * $image_info[0];
	} else {
		$func_info .= "Width less than Height, making Height propotional";
		$height = ($width / $image_info[0]) * $image_info[1];
	}
		
	$imageasd = imagecreatetruecolor($width, $height);
		
	imagecopyresampled($imageasd, $image, 0, 0, 0, 0, $width, $height, $image_info[0], $image_info[1]);
	
	imagejpeg($imageasd, $filename, 100);
	
	return true;
}
?>