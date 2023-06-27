<?php
App::import('Controller', 'Incomes');
class CobrancasController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'Boleto', 'ExcelGenerator', 'Sms'];
    public $uses = ['Income', 'Status', 'Revenue', 'BankAccount', 'CostCenter', 'Customer', 'ChargesHistory', 'User', 'DistribuicaoCobranca', 'DistribuicaoCobrancaUsuario', 'Resale'];

    public $paginate = [
        'limit' => 50,
        'order' => ['Income.vencimento' => 'desc'],
        'recursive' => 1,
        'group' => 'Income.id',
        'fields' => ['Income.*', 'max(Histories.return_date) as return_date', 'Customers.codigo_associado', 'Customers.nome_secundario', 'DistribuicaoCobrancaUsuario.*'],
        'joins' => [
            [
                'table' => 'incomes',
                'alias' => 'Incomes',
                'type' => 'INNER',
                'conditions' => ['Incomes.id = DistribuicaoCobrancaUsuario.income_id']
            ],
            [
                'table' => 'customers',
                'alias' => 'Customers',
                'type' => 'INNER',
                'conditions' => ['Customers.id = Incomes.customer_id']
            ],
            [
                'table' => 'charges_histories',
                'alias' => 'Histories',
                'type' => 'LEFT',
                'conditions' => ['Incomes.id = Histories.income_id', 'Histories.return_date > current_date']
            ]
        ]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();

        //$this->Auth->allow('cancela_cobrancas_vencidas');
    }

    public function cancela_cobrancas_vencidas()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $data_buscar = date('Y-m-d', strtotime('-4 weekdays'));

        $contas = $this->Income->find('all', ['conditions' => ['Income.check_cobranca' => 1, 'Income.vencimento' => $data_buscar, 'Income.status_id' => 15], 'recursive' => -1]);

        foreach ($contas as $conta) {
            //contas filhas
            $this->Income->updateAll(
                ["Income.cobranca_id" => null, "Income.status_id" => 15, "Income.updated" => "current_timestamp", "user_updated_id" => CakeSession::read("Auth.User.id")], //set
                ['Income.id in ('.$conta['Income']['cobranca_id_log'].')'] //where
            );

            //contaa pai - gerada pela cobranca
            $this->Income->updateAll(
                ["Income.updated" => "'".date("Y-m-d H:i:s")."'", "Income.status_id" => 18, "Income.cobranca_id_log" => null, "Income.check_cobranca" => 0, "Income.updated" => "current_timestamp", "user_updated_id" => CakeSession::read("Auth.User.id")], //set
                ['Income.id' => $conta['Income']['id']] //where
            );
        }
    }

    public function divisao_cobradores()
    {
        $this->Permission->check(18, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = ['limit' => 10, 'order' => ['DistribuicaoCobranca.created' => 'desc']];

        if ($this->request->is('post')) {
            $de = !empty($_POST['de']) ? date("Y-m-d", strtotime(str_replace("/", "-", $_POST['de']))) : null;
            $ate = !empty($_POST['de']) ? date("Y-m-d", strtotime(str_replace("/", "-", $_POST['ate']))) : null;
            $sContas = empty($_POST['sContas']) ? null : $_POST['sContas'];
            $sCliente = empty($_POST['sCliente']) ? null : $_POST['sCliente'];
            $period = empty($_POST['period']) ? null : $_POST['period'];
            $resale = empty($_POST['resale']) ? null : $_POST['resale'];

            $this->divide_cobrancas($this->request->data['DistribuicaoCobranca']['cobrador_id'], $de, $ate, $sContas, $sCliente, $period, $resale);

            $this->Session->setFlash(__('A divisão foi salva com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'divisao_cobradores']);
        }

        $cobradors = $this->User->find('list', ['conditions' => ['User.group_id' => [3,8]], 'order' => ['User.name' => 'asc']]);

        $form_action = '../cobrancas/divisao_cobradores';
        $statusCliente = $this->Status->find('list', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);
        $statusContas = $this->Status->find('list', ['conditions' => ['Status.categoria' => 5], 'order' => 'Status.name']);
        $resales = $this->Resale->find("list", ['conditions' => ['Resale.status_id' => 1, 'Resale.id' => CakeSession::read("Auth.User.resales")], 'order' => ['Resale.nome_fantasia' => 'asc']]);

        //$log = $this->DistribuicaoCobranca->find('all', ['group' => ['DistribuicaoCobranca.created'], 'order' => ['DistribuicaoCobranca.created' => 'desc']]);
        $log = $this->Paginator->paginate('DistribuicaoCobranca');

        $action = 'Divisão de cobradores';
        $breadcrumb = ['Configuração' => '', 'Divisão de cobradores' => ''];
        $this->set(compact('cobradors', 'form_action', 'log', 'block_save', 'statusCliente', 'statusContas', 'resales', 'action', 'breadcrumb'));
    }

    public function visualizar_divisao($id)
    {
        $this->Permission->check(18, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->DistribuicaoCobranca->id = $id;

        $this->DistribuicaoCobranca->unbindModel(['hasMany' => ['DistribuicaoCobrancaUsuario']]);
        $data = $this->DistribuicaoCobranca->find('first', ['conditions' => ['DistribuicaoCobranca.id' => $id], 'fields' => ['DistribuicaoCobranca.*'], 'recursive' => 2]);

        $valor_cobrado = [];
        foreach ($data['QtdeUsuarios'] as $user) {
            $valor_cobrado[$user['user_id']] = $this->DistribuicaoCobranca->query("
				SELECT SUM(i.valor_total) AS total
				FROM distribuicao_cobranca_usuarios AS d
				INNER JOIN incomes i ON i.id = d.income_id
				WHERE d.distribuicao_cobranca_id IN (".$id.") and d.user_id = ".$user['user_id']." and d.data_cancel = '1901-01-01'
				GROUP BY d.distribuicao_cobranca_id, d.user_id
			");

            $exito[$user['user_id']] = $this->DistribuicaoCobranca->query(
                "
				SELECT sum(i.valor_total) as valor_total, sum(i.valor_pago) as valor_total_pago, count(1) as qtde, MAX(h.created) AS ultimo_registro
				FROM distribuicao_cobranca_usuarios d
				inner join incomes i on i.id = d.income_id
				inner join charges_histories h on h.income_id = i.id
				WHERE d.distribuicao_cobranca_id IN (".$id.") 
					and d.data_cancel = '1901-01-01' 
					and h.cobranca_id = '".$data['DistribuicaoCobranca']['id']."' 
					and h.call_status = 1 
					and d.user_id = ".$user['user_id']
            );
        }
        
        if (isset($_GET['exportar'])) {
            $nome = 'relatorio_diario_'.date('d_m_Y', strtotime($data['DistribuicaoCobranca']['created']));
            $this->ExcelGenerator->gerarExcelDiarioCobranca($nome, $data, $valor_cobrado, $exito);
            $this->redirect("/files/excel/".$nome.".xlsx");
        }

        $action = 'Divisão de cobradores';
        $breadcrumb = ['Configuração' => '', 'Divisão de cobradores' => '', date('d/m/Y', strtotime($data['DistribuicaoCobranca']['created'])) => ''];
        $this->set(compact('id', 'data', 'valor_cobrado', "exito", "action", "breadcrumb"));
    }

    public function excluir_divisao($id)
    {
        $this->Permission->check(51, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->DistribuicaoCobranca->id = $id;

        $data = ['DistribuicaoCobranca' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

        if ($this->DistribuicaoCobranca->save($data)) {
            $this->DistribuicaoCobrancaUsuario->updateAll(
                ['DistribuicaoCobrancaUsuario.data_cancel' => 'current_timestamp', 'DistribuicaoCobrancaUsuario.usuario_id_cancel' => CakeSession::read("Auth.User.id")], //set
                ["DistribuicaoCobrancaUsuario.distribuicao_cobranca_id" => $id] //where
            );

            $this->Session->setFlash(__('A divisão foi excluido com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect($this->referer());
        }
    }

    public function excel_clientes_cobrados($id)
    {
        $this->autoRender = false;
        $this->DistribuicaoCobrancaUsuario->unbindModel(['belongsTo' => ['DistribuicaoCobranca', 'User']]);
        $this->Income->unbindModel(['belongsTo' => ['BankAccount', 'Revenue', 'CostCenter', 'Billing', 'BillingMonthlyPayment']]);
        $detalhe_cobrancas = $this->DistribuicaoCobrancaUsuario->find("all", ["conditions" => ["DistribuicaoCobrancaUsuario.distribuicao_cobranca_id" => $id], "recursive" => 2]);

        $nome = 'relatorio_diario_detalhado'.date('d_m_Y');
        $this->ExcelGenerator->gerarExcelClientesCobrados($nome, $detalhe_cobrancas);
        $this->redirect("/files/excel/".$nome.".xlsx");
    }

    public function excel_clientes_exito($id)
    {
        $this->autoRender = false;

        $data = $this->DistribuicaoCobranca->find('first', ['conditions' => ['DistribuicaoCobranca.id' => $id], 'recursive' => -1]);
        $detalhe_cobrancas = $this->DistribuicaoCobrancaUsuario->query("
			SELECT *
			FROM distribuicao_cobranca_usuarios d
			inner join incomes i on i.id = d.income_id
			inner join customers c on c.id = i.customer_id
			inner join charges_histories h on h.income_id = i.id
			WHERE d.distribuicao_cobranca_id IN (".$id.") and d.data_cancel = '1901-01-01' and h.call_status = 1 and date(h.created) = '".$data['DistribuicaoCobranca']['created']."'
		");

        $nome = 'relatorio_diario_cobrados'.date('d_m_Y');
        $this->ExcelGenerator->gerarExcelClientesExito($nome, $detalhe_cobrancas);
        $this->redirect("/files/excel/".$nome.".xlsx");
    }

    public function divide_cobrancas($user_ids, $de = null, $ate = null, $sContas = null, $sCliente = null, $period = null, $resale = null)
    {
        $cobradores = $this->User->find('all', ['conditions' => ['User.id' => $user_ids], 'order' => 'rand()']);

        if ($de != null && $ate != null) {
            $vencimento = "BETWEEN '".$de."' AND '".$ate."'";
        } else {
            $vencimento = "< CURRENT_DATE";
        }

        $where = '';
        if ($sContas != null) {
            $where .= ' AND Income.status_id = '.$sContas;
        }

        if ($sCliente != null) {
            $where .= ' AND c.status_id = '.$sCliente;
        }

        if ($resale != null) {
            $where .= ' AND c.cod_franquia = '.$resale;
        }

        if ($period == 2) {
            $where .= " AND h.return_date <= '".date('Y-m-d')."'";
        } elseif ($period == 1) {
            //$where .= " AND (h.call_status = 2 OR h.id IS NULL) ";
            $where .= " AND (h.id IS NULL OR h.return_date <= '".date('Y-m-d')."') ";
        }

        $sql = 'SELECT *
						FROM incomes Income
						LEFT JOIN charges_histories h ON h.income_id = Income.id
						INNER JOIN customers c ON c.id = Income.customer_id
						WHERE Income.vencimento '.$vencimento.' AND (Income.socio_id != 2 or Income.socio_id is null) AND Income.data_cancel = "1901-01-01" '.$where;

        $cobrancas_por_cliente = $this->Income->query($sql.' GROUP BY Income.customer_id ORDER BY RAND()');

        $dividido = count($cobrancas_por_cliente) / count($cobradores);

        if (count($cobrancas_por_cliente) < count($cobradores)) {
            $this->Session->setFlash(__("Você selecionou ".count($cobradores)." cobradores para somente ".count($cobrancas_por_cliente)." contas encontradas. O número de cobradores deve ser menor ou igual ao número de contas selecionadas."), 'default', ['class' => "alert alert-warning"]);
            $this->redirect(['action' => 'divisao_cobradores']);
        }

        $this->DistribuicaoCobranca->create();
        $data_distribuicao['DistribuicaoCobranca']['user_creator_id'] = CakeSession::read("Auth.User.id");
        $data_distribuicao['DistribuicaoCobranca']['to'] = $ate;
        $data_distribuicao['DistribuicaoCobranca']['from'] = $de;
        $data_distribuicao['DistribuicaoCobranca']['status_cliente_id'] = $sCliente;
        $data_distribuicao['DistribuicaoCobranca']['status_conta_id'] = $sContas;
        $data_distribuicao['DistribuicaoCobranca']['resale_id'] = $resale;
        $data_distribuicao['DistribuicaoCobranca']['period'] = $period;
        $data_distribuicao['DistribuicaoCobranca']['status_id'] = 1;

        $this->DistribuicaoCobranca->query('UPDATE distribuicao_cobranca SET status_id = 2 where status_id = 1 and resale_id = '.$resale);
        $this->DistribuicaoCobranca->save($data_distribuicao);

        $limit_start = 0;
        $limit_per_user = round($dividido);
        $dados = [];
        for ($i=0; $i < count($cobradores); $i++) {
            for ($a=round($limit_start); $a < round($limit_per_user); $a++) {
                if (isset($cobrancas_por_cliente[$a])) {
                    $cobrancas = $this->Income->query($sql.' and Income.customer_id = '.$cobrancas_por_cliente[$a]['Income']['customer_id'].' group by Income.id');

                    foreach ($cobrancas as $cobranca) {
                        $dados[] = ['DistribuicaoCobrancaUsuario' => ['distribuicao_cobranca_id' => $this->DistribuicaoCobranca->id, 'user_id' => $cobradores[$i]['User']['id'], 'income_id' => $cobranca['Income']['id'], 'user_creator_id' => CakeSession::read("Auth.User.id")]];
                    }
                }
            }

            $limit_start += $dividido;
            $limit_per_user += $dividido;
        }

        $this->DistribuicaoCobrancaUsuario->saveMany($dados);
    }

    public function index()
    {
        $this->Permission->check(18, "leitura") ? "" : $this->redirect("/not_allowed");

        $this->Paginator->settings = $this->paginate;

        $joins = [
            [
                'table' => 'charges_histories',
                'alias' => 'Histories',
                'type' => 'LEFT',
                'conditions' => ['DistribuicaoCobrancaUsuario.income_id = Histories.income_id', 'Histories.return_date > current_date']
            ],
            [
                'table' => 'incomes',
                'alias' => 'Incomes',
                'type' => 'INNER',
                'conditions' => ['Incomes.id = DistribuicaoCobrancaUsuario.income_id']
            ],
            [
                'table' => 'customers',
                'alias' => 'Customers',
                'type' => 'INNER',
                'conditions' => ['Customers.id = Incomes.customer_id']
            ]
        ];

        if (CakeSession::read('Auth.User.group_id') == 1 || CakeSession::read('Auth.User.group_id') == 7) {
            //$condition = ["and" => ['DistribuicaoCobranca.status_id' => 1, 'Histories.return_date is null'], "or" => []];
            $condition = ["and" => ['DistribuicaoCobranca.status_id' => 1, 'DistribuicaoCobranca.resale_id' => CakeSession::read("Auth.User.resales")], "or" => []];
        } else {
            //$condition = ["and" => ['DistribuicaoCobranca.status_id' => 1, 'Histories.return_date is null', 'DistribuicaoCobrancaUsuario.user_id' => CakeSession::read('Auth.User.id')], "or" => []];
            $condition = ["and" => ['DistribuicaoCobranca.status_id' => 1, 'DistribuicaoCobranca.resale_id' => CakeSession::read("Auth.User.resales"), 'DistribuicaoCobrancaUsuario.user_id' => CakeSession::read('Auth.User.id')], "or" => []];
        }

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Income.name LIKE' => "%".$_GET['q']."%", 'Customers.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customers.codigo_associado' => $_GET['q']]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $get_de = isset($_GET["de"]) ? $_GET["de"] : '';
        $get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';
        
        if ($get_de != "" and $get_ate != "") {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            $condition['and'] = array_merge($condition['and'], ['Income.vencimento >=' => $de, 'Income.vencimento <=' => $ate]);
        }

        $data = $this->Paginator->paginate('DistribuicaoCobrancaUsuario', $condition);

        if (CakeSession::read('Auth.User.group_id') == 1) {
            $total_registros = $this->DistribuicaoCobrancaUsuario->find('count', [
                'conditions' => $condition,
                'group' => 'Income.id',
                'joins' => $joins
            ]);

            $total_clientes = $this->DistribuicaoCobrancaUsuario->find('count', [
                'conditions' => $condition,
                'group' => 'Income.customer_id',
                'joins' => $joins
            ]);

            $valor_total = $this->DistribuicaoCobrancaUsuario->find('first', [
                'conditions' => $condition,
                'fields' => 'sum(Income.valor_total) as valor_total',
                'joins' => $joins
            ]);
        }

        /*for ($i=0; $i < count($data); $i++) {
            $retorno_sucesso = $this->ChargesHistory->find('first', ['conditions' => ['ChargesHistory.call_status' => 1, 'ChargesHistory.income_id' => $data[$i]['Income']['id'], 'date_format(ChargesHistory.created, "%Y-%m-%d")' => date('Y-m-d')]]);
            if (!empty($retorno_sucesso)) {
                unset($data[$i]);
            }
        }*/
        // $data = array_values($data);

        // pega funcao do IncomesController
        $Incomes = new IncomesController;

        $juros_multa = [];
        foreach ($data as $value) {
            $juros_multa[$value['Income']['id']] = $Incomes->calc_juros_multa($value['Income']['id']);
        }

        $action = 'Cobrança';
        $breadcrumb = ['Lista' => ''];
        $this->set(compact('data', 'juros_multa', 'total_clientes', 'total_registros', 'valor_total', 'action', 'breadcrumb'));
    }

    public function visualizar($id)
    {
        $this->Permission->check(18, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Income->id = $id;

        $this->request->data = $this->Income->read();

        $sql = 'SELECT *
						FROM incomes Income
						LEFT JOIN charges_histories h ON h.income_id = Income.id
						WHERE Income.vencimento < CURRENT_DATE AND Income.status_id = 15 AND Income.socio_id != 2 AND Income.data_cancel = "1901-01-01" AND (h.call_status = 2 OR h.id IS NULL) AND Income.customer_id IS NOT NULL ';

        $demais_pendencias = $this->Income->query($sql.'and Income.id != '.$id.' and Income.customer_id = '.$this->request->data['Income']['customer_id']);

        // pega funcao do IncomesController
        $Incomes = new IncomesController;

        $juros_multa = $Incomes->calc_juros_multa($id);

        $action = 'Cobrança';
        $breadcrumb = ['Visualizar' => '', $this->request->data['Customer']['nome_primario'] => ''];
        $this->set("form_action", "view");
        $this->set(compact('id', 'demais_pendencias', 'juros_multa', 'action', 'breadcrumb'));
    }

    public function save_historico($id)
    {
        if ($this->request->is('post')) {
            $this->ChargesHistory->create();
            $this->ChargesHistory->validates();
            $cobranca = $this->DistribuicaoCobranca->find('first', ['conditions' => ['DistribuicaoCobranca.status_id' => 1]]);
            $this->request->data['ChargesHistory']['cobranca_id'] = $cobranca['DistribuicaoCobranca']['id'];
            $this->request->data['ChargesHistory']['user_creator_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['ChargesHistory']['income_id'] = $id;

            $idContasReceber = substr($_POST['idsContasReceber'], 0, -1);

            $enviarEmail = false;
            $enviarEmailID = '';

            if (isset($this->request->data['ChargesHistory']['generate_new_income'])) {
                if ($this->request->data['ChargesHistory']['generate_new_income'] == 1) {
                    /*$data_new_income = ['Income' => ["vencimento" => $this->request->data['ChargesHistory']['due_date'],
                                                                                     "status_id" => 15,
                                                                                     "valor_bruto" => $this->request->data['ChargesHistory']['value'],
                                                                                     "valor_total" => $this->request->data['ChargesHistory']['total_value'],
                                                                                     "cobranca_id_log" => $idsContasReceber,
                                                                                     "check_cobranca" => 1,
                                                                                     "data_cobranca_criada" => date('Y-m-d'),
                                                                                     "customer_id" => $this->request->data['ChargesHistory']['customer_id'],
                                                                                     "user_creator_id" => CakeSession::read("Auth.User.id")
                                                                                    ]];

                    $this->Income->create();
                    $this->Income->save($data_new_income);

                    $this->Income->updateAll(
                        ["Income.status_id" => 19, "Income.cobranca_id" => $this->Income->id, "Income.updated" => "current_timestamp", "user_updated_id" => CakeSession::read("Auth.User.id")], //set
                        ['Income.id in ('.$idsContasReceber.')'] //where
                    );
                    $enviarEmail = true;
                    $enviarEmailID = $this->Income->id;*/

                    $update_income = [
                        'Income' => [
                            "id" => $idContasReceber,
                            "vencimento" => $this->request->data['ChargesHistory']['due_date'],
                            "valor_bruto" => $this->request->data['ChargesHistory']['value'],
                            "valor_total" => $this->request->data['ChargesHistory']['total_value'],
                            "check_cobranca" => 1,
                            "data_cobranca_criada" => date('Y-m-d'),
                        ]
                    ];

                    $this->Income->save($update_income);
                    
                    $this->request->data['ChargesHistory']['new_income_id'] = $this->Income->id;
                }
            }

            if (isset($this->request->data['ChargesHistory']['resend_billet'])) {
                if ($this->request->data['ChargesHistory']['resend_billet'] == 1) {
                    $enviarEmail = true;
                    $enviarEmailID = $id;
                }
            }

            if ($this->ChargesHistory->save($this->request->data)) {
                if ($enviarEmail) {
                    $this->enviar_email($enviarEmailID);
                }
                
                $this->Session->setFlash(__('O histórico foi salvo com sucesso'), 'default', ['class' => "alert alert-success"]);
                $this->redirect(['action' => 'historico/'.$id]);
            } else {
                $this->Session->setFlash(__('O histórico não pode ser salvo, Por favor tente de novo.'), 'default', ['class' => "alert alert-danger"]);
            }
        }
    }

    public function historico($id)
    {
        $this->Permission->check(18, "escrita") ? "" : $this->redirect("/not_allowed");

        $data = $this->ChargesHistory->find('all', ['conditions' => ['ChargesHistory.income_id' => $id], 'order' => ['ChargesHistory.created' => 'desc']]);

        $action = 'Cobrança';
        $breadcrumb = ['Visualizar' => '', 'Novo histórico' => ''];
        $this->set("form_action", "../cobrancas/historico/".$id);
        $this->set(compact('id', 'data', 'action', 'breadcrumb'));
    }

    public function enviar_email($id)
    {
        $this->autoRender = false;

        $this->Income->id = $id;
        $mensalidade = $this->Income->read();
        
        $this->send_all_emails($mensalidade, $id);

        //$this->send_all_sms($mensalidade, 'negociado');
    }

    public function send_all_emails($mensalidade, $id)
    {
        if ($mensalidade['Customer']['email'] != '') {
            $dados = ['viewVars' => ['nome_fantasia'  => $mensalidade['Customer']['nome_secundario'],
                'email' => $mensalidade['Customer']['email'],
                'cnpj' => $mensalidade['Customer']['documento'],
                'codigo_associado' => $mensalidade['Customer']['codigo_associado'],
                'link' => Configure::read('Areadoassociado.link').'financeiro/gerar_boleto/'.base64_encode($id)
            ],
                'template' => 'boleto_negociado',
                'layout'   => 'new_layout',
                'subject'  => 'Credcheck - Distribuidor Autorizado Serasa Experian',
                'config'   => 'fatura'
            ];

            if (!$this->Email->send($dados)) {
                $this->Session->setFlash(__('Email não pôde ser enviado com sucesso'), 'default', ['class' => "alert alert-danger"]);
                $this->redirect($this->referer());
            }
        }

        if ($mensalidade['Customer']['email1'] != '') {
            $dados = ['viewVars' => ['nome_fantasia'  => $mensalidade['Customer']['nome_secundario'],
                'email' => $mensalidade['Customer']['email1'],
                'cnpj' => $mensalidade['Customer']['documento'],
                'codigo_associado' => $mensalidade['Customer']['codigo_associado'],
                'link' => Configure::read('Areadoassociado.link').'financeiro/gerar_boleto/'.base64_encode($id)
            ],
                'template' => 'boleto_negociado',
                'layout'   => 'new_layout',
                'subject'  => 'Credcheck - Distribuidor Autorizado Serasa Experian',
                'config'   => 'fatura'
            ];

            if (!$this->Email->send($dados)) {
                $this->Session->setFlash(__('Email não pôde ser enviado com sucesso'), 'default', ['class' => "alert alert-danger"]);
                $this->redirect($this->referer());
            }
        }

        if ($mensalidade['Customer']['email2'] != '') {
            $dados = ['viewVars' => ['nome_fantasia'  => $mensalidade['Customer']['nome_secundario'],
                'email' => $mensalidade['Customer']['email2'],
                'cnpj' => $mensalidade['Customer']['documento'],
                'codigo_associado' => $mensalidade['Customer']['codigo_associado'],
                'link' => Configure::read('Areadoassociado.link').'financeiro/gerar_boleto/'.base64_encode($id)
            ],
                'template' => 'boleto_negociado',
                'layout'   => 'new_layout',
                'subject'  => 'Credcheck - Distribuidor Autorizado Serasa Experian',
                'config'   => 'fatura'
            ];

            if (!$this->Email->send($dados)) {
                $this->Session->setFlash(__('Email não pôde ser enviado com sucesso'), 'default', ['class' => "alert alert-danger"]);
                $this->redirect($this->referer());
            }
        }

        if ($mensalidade['Customer']['email3'] != '') {
            $dados = ['viewVars' => ['nome_fantasia'  => $mensalidade['Customer']['nome_secundario'],
                'email' => $mensalidade['Customer']['email3'],
                'cnpj' => $mensalidade['Customer']['documento'],
                'codigo_associado' => $mensalidade['Customer']['codigo_associado'],
                'link' => Configure::read('Areadoassociado.link').'financeiro/gerar_boleto/'.base64_encode($id)
            ],
                'template' => 'boleto_negociado',
                'layout'   => 'new_layout',
                'subject'  => 'Credcheck - Distribuidor Autorizado Serasa Experian',
                'config'   => 'fatura'
            ];

            if (!$this->Email->send($dados)) {
                $this->Session->setFlash(__('Email não pôde ser enviado com sucesso'), 'default', ['class' => "alert alert-danger"]);
                $this->redirect($this->referer());
            }
        }
    }

    public function send_all_sms($mensalidade, $tipo)
    {
        // pega funcao do IncomesController
        $Incomes = new IncomesController;

        $juros_multa = $Incomes->calc_juros_multa($mensalidade['Income']['id']);

        $dados = ['vencimento' => $juros_multa['data_venc'],
            'valor_multa' => $juros_multa['valor_multa'],
            'valor_juros' => $juros_multa['valor_juros'],
            'valor_juros_dia' => $juros_multa['valor_juros_dia'],
            'valor' => $mensalidade['Income']['valor_total_nao_formatado'],
            'income_id' => $mensalidade['Income']['id'],
            'cobrar_juros' => $mensalidade['Customer']['cobrar_juros'],
            'nome_fantasia' => $mensalidade['Customer']['nome_primario'],
            'cnpj' => $mensalidade['Customer']['documento'],
            'endereco' => $mensalidade['Customer']['endereco'],
            'bairro' => $mensalidade['Customer']['bairro'],
            'cidade' => $mensalidade['Customer']['cidade'],
            'estado' => $mensalidade['Customer']['estado'],
            'cep' => $mensalidade['Customer']['cep']
        ];

        $linha = $this->Boleto->gerar($dados, $this->base, $mensalidade['Income']['id'], 'retorna_linha_digitavel');

        if ($mensalidade['Customer']['celular'] != '') {
            $this->Sms->send(['celular' => $mensalidade['Customer']['celular'], 'linha' => $linha], $tipo);
        }

        if ($mensalidade['Customer']['celular1'] != '') {
            $this->Sms->send(['celular' => $mensalidade['Customer']['celular1'], 'linha' => $linha], $tipo);
        }

        if ($mensalidade['Customer']['celular2'] != '') {
            $this->Sms->send(['celular' => $mensalidade['Customer']['celular2'], 'linha' => $linha], $tipo);
        }

        if ($mensalidade['Customer']['celular3'] != '') {
            $this->Sms->send(['celular' => $mensalidade['Customer']['celular3'], 'linha' => $linha], $tipo);
        }

        if ($mensalidade['Customer']['celular4'] != '') {
            $this->Sms->send(['celular' => $mensalidade['Customer']['celular4'], 'linha' => $linha], $tipo);
        }

        if ($mensalidade['Customer']['celular5'] != '') {
            $this->Sms->send(['celular' => $mensalidade['Customer']['celular5'], 'linha' => $linha], $tipo);
        }
    }

    public function delete_historico($id, $historico_id)
    {
        $this->Permission->check(18, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->ChargesHistory->id = $historico_id;
        $this->request->data = $this->ChargesHistory->read();

        $this->request->data['ChargesHistory']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['ChargesHistory']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->ChargesHistory->save($this->request->data)) {
            $this->Session->setFlash(__('O histórico foi excluido com sucesso'), 'default', ['class' => "alert alert-success"]);
            $this->redirect(['action' => 'historico/'.$id]);
        }
    }
}
