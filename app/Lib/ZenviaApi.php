<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;

class ZenviaApi
{
    public function makeRequest($method, $endpoint, $params = [], $header = [])
    {
        $client = new Client();

        try {
            $response = $client->post(Configure::read('Zenvia.BaseUrl') . $endpoint, [
                'headers' => [
                    'X-API-TOKEN' => Configure::read('Zenvia.Token'),
                    'Content-Type' => 'application/json'
                ],
                'json' => $params
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $response = $e->getResponse(); 
            $error = json_decode($response->getBody()->getContents(), true);
        }

        return compact('error', 'contents');
    }

    public function sendSms($to, $message)
    {
        $arr = [
           "from" => "5510999999999", 
           "to" => $to, 
           "contents" => [
                 [
                    "type" => "text", 
                    "text" => $message 
                 ] 
              ] 
        ];

        return $this->makeRequest('POST', '/channels/sms/messages', $arr);
    }
}