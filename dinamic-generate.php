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

$request = [
  'calendario' => [
    'expiracao' => 3600
  ],
  'devedor' => [
    'cpf' => '12312312332',
    'nome' => 'Francisco da Silva'
  ],
  'valor' => [
    'original' => '10.00'
  ],
  'chave' => '12345678909',
  'solicitacaoPagador' => 'Pagamento do Pedido 123'
];

$response = $obj_api_pix->create_cob('BRUNO12345678912345123451', $request);

if (!isset($response['location'])) {
  echo "Problemas ao gerar pix dinâmico";
  echo "<pre>";
  print_r($response);
  echo "</pre>"; exit;
}

//Instancia do payload pix
$obj_payload = (new Payload)->set_merchant_name('Bruno Andrade')
                            ->set_merchant_city('Maceió')
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

<h1>Qr Code Dinâmico do Pix</h1>
<br>

<img src="data:image/png;base64, <?php echo base64_encode($qr_code_image) ?>" alt="">

<br><br>

Código pix: <strong><?php echo $payload_qr_code ?></strong>