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
    public $uses = ['Income', 'Status', 'Revenue', 'BankAccount', 'CostCenter', 'Customer', 'Instituicao', 'TmpRetornoCnab', 'ChargesHistory', 'Socios', 'Log', 'Resale', 'CnabItem', 'Order', 'OrderBalance'];

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

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        if (isset($_GET["sc"]) and $_GET["sc"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET['sc']]);
        }

        if (!empty($_GET["f"])) {
            $condition['and'] = array_merge($condition['and'], ['Customer.cod_franquia' => $_GET['f']]);
        }

        if (isset($_GET["atraso"]) and $_GET["atraso"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id IN (15,16) ']);
            $condition['and'] = array_merge($condition['and'], ['Income.vencimento <' => date("Y-m-d")]);
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

                $nome = 'contas_receber_'.date('d_m_Y').".xlsx";
                
                $this->ExcelGenerator->gerarExcelContasReceber($nome, $data);
                $this->redirect("/files/excel/".$nome);
            } else {

                $this->Income->recursive = -1;
                $this->Income->unbindModel(['belongsTo' => ['Customer', 'BankAccount', 'Status']], false);
                
                $joins = [
                    'fields' => ['Income.*', 'Customer.*', 'BankAccount.*', 'Status.*', 'Order.*'],
                    'joins' => [['table' => 'customers',
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
                ]
                    ]
                ];

                $this->paginate['Income'] = array_merge($this->paginate['Income'], $joins);
                $this->Paginator->settings = $this->paginate;
                $data = $this->Paginator->paginate('Income', $condition);

                $total_income = $this->Income->find('first', 
                    [
                        'conditions' => $condition, 
                        'joins' => [['table' => 'customers',
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
                        ]
                        ],
                        'fields' => ['sum(Income.valor_total) as total_income']
                    ]);

            }
        }

                

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 5]]);
        $statusCliente = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);
        $codFranquias = $this->Resale->find('all', ['conditions' => ['Resale.status_id' => 1, 'Resale.id' => CakeSession::read("Auth.User.resales")], ['order' => 'Resale.nome_fantasia']]);
        $action = 'Contas a Receber';
        $this->set(compact('status', 'limit', 'statusCliente', 'data', 'codFranquias', 'total_income', 'action'));
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
        $bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => 'BankAccount.name']);
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
        $bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => 'BankAccount.name']);
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
            $this->Flash->set(__('Status alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index/?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '')]);
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
                $this->Order->id = $item['Income']['order_id'];
                $this->Order->save([
                    'Order' => [
                        'status_id' => 85,
                        'payment_date' => date('Y-m-d'),
                    ]
                ]);
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

    private function get_nfse_type_data($income) {
        [$fee_economia, $_] = $this->get_nfse_order_fee_economia($income);

        $data = $this->get_tpp_data($income);
        if ($fee_economia > 0) {
            $data = $this->get_gestao_eficiente_data($income);
        }

        if ($income['Order']['observation']) {
            $data['obs'] .= "\n\n{$income['Order']['observation']}";
        }

        return $data;
    }

    private function get_gestao_eficiente_data($income) {
        [$fee_economia, $vl_economia] = $this->get_nfse_order_fee_economia($income);
        $fee_economia_formatted = number_format($fee_economia, 2, ',', '.');
        $vl_economia_formatted = number_format($vl_economia, 2, ',', '.');
        $obs = "Gestão Eficiente - Pedido Nº {$income['Order']['id']}

        Item                                    R$ {$income['Order']['subtotal']}
        Repasse Operadora                       R$ {$income['Order']['transfer_fee']}
        Economia                                R$ {$vl_economia_formatted}

        Informações Adicionais

        Gestão Eficiente                        R$ {$fee_economia_formatted}";

        return [
            "obs" => $obs,
            "valor" => $fee_economia,
        ];
    }

    private function get_tpp_data($income) {
        $total = $income['Order']['commission_fee_not_formated'] + $income['Order']['tpp_fee'];
        $total_formatted = number_format($total, 2, ',', '.');
        $tpp_fee_formatted = number_format($income['Order']['tpp_fee'], 2, ',', '.');
        $obs = "Prestação de Serviços - Taxa Administrativa / Taxa Processamento de Pedidos
        
        Pedido Nº {$income['Order']['id']}
        
        Item                                    R$ {$income['Order']['total']}
        Repasse Operadora                       R$ {$income['Order']['transfer_fee']}
        
        Informações Adicionais
        
        Taxa Administrativa                     R$ {$income['Order']['commission_fee']}
        Taxa Processamento de Pedidos           R$ {$tpp_fee_formatted}
        Total---------------------------------  R$ {$total_formatted}";

        if ($income['Order']['observation']) {

        }
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
            return strtolower($item['municipio-nome']) === strtolower($municipio);
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
            "razao_social" => $razao_social,
            "email" => $tomador['email'],
            "endereco" => [
                "logradouro" => $tomador['endereco'],
                "numero" => $tomador['numero'],
                "complemento" => $tomador['complemento'],
                "bairro" => $tomador['bairro'],
                "codigo_municipio" => $this->get_municipio_id($tomador['estado'], $tomador['cidade']),
                "uf" => $tomador['estado'],
                "cep" => $tomador['cep'],
            ]
        ];
    }

    public function nfse($id)
    {
        $this->Permission->check(23, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $this->Income->id = $id;
        $this->request->data = $this->Income->read();

        $preview_data = $this->get_nfse_type_data($this->request->data);

        $pdf_link = null;
        if ($this->request->data['Income']['nfse_chave'] && ($this->request->data['Income']['nfse_status_id'] == 107 || $this->request->data['Income']['nfse_status_id'] == 108)) {
            $nfse = $this->connect_nfse_sdk();

            $payload = [
                "chave" => $this->request->data['Income']['nfse_chave'],
            ];

            $response = $nfse->consulta($payload);

            $pdf_link = $response->link_pdf;
        }

        $action = 'Contas a receber';
        $breadcrumb = ['Nota Fiscal de Serviço' => ''];
        $this->set(compact( 'id', 'action', 'breadcrumb', 'preview_data', 'pdf_link'));
    }

    public function cria_nfse($id) {
        $this->Permission->check(23, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->Income->recursive = 2;
        $income = $this->Income->find('first', ['conditions' => ['Income.id' => $id]]);

        try {
            $nfse = $this->connect_nfse_sdk();

            $data = $this->get_nfse_type_data($income);

            $today = new DateTime();

            $payload = [
                "numero" => $income['Income']['id'],
                "serie" => "1",
                "data_emissao" => $today->format('Y-m-d\TH:i:sP'),
                "servico" => [
                    "itens" => [
                        [
                            "codigo" => "6298",
                            "discriminacao" => $data['obs'],
                            "valor_servicos" => $data['valor']
                        ]
                    ]
                ],
                "tomador" => $this->get_nfse_tomador($income)
            ];

            $response = $nfse->cria($payload);

            if (!$response->sucesso) {
                $this->Flash->set(__($response->mensagem), ['params' => ['class' => "alert alert-danger"]]);
                $this->redirect($this->referer());
            }

            $this->Income->id = $id;
            $this->Income->save([
                'nfse_chave' => $response->chave,
                'nfse_status_id' => 106
            ]);

            $this->Flash->set(__('A nota fiscal foi emitida com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
        } catch (\Exception $e) {
            $this->Flash->set(__('Não foi possível emitir a nota fiscal. Tente novamente mais tarde.'), ['params' => ['class' => "alert alert-danger"]]);
        }
        $this->redirect($this->referer());
    }

    public function cancela_nfse($id) {
        $this->Permission->check(23, "escrita") ? "" : $this->redirect("/not_allowed");

        $income = $this->Income->find('first', ['conditions' => ['Income.id' => $id]]);

        if ($income['Income']['nfse_status_id'] != 107) {
            $this->Flash->set(__('Só é possível cancelar uma nota fiscal emitida.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }

        try {
            $nfse = $this->connect_nfse_sdk();

            $payload = [
                "chave" => $income['Income']['nfse_chave']
            ];

            $nfse->cancela($payload);

            $this->Income->id = $id;
            $this->Income->save([
                'nfse_status_id' => 108
            ]);

            $this->Flash->set(__('A nota fiscal foi cancelada com sucesso.'), ['params' => ['class' => "alert alert-success"]]);
        } catch (\Exception $e) {
            $this->Flash->set(__('Não foi possível cancelar a nota fiscal. Tente novamente mais tarde.'), ['params' => ['class' => "alert alert-danger"]]);
        }
        $this->redirect($this->referer());
    }

    public function imprime_danfse($id) {
        $this->Permission->check(23, "escrita") ? "" : $this->redirect("/not_allowed");

        $income = $this->Income->find('first', ['conditions' => ['Income.id' => $id]]);

        if ($income['Income']['nfse_status_id'] != 107 && $income['Income']['nfse_status_id'] != 108) {
            $this->Flash->set(__('Só é possível imprimir uma nota fiscal emitida ou cancelada.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
        }

        try {
            $nfse = $this->connect_nfse_sdk();

            $payload = [
                "chave" => $income['Income']['nfse_chave'],
            ];

            $response = $nfse->consulta($payload);

            if (!$response->sucesso) {
                throw new \Exception('PDF não encontrado.');
            }

            $pdf = base64_decode($response->pdf);

            $this->printPdf($pdf);
        } catch (\Exception $e) {
            $this->Flash->set(__('Não foi possível imprimir a nota fiscal. Tente novamente mais tarde.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
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

            $data = json_decode($body);

            if ($data->origem === 'TESTE') {
                return 'Teste realizado com sucesso!';
            }

            $success = $data->sucesso;
            $chave_nfse = $data->chave;

            $income = $this->Income->find('first', ['conditions' => ['Income.nfse_chave' => $chave_nfse]]);

            if (!$income) {
                return 'Chave NFS-e não encontrada.';
            } else if ($income['Income']['nfse_status_id'] != 106) {
                return 'Só é possível atualizar o status de notas fiscais "Em Processamento"';
            }

            $this->Income->id = $income['Income']['id'];
            $this->Income->save([
                'nfse_status_id' => $success ? 107 : 108
            ]);

            return 'Status atualizado com sucesso.';
        } catch (\Exception $e) {
            return 'Assinatura inválida!';
        }
    }
}
