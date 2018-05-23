<?php
namespace BG\Barcode;
require('Base2DBarcode.php');

if (array_key_exists('code',$_GET)) {
	$code = $_GET['code'];
}
else {
	echo "Code is required";
}

if (array_key_exists('type',$_GET)) {
	$type = $_GET['type'];
}
else {
	echo "Type is required";
}

if (array_key_exists('width',$_GET)) {
	$w= $_GET['width'];
}
else {
	$w = 10;
}

if (array_key_exists('height',$_GET)) {
	$h = $_GET['height'];
}
else {
	$h = 10;
}


$barcoder = new Base2DBarcode();
echo $barcoder->getBarcodeTableHTML($code, $type, $w, $h);
?>
