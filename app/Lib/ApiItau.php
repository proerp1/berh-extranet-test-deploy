<?php

App::uses('Controller', 'Controller');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class ApiItau extends Controller
{
    public $uses = ['Pedido', 'EconomicGroup'];

    private $baseUrl;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->baseUrl = Configure::read('Itau.BaseUrl');
        $this->clientId = Configure::read('Itau.ClientId');
        $this->clientSecret = Configure::read('Itau.ClientSecret');
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function authenticate()
    {
        $client = new Client();

        try {
            $response = $client->post('https://sts.itau.com.br/api/oauth/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'cert' => Configure::read('Extranet.path').'app/Lib/chave_itau/berh.pem',
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);

            if (isset($contents['access_token'])) {
                CakeSession::write('ApiItau.token', $contents['access_token']);
            }

            return true;
        } catch (Exception $e) {
            return ['success' => false, 'code' => $e->getCode(), 'error' => $e->getMessage()];
        }
    }

    public function makeRequest($method, $endpoint, $params = [], $baseUrl = '')
    {
        $requestedUrl = $this->baseUrl.$endpoint;

        $this->authenticate();

        $client = new Client();

        try {
            $response = $client->request(
                $method,
                $requestedUrl,
                array_merge($params, [
                    'headers' => [
                        'x-itau-apikey' => $this->clientId,
                        'x-itau-correlationID' => 2,
                        'Authorization' => 'Bearer '.CakeSession::read('ApiItau.token'),
                    ],
                    'cert' => Configure::read('Extranet.path').'app/Lib/chave_itau/berh.pem',
                ])
            );

            $contents = json_decode($response->getBody()->getContents(), true);

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

            $pessoa = [
                'nome_pessoa' => substr($this->removeAccents($econ['EconomicGroup']['name']), 0, 50),
                'tipo_pessoa' => [
                    'codigo_tipo_pessoa' => 'J',
                    'numero_cadastro_nacional_pessoa_juridica' => str_replace(['.', '/', '-'], '', $econ['EconomicGroup']['document']),
                ],
            ];
            $endereco = [
                'nome_logradouro' => substr($this->removeAccents($econ['EconomicGroup']['endereco']), 0, 45),
                'nome_bairro' => substr($this->removeAccents($econ['EconomicGroup']['bairro']), 0, 15),
                'nome_cidade' => substr($this->removeAccents($econ['EconomicGroup']['cidade']), 0, 20),
                'sigla_UF' => $econ['EconomicGroup']['estado'],
                'numero_CEP' => str_replace('-', '', $econ['EconomicGroup']['cep']),
            ];
        } else {
            $nomeCampoDoc = $conta['Customer']['tipo_pessoa'] == 2 ? 'numero_cadastro_nacional_pessoa_juridica' : 'numero_cadastro_pessoa_fisica';
            $pessoa = [
                'nome_pessoa' => substr($this->removeAccents($conta['Customer']['nome_primario']), 0, 50),
                'tipo_pessoa' => [
                    'codigo_tipo_pessoa' => $conta['Customer']['tipo_pessoa'] == 2 ? 'J' : 'F',
                    $nomeCampoDoc => str_replace(['.', '/', '-'], '', $conta['Customer']['documento']),
                ],
            ];
            $endereco = [
                'nome_logradouro' => substr($this->removeAccents($conta['Customer']['endereco']), 0, 45),
                'nome_bairro' => substr($this->removeAccents($conta['Customer']['bairro']), 0, 15),
                'nome_cidade' => substr($this->removeAccents($conta['Customer']['cidade']), 0, 20),
                'sigla_UF' => $conta['Customer']['estado'],
                'numero_CEP' => str_replace('-', '', $conta['Customer']['cep']),
            ];
        }

        $multaArr = [
            'codigo_tipo_multa' => '03',
            'quantidade_dias_multa' => 0
        ];

        $jurosArr = [
            'codigo_tipo_juros' => '05',
            'quantidade_dias_juros' => 0
        ];

        if ($conta['Customer']['cobrar_juros'] == 'S') {
            $multaArr = [
                'valor_multa' => $multa,
                'codigo_tipo_multa' => '03',
                'quantidade_dias_multa' => 1
            ];

            $jurosArr = [
                'percentual_juros' => $juros,
                'codigo_tipo_juros' => '05',
                'quantidade_dias_juros' => 1
            ];
        }

        $params = [
            'data' => [
                'etapa_processo_boleto' => Configure::read('App.type') == 'dev' ? 'validacao' : 'efetivacao', // envia o tipo 'validacao' para testes
                'codigo_canal_operacao' => 'API',
                'beneficiario' => [
                    'id_beneficiario' => $conta['BankAccount']['id_beneficiario'],
                ],
                'dado_boleto' => [
                    'descricao_instrumento_cobranca' => 'boleto',
                    'tipo_boleto' => 'a vista',
                    'codigo_carteira' => $conta['BankTicket']['carteira'],
                    'valor_total_titulo' => $valor,
                    'codigo_especie' => '01',
                    'valor_abatimento' => '000',
                    'data_emissao' => date('Y-m-d'),
                    'forma_envio' => 'impressao',
                    'pagador' => [
                        'pessoa' => $pessoa,
                        'endereco' => $endereco,
                    ],
                    'dados_individuais_boleto' => [
                        [
                            'numero_nosso_numero' => str_pad($conta['Income']['id'], 8, '0', STR_PAD_LEFT),
                            'data_vencimento' => $conta['Income']['vencimento_nao_formatado'],
                            'valor_titulo' => $valor,
                            'texto_seu_numero' => str_pad($conta['Income']['id'], 8, '0', STR_PAD_LEFT),
                        ],
                    ],
                    'multa' => $multaArr,
                    'juros' => $jurosArr,
                    'recebimento_divergente' => [
                        'codigo_tipo_autorizacao' => '03',
                    ],
                    'instrucao_cobranca' => [
                        [
                            'codigo_instrucao_cobranca' => '8',
                            'quantidade_dias_apos_vencimento' => 5,
                            'dia_util' => false,
                        ],
                    ],
                    'desconto_expresso' => false,
                ],
            ],
        ];

        return $this->makeRequest('POST', '/boletos', [
            'json' => $params,
        ]);
    }

    public function buscarBoleto($conta)
    {
        $this->setBaseUrl(Configure::read('Itau.BaseUrlBusca'));

        return $this->makeRequest('GET', '/boletos', [
            'query' => [
                'id_beneficiario' => $conta['BankAccount']['id_beneficiario'],
                'codigo_carteira' => $conta['BankTicket']['carteira'],
                'nosso_numero' => str_pad($conta['Income']['id'], 8, '0', STR_PAD_LEFT),
                // 'view' => 'specific',
            ],
        ]);
    }

    public function alterarBoleto($id_boleto, $dados)
    {
        $response = $this->alterarVencimento($id_boleto, $dados['vencimento']);
        $responseValor = $this->alterarValor($id_boleto, $dados['valor']);
        $responseMulta = $this->alterarMulta($id_boleto, $dados['multa']);
        // $responseJuros = $this->alterarJuros($id_boleto, $dados['juros']);

        return compact('response', 'responseValor', 'responseMulta');
    }

    private function alterarVencimento($id_boleto, $vencimento)
    {
        return $this->makeRequest('PATCH', "/boletos/{$id_boleto}/data_vencimento", [
            'json' => [
                'data_vencimento' => $vencimento,
            ],
        ]);
    }

    private function alterarValor($id_boleto, $valor)
    {
        return $this->makeRequest('PATCH', "/boletos/{$id_boleto}/valor_nominal", [
            'json' => [
                'valor_titulo' => $valor,
            ],
        ]);
    }

    private function alterarMulta($id_boleto, $multa)
    {
        return $this->makeRequest('PATCH', "/boletos/{$id_boleto}/multa", [
            'json' => [
                'multa' => [
                    /*'codigo_tipo_multa' => '03',
                    'quantidade_dias_multa' => 1,
                    'valor_multa' => $multa,*/
                    'codigo_tipo_multa' => '03',
                    'quantidade_dias_multa' => 0,
                    //'valor_multa' => 0,
                ],
            ],
        ]);
    }

    private function alterarJuros($id_boleto, $juros)
    {
        return $this->makeRequest('PATCH', "/boletos/{$id_boleto}/juros", [
            'json' => [
                'juros' => [
                    /*'codigo_tipo_juros' => '05',
                    'quantidade_dias_juros' => 1,
                    'percentual_juros' => $juros,*/
                    'codigo_tipo_juros' => '05',
                    'quantidade_dias_juros' => 0,
                    //'percentual_juros' => 0,
                ],
            ],
        ]);
    }

    private function removeAccents($string)
    {
        return preg_replace(
            [
                '/\xc3[\x80-\x85]/',
                '/\xc3\x87/',
                '/\xc3[\x88-\x8b]/',
                '/\xc3[\x8c-\x8f]/',
                '/\xc3([\x92-\x96]|\x98)/',
                '/\xc3[\x99-\x9c]/',

                '/\xc3[\xa0-\xa5]/',
                '/\xc3\xa7/',
                '/\xc3[\xa8-\xab]/',
                '/\xc3[\xac-\xaf]/',
                '/\xc3([\xb2-\xb6]|\xb8)/',
                '/\xc3[\xb9-\xbc]/',
            ],
            str_split('ACEIOUaceiou', 1),
            $this->isUtf8($string) ? $string : utf8_encode($string)
        );
    }

    private function isUtf8($string)
    {
        return preg_match(
            '%^(?:
                 [\x09\x0A\x0D\x20-\x7E]
                | [\xC2-\xDF][\x80-\xBF]
                | \xE0[\xA0-\xBF][\x80-\xBF]
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
                | \xED[\x80-\x9F][\x80-\xBF]
                | \xF0[\x90-\xBF][\x80-\xBF]{2}
                | [\xF1-\xF3][\x80-\xBF]{3}
                | \xF4[\x80-\x8F][\x80-\xBF]{2}
                )*$%xs',
            $string
        );
    }
}
