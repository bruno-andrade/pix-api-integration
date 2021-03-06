<?php 

namespace App\Pix;

class Payload {

  /**
  * IDs do Payload do Pix
  * @var string
  */
  const ID_PAYLOAD_FORMAT_INDICATOR = '00';
  const ID_POINT_OF_INITIATION_METHOD = '01';
  const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
  const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
  const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
  const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
  const ID_MERCHANT_ACCOUNT_INFORMATION_URL = '25';
  const ID_MERCHANT_CATEGORY_CODE = '52';
  const ID_TRANSACTION_CURRENCY = '53';
  const ID_TRANSACTION_AMOUNT = '54';
  const ID_COUNTRY_CODE = '58';
  const ID_MERCHANT_NAME = '59';
  const ID_MERCHANT_CITY = '60';
  const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
  const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';
  const ID_CRC16 = '63';

  private $pix_key;
  private $description;
  private $merchant_name;
  private $merchant_city;
  private $txid;
  private $amount;
  private $unique_payment = false;
  private $url;

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
   * Método responsável por definir o valor do $unique_payment
   * @param boolean $unique_payment
   */
  public function set_unique_payment($unique_payment){
    $this->unique_payment = $unique_payment;
    return $this;
  }

  /**
   * Método responsável por definir o valor do $url
   * @param string $url
   */
  public function set_url($url){
    $this->url = $url;
    return $this;
  }

  /**
   * Responsável por retornar o valor completo de um objeto
   * @param string $id
   * @param string $value
   * @return string $id.$size.$value  
   */
  private function get_value($id, $value){
    $size = str_pad(mb_strlen($value), 2, '0', STR_PAD_LEFT);
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
    $key = strlen($key) ? $this->get_value(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $this->pix_key) : '';

    //Descrição do pagamento
    $description = strlen($this->description) ? $this->get_value(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->description) : '';

    //Url do QrCode Dinâmico
    $url = strlen($this->url) ? $this->get_value(self::ID_MERCHANT_ACCOUNT_INFORMATION_URL, preg_replace('/^htpps?\:\/\//','',$this->url)) : '';

    // Retorno completo
    return $this->get_value(self::ID_MERCHANT_ACCOUNT_INFORMATION, $gui.$key.$description.$url);
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
   * Método responsável por retornar o valor do ID_POINT_INITIATION_METHOD
   * @return string
   */
  private function get_unique_payment(){
    return $this->unique_payment ? $this->get_value(self::ID_POINT_OF_INITIATION_METHOD, '12') : '';
  }

  /**
   * Método responsável por gerar o código completo do payload Pix
   * @return string
   */
  public function get_payload(){
    //Cria o payload
    $payload = $this->get_value(self::ID_PAYLOAD_FORMAT_INDICATOR, '01').
               $this->get_unique_payment().
               $this->get_merchant_account_info().
               $this->get_value(self::ID_MERCHANT_CATEGORY_CODE, '0000').
               $this->get_value(self::ID_TRANSACTION_CURRENCY, '986').
               //$this->get_value(self::ID_TRANSACTION_AMOUNT, $this->amount).
               $this->get_value(self::ID_COUNTRY_CODE, 'BR').
               $this->get_value(self::ID_MERCHANT_NAME, $this->merchant_name).
               $this->get_value(self::ID_MERCHANT_CITY, $this->merchant_city).
               $this->get_additional_data_field_template();

    return $payload.$this->get_CRC16($payload);
  }

   /**
   * Método definido pelo Bacen, responsável por calcular o valor da hash de validação do código pix
   * @return string
   */
  private function get_CRC16($payload) {
    //Adiciona dados gerais no payload
    $payload .= self::ID_CRC16.'04';

    //Dados definidos pelo bacen
    $polinomio = 0x1021;
    $resultado = 0xFFFF;

    //Checksum
    if (($length = strlen($payload)) > 0) {
        for ($offset = 0; $offset < $length; $offset++) {
            $resultado ^= (ord($payload[$offset]) << 8);
            for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                if (($resultado <<= 1) & 0x10000) $resultado ^= $polinomio;
                $resultado &= 0xFFFF;
            }
        }
    }

    //Retorna código CRC16 de 4 caracteres
    return self::ID_CRC16.'04'.strtoupper(dechex($resultado));
  }

}