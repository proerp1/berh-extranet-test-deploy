<?php

App::uses('Controller', 'Controller');

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class ApiBtgPactual extends Controller
{
    public $uses = ['Pedido', 'EconomicGroup', 'CnabItem'];

    private $baseUrl;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->baseUrl = Configure::read('Btg.BaseUrl');
        $this->baseUrlApi = Configure::read('Btg.BaseUrlApi');
        $this->clientId = Configure::read('Btg.ClientId');
        $this->clientSecret = Configure::read('Btg.ClientSecret');
    }

    public function authenticate()
    {
        $client = new Client();

        try {
            $response = $client->post($this->baseUrl.'/oauth2/token', [
                'form_params' => [
                    'code' => Configure::read('Btg.Code'),
                    'redirect_uri' => 'https://localhost.com',
                    'grant_type' => 'authorization_code'
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic '.base64_encode($this->clientId.':'.$this->clientSecret)
                ]
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);

            /*if (isset($contents['access_token'])) {
                CakeSession::write('ApiBtg.credentials', $contents);
            }*/

            return true;
        } catch (ClientException $e) {
            echo Psr7\Message::toString($e->getRequest());
            echo Psr7\Message::toString($e->getResponse());
        }
    }

    public function makeRequest($method, $endpoint, $params = [], $pdf = false)
    {
        $requestedUrl = $this->baseUrlApi.$endpoint;

        // $this->authenticate();

        $client = new Client();

        try {
            if ($pdf) {
                $accept = 'application/pdf';
            }

            $response = $client->request(
                $method,
                $requestedUrl,
                array_merge($params, [
                    'headers' => [
                        'accept' => $accept,
                        'Content-Type' => 'application/json',
                        'authorization' => 'Bearer '.Configure::read('Btg.AccessToken'),
                    ],
                ])
            );

            if ($pdf) {
                $contents = $response->getBody()->getContents();
            } else {
                $contents = json_decode($response->getBody()->getContents(), true);
            }

            return ['success' => true, 'contents' => $contents, 'params' => $params, 'requestedUrl' => $requestedUrl, 'code' => $response->getStatusCode()];
        } catch (ClientException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(), true);

            $message = $e->getMessage();
            if (isset($error['mensagem'])) {
                $message = $error['mensagem'];
            }

            if (isset($error['campos'])) {
                $message = Hash::extract($error['campos'], '{n}.mensagem');
            }

            return ['success' => false, 'code' => $e->getCode(), 'error' => $message, 'params' => $params, 'requestedUrl' => $requestedUrl, 'headers' => $e->getRequest()->getHeaders()];
        } catch (ServerException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(), true);

            return ['success' => false, 'code' => $e->getCode(), 'error' => $error, 'params' => $params, 'requestedUrl' => $requestedUrl, 'headers' => $e->getRequest()->getHeaders()];
        }
    }

    public function gerarBoleto($conta)
    {
        $valor = str_pad(str_replace('.', '', $conta['Income']['valor_total_nao_formatado']), 17, '0', STR_PAD_LEFT);
        $multa = str_pad(str_replace('.', '', $conta['BankTicket']['multa_boleto']), 12, '0', STR_PAD_LEFT);
        $juros = str_pad(str_replace('.', '', $conta['BankTicket']['juros_boleto_dia']), 12, '0', STR_PAD_LEFT);

        if (!empty($conta['Order']) && $conta['Order']['economic_group_id'] != null) {
            $econ = $this->EconomicGroup->find('first', ['conditions' => ['EconomicGroup.id' => $conta['Order']['economic_group_id']], 'recursive' => -1]);

            $name = $econ['EconomicGroup']['name'];
            $doc = str_replace(['.', '/', '-'], '', $econ['EconomicGroup']['document']);

            $payer = [
                'name' => $econ['EconomicGroup']['name'],
                'taxId' => str_replace(['.', '/', '-'], '', $econ['EconomicGroup']['document']),
                'address' => [
                    'street' => $econ['EconomicGroup']['endereco'],
                    'number' => $econ['EconomicGroup']['numero'],
                    'city' => $econ['EconomicGroup']['cidade'],
                    'state' => $econ['EconomicGroup']['estado'],
                    'zipCode' => str_replace('-', '', $econ['EconomicGroup']['cep']),
                ]
            ];
        } else {
            $payer = [
                'name' => $conta['Customer']['nome_primario'],
                'taxId' => str_replace(['.', '/', '-'], '', $conta['Customer']['documento']),
                'address' => [
                    'street' => $conta['Customer']['endereco'],
                    'number' => $conta['Customer']['numero'],
                    'city' => $conta['Customer']['cidade'],
                    'state' => $conta['Customer']['estado'],
                    'zipCode' => str_replace('-', '', $conta['Customer']['cep']),
                ]
            ];
        }

        $params = [
            'payer' => $payer,
            'referenceNumber' => $conta['Income']['id'],
            'amount' => 1,
            'dueDate' => $conta['Income']['vencimento_nao_formatado'],
            'installments' => 1
        ];

        return $this->makeRequest('POST', '/v1/bank-slips?accountId='.Configure::read('Btg.AccountId'), [
            'json' => $params,
        ]);
    }

    public function gerarPdf($incomeId)
    {
        $item = $this->CnabItem->find('first', [
            'conditions' => [
                'CnabItem.income_id' => $incomeId,
            ],
            'recursive' => -1
        ]);

        $params = [
            'query' => [
                'accountId' => Configure::read('Btg.AccountId'),
                'bankSlipId' => $item['CnabItem']['id_web'],
            ],
        ];

        return $this->makeRequest('GET', '/v1/bank-slips', $params, true);
    }
}
