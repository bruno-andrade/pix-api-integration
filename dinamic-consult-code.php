<?php 

require __DIR__.'/vendor/autoload.php';

use \App\Pix\Api;
use \App\Pix\Data;

$obj_data    = (new Data())->get_data();
$obj_api_pix = new Api($obj_data['route'], $obj_data['client_id'], $obj_data['client_secret']);

echo "<pre>";
if ($obj_data) {
  echo "<br><h3>DADOS</h3>";
  print_r ($obj_data);
}else{
  echo "RESPONSE VAZIO\n\n";
}
echo "</pre>";


$response = $obj_api_pix->consult_cob('MODULOPIX2553613F402560C61');


echo "<pre>";
if ($response) {
  echo "<br><h3>CONSULTA GERADA</h3>";
  print_r ($response);
}else{
  echo "RESPONSE VAZIO\n\n";
}
echo "</pre>";

