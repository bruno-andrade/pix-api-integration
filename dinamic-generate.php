<style>
  h3 {
    margin: 0;
  }
</style>

<?php 

require __DIR__.'/vendor/autoload.php';

use \App\Pix\Api;
use \App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$obj_api_pix = new Api('.hm.bb.com.br/', 
                       'eyJpZCI6ImY4Mzk5MmUtNyIsImNvZGlnb1B1YmxpY2Fkb3IiOjAsImNvZGlnb1NvZnR3YXJlIjoyMTkyOCwic2VxdWVuY2lhbEluc3RhbGFjYW8iOjF9',
                       'eyJpZCI6IjJhIiwiY29kaWdvUHVibGljYWRvciI6MCwiY29kaWdvU29mdHdhcmUiOjIxOTI4LCJzZXF1ZW5jaWFsSW5zdGFsYWNhbyI6MSwic2VxdWVuY2lhbENyZWRlbmNpYWwiOjEsImFtYmllbnRlIjoiaG9tb2xvZ2FjYW8iLCJpYXQiOjE2MzA5NTYxNjg4Mzl9'
                      );

$request = [
  'calendario' => [
    'expiracao' => 3600
  ],
  'devedor' => [
    'cpf' => "05399512424",
    'nome' => 'BRUNO PEREIRA ANDRADE'
  ],
  'valor' => [
    'original' => '0.01'
  ],
  'chave' => '28779295827',
  'solicitacaoPagador' => 'Pagamento do Pedido 123'
];

$response = $obj_api_pix->create_cob('QA212AS212BS212BS212Z2975Q', $request);
$consulta = $obj_api_pix->consult_cob('QA212AS212BS212BS212Z2975Q');
$response = json_decode($response, true);
$consulta = json_decode($consulta, true);


echo "<pre>";
if ($response) {
  echo "<br><h3>PIX GERADO</h3>";
  print_r ($response);
}else{
  echo "RESPONSE VAZIO\n\n";
}
echo "</pre>";

//Instancia do payload pix
$obj_payload = (new Payload)->set_merchant_name('BRUNO ANDRADE')
                            ->set_merchant_city('MACEIO')
                            ->set_amount($response['valor']['original'])
                            ->set_txid($response['txid'])
                            ->set_url($response['location'])
                            ->set_unique_payment(true);



//Código de pagamento PIX
$payload_qr_code = $obj_payload->get_payload();

//Instância do QR Code
$obj_qr_code = new QrCode($payload_qr_code);

$qr_code_image = (new Output\Png)->output($obj_qr_code, 400);


?>
<pre>  
  <h3>QR CODE DINÂMICO DO PIX</h3>  
  <img src="data:image/png;base64, <?php echo base64_encode($qr_code_image) ?>" alt="">    
  Código pix (Cópia e Cola): <strong><?php echo $payload_qr_code ?></strong>
</pre>

<?php

echo "<pre>";
if ($consulta) {
  echo "<br><h3>CONSULTA GERADA</h3>";
  print_r ($consulta);
}else{
  echo "RESPONSE VAZIO\n\n";
}
echo "</pre>";

?>