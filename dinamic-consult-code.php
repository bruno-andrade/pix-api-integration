<?php 

require __DIR__.'/vendor/autoload.php';

use \App\Pix\Api;
use \App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$obj_api_pix = new Api('https://api.hm.bb.com.br', 
                       'eyJpZCI6ImY4Mzk5MmUtNyIsImNvZGlnb1B1YmxpY2Fkb3IiOjAsImNvZGlnb1NvZnR3YXJlIjoyMTkyOCwic2VxdWVuY2lhbEluc3RhbGFjYW8iOjF9',
                       'eyJpZCI6IjJhIiwiY29kaWdvUHVibGljYWRvciI6MCwiY29kaWdvU29mdHdhcmUiOjIxOTI4LCJzZXF1ZW5jaWFsSW5zdGFsYWNhbyI6MSwic2VxdWVuY2lhbENyZWRlbmNpYWwiOjEsImFtYmllbnRlIjoiaG9tb2xvZ2FjYW8iLCJpYXQiOjE2MzA5NTYxNjg4Mzl9',
                       '');

$response = $obj_api_pix->consult_cob();

if (!isset($response['location'])) {
  echo "Problemas ao gerar pix dinâmico";
  echo "<pre>";
  print_r($response);
  echo "</pre>"; exit;
}

echo "Consulta realizada";
echo "<pre>";
print_r($response);
echo "</pre>"; exit;