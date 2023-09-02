<?php
App::uses('BoletoItau', 'Lib');
class IncomesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Boleto', 'HtmltoPdf', 'ExcelGenerator', 'GerarCaixaNossoNumero', 'Email'];
    public $uses = ['Income', 'Status', 'Revenue', 'BankAccount', 'CostCenter', 'Customer', 'Instituicao', 'TmpRetornoCnab', 'ChargesHistory', 'Socios', 'Log', 'Resale'];

    public $paginate = [
        'Income' => ['limit' => 10, 'order' => ['Income.vencimento' => 'desc'], 'group' => 'Income.id'],
        'ChargesHistory' => ['limit' => 10, 'order' => ['ChargesHistory.created' => 'desc']]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allow('gerar_boleto');
    }

    public function index()
    {
        $this->Permission->check(23, "leitura") ? "" : $this->redirect("/not_allowed");
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

        if ($this->request->is('get')) {

            if (isset($_GET['exportar'])) {
                $data = $this->Income->find('all', ['conditions' => $condition, 'order' => ['Income.vencimento' => 'desc'], 'group' => 'Income.id']);

                $nome = 'contas_receber_'.date('d_m_Y');
                
                $this->ExcelGenerator->gerarExcelContasReceber($nome, $data);
                $this->redirect("/files/excel/".$nome.".xlsx");
            } else {

                $this->Income->recursive = -1;
                $this->Income->unbindModel(['belongsTo' => ['Customer', 'BankAccount', 'Status']], false);
                
                $joins = [
                    'fields' => ['Income.*', 'Customer.*', 'BankAccount.*', 'Status.*'],
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
        $this->set(compact('status', 'statusCliente', 'data', 'codFranquias', 'total_income', 'action'));
    }
    
    public function add()
    {
        $this->Permission->check(23, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->Income->create();
            if ($this->Income->validates()) {
                $this->request->data['Income']['user_creator_id'] = CakeSession::read("Auth.User.id");
                $this->request->data['Income']['parcela'] = 1;
                $this->request->data['Income']['status_id'] = 15;
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
            } else {
                $this->Flash->set(__('A conta a receber não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 5]]);
        $revenues = $this->Revenue->find('list', ['conditions' => ['Revenue.status_id' => 1], 'order' => 'Revenue.name']);
        $bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => 'BankAccount.name']);
        $costCenters = $this->CostCenter->find('list', ['conditions' => ['CostCenter.status_id' => 1], 'order' => 'CostCenter.name']);
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
        $this->set(compact('statuses', 'revenues', 'bankAccounts', 'costCenters', 'customers', 'socios', 'cancelarConta', 'action', 'breadcrumb'));
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
        $costCenters = $this->CostCenter->find('list', ['conditions' => ['CostCenter.status_id' => 1], 'order' => 'CostCenter.name']);
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

                $this->Flash->set(__('A conta a receber foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index/?'.$this->request->data['query_string']]);
            } else {
                $this->Flash->set(__('A conta a receber não pode ser alterada, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->Income->validationErrors;
        $this->request->data = $this->Income->read();
        $this->Income->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 5]]);
        $socios = $this->Socios->find('list');
        $revenues = $this->Revenue->find('list', ['conditions' => ['Revenue.status_id' => 1], 'order' => 'Revenue.name']);
        $bankAccounts = $this->BankAccount->find('list', ['conditions' => ['BankAccount.status_id' => 1], 'order' => 'BankAccount.name']);
        $costCenters = $this->CostCenter->find('list', ['conditions' => ['CostCenter.status_id' => 1], 'order' => 'CostCenter.name']);
        $dataCustomers = $this->Customer->find('all', ['conditions' => ['or' => ['Customer.id' => $this->request->data["Customer"]["id"]]], 'fields' => ['Customer.id', 'concat(Customer.codigo_associado, " - ", Customer.nome_secundario) as name'], 'order' => 'Customer.codigo_associado']);

        $customers = [];
        foreach ($dataCustomers as $customer) {
            $customers[$customer['Customer']['id']] = $customer[0]['name'];
        }

        $cancelarConta = $this->Permission->check(58, "escrita");

        $action = 'Contas a receber';
        $breadcrumb = ['Alterar conta' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'revenues', 'bankAccounts', 'costCenters', 'customers', 'socios', 'cancelarConta', 'action', 'breadcrumb'));
        
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

        if ($this->Income->save($this->request->data)) {
            $this->Flash->set(__('A conta a receber foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'edit/'.$id]);
        }
    }

    public function gerar_boleto($id, $pdf = false)
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $conta = $this->Income->getDadosBoleto($id);

        $Bancoob = new BoletoItau();
        $Bancoob->printBoleto($conta, $pdf);
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

        $view = new View($this, false);
        $view->layout=false;

        $html=$view->render('../EmailsCampanhas/template_email2');

        $conta = $this->Income->find('first', [
        	'fields' => [
                'Customer.id', 'Customer.codigo_associado', 'Customer.nome_secundario', 'Customer.documento', 'Customer.email','Customer.email1', 'Income.id'
            ],
        	'conditions' => ['Income.id' => $id]
        ]);

        $dados = [
            'subject' => 'Envio de boleto',
            'content' => $html,
            'config' => 'fatura',
            'avulso' => true,
            'customers' => [
                [
                	'Customer' => [
                        'id' => $conta['Customer']['id'],
                        'codigo_associado' => $conta['Customer']['codigo_associado'],
                        'nome_secundario' => $conta['Customer']['nome_secundario'],
                        'documento' => $conta['Customer']['documento'],
                        'email' => $conta['Customer']['email'],
                        'email1' => $conta['Customer']['email1'],
                    ],
                    'MailList' => [
                        'income_id' => $conta['Income']['id']
                    ]
                ]
            ],
        ];

        $response = $this->Email->send_many($dados);

        if (empty($response)) {
        	$this->Flash->set(__('Enviado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        } else {
        	$this->Flash->set(__('Houve um problema'), ['params' => ['class' => "alert alert-danger"]]);
        }

        $this->redirect($this->referer());
    }
}
