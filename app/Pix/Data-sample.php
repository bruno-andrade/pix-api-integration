<?php

namespace App\Pix;

class Data {

  private $data;

  public function get_data(){
    $this->data = [
      'client_id'     => '',
      'client_secret' => '',
      'route'         => '.hm.bb.com.br/',
      'merchant_name' => 'SEU NOME',
      'merchant_city' => 'SUA CIDADE',
      'txid'          => 'MODULOPIX2553'.strtoupper(uniqid())
    ];
    return $this->data;
  }

}