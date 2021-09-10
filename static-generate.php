<?php 

require __DIR__.'/vendor/autoload.php';

use \App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

//Instancia do payload pix
$obj_payload = (new Payload)->set_pix_key('chavepix123')
                            ->set_description('Pagamento do pedido x123')
                            ->set_merchant_name('Bruno Andrade')
                            ->set_merchant_city('Maceió')
                            ->set_amount(100.00)
                            ->set_txid('XA212AS212BS212BS212Z29759');


//Código de pagamento PIX
$payload_qr_code = $obj_payload->get_payload();

//Instância do QR Code
$obj_qr_code = new QrCode($payload_qr_code);

$qr_code_image = (new Output\Png)->output($obj_qr_code, 400);


?>

<h1>Qr Code</h1>
<br>

<img src="data:image/png;base64, <?php echo base64_encode($qr_code_image) ?>" alt="">

<br><br>

Código pix: <strong><?php echo $payload_qr_code ?></strong>