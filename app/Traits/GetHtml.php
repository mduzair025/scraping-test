<?php

namespace App\Traits;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Sunra\PhpSimple\HtmlDomParser;


trait GetHtml

{

  public function set($url)
  {
    $client = new Client();

    //Get request
    $response = $client->request(
      'GET',
      $url
    );

    $response_status_code = $response->getStatusCode();
    $html = $response->getBody()->getContents();
    if($response_status_code==200){
      $dom = HtmlDomParser::str_get_html( $html );
      return $dom;
    }
    return 'error';
  }
}