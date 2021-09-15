<?php

namespace App\Pix;

class Data {

  private $data;
  private $header; 
  private $date;
  private $txid_prefix;

  /**
   * Método responsável por retornar os dados necessário para iniciar um PIX
   * @return String
   */
  public function get_data($txid){    
    $this->data = [
      'client_id'     => '',
      'client_secret' => '',
      'route'         => '.hm.bb.com.br/',
      'merchant_name' => 'BRUNO ANDRADE',
      'merchant_city' => 'MACEIÓ',
      'txid'          =>  $txid,      
    ];
    return $this->data;
  }
  /**
   * Método responsável por armazenar o header da requisição 
   * @return Array
   */
  public function get_header(){
    $this->header = [
      'Authorization: Basic ',
      'Content-Type: application/x-www-form-urlencoded',
      'Cookie: JSESSIONID=jR3PwiI0235yYToQ-STY74AuSCJC5aTPHFLOd8Uj-hTtL_Yt1Exv!557095713',
    ];   
    return $this->header; 
  }

  /**
   * Método responsável por criar o TXID atrelado ao código do pedido
   * @return String
   */
  public function get_txid_prefix($cod_pedido){
    $this->date = getdate();
    $this->date['mday'] < 10 ? $this->date['mday'] = '0'.$this->date['mday'] : $this->date['mday']; 
    $this->date['mon']  < 10 ? $this->date['mon']  = '0'.$this->date['mon']  : $this->date['mon']; 
    $this->txid_prefix = 'BPIX'.$this->date['year'].$this->date['mon'].$this->date['mday'];

    //preenche tudo com '0' 
    $this->txid_prefix = str_pad($this->txid_prefix, 26, 0, STR_PAD_RIGHT);
    //define o tamanho do que precisa ser removido do final da string
    $length = strlen($this->txid_prefix) - strlen($cod_pedido);
    //corta o final da string
    $this->txid_prefix = substr($this->txid_prefix, 0, $length);
    //adiciona o código do pedido
    $this->txid_prefix = $this->txid_prefix.$cod_pedido;

    return $this->txid_prefix;
  }

}