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
                    'grant_type' => 'refresh_token',
                    'refresh_token' => Configure::read('Btg.RefreshToken')
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic '.base64_encode($this->clientId.':'.$this->clientSecret)
                ]
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);

            if (isset($contents['access_token'])) {
                CakeSession::write('ApiBtg.credentials', $contents);
            }

            return true;
        } catch (ClientException $e) {
            echo Psr7\Message::toString($e->getRequest());
            echo Psr7\Message::toString($e->getResponse());
        }
    }

    public function makeRequest($method, $endpoint, $params = [], $pdf = false)
    {
        $requestedUrl = $this->baseUrlApi.$endpoint;

        $this->authenticate();

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
                        'authorization' => 'Bearer '.CakeSession::read('ApiBtg.credentials.access_token'),
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
                'email' => $conta['Customer']['email'],
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
            'amount' => $conta['Income']['valor_total_nao_formatado'],
            'dueDate' => $conta['Income']['vencimento_nao_formatado'],
            'installments' => 1,
            'description' => $conta['BankTicket']['instrucao_boleto_1'].' '.$conta['BankTicket']['instrucao_boleto_2'].' '.$conta['BankTicket']['instrucao_boleto_3'].' '.$conta['BankTicket']['instrucao_boleto_4']
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

    public function alterarBoleto($bankSlipId, $conta)
    {
        $params = [
            'query' => [
                'accountId' => Configure::read('Btg.AccountId'),
            ],
            'json' => [
                'amount' => $conta['Income']['valor_total_nao_formatado'],
                'dueDate' => $conta['Income']['vencimento_nao_formatado'],
                "interests" => [
                    "arrears" => [
                        "type" => "PERCENTAGE",
                        "value" => 0
                    ],
                    "penalty" => [
                        "type" => "PERCENTAGE",
                        "value" => 0
                    ]
                ],
                "discounts" => [
                    [
                        "type" => "PERCENTAGE",
                        "value" => 0
                    ]
                ]
            ]
        ];

        return $this->makeRequest('PUT', '/v1/bank-slips/'.$bankSlipId, $params, true);
    }

    public function teste()
    {
        $client = new Client();
        $headers = [
          'Authorization' => 'Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6ImF0K2p3dCIsImtpZCI6InZfT2NvN21uRjBwbERCTU9FTUxlRjFhc01jR3hERURxVVhXdktHWUtWOFkifQ.eyJzdWIiOiIzNjY0NDc2NDg0MyIsImVtcHJlc2FzLmJ0Z3BhY3R1YWwuY29tL2FjY291bnRzIjoiNDg1MDM5ODQwMDAxNTAiLCJpc3MiOiJodHRwczovL2lkLmJ0Z3BhY3R1YWwuY29tIiwic2Vzc2lvbl9pZCI6ImRmYTE0Nzg1LTk1ZTUtNGJkMi04YjQ3LTlkZWRiZWUwODMwMSIsInNlc3Npb25JZCI6ImRmYTE0Nzg1LTk1ZTUtNGJkMi04YjQ3LTlkZWRiZWUwODMwMSIsImNsaWVudF9pZCI6IjM3ODVmY2IyLTdmNTAtNGY3YS04ZjdhLTVmNDUzZDRhMDc0MiIsImF1ZCI6Imh0dHBzOi8vYXBpLmVtcHJlc2FzLmJ0Z3BhY3R1YWwuY29tL2NvbXBhbmllcy80ODUwMzk4NDAwMDE1MCIsImdyYW50X3R5cGUiOiJhdXRob3JpemF0aW9uX2NvZGUiLCJzY29wZSI6ImVtcHJlc2FzLmJ0Z3BhY3R1YWwuY29tL2FjY291bnRzIG9wZW5pZCBlbXByZXNhcy5idGdwYWN0dWFsLmNvbS9iYW5rLXNsaXBzIiwiZXhwIjoxNzMyNzM0ODcyLCJlbXByZXNhcy5idGdwYWN0dWFsLmNvbS9iYW5rLXNsaXBzIjoiNDg1MDM5ODQwMDAxNTAiLCJpYXQiOjE3MzI2NDg0NzIsImp0aSI6ImJMa25ObFVMUnh6N0xBRjBDRUNHSnJkcWtKYVJOWlp5Zzh2dHhiNGNyMnMifQ.Qc_fGU0_TtAWMd-Vpseq7RB2y15c2M8rdxd5lpt7ZNRi3pOVB3ioaPfX-TXTSKd5t2K_zza-_k5Ojka-tFIjckMt18aOkKhG9SDcRpxEd1Sn2KTYBWkIg_DJaA1jtTi0yCqBDhErzSf3uHhYPNW0cGbKCkgyic3K4MYFNWk_Whe8bW8dBxKbK__4MsZBdUzZiuBTB7TM7LEEj1hnqPIJhRhvK_POhVcfcXlxTNJWyFq-4vCseHx24HuK9_PB5jqsWQHtuuoTYRG_UDLSInLf_ccuXby1pz4-amtU1vhI-fQWoXO0WMfD0-XMftPDwRMG7J2g6N5btb_Hj1kOh-09Qg',
        ];
        $request = new Request('GET', 'https://api.empresas.btgpactual.com/v1/accounts', $headers);
        $res = $client->sendAsync($request)->wait();
        debug($res);die(); 
    }
}
