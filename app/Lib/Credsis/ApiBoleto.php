<?php
App::uses('Controller', 'Controller');
App::uses('SoapBoleto', 'Lib/Credsis');
App::uses('PdfMerger', 'Lib');
App::uses('HtmltoPdf', 'Lib');

class ApiBoleto extends Controller
{
    public $uses = ['CnabItem', 'Billing', 'Pefin', 'BillingNovaVida', 'ClienteMeProteja', 'Negativacao'];

    /*
    private $token = '5e800d22830b5efce1c0ecfc92030351fd1346dc0824e36845f3f9f089e8becad614749b023cff0baeda1f5cd1ee343d913e0505ee25f056916873db8ef8d9f0';
    private $convenio = '10353';
    */
    private $url = 'http://www.credisiscobranca.com.br/v2/ws?wsdl';

    private function makeRequest($token, $convenio)
    {
        $client = new SoapBoleto($this->url);

        //Create Soap Header.
        //$headerVar = new SoapVar('<ns1:Chave><token>'.$this->token.'</token><convenio>'.$this->convenio.'</convenio></ns1:Chave>',XSD_ANYXML);
        $headerVar = new SoapVar('<ns1:Chave><token>'.$token.'</token><convenio>'.$convenio.'</convenio></ns1:Chave>',XSD_ANYXML);
        $header = new SoapHeader('http://tempuri.org/','RequestParams',$headerVar);

        $client->__setSoapHeaders($header); 

        return $client;
    }

    private function makeResponse($response, $request)
    {
        $responseArr = json_decode(json_encode($response), true);

        if (isset($responseArr['erros'])) {
            return [
                'success' => false,
                'error' => $response,
                'request' => $request
            ];
        } else {
            return [
                'success' => true,
                'obj' => $responseArr,
                'request' => $request
            ];
        }
    }

    public function getAvalista()
    {
        return [
            'nome' => 'credcheck',
            'nomeFantasia' => 'credcheck',
            'cpfCnpj' => '44122222222',
            'identidade' => 'teste',
            'dataNascimento' => '1994-09-02',
            'endereco' => [
                'endereco' => 'rua teste',
                'bairro' => 'tete',
                'complemento' => '',
                'cep' => '02222020',
                'cidade' => 'sao paulo',
                'uf' => 'sp',
                'numero' => '12',
            ],
            'contatos' => [
                [
                    'contato' => '',
                    'tipoContato' => '',
                ]
            ]
        ];
    }

    public function gerarBoleto($boleto)
    {
        $client = $this->makeRequest($boleto['BankTickets']['token'], $boleto['BankTickets']['codigo_cedente']);

        $dataEmissao = date('Y-m-d');
        if (strtotime($boleto['Income']['vencimento_nao_formatado']) < strtotime($dataEmissao)) {
            $dataEmissao = $boleto['Income']['vencimento_nao_formatado'];
        }

        $params = [
            'pagador' => [
                'nome' => $boleto['Customer']['nome_primario'],
                'nomeFantasia' => $boleto['Customer']['nome_secundario'],
                'cpfCnpj' => $boleto['Customer']['documento'],
                // 'identidade' => '',
                // 'dataNascimento' => '',
                'endereco' => [
                    'endereco' => $boleto['Customer']['endereco'],
                    'bairro' => $boleto['Customer']['bairro'],
                    'complemento' => $boleto['Customer']['complemento'],
                    'cep' => str_replace(['-'], '', $boleto['Customer']['cep']),
                    'cidade' => $boleto['Customer']['cidade'],
                    'uf' => $boleto['Customer']['estado'],
                    'numero' => $boleto['Customer']['numero'],
                ],
                'contatos' => [
                    [
                        'contato' => $boleto['Customer']['telefone1'],
                        'tipoContato' => 2,
                    ]
                ]
            ],
            'documento' => $boleto['Customer']['documento'],
            'dataEmissao' => $dataEmissao,
            'dataVencimento' => $boleto['Income']['vencimento_nao_formatado'],
            'dataLimitePagamento' => date('Y-m-d', strtotime('+90 days', strtotime($boleto['Income']['vencimento_nao_formatado']))),
            'valor' => $boleto['Income']['valor_total_nao_formatado'],
            // 'valor' => 1.00,
            'quantidadeParcelas' => 1,
            'intervaloParcela' => 0,
            'codigoEspecie' => '03',
            'protesto' => [
                'dias' => '',
                'tipo' => '1'
            ],
            'tipoEnvio' => 'NENHUM',
            'instrucao' => $boleto['BankTickets']['instrucao_boleto_1'],
            'multa' => [
                'valor' => $boleto['BankTickets']['multa_boleto'],
                'carencia' => [
                    'dias' => 0,
                    'tipo' => 2
                ],
                'tipo' => 2
            ],
            'juros' => [
                'valor' => $boleto['BankTickets']['juros_boleto_dia'],
                'carencia' => [
                    'dias' => 0,
                    'tipo' => 2
                ],
                'tipo' => 2
            ],
        ];
        

        try {
            $result = $client->gerarBoleto($params);

            $request = $client->__getLastRequest();

            return $this->makeResponse($result, $request);
        } catch (Exception $e) {
            return ['success' => false, 'code' => $e->getCode(), 'error' => $e->getMessage()];
        }
    }

