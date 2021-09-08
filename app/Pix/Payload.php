<?php 

namespace App\Pix;



class Payload {

  /**
  * IDs do Payload do Pix
  * @var string
  */
  const ID_PAYLOAD_FORMAT_INDICATOR = '00';
  const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
  const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
  const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
  const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
  const ID_MERCHANT_CATEGORY_CODE = '52';
  const ID_TRANSACTION_CURRENCY = '53';
  const ID_TRANSACTION_AMOUNT = '54';
  const ID_COUNTRY_CODE = '58';
  const ID_MERCHANT_NAME = '59';
  const ID_MERCHANT_CITY = '60';
  const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
  const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';
  const ID_CRC16 = '63';

  /**
   *  Chave Pix
   *  @var string
   */

  private $pix_key;

  
  /**
   *  Descrição do pagamento
   *  @var string
   */

  private $description;


  /**
   *  Nome do titular da conta
   *  @var string
   */

  private $merchant_name;


  /**
   *  Cidade do titular da conta
   *  @var string
   */

  private $merchant_city;


  /**
   *  ID da transação PIX
   *  @var string
   */

  private $txid;


  /**
   *  Valor da transação
   *  @var string
   */

  private $amount;


  /**
   * Método responsável por definir o valor do $pix_key
   * @param string $pix_key
   */
  public function set_pix_key($pix_key){
    $this->pix_key = $pix_key;
    return $this;
  }

  /**
   * Método responsável por definir o valor da $description
   * @param string $description
   */
  public function set_description($description){
    $this->description = $description;
    return $this;
  }


  /**
   * Método responsável por definir o valor do $merchant_name
   * @param string $merchant_name
   */
  public function set_merchant_name($merchant_name){
    $this->merchant_name = $merchant_name;
    return $this;
  }


  /**
   * Método responsável por definir o valor da $merchant_city
   * @param string $merchant_city
   */
  public function set_merchant_city($merchant_city){
    $this->merchant_city = $merchant_city;
    return $this;
  }


  /**
   * Método responsável por definir o valor do $txid
   * @param string $txid
   */
  public function set_txid($txid){
    $this->txid = $txid;
    return $this;
  }


  /**
   * Método responsável por definir o valor do $amount
   * @param float $amount
   */
  public function set_amount($amount){
    $this->amount = (string)number_format($amount, 2, '.', '');
    return $this;
  }

  /**
   * Responsável por retornar o valor completo de um objeto
   * @param string $id
   * @param string $value
   * @return string $id.$size.$value  
   */
  private function get_value($id, $value){
    $size = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
    return $id.$size.$value;
  }

  /**
   * Método responsável por retornar os valores completos das informações da conta
   * @return string
   */
  private function get_merchant_account_info(){
    //Domínio do banco
    $gui = $this->get_value(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');

    //Chave pix
    $key = $this->get_value(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $this->pix_key);

    //Descrição do pagamento
    $description = strlen($this->description) ? $this->get_value(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->description) : '';

    return $this->get_value(self::ID_MERCHANT_ACCOUNT_INFORMATION, $gui.$key.$description);
  }

  /**
   * Método responsável por retornar os valores completos do campo adicional do pix (TXID)
   * @return string
   */
  private function get_additional_data_field_template(){
    //TXID
    $txid = $this->get_value(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->txid);

    //Retorna o valor completo
    return $this->get_value(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $txid);
  }

  /**
   * Método responsável por gerar o código completo do payload Pix
   * @return string
   */
  public function get_payload(){
    //Cria o payload
    $payload = $this->get_value(self::ID_PAYLOAD_FORMAT_INDICATOR, '01').
               $this->get_merchant_account_info().
               $this->get_value(self::ID_MERCHANT_CATEGORY_CODE, '0000').
               $this->get_value(self::ID_TRANSACTION_CURRENCY, '986').
               $this->get_value(self::ID_TRANSACTION_AMOUNT, $this->amount).
               $this->get_value(self::ID_COUNTRY_CODE, 'BR').
               $this->get_value(self::ID_MERCHANT_NAME, $this->merchant_name).
               $this->get_value(self::ID_MERCHANT_CITY, $this->merchant_city).
               $this->get_additional_data_field_template();

    return $payload.$this->get_CRC16($payload);
  }

   /**
   * Método responsável por calcular o valor da hash de validação do código pix
   * @return string
   */
  private function get_CRC16($payload) {
    //ADICIONA DADOS GERAIS NO PAYLOAD
    $payload .= self::ID_CRC16.'04';

    //DADOS DEFINIDOS PELO BACEN
    $polinomio = 0x1021;
    $resultado = 0xFFFF;

    //CHECKSUM
    if (($length = strlen($payload)) > 0) {
        for ($offset = 0; $offset < $length; $offset++) {
            $resultado ^= (ord($payload[$offset]) << 8);
            for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                if (($resultado <<= 1) & 0x10000) $resultado ^= $polinomio;
                $resultado &= 0xFFFF;
            }
        }
    }

    //RETORNA CÓDIGO CRC16 DE 4 CARACTERES
    return self::ID_CRC16.'04'.strtoupper(dechex($resultado));
  }


}