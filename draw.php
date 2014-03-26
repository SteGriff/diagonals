<?php

$right = 0;
$bottom = 0;
$im = null;
$foreground = null;

function GET($key, $default){
	//Check value is set and specified in query
	if (isset($_GET[$key]) && $_GET[$key] != ''){
		//Return false if it is string=='false'
		//otherwise return its given value
		return ($_GET[$key] == 'false' ? false : $_GET[$key]);
	}
	else{ 
		//If not specified, return default value
		return $default;
	}
}

function twistHas($twist, $mode){
	//Does twist variable contain given mode?
	return $twist == $mode || $twist == 3;
}

function drawBorder($border){
	global $right, $bottom, $im, $foreground;
	if ($border < 1){ return; }
	
	//Bounding rectangle

	//Draw single pixel border
	imagerectangle($im, 0, 0, $right, $bottom, $foreground);
	//Draw more border thickness if specified
	$b = 0;
	for ($border > 1; $b < $border; $b++){
		imagerectangle($im, $b, $b, $right-$b, $bottom-$b, $foreground);
	}

	//When finished generation
	imagealphablending($im, false);
	imagesavealpha($im, true);
	return $im;
}

function drawDiagonals($phase, $twist){
	global $right, $bottom, $im, $foreground;

	// Spread across bottom
	if (twistHas($twist, 1)){
		for ($i = $bottom; $i > 0; $i -= $phase){
			imageline($im, 0, $bottom, $right, $i, $foreground);
		}
	}
	else{
		for ($left = 0; $left < $right; $left += $phase){
			imageline($im, $left, $bottom, $left+$bottom, 0, $foreground);
		}
	}
	
	// Spread up left side
	if (twistHas($twist, 2)){
		for ($i = $bottom; $i > 0; $i -= $phase){
			imageline($im, 0, $i, $bottom, 0, $foreground);
		}
	}
	else{
		for ($i = $bottom; $i > 0; $i -= $phase){
			imageline($im, 0, $i, $i, 0, $foreground);
		}
	}
}

function drawObject($diagonals, $width, $height, $phase, $border, $twist){
	global $right, $bottom, $im, $foreground;
	
	//Returns image resource
	
	//Set up image
	//Do a load of dumb stuff to make a transparent background
	$im = imagecreatetruecolor($width, $height);
	imagealphablending($im,false);
	
	$transparent = imagecolorallocatealpha($im, 0,0,0,127);
	imagefilledrectangle($im,0,0,$width,$height,$transparent);
	imagealphablending($im, true);

	//Set cropped dimensions (global)
	$right = $width - 1;
	$bottom = $height - 1;

	//Add a colour to use (global)
	$foreground = imagecolorallocate($im, 0, 0, 0); //Black

	if ($border > 0){
		drawBorder($border);
	}
	
	if ($diagonals){
		drawDiagonals($phase, $twist);
	}
	
	//When finished generation
	imagealphablending($im, false);
	imagesavealpha($im, true);
	return $im;
}

//Start
header('Content-Type: image/png');
$im = null;

$phase = GET('phase', 10);
$height = GET('height', 20);
$width = GET('width', 100);
$border = GET('border', true);
$diagonals = GET('diagonals', true);

//Twist one half or both: 0-none, 1-a, 2-b, 3-a+b
$twist = GET('twist', 0);

$im = drawObject($diagonals, $width, $height, $phase, $border, $twist);

imagepng( $im );
imagedestroy($im);
?>