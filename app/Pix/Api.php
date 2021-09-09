<?php 

namespace App\Pix;

class Api{

  private $base_url;
  private $client_id;
  private $client_secret;
  private $certificate;


  /**
   * Define os dados iniciais da classe
   * @param string $base_url
   * @param string $client_id
   * @param string $client_secret
   * @param string $certificate
   */
  public function __construct($base_url, $client_id, $client_secret, $certificate){
    $this->base_url      = $base_url;
    $this->client_id     = $client_id;
    $this->client_secret = $client_secret;
    $this->certificate   = $certificate;
  }

  /**
   * Método responsável por criar uma cobrança imediata
   * @param string $txid
   * @param array $request
   */
  public function create_cob($txid, $request){
    return $this->send('PUT', '/v2/cob'.$txid.$request);
  }

  /**
   * Método responsável por consultar uma cobrança imediata
   * @param string $txid
   * @param array $request
   */
  public function consult_cob($txid){
    return $this->send('GET', '/v2/cob'.$txid);
  }

  /**
   * Método responsável por obter o token de acesso às API's Pix
   */
  private function get_access_token(){
    //Endpoint Completo
    $endpoint = $this->base_url.'/oauth/token';

    //Headers
    $headers = [
      'Content-Type: application/json'
    ];

    //Corpo da Requisição
    $request = [
      'grant_type' => 'client_credentials'
    ];

    //Configuração do cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL            => $endpoint,
      CURLOPT_USERPWD        => $this->client_id . ':' . $this->client_secret,
      CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST  => 'POST',
      CURLOPT_POSTFIELDS     => json_encode($request),
      CURLOPT_SSLCERT        => $this->certificate,
      CURLOPT_SSLCERTPASSWD  => '',
      CURLOPT_HTTPHEADER     => $headers
    ]);

    //Executa o cURL
    $response = curl_exec($curl);
    curl_close($curl);

    $response_array = json_decode($response, true);

    //Retorna o Access Token
    return $response_array['access_token'] ?? '';

  }

  /**
   * Método responsável por enviar requisições para o PSP
   * @param string $method
   * @param string $resource
   * @param array $request
   */
  private function send($method, $resource, $request = []){
    //Endpoint completo
    $endpoint = $this->base_url.$resource;

    //Headers
    $headers = [
      'Cache-Controle: no-cache',
      'Content-type: application/json',
      'Authorization: Bearer '.$this->get_access_token()
    ];

    //Configuração do cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL            => $endpoint,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST  => $method,
      CURLOPT_SSLCERT        => $this->certificate,
      CURLOPT_SSLCERTPASSWD  => '',
      CURLOPT_HTTPHEADER     => $headers
    ]);

    switch ($method){
      case 'POST':
      case 'PUT':
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
        break;
    }

    //Executa o cURL
    $response = curl_exec($curl);
    curl_close($curl);


  }


}