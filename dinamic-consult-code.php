<?php 

require __DIR__.'/vendor/autoload.php';

use \App\Pix\Api;
use \App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$obj_api_pix = new Api('.hm.bb.com.br/', 
                       'eyJpZCI6ImY4Mzk5MmUtNyIsImNvZGlnb1B1YmxpY2Fkb3IiOjAsImNvZGlnb1NvZnR3YXJlIjoyMTkyOCwic2VxdWVuY2lhbEluc3RhbGFjYW8iOjF9',
                       'eyJpZCI6IjJhIiwiY29kaWdvUHVibGljYWRvciI6MCwiY29kaWdvU29mdHdhcmUiOjIxOTI4LCJzZXF1ZW5jaWFsSW5zdGFsYWNhbyI6MSwic2VxdWVuY2lhbENyZWRlbmNpYWwiOjEsImFtYmllbnRlIjoiaG9tb2xvZ2FjYW8iLCJpYXQiOjE2MzA5NTYxNjg4Mzl9');

$response = $obj_api_pix->consult_cob('B1R2F3S5A9B1R2F3S5A9153459');


echo "<pre>";
if ($response) {
  echo "<h3>RESPONSE:</h3><br>";
  print_r ($response);
}else{
  echo "RESPONSE VAZIO\n\n";
}
echo "</pre>"; exit;