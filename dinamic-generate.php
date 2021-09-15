<style>
  h3 {
    margin: 0;
  }
</style>


<?php 


/**
 * NA PAGINA DE "OBRIGADO" (THANKYOU) VAMOS GERAR O QRCODE COM OS DADOS OBTIDOS NO PEDIDO
 * Verificar como isso pode ser feito
 * acredito que a maneira mais rápida é colocar isso dentro do functions.php
 * em seguida utilizamos as funções direto na pagina thankyou e geramos o qrcode
 *  
 */



require __DIR__.'/vendor/autoload.php';

use \App\Pix\Api;
use \App\Pix\Payload;
use \App\Pix\Data;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$cod_pedido  = isset($_POST['cod_pedido']) ? $_POST['cod_pedido'] : strtoupper(uniqid());
$obj_data    = new Data();
$txid_prefix = $obj_data->get_txid_prefix($cod_pedido);
$data        = $obj_data->get_data($txid_prefix);
$obj_api_pix = new Api($data['route'], $data['client_id'], $data['client_secret']);

echo "<pre>";
if ($data) {
  echo "<br><h3>DADOS</h3>";
  print_r ($data);
}else{
  echo "RESPONSE VAZIO\n\n";
}
echo "</pre>";
print_r(strlen($data['txid']));


$request = [
  'calendario' => [
    'expiracao' => 86400
  ],
  'devedor' => [
    'cpf' => "24409085093",
    'nome' => 'BRUNO PEREIRA ANDRADE'
  ],
  'valor' => [
    'original' => '0.01'
  ],
  'chave' => '28779295827',
  'solicitacaoPagador' => 'Pagamento do Pedido 123'
];
$response = $obj_api_pix->create_cob($data['txid'], $request);


echo "<pre>";
if ($response) {
  echo "<br><h3>PIX GERADO</h3>";
  print_r ($response);
}else{
  echo "RESPONSE VAZIO\n\n";
}
echo "</pre>";

//Instancia do payload pix
$obj_payload = (new Payload)->set_merchant_name($data['merchant_name'])
                            ->set_merchant_city($data['merchant_city'])
                            ->set_amount($response['valor']['original'])
                            ->set_txid('***')
                            ->set_url($response['location'])
                            ->set_unique_payment(true);


//Código de pagamento PIX
$payload_qr_code = $obj_payload->get_payload();

//Instância do QR Code
$obj_qr_code = new QrCode($payload_qr_code);

$qr_code_image = (new Output\Png)->output($obj_qr_code, 250);


?>
<pre>  
  <h3>QR CODE DINÂMICO DO PIX</h3>  
  <img src="data:image/png;base64, <?php echo base64_encode($qr_code_image) ?>" alt="">    
  Código pix (Cópia e Cola): <strong><?php echo $payload_qr_code ?></strong>
</pre>

