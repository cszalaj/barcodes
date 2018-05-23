<?php
namespace BG\Barcode;
require('Base1DBarcode.php');
require('Base2DBarcode.php');

$barcoder = new Base1DBarcode();
$barcoder2 = new Base2DBarcode();

echo $barcoder->getBarcodeTableHTML('123445667890','C128');
echo('<hr>');
echo $barcoder->getBarcodeHTML('123445667890','C128');
echo('<hr>');
echo($barcoder2->getBarcodeTableHTML('1234567890','QRCODE'));
echo('<hr>');
echo($barcoder2->getBarcodeHTML('1234567890','QRCODE'));

?>
