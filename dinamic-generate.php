<style>
  h3 {
    margin: 0;
  }
</style>

<?php 

require __DIR__.'/vendor/autoload.php';

use \App\Pix\Api;
use \App\Pix\Payload;
use \App\Pix\Data;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$obj_data = (new Data())->get_data();

echo "<pre>";
if ($obj_data) {
  echo "<br><h3>DADOS</h3>";
  print_r ($obj_data);
}else{
  echo "RESPONSE VAZIO\n\n";
}
echo "</pre>";

$obj_api_pix = new Api($obj_data['route'], $obj_data['client_id'], $obj_data['client_secret']);

$request = [
  'calendario' => [
    'expiracao' => 186400
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

$response = $obj_api_pix->create_cob($obj_data['txid'], $request);


echo "<pre>";
if ($response) {
  echo "<br><h3>PIX GERADO</h3>";
  print_r ($response);
}else{
  echo "RESPONSE VAZIO\n\n";
}
echo "</pre>";

//Instancia do payload pix
$obj_payload = (new Payload)->set_merchant_name($obj_data['merchant_name'])
                            ->set_merchant_city($obj_data['merchant_city'])
                            ->set_amount($response['valor']['original'])
                            ->set_txid('***')
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

