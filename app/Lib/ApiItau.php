<?php

App::uses('Controller', 'Controller');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;

class ApiItau extends Controller
{
    public $uses = ['Pedido'];

    private $baseUrl;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->baseUrl = Configure::read('Itau.BaseUrl');
        $this->clientId = Configure::read('Itau.ClientId');
        $this->clientSecret = Configure::read('Itau.ClientSecret');
    }

    public function authenticate()
    {
        $client = new Client();

        try {
            $response = $client->post('https://devportal.itau.com.br/api/jwt', [
                'json' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);

            if (isset($contents['access_token'])) {
                Configure::write('ApiItau.token', $contents['access_token']);
            }

            return true;
        } catch (Exception $e) {
            return ['success' => false, 'code' => $e->getCode(), 'error' => $e->getMessage(), 'params' => $params, 'requestedUrl' => $requestedUrl];
        }
    }

    public function makeRequest($method, $endpoint, $params = [])
    {
        $requestedUrl = $this->baseUrl.$endpoint;

        $client = new Client();

        try {
            $response = $client->request($method, $requestedUrl, 
                array_merge($params, [
                    'headers' => [
                        'x-sandbox-token' => Configure::read('ApiItau.token'),
                    ]
                ])
            );

            $contents = json_decode($response->getBody()->getContents(), true);

            return ['success' => true, 'contents' => $contents, 'params' => $params, 'requestedUrl' => $requestedUrl, 'code' => $response->getStatusCode()];
        } catch (ClientException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents(), true);

            if (
                ($e->getCode() == 400 && isset($error['message']) && $error['message'] == 'Missing required request parameters: [x-sandbox-token]') 
                || $e->getCode() == 401
            ) {
                $this->authenticate();
                return $this->makeRequest($method, $endpoint, $params);
            }

            $message = $e->getMessage();
            if (isset($error['arquivos'])) {
                $message = $error['arquivos'];
            }

            return ['success' => false, 'code' => $e->getCode(), 'error' => $message, 'params' => $params, 'requestedUrl' => $requestedUrl];
        }
    }

    public function gerarBoleto($conta)
    {
        $params = [
            'etapa_processo_boleto' => 'efetivacao',
            'codigo_canal_operacao' => 'BKL',
            'beneficiario' => [
                'id_beneficiario' => $conta['BankAccount']['agency'].str_replace('-', '', $conta['BankAccount']['account_number']),
            ],
            'dado_boleto' => [
                'descricao_instrumento_cobranca' => 'boleto',
                'tipo_boleto' => 'a vista',
                'codigo_carteira' => $conta['BankTickets']['carteira'],
                'valor_total_titulo' => $conta['Income']['valor_total_nao_formatado'],
                'codigo_especie' => '01',
                'forma_envio' => 'impressão',
                'pagador' => [
                    'pessoa' => [
                        'nome_pessoa' => $conta['Customer']['nome_primario'],
                        'tipo_pessoa' => [
                            'codigo_tipo_pessoa' => $conta['Customer']['tipo_pessoa'] == 2 ? 'J' : 'F',
                            'numero_cadastro_nacional_pessoa_juridica' => $conta['Customer']['documento'],
                        ],
                    ],
                    'endereco' => [
                        'nome_logradouro' => $conta['Customer']['endereco'],
                        'nome_bairro' => $conta['Customer']['bairro'],
                        'nome_cidade' => $conta['Customer']['cidade'],
                        'sigla_UF' => $conta['Customer']['estado'],
                        'numero_CEP' => str_replace('-', '', $conta['Customer']['cep']),
                    ]
                ],
                'dados_individuais_boleto' => [
                    [
                        'data_vencimento' => $conta['Income']['vencimento_nao_formatado'],
                        'valor_titulo' => $conta['Income']['valor_total_nao_formatado'],
                        'texto_seu_numero' => $conta['Income']['id'],
                    ],
                ],
                'desconto_expresso' => false,
                'codigo_tipo_vencimento' => 1,
                'descricao_especie' => 'BDP Boleto proposta',
                'codigo_aceite' => 'S',
                'data_emissao' => '2000-01-01',
                'pagamento_parcial' => true,
                'quantidade_maximo_parcial' => 2,
                'valor_abatimento' => '100.00',
                'juros' => [
                    'codigo_tipo_juros' => '91',
                    'quantidade_dias_juros' => 1,
                    'percentual_juros' => $conta['BankTickets']['juros_boleto_dia'],
                ],
                'multa' => [
                    'codigo_tipo_multa' => '01',
                    'quantidade_dias_multa' => 1,
                    'valor_multa' => $conta['BankTickets']['multa_boleto'],
                ],
                'desconto' => [
                    'codigo_tipo_desconto' => '00'
                ],
                /*'mensagens_cobranca' => [
                    [
                        'mensagem' => 'abc',
                    ],
                ],*/
                'recebimento_divergente' => [
                    'codigo_tipo_autorizacao' => '03'
                ],
            ]
        ];

        return $this->makeRequest('POST', '/boletos', [
            'json' => $params
        ]);
    }

    public function buscarBoleto($conta, $nosso_numero)
    {
        return $this->makeRequest('GET', '/boletos', [
            'query' => [
                'id_beneficiario' => $conta['BankAccount']['agency'].str_replace('-', '', $conta['BankAccount']['account_number']),
                // 'codigo_carteira' => '12',
                'nosso_numero' => $nosso_numero,
                'view' => 'specific',
            ]
        ]);
    }

    public function alterarBoleto($id_boleto, $dados)
    {
        $response = $this->alterarVencimento($id_boleto, $dados['vencimento']);
        $responseValor = $this->alterarValor($id_boleto, $dados['valor']);
        $responseMulta = $this->alterarMulta($id_boleto, $dados['multa']);
        // $responseJuros = $this->alterarJuros($id_boleto, $dados['juros']);

        return compact('response', 'responseValor', 'responseMulta', 'responseJuros');
    }

    private function alterarVencimento($id_boleto, $vencimento)
    {
        return $this->makeRequest('PATCH', "/boletos/{$id_boleto}/data_vencimento", [
            'json' => [
                'data_vencimento' => $vencimento,
            ]
        ]);
    }

    private function alterarValor($id_boleto, $valor)
    {
        return $this->makeRequest('PATCH', "/boletos/{$id_boleto}/valor_nominal", [
            'json' => [
                'valor_titulo' => $valor,
            ]
        ]);
    }

    private function alterarMulta($id_boleto, $multa)
    {
        return $this->makeRequest('PATCH', "/boletos/{$id_boleto}/multa", [
            'json' => [
                'multa' => [
                    'codigo_tipo_multa' => '01',
                    'quantidade_dias_multa' => 1,
                    'valor_multa' => $multa,
                ],
            ]
        ]);
    }

    private function alterarJuros($id_boleto, $juros)
    {
        return $this->makeRequest('PATCH', "/boletos/{$id_boleto}/juros", [
            'json' => [
                'juros' => [
                    'codigo_tipo_juros' => '90',
                    'quantidade_dias_juros' => 1,
                    'percentual_juros' => $juros,
                ],
            ]
        ]);
    }
}
