<?php 

namespace App\Pix;

class Api{

  private $base_url;
  private $client_id;
  private $client_secret;
  private $gw_dev_app_key;


  /**
   * Define os dados iniciais da classe
   * @param string $base_url
   * @param string $client_id
   * @param string $client_secret
   * @param string $certificate
   */
  public function __construct($base_url, $client_id, $client_secret, $gw_dev_app_key = 'd27b677901ffabb0136fe17dd0050c56b941a5bc'){
    $this->base_url       = $base_url;
    $this->client_id      = $client_id;
    $this->client_secret  = $client_secret;
    $this->gw_dev_app_key = $gw_dev_app_key;
  }

  /**
   * Método responsável por criar uma cobrança imediata
   * @param string $txid
   * @param array $request
   */
  public function create_cob($txid, $request){
    return $this->send('PUT', 'pix/v1/cob/'.$txid.'?gw-dev-app-key='.$this->gw_dev_app_key, $request);
  }

  /**
   * Método responsável por consultar uma cobrança imediata
   * @param string $txid
   * @param array $request
   */
  public function consult_cob($txid){
    return $this->send('GET', 'pix/v1/cob/'.$txid.'?gw-dev-app-key='.$this->gw_dev_app_key);
  }

  /**
   * Método responsável por obter o token de acesso às API's Pix
   */
  private function get_access_token(){
    
    //Endpoint Completo
    $endpoint       = 'https://oauth'.$this->base_url.'/oauth/token';
    $gw_key         = $this->gw_dev_app_key;
    $final_endpoint = $endpoint.'?gw-dev-app-key='.$gw_key;  
    

    //Headers
    $headers = array(
      'Authorization: Basic ZXlKcFpDSTZJbVk0TXprNU1tVXROeUlzSW1OdlpHbG5iMUIxWW14cFkyRmtiM0lpT2pBc0ltTnZaR2xuYjFOdlpuUjNZWEpsSWpveU1Ua3lPQ3dpYzJWeGRXVnVZMmxoYkVsdWMzUmhiR0ZqWVc4aU9qRjk6ZXlKcFpDSTZJakpoSWl3aVkyOWthV2R2VUhWaWJHbGpZV1J2Y2lJNk1Dd2lZMjlrYVdkdlUyOW1kSGRoY21VaU9qSXhPVEk0TENKelpYRjFaVzVqYVdGc1NXNXpkR0ZzWVdOaGJ5STZNU3dpYzJWeGRXVnVZMmxoYkVOeVpXUmxibU5wWVd3aU9qRXNJbUZ0WW1sbGJuUmxJam9pYUc5dGIyeHZaMkZqWVc4aUxDSnBZWFFpT2pFMk16QTVOVFl4TmpnNE16bDk=',
      'Content-Type: application/x-www-form-urlencoded',
      'Cookie: JSESSIONID=jR3PwiI0235yYToQ-STY74AuSCJC5aTPHFLOd8Uj-hTtL_Yt1Exv!557095713'
    );

    //Corpo da Requisição
    $request = 'grant_type=client_credentials&scope=cob.read%20cob.write%20pix.read%20pix.write';    

    //Configuração do cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL            => $final_endpoint,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING       => '',
      CURLOPT_MAXREDIRS      => 10,
      CURLOPT_TIMEOUT        => 0,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST  => 'POST',
      CURLOPT_POSTFIELDS     => $request,
      CURLOPT_HTTPHEADER     => $headers
    ]);
    

    //Executa o cURL
    $response = curl_exec($curl);
    curl_close($curl);

    $response_array = json_decode($response, true);

    echo "<pre>";
    if ($response_array) {
      echo "<h3>TOKEN GERADO</h3>";
      print_r($response_array);
    }else{
      echo "PROBLEMAS AO GERAR TOKEN";
      print_r($response_array);
    }
    echo "</pre>";
    

    //Retorna o Access Token
    return $response_array['access_token']; 

  }

  /**
   * Método responsável por enviar requisições para o PSP
   * @param string $method
   * @param string $resource
   * @param array $request
   */


 
  private function send($method, $resource, $request = []){    

    //Endpoint completo
    $endpoint = 'https://api'.$this->base_url.$resource;

    //Headers
    $headers = [
      'Authorization: Bearer '.$this->get_access_token(),
      'Content-Type: application/json'
    ];   

    //Configuração do cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL             => $endpoint,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_ENCODING        => '',
      CURLOPT_MAXREDIRS       => 10,
      CURLOPT_TIMEOUT         => 0,
      CURLOPT_SSL_VERIFYPEER  => false,
      CURLOPT_FOLLOWLOCATION  => true,
      CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST   => $method,
      CURLOPT_HTTPHEADER      => $headers
    ]);

    switch ($method){
      case 'POST':
      case 'PUT':
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
        break;
    }

    //Executa o cURL
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($response) {
      return json_decode($response, true);
    }else{
      return $err;
    }



  }


}