    public function alterarBoleto($idWeb, $boleto)
    {
        $client = $this->makeRequest($boleto['BankTickets']['token'], $boleto['BankTickets']['codigo_cedente']);
        
        $params = [
            'idWeb' => $idWeb,
            'valor' => $boleto['Income']['valor_total_nao_formatado'],
            'protesto' => [
                'dias' => '',
                'tipo' => '1'
            ],
            'dataVencimento' => $boleto['Income']['vencimento_nao_formatado'],
            'multa' => [
                'valor' => $boleto['BankTickets']['multa_boleto'],
                'carencia' => [
                    'dias' => 0,
                    'tipo' => 2
                ],
                'tipo' => 2
            ],
            'juros' => [
                'valor' => $boleto['BankTickets']['juros_boleto_dia'],
                'carencia' => [
                    'dias' => 0,
                    'tipo' => 2
                ],
                'tipo' => 2
            ],
            'documento' => $boleto['Customer']['documento'],
            'dataLimitePagamento' => date('Y-m-d', strtotime('+90 days', strtotime($boleto['Income']['vencimento_nao_formatado']))),
        ];
        try {
            $result = $client->alterarBoleto($params);

            $request = $client->__getLastRequest();

            return $this->makeResponse($result, $request);
        } catch (Exception $e) {
            return ['success' => false, 'code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        
    }

    public function buscarBoleto($idWeb, $formato = 'pdf')
    {

        $banco_token = $this->CnabItem->find('first', [
                'fields' => ['BankTickets.*'],
                'conditions' => [
                    'CnabItem.id_web' => $idWeb,
                ],
                'joins' => [
                    [
                        'table' => 'incomes',
                        'alias' => 'Income',
                        'type' => 'left',
                        'conditions' => ['Income.id = CnabItem.income_id'],
                    ],
                    [
                        'table' => 'bank_accounts',
                        'alias' => 'BankAccount',
                        'type' => 'inner',
                        'conditions' => [
                            'BankAccount.id = Income.bank_account_id', 'BankAccount.data_cancel' => '1901-01-01'
                        ]
                    ],
                    [
                        'table' => 'bank_tickets',
                        'alias' => 'BankTickets',
                        'type' => 'inner',
                        'conditions' => [
                            'BankAccount.id = BankTickets.bank_account_id', 'BankTickets.data_cancel' => '1901-01-01'
                        ]
                    ]
                ],
                'recursive' => -1
            ]);

        $client = $this->makeRequest($banco_token['BankTickets']['token'], $banco_token['BankTickets']['codigo_cedente']);

        $params = [
            'idWeb' => $idWeb,
            'layout' => 'default',
            'formato' => $formato,
        ];

        try {
            $result = $client->buscarBoleto($params);

            $request = $client->__getLastRequest();

            return $this->makeResponse($result, $request);
        } catch (Exception $e) {
            return ['success' => false, 'code' => $e->getCode(), 'error' => $e->getMessage()];
        }
    }

    public function buscarBoletoLogo($idWeb, $formato = 'pdf')
    {
        
        $banco_token = $this->CnabItem->find('first', [
                'fields' => ['BankTickets.*'],
                'conditions' => [
                    'CnabItem.id_web' => $idWeb,
                ],
                'joins' => [
                    [
                        'table' => 'incomes',
                        'alias' => 'Income',
                        'type' => 'left',
                        'conditions' => ['Income.id = CnabItem.income_id'],
                    ],
                    [
                        'table' => 'bank_accounts',
                        'alias' => 'BankAccount',
                        'type' => 'inner',
                        'conditions' => [
                            'BankAccount.id = Income.bank_account_id', 'BankAccount.data_cancel' => '1901-01-01'
                        ]
                    ],
                    [
                        'table' => 'bank_tickets',
                        'alias' => 'BankTickets',
                        'type' => 'inner',
                        'conditions' => [
                            'BankAccount.id = BankTickets.bank_account_id', 'BankTickets.data_cancel' => '1901-01-01'
                        ]
                    ]
                ],
                'recursive' => -1
            ]);

        $client = $this->makeRequest($banco_token['BankTickets']['token'], $banco_token['BankTickets']['codigo_cedente']);


        $params = [
            'idWeb' => $idWeb,
            'layout' => 'default',
            'formato' => $formato,
        ];

        try {
            $result = $client->buscarBoleto($params);

            $request = $client->__getLastRequest();

            $boleto = $this->makeResponse($result, $request);
            $bin = base64_decode($boleto['obj']['boleto'], true);

            $faturamento_cliente = $this->CnabItem->find('first', [
                'fields' => ['BillingMonthlyPayment.*', 'PefinMaintenance.*', 'Customer.*'],
                'conditions' => [
                    'CnabItem.id_web' => $idWeb,
                ],
                'joins' => [
                    [
                        'table' => 'incomes',
                        'alias' => 'Income',
                        'type' => 'left',
                        'conditions' => ['Income.id = CnabItem.income_id'],
                    ],
                    [
                        'table' => 'billing_monthly_payments',
                        'alias' => 'BillingMonthlyPayment',
                        'type' => 'left',
                        'conditions' => ['Income.billing_monthly_payment_id = BillingMonthlyPayment.id'],
                    ],
                    [
                        'table' => 'pefin_maintenances',
                        'alias' => 'PefinMaintenance',
                        'type' => 'left',
                        'conditions' => ['PefinMaintenance.id = BillingMonthlyPayment.pefin_maintenance_id'],
                    ],
                    [
                        'table' => 'customers',
                        'alias' => 'Customer',
                        'type' => 'left',
                        'conditions' => ['Customer.id = BillingMonthlyPayment.customer_id'],
                    ]
                ],
                'recursive' => -1
            ]);

            $id = $faturamento_cliente['BillingMonthlyPayment']['billing_id'];
            $customer_id = $faturamento_cliente['BillingMonthlyPayment']['customer_id'];

            $this->Billing->id = $id;
            $faturamento = $this->Billing->read();
            $negativacao = $this->Negativacao->find_negativacao_cliente($id, $customer_id);
            $pefin = $this->Pefin->find_pefin_cliente($id, $customer_id);
            $berh = $this->BillingNovaVida->find('all', ['conditions' => ['BillingNovaVida.billing_id' => $id, 'BillingNovaVida.customer_id' => $customer_id]]);
            $meproteja = $this->ClienteMeProteja->find('all', ['conditions' => ['ClienteMeProteja.billingID' => $id, 'ClienteMeProteja.clienteID' => $customer_id]]);

            $tipo = $negativacao ? $negativacao[0]['n']['type'] : 1;

            $view = new View($this, false);
            $view->layout=false;

            $view->set(compact('faturamento_cliente', 'negativacao', 'pefin', 'berh', 'meproteja', 'tipo', 'faturamento'));
            $html=$view->render('../Elements/boleto_demonstrativo');

            $HtmltoPdf = new HtmltoPdf();
            $string = $HtmltoPdf->convert($html, 'teste', 'string');

            $files = [$string, $bin];

            $PdfMerger = new PdfMerger();
            $content = $PdfMerger->merge($files);

            return $content;
        } catch (Exception $e) {
            return ['success' => false, 'code' => $e->getCode(), 'error' => $e->getMessage()];
        }
    }
}
