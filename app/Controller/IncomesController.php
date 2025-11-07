<?php

use GuzzleHttp\Client;
use CloudDfe\SdkPHP\Nfse;
use CloudDfe\SdkPHP\Webhook;

App::uses('BoletoItau', 'Lib');
App::uses('ApiItau', 'Lib');
App::uses('ApiBtgPactual', 'Lib');
class IncomesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Boleto', 'HtmltoPdf', 'ExcelGenerator', 'GerarCaixaNossoNumero', 'Email'];
    public $uses = ['Income', 'Status', 'Revenue', 'BankAccount', 'CostCenter', 'Customer', 'Instituicao', 'TmpRetornoCnab', 'ChargesHistory', 'Socios', 'Log', 'Resale', 'CnabItem', 'Order', 'OrderBalance', 'IncomeNfse', 'OrderDocument'];

    public $paginate = [
        'Income' => [
            'limit' => 200,
            'order' => [
                'Income.vencimento' => 'desc',
            ],
            'group' => 'Income.id',
            'paramType' => 'querystring'
        ],
        'ChargesHistory' => [
            'limit' => 10,
            'order' => [
                'ChargesHistory.created' => 'desc',
            ],
        ],
    ];
    

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allow(['gerar_boleto', 'atualiza_status_nfse']);
    }

    public function index()
    {
        $this->Permission->check(23, "leitura") ? "" : $this->redirect("/not_allowed");

        $limit = !empty($this->request->query('limit')) ? (int)$this->request->query('limit') : 50;

        $this->paginate['Income']['limit'] = $limit;
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['Customer.cod_franquia' => CakeSession::read("Auth.User.resales")], "or" => []];

        $total_income = 0;
        
        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Income.name LIKE' => "%".$_GET['q']."%", 'Income.doc_num' => $_GET['q'], 'BankAccount.name LIKE' => "%".$_GET['q']."%", 'Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado' => $_GET['q']]);
        }

        if (!empty($_GET['c'])) {
            if (is_array($_GET['c'])) {
                $condition['Income.customer_id'] = $_GET['c'];
            }
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }        

        if (isset($_GET["sc"]) and $_GET["sc"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET['sc']]);
        }

        if (!empty($_GET["f"])) {
            $condition['and'] = array_merge($condition['and'], ['Customer.cod_franquia' => $_GET['f']]);
        }

        if (!empty($_GET["payment_method"])) {
            $condition['and']['Income.payment_method'] = $_GET["payment_method"];
        }

        if (isset($_GET["atraso"]) and $_GET["atraso"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id IN (15,16) ']);
            $condition['and'] = array_merge($condition['and'], ['Income.vencimento <' => date("Y-m-d")]);
        }

        if (isset($_GET["nfse"]) && $_GET["nfse"] != '') {
            $comparator = $_GET["nfse"] == 'S' ? 'in' : 'not in';
            $condition['and'] = array_merge($condition['and'], ["Income.id $comparator (select distinct income_id from income_nfse)"]);
        }

        if (!empty($_GET['nfse_antecipada']) && $_GET['nfse_antecipada'] != '') {
            $comparator = $_GET['nfse_antecipada'] == 'S' ? '=' : '!=';
            $condition['and'] = array_merge($condition['and'], ["Customer.emitir_nota_fiscal $comparator 'A'"]);
        }

        if (!empty($_GET['cond_pag'])) {
            $condition['and'] = array_merge($condition['and'], ['Order.condicao_pagamento' => $_GET['cond_pag']]);
        }

        $get_de = isset($_GET["de"]) ? $_GET["de"] : '';
        $get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';
        
        if ($get_de != "" and $get_ate != "") {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['de'])));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['ate'])));

            if (isset($_GET["t"]) and $_GET["t"] == 17) {
                $condition['and'] = array_merge($condition['and'], ['Income.data_pagamento >=' => $de, 'Income.data_pagamento <=' => $ate]);
            } else {
                $condition['and'] = array_merge($condition['and'], ['Income.vencimento >=' => $de, 'Income.vencimento <=' => $ate]);
            }
        }

        $get_comp_de = isset($_GET["comp_de"]) ? $_GET["comp_de"] : '';
        $get_comp_ate = isset($_GET["comp_ate"]) ? $_GET["comp_ate"] : '';
        
        if ($get_comp_de != "" and $get_comp_ate != "") {
            $comp_de = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['comp_de'])));
            $comp_ate = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['comp_ate'])));

            $condition['and'] = array_merge($condition['and'], ['Income.data_competencia >=' => $comp_de, 'Income.data_competencia <=' => $comp_ate]);
        }

        $get_created_de = isset($_GET["created_de"]) ? $_GET["created_de"] : '';
        $get_created_ate = isset($_GET["created_ate"]) ? $_GET["created_ate"] : '';
        
        if ($get_created_de != "" && $get_created_ate != "") {
            $created_de = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $_GET['created_de'])));
            $created_ate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $_GET['created_ate'])));
        
            $condition['and'] = array_merge($condition['and'], ['Income.created >=' => $created_de, 'Income.created <=' => $created_ate]);
        }

        $get_pagamento_de = isset($_GET["pagamento_de"]) ? $_GET["pagamento_de"] : '';
        $get_pagamento_ate = isset($_GET["pagamento_ate"]) ? $_GET["pagamento_ate"] : '';
        
        if ($get_pagamento_de != "" && $get_pagamento_ate != "") {
            $pagamento_de = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $_GET['pagamento_de'])));
            $pagamento_ate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $_GET['pagamento_ate'])));
        
            // Alterar a condição para filtrar pela coluna 'data_pagamento'
            $condition['and'] = array_merge($condition['and'], [
                'Income.data_pagamento >=' => $pagamento_de,
                'Income.data_pagamento <=' => $pagamento_ate
            ]);
        }
        
        if ($this->request->is('get')) {
            if (isset($_GET['exportar'])) {
                $data = $this->Income->find('all', ['conditions' => $condition, 'order' => ['Income.vencimento' => 'desc'], 'group' => 'Income.id']);

                $nome = 'contas_receber_' . date('d_m_Y_H_i_s') . '.xlsx';
                
                $this->ExcelGenerator->gerarExcelContasReceber($nome, $data);
                $this->redirect("/files/excel/".$nome);
            } else {
                $this->Income->recursive = -1;
                $this->Income->unbindModel(['belongsTo' => ['Customer', 'BankAccount', 'Status']], false);

                $joins = [
                    'fields' => [
                        'Income.*', 
                        'Customer.*', 
                        'BankAccount.*', 
                        'Status.*', 
                        'Order.*',
                        'UserUpdated.name',
                        "(CASE WHEN Order.condicao_pagamento = 1 THEN 'Pré pago' WHEN Order.condicao_pagamento = 2 THEN 'Faturado' ELSE '' END) AS desc_condicao_pagamento",
                        '(SELECT GROUP_CONCAT(nfse.tipo) 
                        FROM income_nfse nfse 
                        WHERE nfse.income_id = Income.id 
                        GROUP BY nfse.income_id) AS nfses'
                    ],
                    'joins' => [
                        ['table' => 'customers',
                            'alias' => 'Customer',
                            'type' => 'INNER',
                            'conditions' => ['Income.customer_id = Customer.id', 'Customer.data_cancel' => '1901-01-01 00:00:00']
                        ],
                        ['table' => 'bank_accounts',
                            'alias' => 'BankAccount',
                            'type' => 'INNER',
                            'conditions' => ['Income.bank_account_id = BankAccount.id']
                        ],
                        ['table' => 'statuses',
                            'alias' => 'Status',
                            'type' => 'INNER',
                            'conditions' => ['Income.status_id = Status.id']
                        ],
                        ['table' => 'orders',
                            'alias' => 'Order',
                            'type' => 'LEFT',
                            'conditions' => ['Income.order_id = Order.id']
                        ],
                        ['table' => 'users',
                            'alias' => 'UserUpdated',
                            'type' => 'LEFT',
                            'conditions' => ['Income.user_updated_id = UserUpdated.id']
                        ],
                        ['table' => 'income_nfse',
                            'alias' => 'IncomeNfse',
                            'type' => 'LEFT',
                            'conditions' => ['IncomeNfse.income_id = Income.id']
                        ]
                    ]
                ];

                $this->paginate['Income'] = array_merge($this->paginate['Income'], $joins);
                $this->Paginator->settings = $this->paginate;
                $data = $this->Paginator->paginate('Income', $condition);

                $total_income = $this->Income->find('first', [
                    'conditions' => $condition, 
                    'fields' => ['sum(Income.valor_total) as total_income'],
                    'joins' => [
                        ['table' => 'customers',
                            'alias' => 'Customer',
                            'type' => 'INNER',
                            'conditions' => ['Income.customer_id = Customer.id', 'Customer.data_cancel' => '1901-01-01 00:00:00']
                        ],
                        ['table' => 'bank_accounts',
                            'alias' => 'BankAccount',
                            'type' => 'INNER',
                            'conditions' => ['Income.bank_account_id = BankAccount.id']
                        ],
                        ['table' => 'statuses',
                            'alias' => 'Status',
                            'type' => 'INNER',
                            'conditions' => ['Income.status_id = Status.id']
                        ],
                        ['table' => 'orders',
                            'alias' => 'Order',
                            'type' => 'LEFT',
                            'conditions' => ['Income.order_id = Order.id']
                        ],
                    ],
                ]);
            }
        }           
        
        if ($this->request->is('get') && isset($_GET['exportarnibo'])) {
               $data = $this->Income->find('all', [
                'conditions' => $condition,
                'order' => ['Income.vencimento' => 'desc'],
                'group' => 'Income.id',
                'joins' => [
                    [
                        'table' => 'customers',
                        'alias' => 'Customer',
                        'type' => 'INNER',
                        'conditions' => [
                            'Income.customer_id = Customer.id',
                            'Customer.data_cancel' => '1901-01-01 00:00:00'
                        ]
                    ],
                    [
                        'table' => 'bank_accounts',
                        'alias' => 'BankAccount',
                        'type' => 'INNER',
                        'conditions' => ['Income.bank_account_id = BankAccount.id']
                    ],
                    [
                        'table' => 'statuses',
                        'alias' => 'Status',
                        'type' => 'INNER',
                        'conditions' => ['Income.status_id = Status.id']
                    ],
                    [
                        'table' => 'orders',
                        'alias' => 'Order',
                        'type' => 'LEFT',
                        'conditions' => ['Income.order_id = Order.id']
                    ],
                    [
                        'table' => 'income_nfse',
                        'alias' => 'IncomeNfse',
                        'type' => 'LEFT',
                        'conditions' => ['IncomeNfse.income_id = Income.id']
                    ],
                    [
                        'table' => 'revenues',
                        'alias' => 'Revenue',
                        'type' => 'LEFT',
                        'conditions' => ['Income.revenue_id = Revenue.id']
                    ],
                    [
                        'table' => 'cost_center',
                        'alias' => 'CostCenter',
                        'type' => 'LEFT',
                        'conditions' => ['Income.cost_center_id = CostCenter.id']
                    ]
                ],
                'fields' => [
                    'Income.*',
                    'Customer.*',
                    'BankAccount.*',
                    'Status.*',
                    'Order.*',
                    'Revenue.name',
                    'CostCenter.name',
                    '(SELECT GROUP_CONCAT(nfse.tipo) FROM income_nfse nfse WHERE nfse.income_id = Income.id GROUP BY nfse.income_id) as nfses'
                ]
            ]);


                $nome = 'nibo_contas_receber_' . date('d_m_Y_H_i_s') . '.xlsx';
                $this->ExcelGenerator->gerarExcelNiboContasReceber($nome, $data);
                $this->redirect("/files/excel/" . $nome);
            }


        $payment_method = ['1' => 'Boleto','3' => 'Cartão de crédito','6' => 'Crédito em conta corrente','5' => 'Cheque','4' => 'Depósito','7' => 'Débito em conta','8' => 'Dinheiro','2' => 'Transfêrencia','9' => 'Desconto','11' => 'Pix','10' => 'Outros'];


        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 5]]);
        $statusCliente = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);
        $codFranquias = $this->Resale->find('all', ['conditions' => ['Resale.status_id' => 1, 'Resale.id' => CakeSession::read("Auth.User.resales")], ['order' => 'Resale.nome_fantasia']]);
        
        $action = 'Contas a Receber';

        $this->set(compact('status', 'limit', 'statusCliente', 'data', 'codFranquias', 'total_income', 'action', 'payment_method'));
    }
    
    public function add()
    {
        $this->Permission->check(23, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->request->data['Income']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['Income']['parcela'] = 1;
            $this->request->data['Income']['status_id'] = 15;

            $this->Income->create();
            if ($this->Income->save($this->request->data)) {
                $id_origem = $this->Income->id;
                if ($this->request->data['Income']['recorrencia'] == 1) {
                    for ($i=0; $i < $this->request->data['Income']['quantidade']; $i++) {
                        $year = substr($this->request->data['Income']['vencimento'], 6, 4);
                        $month = substr($this->request->data['Income']['vencimento'], 3, 2);
                        $date = substr($this->request->data['Income']['vencimento'], 0, 2);
                        $data = $year."-".$month."-".$date;

                        $cont = $i+1;
                        $meses = $cont*$this->request->data['Income']["periodicidade"];

                        $effectiveDate = date('d/m/Y', strtotime("+".$meses." months", strtotime($data)));

                        $data_save = $this->request->data;
                        $data_save['Income']['vencimento'] = $effectiveDate;
                        $data_save['Income']['parcela'] = $cont+1;
                        $data_save['Income']['conta_origem_id'] = $id_origem;

                        $this->Income->create();
                        $this->Income->save($data_save);
                    }
                }

                $this->Flash->set(__('A conta a receber foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index/?'.$this->request->data['query_string']]);
            } else {
                $this->Flash->set(__('A conta a receber não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 5]]);
        $revenues = $this->Revenue->find('list', ['conditions' => ['Revenue.status_id' => 1], 'order' => 'Revenue.name']);
        $bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1, 'BankAccount.id !=' => 5], 'order' => 'BankAccount.name']);
        $costCenters = $this->CostCenter->find('list', ['conditions' => ['CostCenter.status_id' => 1, 'CostCenter.customer_id' => 0], 'order' => 'CostCenter.name']);
        $orderArr = $this->Order->find('all', [
            'fields' => ['Order.id', 'Customer.nome_primario'],
            'contain' => ['Customer'],
            'order' => 'Order.id'
        ]);

        $orders = [];
        foreach ($orderArr as $order) {
            $orders[$order['Order']['id']] = $order['Order']['id'].' - '.$order['Customer']['nome_primario'];
        }

        $dataCustomers = $this->Customer->find('all', ['fields' => ['Customer.id', 'concat(Customer.codigo_associado, " - ", Customer.nome_secundario) as name'], 'order' => 'Customer.codigo_associado']);

        $customers = [];
        foreach ($dataCustomers as $customer) {
            $customers[$customer['Customer']['id']] = $customer[0]['name'];
        }
        $socios = $this->Socios->find('list');

        $cancelarConta = $this->Permission->check(58, "escrita");

        $action = 'Contas a receber';
        $breadcrumb = ['Nova conta' => ''];
        $this->set("form_action", "add");
        $this->set(compact('statuses', 'revenues', 'bankAccounts', 'costCenters', 'customers', 'socios', 'cancelarConta', 'action', 'breadcrumb', 'orders'));
    }
    
    public function add_retorno($retorno_id, $tmp_id)
    {
        $this->Permission->check(23, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->TmpRetornoCnab->id = $tmp_id;

        if ($this->request->is(['post', 'put'])) {
            $this->Income->create();
            $this->Income->validates();

            $this->request->data['Income']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['Income']['parcela'] = 1;
            $this->request->data['Income']['status_id'] = 15;
            $this->request->data['Income']['recorrencia'] = 2;

            if ($this->Income->save($this->request->data)) {
                $this->TmpRetornoCnab->save(['TmpRetornoCnab' => ['income_id' => $this->Income->id, 'encontrado' => 1, 'user_updated_id' => CakeSession::read('Auth.User.id')]]);

                $this->Flash->set(__('A conta a receber foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['controller' => 'retorno_cnabs', 'action' => 'detalhes/'.$retorno_id]);
            } else {
                $this->Flash->set(__('A conta a receber não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }
        $retorno = $this->TmpRetornoCnab->read();

        $this->request->data['Income']['vencimento'] = date('d/m/Y', strtotime($retorno['TmpRetornoCnab']['vencimento']));
        $this->request->data['Income']['valor_bruto'] = number_format($retorno['TmpRetornoCnab']['valor_pago'], 2, ',', '.');
        $this->request->data['Income']['valor_total'] = number_format($retorno['TmpRetornoCnab']['valor_liquido'], 2, ',', '.');
        $this->request->data['Income']['doc_num'] = $retorno['TmpRetornoCnab']['nosso_numero'];
        $this->request->data['Income']['nosso_numero'] = $retorno['TmpRetornoCnab']['nosso_numero'];

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 5]]);
        $revenues = $this->Revenue->find('list', ['conditions' => ['Revenue.status_id' => 1], 'order' => 'Revenue.name']);
        $bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => 'BankAccount.name']);
        $costCenters = $this->CostCenter->find('list', ['conditions' => ['CostCenter.status_id' => 1, 'CostCenter.customer_id' => 0], 'order' => 'CostCenter.name']);
        $customers = $this->Customer->find('list', ['conditions' => ['Customer.status_id' => [3,4]], 'order' => 'Customer.nome_secundario']);

        $this->set("action", "Nova conta a receber");
        $this->set("retorno", true);
        $this->set("form_action", "add_retorno/".$retorno_id.'/'.$tmp_id);
        $this->set(compact('statuses', 'revenues', 'bankAccounts', 'costCenters', 'customers'));
        $this->render("add");
    }

    public function edit($id = null)
    {
        $this->Permission->check(23, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Income->id = $id;
        $this->Income->recursive = 2;
        if ($this->request->is(['post', 'put'])) {
            $this->Income->validates();
            $this->request->data['Income']['user_updated_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['Income']['updated'] = date('Y-m-d H:i:s');

            $log_old_value = $this->request->data["log_old_value"];
            unset($this->request->data["log_old_value"]);
            
            $dados_log = [
                "old_value" => $log_old_value,
                "new_value" => json_encode($this->request->data),
                "route" => "incomes/edit",
                "log_action" => "Alterou",
                "log_table" => "Income",
                "primary_key" => $id,
                "parent_log" => 0,
                "user_type" => "ADMIN",
                "user_id" => CakeSession::read("Auth.User.id"),
                "message" => "A conta a receber foi alterada com sucesso",
                "log_date" => date("Y-m-d H:i:s"),
                "data_cancel" => "1901-01-01",
                "usuario_data_cancel" => 0,
                "ip" => $_SERVER["REMOTE_ADDR"]
            ];
            if ($this->Income->save($this->request->data)) {
                $this->Log->save($dados_log);
                $id_origem = $this->Income->id;
                if ($this->request->data['Income']['recorrencia'] == 1) {
                    for ($i=0; $i < $this->request->data['Income']['quantidade']; $i++) {
                        $year = substr($this->request->data['Income']['vencimento'], 6, 4);
                        $month = substr($this->request->data['Income']['vencimento'], 3, 2);
                        $date = substr($this->request->data['Income']['vencimento'], 0, 2);
                        $data = $year."-".$month."-".$date;

                        $cont = $i+1;
                        $meses = $cont*$this->request->data['Income']["periodicidade"];

                        $effectiveDate = date('d/m/Y', strtotime("+".$meses." months", strtotime($data)));

                        $data_save = $this->request->data;
                        $data_save['Income']['vencimento'] = $effectiveDate;
                        $data_save['Income']['parcela'] = $cont+1;
                        $data_save['Income']['conta_origem_id'] = $id_origem;

                        $this->Income->create();
                        $this->Income->save($data_save);
                    }
                }

                // Define a mensagem de sucesso com Flash
                $this->Flash->set(__('A conta a receber foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);

                // Renderiza a mesma tela para continuar na página de edição
                $this->render("add"); 
            } else {
                $this->Flash->set(__('A conta a receber não pode ser alterada, por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Income->validationErrors;
        $this->request->data = $this->Income->read();
        $this->Income->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 5]]);
        $socios = $this->Socios->find('list');
        $revenues = $this->Revenue->find('list', ['conditions' => ['Revenue.status_id' => 1], 'order' => 'Revenue.name']);
        $bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1, 'BankAccount.id !=' => 5], 'order' => 'BankAccount.name']);
        $costCenters = $this->CostCenter->find('list', ['conditions' => ['CostCenter.status_id' => 1, 'CostCenter.customer_id' => 0], 'order' => 'CostCenter.name']);
        $orderArr = $this->Order->find('all', [
            'fields' => ['Order.id', 'Customer.nome_primario'],
            'contain' => ['Customer'],
            'order' => 'Order.id'
        ]);
        $orders = [];
        foreach ($orderArr as $order) {
            $orders[$order['Order']['id']] = $order['Order']['id'].' - '.$order['Customer']['nome_primario'];
        }

        $dataCustomers = $this->Customer->find('all', ['conditions' => ['or' => ['Customer.id' => $this->request->data["Customer"]["id"]]], 'fields' => ['Customer.id', 'concat(Customer.codigo_associado, " - ", Customer.nome_secundario) as name'], 'order' => 'Customer.codigo_associado']);

        $customers = [];
        foreach ($dataCustomers as $customer) {
            $customers[$customer['Customer']['id']] = $customer[0]['name'];
        }

        $cancelarConta = $this->Permission->check(58, "escrita");

        $action = 'Contas a receber';
        $breadcrumb = ['Alterar conta' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'revenues', 'bankAccounts', 'costCenters', 'customers', 'socios', 'cancelarConta', 'action', 'breadcrumb', 'orders'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(23, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->Income->id = $id;

        $data = ['Income' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

        if ($this->Income->save($data)) {
            $this->Flash->set(__('A conta a receber foi excluida com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')]);
        }
    }

    public function change_status($id, $status)
    {
        $this->Permission->check(23, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Income->id = $id;

        $old_status = $this->Income->read();
        if ($old_status['Income']['status_id'] == 19) { // se a conta for status em negociação, remove o vinculo que ela tem com a nova conta negociada
            $cobranca = $this->Income->find('first', ['conditions' => ['Income.id' => $old_status['Income']['cobranca_id']]]);
            $ids = explode(',', $cobranca['Income']['cobranca_id_log']);

            //remover id do log
            foreach ($ids as $key => $value) {
                if ($value == $id) {
                    unset($ids[$key]);
                }
            }
            $novo_log = implode(',', $ids);

            $novo_valor = $cobranca['Income']['valor_total_nao_formatado']-$old_status['Income']['valor_total_nao_formatado'];
            $observacao = 'Conta '.$old_status['Income']['doc_num'].' já foi paga | '.$cobranca['Income']['observation'];

            $this->Income->updateAll(
                ['Income.cobranca_id_log' => "'".$novo_log."'", 'Income.valor_total' => str_replace(',', '.', $novo_valor), 'Income.valor_bruto' => str_replace(',', '.', $novo_valor), 'Income.observation' => "'".$observacao."'"], //set
                ['Income.id' => $old_status['Income']['cobranca_id']] //where
            );

            $data = ['Income' => ['status_id' => $status, 'cobranca_id' => null]];
        } else {
            $data = ['Income' => ['status_id' => $status]];
        }

        if ($this->Income->save($data, ['validate' => false])) {
            if ($old_status['Income']['status_id'] != $status) {
                $newStatus = $this->Status->find('first', ['conditions' => ['Status.id' => $status]]);
                $oldStatus = $this->Status->find('first', ['conditions' => ['Status.id' => $old_status['Income']['status_id']]]);

                $this->ChargesHistory->create();
                $charges = $this->ChargesHistory->save([
                    'call_status' => 0,
                    'cobranca_id' => 0,
                    'customer_id' => 0,
                    'income_id' => $id,
                    'text' => 'Mudança do status '.$oldStatus['Status']['name'].' para '.$newStatus['Status']['name'],
                    'user_creator_id' => CakeSession::read('Auth.User.id')
                ]);
            }
            $this->Flash->set(__('Status alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')]);
        }
    }

  public function reabrir_conta($id, $status)
  {
    $this->Permission->check(23, "escrita") ? "" : $this->redirect("/not_allowed");

    $this->Income->id = $id;
    $log_old_value = $this->Income->read();

    $data = ['Income' => ['status_id' => $status, 'valor_pago' => null, 'data_pagamento' => null, 'data_baixa' => null]];

    if ($this->Income->save($data)) {
      $new_value = $this->Income->read();

      $dados_log = [
        "old_value" => json_encode($log_old_value),
        "new_value" => json_encode($new_value),
        "route" => "incomes/reabrir_conta",
        "log_action" => "reabrir_conta",
        "log_table" => "Income",
        "primary_key" => $id,
        "parent_log" => 0,
        "user_type" => "ADMIN",
        "user_id" => CakeSession::read("Auth.User.id"),
        "message" => "A conta a receber foi alterada com sucesso",
        "log_date" => date("Y-m-d H:i:s"),
        "data_cancel" => "1901-01-01",
        "usuario_data_cancel" => 0,
        "ip" => $_SERVER["REMOTE_ADDR"]
      ];

      $this->Log->save($dados_log);

      $this->Flash->set(__('Conta reaberta com sucesso'), ['params' => ['class' => "alert alert-success"]]);
      $this->redirect(array('action' => 'index/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')));
    }
  }

    public function baixar_titulo($id)
    {
        $this->Permission->check(23, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Income->id = $id;

        $valueFormatado = str_replace('.', '', $this->request->data['Income']['valor_pago']);
        $valueFormatado = str_replace(',', '.', $valueFormatado);
        $this->request->data['Income']['valor_pago'] = $valueFormatado;
        $this->request->data['Income']['data_pagamento'] = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->data['Income']['data_pagamento'])));
        $this->request->data['Income']['usuario_id_baixa'] = CakeSession::read("Auth.User.id");
        $this->request->data['Income']['data_baixa'] = date('Y-m-d H:i:s');

        $itens = $this->CnabItem->find('all', [
            'conditions' => [
                'Income.id' => $id,
            ],
        ]);

        foreach ($itens as $item) {
            $this->CnabItem->id = $item['CnabItem']['id'];
            $this->CnabItem->save([
                'CnabItem' => [
                    'status_id' => 61
                ]
            ]);

            if ($item['Income']['order_id'] != null) {
                $this->Order->atualizarStatusPagamento($item['Income']['order_id']);
            }
        }

        $this->Income->save($this->request->data);

        $this->Flash->set(__('A conta a receber foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect($this->referer());
    }

    public function gerar_boleto($id, $pdf = false)
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $conta = $this->Income->getDadosBoleto($id);

        if (!empty($conta)) {
            if ($conta['BankAccount']['bank_id'] == 9) {
                $ApiBtgPactual = new ApiBtgPactual();
                $boleto = $ApiBtgPactual->gerarPdf($id);

                if (!empty($boleto['error'])) {
                    $this->Flash->set(__($boleto['error']), ['params' => ['class' => "alert alert-danger"]]);
                    $this->redirect($this->referer());
                }

                $this->printPdf($boleto['contents']);
            } else {
                $ApiItau = new ApiItau();
                $boleto = $ApiItau->buscarBoleto($conta);

                if ($boleto['success'] && !empty($boleto['contents']['data'])) {
                    $conta['mensagens_cobranca'] = Hash::extract($boleto['contents']['data'][0]['dado_boleto']['dados_individuais_boleto'][0]['mensagens_cobranca'], '{n}.mensagem');
                }

                $Bancoob = new BoletoItau();
                $Bancoob->printBoleto($conta, $pdf);
            }
        } else {
            $this->Flash->set(__('Não foi possível gerar o boleto'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }
    }

    public function printPdf($pdf)
    {
        $pdf_base64 = $pdf;

        // Decodificar a string base64 para binário
        $pdf_content = $pdf_base64;

        // Definir os cabeçalhos HTTP para exibir o PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="arquivo.pdf"');
        header('Content-Length: ' . strlen($pdf_content));

        // Exibir o conteúdo PDF
        echo $pdf_content;
    }

    public function calc_juros_multa($id)
    {
        $this->autoRender = false;
        $this->layout = 'ajax';
        // codigo da extranet antiga
        $conta = $this->Income->find('first', ['conditions' => ['Income.id' => $id]]);
            
        $instituicao = $this->Instituicao->find('first');
        $multa = $instituicao['Instituicao']['multa'];
        $juros = $instituicao['Instituicao']['juros'];

        $valor_multa = 0;
        $valor_juros = 0;
        $valor_juros_dia = 0;

        if (date('Y-m-d') > $conta['Income']['vencimento_nao_formatado'] || $conta['Income']['data_agendamento'] > $conta['Income']['vencimento_nao_formatado']) {
            $valor_multa = round((($conta['Income']['valor_total_nao_formatado']) * $multa) / 100, 2);
        
            if ($conta['Income']['data_agendamento'] == "") {
                $data_venc = date('d/m/Y');
                $data_atual = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            } else {
                $d          = explode("-", $conta['Income']['data_agendamento']);
                $data_venc  = $d[2]. '/' . $d[1] . '/' . $d[0];
                $data_atual = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
            }
                
            $databd = $conta['Income']['vencimento_nao_formatado'];
            $databd = explode("-", $databd);
                
            $data = mktime(0, 0, 0, $databd[1], $databd[2], $databd[0]);
                
            $dias = ($data_atual - $data) / 86400;
            $dias = ceil($dias);

            $valor_juros_dia = round((($conta['Income']['valor_total_nao_formatado'] * $juros) / 100), 2);
            // $valor_juros = round((($conta['Income']['valor_total_nao_formatado'] * $juros) / 100) * $dias, 2);
            // $valor_juros = round($conta['Income']['valor_total_nao_formatado'] + ($valor_juros_dia * $dias), 2); comentado por rodolfo 21/05
            $valor_juros = round(($valor_juros_dia * $dias), 2);
        } else {
            $d         = explode('-', $conta['Income']['vencimento_nao_formatado']);
            $data_venc = $d[2].'/'.$d[1].'/'.$d[0];
        }

        $juros_multa = $valor_juros + $valor_multa;

        /*$juros_multa = str_replace('.', '', $juros_multa);
        $juros_multa = str_replace(',', '.', $juros_multa);*/

        // comentado por rodolfo
        //if ($conta['Customer']['codigo_associado'] == '11227' || $conta['Customer']['cobrar_juros'] == 'N'){
        if ($conta['Customer']['cobrar_juros'] == 'N') {
            $valor_multa = 0;
            $valor_juros = 0;
            $juros_multa = 0;
        }
        // codigo da extranet antiga - fim

        return ['valor_multa' => $valor_multa, 'valor_juros' => $valor_juros, 'valor_juros_dia' => $valor_juros_dia, 'data_venc' => $data_venc, 'juros_multa' => $juros_multa];
    }

    public function calc_juros_multa_by_date()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $valor = $_POST['valor'];

        // $valor = str_replace('.', '', $valor);
        // $valor = str_replace(',', '.', $valor);

        $data_vencimento = $_POST['vencimento'];

        $data_agendamento = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['data'])));

        // codigo da extranet antiga
        $instituicao = $this->Instituicao->find('first');
        $multa = $instituicao['Instituicao']['multa'];
        $juros = $instituicao['Instituicao']['juros'];

        $valor_multa = 0;
        $valor_juros = 0;
        $valor_juros_dia = 0;

        if (date('Y-m-d') > $data_vencimento || $data_agendamento > $data_vencimento) {
            if ($_POST['cobrar_juros'] == 'S') {
                $valor_multa = round((($valor) * $multa) / 100, 2);

                if ($data_agendamento == "") {
                    $data_venc = date('d/m/Y');
                    $data_atual = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
                } else {
                    $d          = explode("-", $data_agendamento);
                    $data_venc  = $d[2]. '/' . $d[1] . '/' . $d[0];
                    $data_atual = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
                }
                    
                $databd = $data_vencimento;
                $databd = explode("-", $databd);
                    
                $data = mktime(0, 0, 0, $databd[1], $databd[2], $databd[0]);
                    
                $dias = ($data_atual - $data) / 86400;
                $dias = ceil($dias);
                $valor_juros_dia = round((($valor * $juros) / 100), 2);
                // $valor_juros = round((($valor * $valor_juros_dia) / 100) * $dias, 2);
                // $valor_juros = round($valor + ($valor_juros_dia * $dias), 2); comentado por rodolfo 21/05
                $valor_juros = round(($valor_juros_dia * $dias), 2);

                if ($valor_juros <= 0) {
                    $valor_juros = 0;
                }
            }
        }

        $juros_multa = $valor_juros + $valor_multa;
        // codigo da extranet antiga - fim

        echo json_encode(['total' => number_format($valor+$juros_multa, 2, ',', '.'), 'juros' => number_format($juros_multa, 2, ',', '.')]);
    }

    /*******************
                HISTORICO
    ********************/
    public function historico($id)
    {
        $this->Permission->check(23, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $this->Income->id = $id;
        $this->request->data = $this->Income->read();

        $condition = ["and" => ["ChargesHistory.income_id" => $id], "or" => []];

        $data = $this->Paginator->paginate('ChargesHistory', $condition);

        $action = 'Contas a receber';
        $breadcrumb = ['Historico de cobrança' => ''];
        $this->set(compact('data', 'id', 'action', 'breadcrumb'));
    }

    public function envia_email($id)
    {
        $this->autoRender = false;
        $this->layout = false;

        $conta = $this->Income->find('first', [
        	'fields' => [
                'Customer.id', 'Customer.codigo_associado', 'Customer.nome_secundario', 'Customer.documento', 'Customer.email','Customer.email1', 'Income.id'
            ],
        	'conditions' => ['Income.id' => $id]
        ]);

        $dados = [
            'viewVars' => [
                'nome_fantasia' => $conta['Customer']['nome_secundario'],
                'cnpj' => $conta['Customer']['documento'],
                'codigo_associado' => $conta['Customer']['codigo_associado'],
                'nome'  => $conta['Customer']['nome_secundario'],
                'email' => $conta['Customer']['email'],
                'link'  => Configure::read('Areadoassociado.link').'billings?em_aberto'
            ],
            'template' => 'envia_boleto',
            'subject'  => 'Envio de boleto',
            'config'   => 'default'
        ];

        if (!$this->Email->send($dados)) {
            $this->Flash->set(__('Email não pôde ser enviado com sucesso'), ['params' => ['class' => "alert alert-danger"]]);
        } else {
            $this->Flash->set(__('Enviado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        }

        $this->redirect($this->referer());
    }

    /*******************
    NOTA FISCAL DE SERVIÇO
     ********************/

    private function connect_nfse_sdk() {
        $token = Configure::read('Nfe.TokenEmitente');
        $env = Configure::read('Nfe.Env');

        if (!$token) {
            throw new Exception('Token não configurado.');
        }

        $params = [
            "token" => $token,
            "ambiente" => $env,
            "options" => [
                "debug" => false,
                "timeout" => 60,
                "port" => 443,
                "http_version" => CURL_HTTP_VERSION_NONE
            ]
        ];

        return new Nfse($params);
    }

    private function get_nfse_type_data($income, $type) {
        if ($type === 'ge') {
            $data = $this->get_gestao_eficiente_data($income);
        } else if ($type === 'tpp') {
            $data = $this->get_tpp_data($income);
        } else {
            $data = $this->get_ge_and_tpp_data($income);
        }

        return $data;
    }

    private function get_gestao_eficiente_data($income) {
        [$fee_economia, $vl_economia] = $this->get_nfse_order_fee_economia($income);
        $fee_economia_formatted = number_format($fee_economia, 2, ',', '.');
        $vl_economia_formatted = number_format($vl_economia, 2, ',', '.');
        $obs = "Gestão Eficiente - Pedido Nº {$income['Order']['id']}

        Item R$ {$income['Order']['subtotal']}
        Repasse Operadora R$ {$income['Order']['transfer_fee']}
        Economia R$ {$vl_economia_formatted}
        Desconto R$ {$income['Order']['desconto']}

        Informações Adicionais

        Gestão Eficiente R$ {$fee_economia_formatted}";

        return [
            "obs" => $obs,
            "valor" => $fee_economia,
        ];
    }

    private function get_tpp_data($income) {
        $title = "Prestação de Serviços - Taxa Administrativa / Taxa Processamento de Pedidos";
        $total = $income['Order']['commission_fee_not_formated'] + $income['Order']['tpp_fee'];
        $total_formatted = number_format($total, 2, ',', '.');
        $tpp_fee_formatted = number_format($income['Order']['tpp_fee'], 2, ',', '.');
        $obs = "$title
        
        Pedido Nº {$income['Order']['id']}
        
        Item R$ {$income['Order']['subtotal']}
        Repasse Operadora R$ {$income['Order']['transfer_fee']}
        Desconto R$ {$income['Order']['desconto']}
        
        Informações Adicionais
        
        Taxa Administrativa R$ {$income['Order']['commission_fee']}
        Taxa Processamento de Pedidos R$ {$tpp_fee_formatted}
        Total---------------------------------  R$ {$total_formatted}";

        return [
            "obs" => $obs,
            "valor" => $total,
        ];
    }

    private function get_ge_and_tpp_data($income) {
        [$fee_economia, $vl_economia] = $this->get_nfse_order_fee_economia($income);
        $fee_economia_formatted = number_format($fee_economia, 2, ',', '.');

        $total = $income['Order']['commission_fee_not_formated'] + $income['Order']['tpp_fee'] + $fee_economia;
        $total_formatted = number_format($total, 2, ',', '.');
        $tpp_fee_formatted = number_format($income['Order']['tpp_fee'], 2, ',', '.');
        $admin_fee = $income['Order']['commission_fee'];

        $obs = "Prestação de Serviços - Taxa Administrativa / Taxa Processamento de Pedidos / Gestão Eficiente
        
        Pedido Nº {$income['Order']['id']}
        
        Item R$ {$income['Order']['subtotal']}
        Repasse Operadora R$ {$income['Order']['transfer_fee']}
        Desconto R$ {$income['Order']['desconto']}
        
        Informações Adicionais
        
        Taxa Administrativa R$ {$admin_fee}
        Taxa Processamento de Pedidos R$ {$tpp_fee_formatted}
        Gestão Eficiente R$ {$fee_economia_formatted}
        Total---------------------------------  R$ {$total_formatted}";

        return [
            "obs" => $obs,
            "valor" => $total,
        ];
    }

    private function get_nfse_order_fee_economia($income) {
        if (!$income['Order']['id']) return null;

        $order_balances_total = $this->OrderBalance->find('all', ['conditions' => ["OrderBalance.order_id" => $income['Order']['id'], "OrderBalance.tipo" => 1], 'fields' => 'SUM(OrderBalance.total) as total']);
        $vl_economia = $order_balances_total[0][0]['total'];

        $fee_economia = 0;
        if ($income['Order']['fee_saldo_not_formated'] != 0 and $vl_economia != 0) {
            $fee_economia = (($income['Order']['fee_saldo_not_formated'] / 100) * ($vl_economia));
        }

        return [$fee_economia, $vl_economia];
    }

    private function get_municipio_id($uf, $municipio) {
        $client = new Client();
        $response = $client->request(
            'GET',
            "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$uf}/municipios?view=nivelado"
        );

        $data = collect(json_decode($response->getBody()->getContents(), true));

        $igbe_municipio = $data->first(function ($item) use ($municipio) {
            return strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', trim($item['municipio-nome']))) === strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', trim($municipio)));
        });

        return $igbe_municipio ? $igbe_municipio['municipio-id'] : null;
    }

    private function get_nfse_tomador($income) {
        $tomador = $income['Customer'];
        $razao_social = $tomador['nome_primario'];
        $cnpj = $tomador['documento'];
        if ($income['Order']['EconomicGroup']) {
            $tomador = $income['Order']['EconomicGroup'];
            $razao_social = $tomador['razao_social'];
            $cnpj = $tomador['document'];
        }

        return [
            "cnpj" => preg_replace('/\D/', '', $cnpj),
            "razao_social" => mb_substr($razao_social, 0, 75, "UTF-8"),
            "email" => mb_substr($income['Customer']['email'], 0, 60, "UTF-8"),
            "endereco" => [
                "logradouro" => mb_substr($tomador['endereco'], 0, 50, "UTF-8"),
                "numero" => mb_substr($tomador['numero'], 0, 10, "UTF-8"),
                "complemento" => mb_substr($tomador['complemento'], 0, 30, "UTF-8"),
                "bairro" => mb_substr($tomador['bairro'], 0, 30, "UTF-8"),
                "codigo_municipio" => $this->get_municipio_id($tomador['estado'], $tomador['cidade']),
                "uf" => mb_substr($tomador['estado'], 0, 2, "UTF-8"),
                "cep" => str_replace('-', '', $tomador['cep']),
            ]
        ];
    }

    private function get_nfse_pdf_link($nfse) {
        if (!isset($nfse['chave'])) return null;

        $nfse_sdk = $this->connect_nfse_sdk();

        $payload = [
            "chave" => $nfse['chave'],
        ];

        $response = $nfse_sdk->consulta($payload);
        $response = json_decode(json_encode($response), true);

        if (!$response['sucesso']) {
            return null;
        }

        $link_pdf = $response['link_pdf'].'&imprimir=1';

        return str_replace('notaprint', 'notaprintimg', $link_pdf);
    }

    private function get_nfse_data($income, $type, $obs) {
        $data = $this->get_nfse_type_data($income, $type);
        $series = [
          'tpp' => 1,
          'ge' => 2,
          'ge-tpp' => 3
        ];
        $serie = $series[$type];
        $today = new DateTime();

        $data['obs'] .= "\n\n$obs";

        return [
            "numero" => mb_substr($income['Income']['id'], 0, 9, "UTF-8"),
            "serie" => $serie,
            "data_emissao" => $today->format('Y-m-d\TH:i:sP'),
            "servico" => [
                "itens" => [
                    [
                        "codigo" => "3205",
                        "discriminacao" => mb_substr($data['obs'], 0, 2000, "UTF-8"),
                        "valor_servicos" => $data['valor']
                    ]
                ]
            ],
            "tomador" => $this->get_nfse_tomador($income)
        ];
    }

    private function generate_nfse_pdf($nfse, $type) {
        $pdf_link = $this->get_nfse_pdf_link($nfse['IncomeNfse']);

        $imageData = file_get_contents($pdf_link);

        $base64 = base64_encode($imageData);

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);

        $dataUri = "data:$mimeType;base64,$base64";

        $imgName = $this->get_nfse_pdf_name($nfse);

        return $this->HtmltoPdf->convert("<img src='{$dataUri}' />", $imgName, $type);
    }

    private function get_nfse_pdf_name($nfse) {
        $this->Income->recursive = 2;
        $income = $this->Income->find('first', ['conditions' => ['Income.id' => $nfse['IncomeNfse']['income_id']]]);

        $nfse_data = $this->get_nfse_data($income, $nfse['IncomeNfse']['tipo'], $nfse['IncomeNfse']['description']);
        $nf_number = $income['Order']['id'];
        $nome = $income['Customer']['nome_secundario'];
        $valor = number_format($nfse_data['servico']['itens'][0]['valor_servicos'], 2, ',', '.');

        return "NF {$nf_number} - BERH X {$nome} - R$ {$valor}";
    }

    public function nfse($id)
    {
        $this->Permission->check(88, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $this->Income->id = $id;
        $this->Income->recursive = 2;
        $this->request->data = $this->Income->read();

        if (!$this->request->data['Income']['order_id']) {
            $this->Flash->set(__('É necessário que a conta a receber esteja vinculada à um pedido para gerar nota. Por favor, verifique os dados e tente novamente'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        } elseif ($this->request->data['Income']['order_id'] && !$this->request->data['Order']['gera_nfse']) {
            $this->Flash->set(__('O pedido vinculado à conta a receber não gera nota. Por favor, verifique os dados e tente novamente'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }

        $income_nfses = collect($this->request->data['IncomeNfse'])->filter(function ($nfse) {
            return $nfse['data_cancel'] === '1901-01-01 00:00:00';
        });

        if ($this->request->data['Customer']['emitir_nota_fiscal'] === 'M') {
          $nfse_types = collect(['ge', 'tpp']);
        } else {
          $nfse_types = collect(['ge-tpp']);
        }

        $nfses = [];
        $nfse_types->each(function ($type) use ($income_nfses, &$nfses) {
            $nfse = $income_nfses->first(function ($nfse) use ($type, &$nfses) {
                return $nfse['tipo'] === $type;
            }) ?: [
                'tipo' => $type,
                'Status' => [
                    'label' => 'badge-secondary',
                    'name' => 'Não enviado'
                ]
            ];

            $nfse['pdf_link'] = $this->get_nfse_pdf_link($nfse);

            $data = $this->get_nfse_type_data($this->request->data, $type);
            $nfse['preview'] = $data['obs'];
            $nfses[] = $nfse;
        });

        $action = 'Contas a receber';
        $breadcrumb = ['Nota Fiscal de Serviço' => ''];
        $this->set(compact( 'id', 'action', 'breadcrumb', 'nfses'));
    }

    public function cria_nfse($income_id, $type) {
        $this->Permission->check(88, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->Income->recursive = 3;
        $income = $this->Income->find('first', ['conditions' => ['Income.id' => $income_id]]);

        if (!$income['Income']['order_id']) {
            $this->Flash->set(__('É necessário que a conta a receber esteja vinculada à um pedido para gerar nota. Por favor, verifique os dados e tente novamente'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        } elseif ($income['Income']['order_id'] && !$income['Order']['gera_nfse']) {
            $this->Flash->set(__('O pedido vinculado à conta a receber não gera nota. Por favor, verifique os dados e tente novamente'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }

        try {
            $obs = isset($this->request->data['IncomeNfse']) && isset($this->request->data['IncomeNfse']['description'])
              ? $this->request->data['IncomeNfse']['description'] : '';

            $nfse_sdk = $this->connect_nfse_sdk();

            $nfse_data = $this->get_nfse_data($income, $type, $obs);

            $stdResponse = $nfse_sdk->cria($nfse_data);

            $response = json_decode(json_encode($stdResponse), true);

            if ($income_id == 5462) {
                dd($stdResponse, $response);
            }

            if (!$response['sucesso']) {
                if (str_contains($response['mensagem'], 'Esse NFS-e já existe')) {
                    $this->IncomeNfse->save([
                        'tipo' => $type,
                        'chave' => $response['chave'],
                        'status_id' => 107,
                        'income_id' => $income['Income']['id'],
                        'description' => $obs
                    ]);
                } else {
                    $this->Flash->set(__($response['mensagem']), ['params' => ['class' => "alert alert-danger"]]);
                }
                $this->redirect(['action'=> 'nfse', $income_id]);
            }

            $this->IncomeNfse->save([
              'tipo' => $type,
              'chave' => $response['chave'],
              'status_id' => 106,
              'income_id' => $income['Income']['id'],
              'description' => $obs
            ]);

            $this->Flash->set(__('A nota fiscal foi emitida com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
        } catch (\Exception $e) {
            $this->Flash->set(__('Não foi possível emitir a nota fiscal. Tente novamente mais tarde.'), ['params' => ['class' => "alert alert-danger"]]);
        }
        $this->redirect(['action'=> 'nfse', $income_id]);
    }

    public function cancela_nfse($nfse_id) {
        $this->Permission->check(88, "escrita") ? "" : $this->redirect("/not_allowed");

        $income_nfse = $this->IncomeNfse->find('first', ['conditions' => ['IncomeNfse.id' => $nfse_id]]);
        $income_id = $income_nfse['IncomeNfse']['income_id'];

        if ($income_nfse['IncomeNfse']['status_id'] != 107) {
            $this->Flash->set(__('Só é possível cancelar uma nota fiscal emitida.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action'=> 'nfse', $income_id]);
        }

        try {
            $nfse_sdk = $this->connect_nfse_sdk();

            $payload = [
                "chave" => $income_nfse['IncomeNfse']['chave']
            ];

            $nfse_sdk->cancela($payload);

            $this->IncomeNfse->id = $nfse_id;
            $this->IncomeNfse->save([
                'status_id' => 108
            ]);

            $this->Flash->set(__('A nota fiscal foi cancelada com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
        } catch (\Exception $e) {
            $this->Flash->set(__('Não foi possível cancelar a nota fiscal. Tente novamente mais tarde.'), ['params' => ['class' => "alert alert-danger"]]);
        }

        $this->redirect(['action'=> 'nfse', $income_id]);
    }

    public function imprime_nfse($nfse_id) {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $this->Permission->check(88, "leitura") ? "" : $this->redirect("/not_allowed");

        $income_nfse = $this->IncomeNfse->find('first', ['conditions' => ['IncomeNfse.id' => $nfse_id]]);

        if (!$income_nfse) {
            $this->Flash->set(__('Nota fiscal não encontrada. Tente novamente mais tarde.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }

        try {
            $this->generate_nfse_pdf($income_nfse, 'download');
        } catch (\Exception $e) {
            $this->Flash->set(__('Não foi possível imprimir a nota fiscal. Tente novamente mais tarde.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action'=> 'nfse', $income_nfse['IncomeNfse']['income_id']]);
        }
    }

    public function imprime_danfse($nfse_id) {
        $this->Permission->check(88, "escrita") ? "" : $this->redirect("/not_allowed");

        $income_nfse = $this->IncomeNfse->find('first', ['conditions' => ['IncomeNfse.id' => $nfse_id]]);
        $income_id = $income_nfse['IncomeNfse']['income_id'];

        if ($income_nfse['IncomeNfse']['status_id'] != 107 && $income_nfse['IncomeNfse']['status_id'] != 108) {
            $this->Flash->set(__('Só é possível imprimir uma nota fiscal emitida ou cancelada.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action'=> 'nfse', $income_id]);
        }

        try {
            $nfse_sdk = $this->connect_nfse_sdk();

            $payload = [
                "chave" => $income_nfse['IncomeNfse']['chave'],
            ];

            $response = $nfse_sdk->consulta($payload);
            $response = json_decode(json_encode($response), true);

            if (!$response['sucesso']) {
                throw new \Exception('PDF não encontrado.');
            }

            $pdf = base64_decode($response['pdf']);

            $this->printPdf($pdf);
        } catch (\Exception $e) {
            $this->Flash->set(__('Não foi possível imprimir a nota fiscal. Tente novamente mais tarde.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action'=> 'nfse', $income_id]);
        }
    }

    public function atualiza_status_nfse() {
        $this->autoRender = false;
        $this->layout = 'ajax';

        try {
            $body = file_get_contents("php://input");

            $token = Configure::read('Nfe.TokenSoftHouse');

            if (!$token) {
                throw new Exception('Token não configurado.');
            }

            Webhook::isValid($token, $body);

            $data = json_decode($body, true);

            if ($data['origem'] === 'TESTE') {
                return 'Teste realizado com sucesso!';
            }

            $success = $data['sucesso'];
            $chave_nfse = $data['chave'];

            $income_nfse = $this->IncomeNfse->find('first', ['conditions' => ['IncomeNfse.chave' => $chave_nfse]]);

            if (!$income_nfse) {
                return 'Chave NFS-e não encontrada.';
            } else if ($income_nfse['IncomeNfse']['status_id'] != 106) {
                return 'Só é possível atualizar o status de notas fiscais "Em Processamento"';
            }

            $this->IncomeNfse->id = $income_nfse['IncomeNfse']['id'];
            $this->IncomeNfse->save([
                'status_id' => $success ? 107 : 108
            ]);

            if ($success) {
                $this->salva_nfse_no_pedido($income_nfse['IncomeNfse']['id']);
            }

            return 'Status atualizado com sucesso.';
        } catch (\Exception $e) {
            debug($e);
            return 'Assinatura inválida!';
        }
    }

    private function salva_nfse_no_pedido($nfse_id) {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $nfse = $this->IncomeNfse->find('first', ['conditions' => ['IncomeNfse.id' => $nfse_id]]);

        $pdf = $this->generate_nfse_pdf($nfse, 'string');
        $pdf_name = $this->get_nfse_pdf_name($nfse);
        $file_name = $pdf_name.'.pdf';

        $tmpFile = tempnam(sys_get_temp_dir(), 'upload_');
        file_put_contents($tmpFile, $pdf);

        $fileData = array(
            'name' => $file_name,
            'type' => 'application/pdf',
            'tmp_name' => $tmpFile,
            'error' => 0,
            'size' => filesize($tmpFile)
        );

        $result = $this->OrderDocument->save(['OrderDocument' => [
            'name' => $file_name,
            'file_name' => $fileData,
            'order_id' => $nfse['Income']['order_id'],
            'status_id' => 1,
        ]]);

        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }

        $recordId = $result['OrderDocument']['id'];
        if ($result && !empty($result['OrderDocument']['file_name'])) {
            $finalPath = WWW_ROOT . 'files' . DS . 'order_document' . DS . 'file_name' . DS . $recordId . DS . $result['OrderDocument']['file_name'];
            if (file_exists($finalPath)) {
                chmod($finalPath, 0644);
            }
        }

        $this->send_order_document_mail($nfse['Income']['order_id']);
    }

    private function send_order_document_mail($order_id) {
        $order = $this->Order->find('first', [
            'contain' => ['Customer'],
            'conditions' => ['Order.id' => $order_id],
        ]);

        $users[$order['Customer']['email']] = $order['Customer']['nome_primario'];

        if ($order['Customer']['email1'] != '') {
            $users[$order['Customer']['email1']] = $order['Customer']['nome_primario'];
        }

        $dados = [
            'viewVars' => [
                'tos' => $users,
                'pedido' => $order_id,
                'link' => Configure::read('Areadoassociado.link').'orders/edit/'.$order_id,
            ],
            'template' => 'nota_fiscal_criada',
            'subject' => 'BeRH - Nota Fiscal',
            'config' => 'default',
        ];

        $this->Email->send($dados, true);
    }
}
