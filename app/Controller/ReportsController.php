<?php
App::import('Controller', 'Incomes');
class ReportsController extends AppController {
	public $helpers = ['Html', 'Form'];
	public $components = ['Paginator', 'Permission', 'ExcelGenerator', 'Robo'];
	public $uses = ['Income', 'Status', 'Customer', 'Seller', 'PlanCustomer', 'BankAccount', 'Outcome', 'CadastroPefin', 'CustomerPefin', 'ChargesHistory', 'Billing', 'Plan', 'CepbrEstado', 'CepbrCidade', 'NovaVidaLogConsulta', 'Product', 'LoginConsulta', 'MovimentacaoCredor'];

	public $paginate = [
		'CadastroPefin'				=> ['limit' => 20, 'order' => ['Customer.codigo_associado' => 'asc']],
		'Income' 							=> ['limit' => 10, 'order' => ['Income.id' => 'desc'], 'fields' => ['Income.*', 'Customer.*']],
		'NovaVidaLogConsulta' => [
            'fields' => [
                "GROUP_CONCAT(LogItens.campo ORDER BY LogItens.tipo_campo SEPARATOR '<br>') as campo",
                "GROUP_CONCAT(LogItens.tipo_campo ORDER BY LogItens.tipo_campo SEPARATOR '<br>') as tipo_campo",
                "(select GROUP_CONCAT(f.name SEPARATOR ' + ')
				  from nova_vida_log_consulta_features lf
					inner join features f on f.id = lf.feature_id
				  where lf.nova_vida_log_consulta_id = NovaVidaLogConsulta.id) as features",
                'NovaVidaLogConsulta.customer_id',
                'NovaVidaLogConsulta.valor',
                'NovaVidaLogConsulta.created',
                'NovaVidaLogConsulta.id',
                'Customer.codigo_associado',
                'Customer.nome_secundario',
                'Product.name',
            ],
            'joins' => [
                [
                    'table' => 'nova_vida_log_consulta_itens',
                    'alias' => 'LogItens',
                    'type' => 'LEFT',
                    'conditions' => [
                        'LogItens.log_consulta_id = NovaVidaLogConsulta.id'
                    ],
                ]
            ],
            'group' => ['NovaVidaLogConsulta.id'],
            'order' => ['NovaVidaLogConsulta.created' => 'desc', 'Product.name' => 'asc'],
            'limit' => 10,
            'paramType' => 'querystring'
        ],
		'Customer' 						=> ['limit' => 20, 'order' => ['Customer.codigo_associado' => 'asc']],
		'LoginConsulta'				=> ["limit" => -1, 'order' => ["Customer.nome_secundario" => "asc" ]]
	];

	public function beforeFilter() { 
		parent::beforeFilter(); 
	}

	public function bloqueio_diario() {
		$this->Permission->check(56, "leitura") ? "" : $this->redirect("/not_allowed");

		$condition = ["and" => ['MovimentacaoCredor.status_id' => 4], "or" => []];

		$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
		$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';
		$data = [];

		if($get_de != "" and $get_ate != ""){
			$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
			$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

			$condition['and'] = array_merge($condition['and'], ['MovimentacaoCredor.created >=' => $de.' 00:00:00', 'MovimentacaoCredor.created <=' => $ate.' 23:59:59']);
		
			$data = $this->MovimentacaoCredor->find('all', [
				'conditions' => $condition, 
				'recursive' => 2
			]);

			if (isset($_GET['excel'])) {
				$nome = 'bloquei_diario';

				$this->ExcelGenerator->gerarExcelBloqueioDiario($nome, $data);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}
		} else {
			if (($get_de == "" or $get_ate == "") and !($get_de == "" and $get_ate == "")) {
				$this->Flash->set(__('As duas datas precisam ser preenchidas.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$action = 'Bloqueio diário';
        $breadcrumb = ['Relatórios' => '', 'Bloqueio diário' => ''];
		$this->set(compact('data', 'action', 'breadcrumb'));
	}

	public function clientes_bloquear() {
		$this->Permission->check(27, "leitura") ? "" : $this->redirect("/not_allowed");

		$data = [];
		$condition = ["and" => [
			//'Income.vencimento <=' => date('Y-m-d', strtotime('-1 days')), 
			//'Income.status_id' => 15, 
			'Income.status_id in (15)',
			'Income.customer_id is not null'
		], "or" => []];

		if(!empty($_GET['q'])){
			$condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.email LIKE' => "%".$_GET['q']."%", 'Customer.documento LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%"]);
		}

		if(!empty($_GET["t"])){
			$condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET['t']]);
		}

		$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
		$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

		if (!empty($_GET["t"]) and $get_de != "" and $get_ate != "") {

			if($get_de != "" and $get_ate != ""){
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$condition['and'] = array_merge($condition['and'], ['Income.vencimento >=' => $de.' 00:00:00', 'Income.vencimento <=' => $ate.' 23:59:59']);
			} else {
				if (($get_de == "" or $get_ate == "") and !($get_de == "" and $get_ate == "")) {
					$this->Flash->set(__('As duas datas precisam ser preenchidas.'), 'default', array('class' => "alert alert-danger"));
				}
			}

			$data = $this->Income->find('all', [
				'fields' => [
					'Customer.codigo_associado',
					'Customer.nome_secundario',
					'Customer.email',
					'Customer.created',
					'Customer.id',
					'Status.*',
				],
				'conditions' => $condition, 
				'joins' => [
                    [
                        'table' => 'customers',
                        'alias' => 'Customer',
                        'type' => 'INNER',
                        'conditions' => ['Customer.id = Income.customer_id']
                    ],
                    [
                        'table' => 'statuses',
                        'alias' => 'Status',
                        'type' => 'INNER',
                        'conditions' => ['Status.id = Customer.status_id']
                    ],
                ],
				'group' => ['Customer.id'], 
				'recursive' => -1
			]);
		}

		$status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

		$action = 'Clientes para bloquear';
        $breadcrumb = ['Relatórios' => '', 'Clientes para bloquear' => ''];
		$this->set(compact('data', 'status', 'action', 'breadcrumb'));
	}

	public function exportar_clientes_bloquear(){
		ini_set('memory_limit', '-1');
		set_time_limit(0);
		$this->autoRender = false;
		$condition = ["and" => [
			//'Income.vencimento <' => date('Y-m-d', strtotime('-1 days')), 
			//'Income.status_id' => 15, 
			'Income.status_id in (15)',
			'Income.customer_id is not null'], "or" => []];
			
		if(!empty($_GET['q'])){
				$condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.email LIKE' => "%".$_GET['q']."%", 'Customer.documento LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%"]);
		}

		if(!empty($_GET["t"])){
			$condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET['t']]);
		}

		$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
		$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

		if($get_de != "" and $get_ate != ""){
			$dataIni = explode('/', $_GET["de"]);

			$anoDe = $dataIni[2];
			$mesDe = $dataIni[1];
			$diaDe = $dataIni[0];

			$dataFim = explode('/', $_GET["ate"]);

			$anoAte = $dataFim[2];
			$mesAte = $dataFim[1];
			$diaAte = $dataFim[0];

			$de = $anoDe.'-'.$mesDe.'-'.$diaDe;
			$ate = $anoAte.'-'.$mesAte.'-'.$diaAte;

			$condition['and'] = array_merge($condition['and'], ['Income.vencimento >=' => $de.' 00:00:00', 'Income.vencimento <=' => $ate.' 23:59:59']);
		} else {
			if (($get_de == "" or $get_ate == "") and !($get_de == "" and $get_ate == "")) {
				$this->Flash->set(__('As duas datas precisam ser preenchidas.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$data = $this->Income->find('all', ['conditions' => $condition, 'group' => ['Customer.id'], 'recursive' => 2]);

		$nome = 'clientes_bloquear_'.date('d_m_Y_H_i_s');

		$this->ExcelGenerator->gerarExcelClientesBloquear($nome, $data);
		$this->redirect("/files/excel/".$nome.".xlsx");
	}

	/*******************
				CLIENTES			
	********************/
		public function clientes() {
			$this->Permission->check(27, "leitura") ? "" : $this->redirect("/not_allowed");

			$data = [];
			$planos = [];
			$condition = '';
			$having = '';

			if(!empty($_GET['q'])){
				$condition .= " AND (
								Customer.nome_primario LIKE '%".$_GET['q']."%'
								OR Customer.nome_secundario LIKE '%".$_GET['q']."%'
								OR Customer.email LIKE '%".$_GET['q']."%'
								-- OR 'Customer.documento LIKE '%".$_GET['q']."%'
								OR Customer.codigo_associado LIKE '%".$_GET['q']."%')";
			}

			if(!empty($_GET["t"])){
				$condition .= ' AND Customer.seller_id = '.$_GET['t'].'';
			}
			//Estado
			if(!empty($_GET["e"])){
				$condition .= " AND Customer.estado = '".$_GET['e']."'";
			}
			//Cidade
			if(!empty($_GET["c"])){
				$condition .= " AND Customer.cidade LIKE '%".$_GET['c']."%'";
			}
			//Plano
			if(isset($_GET["p"]) && $_GET["p"] != ""){
				$condition .= ' AND PlanCustomer.plan_id = '.$_GET['p'].'';
			}
			//status
			$stat1 = (isset($_GET["s"]) && $_GET["s"] != "") ? $_GET["s"] : '';
			$stat2 = (isset($_GET["2s"]) && $_GET["2s"] != "") ? $_GET["2s"] : '';
			$arrStats = []; 
			if ($stat1 != '' && $stat2 != '') {
				$arrStats = [$stat1, $stat2];
			} else if($stat1 == '' && $stat2 != ''){
				$arrStats = $stat2;
			} else if($stat1 != '' && $stat2 == ''){
				$arrStats = $stat1;
			}

			if(!empty($arrStats)){
				$condition .= ' AND Customer.status_id IN ('.$arrStats.')';
			}

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

			if($get_de != "" and $get_ate != ""){
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$condition .= " AND Customer.created >= '".$de." 00:00:00' AND Customer.created <= '".$ate." 23:59:59'";
			}

			$get_canc_de = isset($_GET["canc_de"]) ? $_GET["canc_de"] : '';
			$get_canc_ate = isset($_GET["canc_ate"]) ? $_GET["canc_ate"] : '';

			if($get_canc_de != "" and $get_canc_ate != ""){
				$canc_de = date('Y-m-d', strtotime(str_replace('/', '-', $get_canc_de)));
				$canc_ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_canc_ate)));

				$having .= " HAVING dataCancelamento >= '".$canc_de." 00:00:00' AND dataCancelamento <= '".$canc_ate." 23:59:59'";
			}

			if (!empty($condition) || !empty($having)) {
				
				$data = $this->Customer->query("
					SELECT `Customer`.*, `PlanCustomer`.*, `Plan`.*, `Seller`.*, `Statuses`.*,
						if(Customer.status_id = 5, (SELECT max(mc.created) FROM movimentacao_credor mc WHERE mc.customer_id = Customer.id AND mc.status_id = Customer.status_id), '') AS dataCancelamento
					FROM `customers` AS `Customer`
					LEFT JOIN `plan_customers` AS `PlanCustomer` ON (`Customer`.`id` = `PlanCustomer`.`customer_id` AND `PlanCustomer`.`status_id` = 1)
					INNER JOIN `statuses` AS `Statuses` ON (`Statuses`.`id` = `Customer`.`status_id`)
					LEFT JOIN `plans` AS `Plan` ON (`Plan`.`id` = `PlanCustomer`.`plan_id`)
					LEFT JOIN `resales` AS `Resale` ON (`Customer`.`cod_franquia` = `Resale`.`id`)
					LEFT JOIN `statuses` AS `Status` ON (`Customer`.`status_id` = `Status`.`id`)
					LEFT JOIN `sellers` AS `Seller` ON (`Customer`.`seller_id` = `Seller`.`id`)
					LEFT JOIN `activity_areas` AS `ActivityArea` ON (`Customer`.`activity_area_id` = `ActivityArea`.`id`)
					LEFT JOIN `plan_customers` AS `PlanoAtivo` ON (`PlanoAtivo`.`customer_id` = `Customer`.`id` AND `PlanoAtivo`.`status_id` = 1)
					WHERE `Customer`.`data_cancel` = '1901-01-01 00:00:00'
						$condition
					GROUP BY `Customer`.`id`
						$having
					ORDER BY `Customer`.`created` ASC ");

				
			}

			$vendedores = $this->Seller->find('all', ['conditions' => ['Seller.status_id' => 1], 'order' => ['Seller.nome_fantasia' => 'asc']]);
			$plans = $this->Plan->find('list');
			$statuses = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2]]);
			
			$estados = ["AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amapá","BA"=>"Bahia","CE"=>"Ceará","DF"=>"Distrito Federal","ES"=>"Espírito Santo","GO"=>"Goiás","MA"=>"Maranhão","MT"=>"Mato Grosso","MS"=>"Mato Grosso do Sul","MG"=>"Minas Gerais","PA"=>"Pará","PB"=>"Paraíba","PR"=>"Paraná","PE"=>"Pernambuco","PI"=>"Piauí","RJ"=>"Rio de Janeiro","RN"=>"Rio Grande do Norte","RO"=>"Rondônia","RS"=>"Rio Grande do Sul","RR"=>"Roraima","SC"=>"Santa Catarina","SE"=>"Sergipe","SP"=>"São Paulo","TO"=>"Tocantins"];

			if(isset($_GET['excel'])){
				$nome = 'clientes';	
				$this->ExcelGenerator->gerarExcelClientes($nome, $data);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$action = 'Clientes';
	        $breadcrumb = ['Relatórios' => '', 'Clientes' => ''];
			$this->set(compact('data', 'vendedores', 'plans', 'statuses', 'estados', 'action', 'breadcrumb'));
		}

	/***************
				FLUXO			
	****************/
		public function fluxo_caixa(){
			$this->Permission->check(30, "leitura") ? "" : $this->redirect("/not_allowed");

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

			$data = [];
			$conta = [];
			$exportar = false;
			$saldo = 0;
			if (!empty($_GET["t"]) and $get_de != "" and $get_ate != "") {
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$this->BankAccount->id = $_GET["t"];
				$conta = $this->BankAccount->find('first', ['conditions' => ['BankAccount.start_date <=' => $de]]);

				$de_anterior = date('Y-m-d', strtotime('-1 month '.$de));
				$ate_anterior = date('Y-m-t', strtotime('-1 month '.$ate));

				$buscaValorPagoDe = $this->Outcome->find('all', ['conditions' => ["Outcome.data_pagamento between '{$de_anterior}' and '{$ate_anterior}'", 'Outcome.status_id' => 13], 'fields' => 'SUM(valor_pago) as valor_pago']);
				$buscaValorRecebidoDe = $this->Income->find('all', ['conditions' => ["Income.data_pagamento between '{$de_anterior}' and '{$ate_anterior}'", 'Income.status_id' => 17], 'fields' => 'SUM(valor_pago) as valor_recebido']);
				$saldo = (!empty($conta) ? $conta['BankAccount']['initial_balance_not_formated'] : 0) + ($buscaValorRecebidoDe[0][0]['valor_recebido'] - $buscaValorPagoDe[0][0]['valor_pago']);

				// $saldo = $saldo_anterior[0][0]['saldo'] + (!empty($conta) ? $conta['BankAccount']['initial_balance_not_formated'] : 0);

				$data = $this->Outcome->query("
					SELECT s.name as status, b.name, 'conta a pagar' AS tipo, o.data_pagamento, o.valor_pago as valor_total, '-' AS operador, o.name AS nome_conta, f.nome_fantasia AS nome
					FROM outcomes o
					LEFT JOIN suppliers f ON f.id = o.supplier_id
					INNER JOIN statuses s ON s.id = o.status_id
					INNER JOIN bank_accounts b ON b.id = o.bank_account_id
					WHERE o.bank_account_id = ".$_GET["t"]." AND o.data_pagamento BETWEEN '".$de."' AND '".$ate."' 
						 AND o.data_cancel = '1901-01-01'
						 AND o.status_id = 13
					UNION
					SELECT s.name as status, b.name, 'conta a receber' AS tipo, i.data_pagamento, i.valor_pago as valor_total, '+' AS operador, i.name AS nome_conta, c.nome_secundario AS nome
					FROM incomes i
					LEFT JOIN customers c ON c.id = i.customer_id
					INNER JOIN statuses s ON s.id = i.status_id
					INNER JOIN bank_accounts b ON b.id = i.bank_account_id
					WHERE i.bank_account_id = ".$_GET["t"]." AND i.data_pagamento BETWEEN '".$de."' AND '".$ate."' 
						AND i.status_id = 17
						AND i.data_cancel = '1901-01-01'
					ORDER BY data_pagamento");
				$exportar = true;
			}

			$conta_bancaria = $this->BankAccount->find('all', ['conditions' => ['BankAccount.status_id' => 1], 'order' => ['BankAccount.name']]);

			if (isset($_GET['exportar'])) {
				$nome = 'fluxo_caixa_'.$de.'_'.$ate;

				$this->ExcelGenerator->gerarExcelFluxo($nome, $data, $conta);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$action = 'Fluxo de caixa';
			$this->set(compact('conta_bancaria', 'data', 'conta', 'exportar', 'saldo', 'action'));
		}

	/*****************
				DESPESA			
	******************/
		public function despesas(){
			$this->Permission->check(31, "leitura") ? "" : $this->redirect("/not_allowed");

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

			$data = [];
			$conta = [];
			$exportar = false;

			if (!empty($_GET["t"]) and $get_de != "" and $get_ate != "") {
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$this->BankAccount->id = $_GET["t"];
				$conta = $this->BankAccount->read();

				$data = $this->Outcome->query("SELECT s.name as status, s.label as status_label, b.name, 'conta a pagar' AS tipo, o.vencimento, o.valor_total, '-' AS operador
																			 FROM outcomes o
																			 INNER JOIN statuses s ON s.id = o.status_id
																			 INNER JOIN bank_accounts b ON b.id = o.bank_account_id
																			 WHERE o.bank_account_id = ".$_GET["t"]." AND o.vencimento BETWEEN '".$de."' AND '".$ate."' 
																			 ORDER BY vencimento");
				$exportar = true;
			}

			$conta_bancaria = $this->BankAccount->find('all', ['conditions' => ['BankAccount.status_id' => 1], 'order' => ['BankAccount.name']]);

			if (isset($_GET['exportar'])) {
				$nome = 'despesas_'.$de.'_'.$ate;

				$this->ExcelGenerator->gerarExcelDespesas($nome, $data, $conta);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$action = 'Despesas';
			$this->set(compact('conta_bancaria', 'data', 'conta', 'exportar', 'action'));
		}

	/*****************
				RECEITA			
	******************/
		public function receitas(){
			$this->Permission->check(32, "leitura") ? "" : $this->redirect("/not_allowed");

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

			$data = [];
			$conta = [];
			$exportar = false;

			if (!empty($_GET["t"]) and $get_de != "" and $get_ate != "") {
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$this->BankAccount->id = $_GET["t"];
				$conta = $this->BankAccount->read();

				$data = $this->Outcome->query("
					SELECT s.name as status, s.label as status_label, b.name, 'conta a receber' AS tipo, i.vencimento, i.valor_total, '+' AS operador
					FROM incomes i
				 	INNER JOIN statuses s ON s.id = i.status_id
				 	INNER JOIN bank_accounts b ON b.id = i.bank_account_id
				 	WHERE i.bank_account_id = ".$_GET["t"]." AND i.vencimento BETWEEN '".$de."' AND '".$ate."' 
				 	ORDER BY vencimento");
				$exportar = true;
			}

			$conta_bancaria = $this->BankAccount->find('all', ['conditions' => ['BankAccount.status_id' => 1], 'order' => ['BankAccount.name']]);

			if (isset($_GET['exportar'])) {
				$nome = 'receitas_'.$de.'_'.$ate;

				$this->ExcelGenerator->gerarExcelReceitas($nome, $data, $conta);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$action = 'Receitas';
	    	$breadcrumb = ['Relatórios' => '', 'Receitas' => ''];
			$this->set(compact('conta_bancaria', 'data', 'conta', 'exportar', 'action', 'breadcrumb'));
		}

	/***************************************
				NEGATIVAÇÃO AGUARDANDO BAIXA			
	****************************************/
		public function aguardando_baixa(){
			$this->Permission->check(32, "leitura") ? "" : $this->redirect("/not_allowed");
			$this->Paginator->settings = $this->paginate;

			$condition = ['and' => ['CadastroPefin.status_id' => 33], 'or' => []];

			$data = $this->Paginator->paginate('CadastroPefin', $condition);

			$action = "Lotes aguardando baixa";
			$breadcrumb = ['Pefin' => '', 'Lotes aguardando baixa' => ''];
			$this->set(compact('data', 'action', 'breadcrumb'));
		}

	/*****************************
				NEGATIVAÇÃO CLIENTE			
	******************************/
		public function negativacoes_cliente(){
			$this->Permission->check(33, "leitura") ? "" : $this->redirect("/not_allowed");
			$this->Paginator->settings = $this->paginate;

			$condition = ['and' => ['CustomerPefin.status_id' => 37, 'Customer.cod_franquia' => CakeSession::read("Auth.User.resales")], 'or' => []];

			if(isset($_GET['q']) and $_GET['q'] != ""){
				$condition['or'] = array_merge($condition['or'], ['CustomerPefin.nosso_numero LIKE' => "%".$_GET['q']."%", 'NaturezaOperacao.nome' => "%".$_GET['q']."%"]);
			}

			$data = $this->CustomerPefin->find('all', ['conditions' => $condition]);

			$action = "Negativações cliente";
			$breadcrumb = ['Pefin' => '', 'Negativações cliente' => ''];
			$this->set(compact('data', 'action', 'breadcrumb'));
		}

		public function insert_pefin(){
			$this->Permission->check(33, "escrita") ? "" : $this->redirect("/not_allowed");

			$ids = substr($_GET['id'], 0, -1);
			
			
			$customer_pefin = $this->CustomerPefin->find('all', ['conditions' => ['CustomerPefin.id in ('.$ids.')']]);

			$save = [];

			foreach ($customer_pefin as $pefin) {
				$save['CadastroPefin'] = array_merge($pefin['CustomerPefin'], 
							['customer_id' => 4294, 
							'nome' => $pefin['Customer']['nome_primario'] != '' ? $pefin['Customer']['nome_primario'] : $pefin['Customer']['nome_secundario'], 
							'documento' => $pefin['Customer']['documento'], 
							'tipo_pessoa' => 1, 
							'endereco' => $pefin['Customer']['endereco'], 
							'numero' => $pefin['Customer']['numero'] ? $pefin['Customer']['numero'] : 0, 
							'complemento' => $pefin['Customer']['complemento'], 
							'bairro' => $pefin['Customer']['bairro'] ? $pefin['Customer']['bairro'] : '-', 
							'cep' => $pefin['Customer']['cep'] ? $pefin['Customer']['cep'] : '-', 
							'cidade' => $pefin['Customer']['cidade'] ? $pefin['Customer']['cidade'] : '-', 
							'status_id' => 22, 
							'estado' => $pefin['Customer']['estado'], 
							'user_creator_id' => CakeSession::read("Auth.User.id")]);

				// Se a Natureza for "Dividas Cheq" não precisa validar já que os campos não vão aparecer pro usuario
				if ($save['CadastroPefin']['natureza_operacao_id'] != 23) {
					unset($this->CadastroPefin->validate['num_banco']);
					unset($this->CadastroPefin->validate['num_agencia']);
					unset($this->CadastroPefin->validate['num_conta_corrente']);
					unset($this->CadastroPefin->validate['num_cheque']);
					unset($this->CadastroPefin->validate['alinea']);
				} else {
					unset($this->CadastroPefin->validate['nosso_numero']);
					unset($this->CadastroPefin->validate['numero_titulo']);
				}

				unset($save['CadastroPefin']['id']);
				$this->CadastroPefin->create();
				if (!$this->CadastroPefin->save($save)) {
					debug($this->CadastroPefin->validationErrors);die(); 
				}
			}
			
			$this->CustomerPefin->updateAll(
				['CustomerPefin.status_id' => 38, "CustomerPefin.user_updated_id" => CakeSession::read("Auth.User.id"), 'CustomerPefin.updated' => 'now()'], //set
				['CustomerPefin.id in ('.$ids.')'] //where
			);

			$this->Flash->set(__('As negativações foram geradas com sucesso'), ['params' => ['class' => "alert alert-success"]]);
			$this->redirect(['action' => 'negativacoes_cliente']);
		}

	/*******************
				COBRANÇA			
	********************/
		public function cobrancas(){
			$this->Permission->check(36, "leitura") ? "" : $this->redirect("/not_allowed");
			$this->Paginator->settings = $this->paginate;

			$condition = ["and" => ["Income.cobranca_id_log > 0"], "or" => []];

			if(isset($_GET["t"]) and $_GET["t"] != ""){
				$condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
			}

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';
			
			if($get_de != "" and $get_ate != ""){
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$condition['and'] = array_merge($condition['and'], ['Income.vencimento >=' => $de, 'Income.vencimento <=' => $ate]);
			}

			$this->Income->unbindModel(['hasOne' => ['CnabItem', 'CnabItemSicoob']], false);
			$data = $this->Paginator->paginate("Income", $condition);

			$total_clientes = $this->Income->find("count", ["conditions" => Hash::merge($condition, ["Income.customer_id IS NOT NULL", "Income.data_cancel" => "1901-01-01 00:00:00"]), 'recursive' => -1]);

			$total_pago = $this->Income->find("all", ["conditions" => $condition, "fields" => "sum(Income.valor_pago) as valor_pago", 'recursive' => -1]);

			$total_cobrancas = $this->Income->find("count", ["conditions" => $condition, 'recursive' => -1]);

			$valor_total = $this->Income->find("all", ["conditions" => $condition, "fields" => "sum(Income.valor_total) as valor_total", 'recursive' => -1]);

			$status = $this->Status->find("all", ["conditions" => ["Status.categoria" => 5]]);

			$Incomes = new IncomesController;

			$juros_multa = [];
			foreach ($data as $value) {
				$juros_multa[$value['Income']['id']] = $Incomes->calc_juros_multa($value['Income']['id']);
			}

			$action = 'Cobranças';
	        $breadcrumb = ['Relatórios' => '', 'Cobranças' => ''];
			$this->set(compact("data", "total_clientes", "total_pago", "total_cobrancas", "valor_total", "status", "juros_multa", "action", "breadcrumb"));
		}

	/*************
				TWW			
	**************/
		public function tww_export(){
			$this->Permission->check(38, "leitura") ? "" : $this->redirect("/not_allowed");
			$buscar = false;

			$condition = ["and" => ['Customer.status_id != ' => 5], "or" => ['Customer.celular != ""', 'Customer.celular1 != ""', 'Customer.celular2 != ""', 'Customer.celular3 != ""', 'Customer.celular4 != ""', 'Customer.celular5 != ""']];

			/*if(!empty($_GET["r"])){
				if ($_GET["r"] == 'igual') {
					if(!empty($_GET["f"])){
						$condition['and'] = array_merge($condition['and'], ['Income.vencimento' => $_GET["f"]]);
						$buscar = true;
					}
				} elseif ($_GET["r"] == 'menor') {
					if(!empty($_GET["f"])){
						$condition['and'] = array_merge($condition['and'], ['Income.vencimento <= ' => $_GET["f"]]);
						$buscar = true;
					}
				} else {
					$condition['and'] = array_merge($condition['and'], ['Income.vencimento <= ' => date("Y-m-d")]);
					$buscar = true;
				}
			}*/

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';
			
			if($get_de != "" and $get_ate != ""){
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['de'])));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['ate'])));

				$condition['and'] = array_merge($condition['and'], ["Income.vencimento between '$de' and '$ate'"]);
			}

			if(!empty($_GET["sc"])){
				$condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET["sc"]]);
				$buscar = true;
			}

			if(!empty($_GET["s"])){
				$condition['and'] = array_merge($condition['and'], ['Income.status_id' => $_GET["s"]]);
				$buscar = true;
			}

			if (!empty($_GET["q"])) {
				$grupo = $_GET["q"];
			} else {
				$buscar = false;
				$grupo = "";
				$this->Flash->set(__('O nome do grupo deve ser preenchido'), 'default', array('class' => "alert alert-danger"));
			}

			$data = [];
			if ($buscar) {
				$this->Income->unbindModel(['belongsTo' => ["BankAccount", "Revenue", "CostCenter", "Billing", "BillingMonthlyPayment"]]);
				$data = $this->Income->find('all', ['conditions' => $condition, 'order' => ['Customer.nome_primario' => 'asc'], 'group' => ['Income.customer_id']]);
			} 

			$faturamentos = $this->Billing->find('all', ['conditions' => ['Billing.status_id' => 1], 'order' => ['Billing.id' => 'desc']]);
			$status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 5], 'order' => 'Status.name']);
			$status_cliente = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

			if (isset($_GET['exportar'])) {
				$nome = 'clientes_tww_'.date('d_m_Y_H_i_s').'.csv';

				$this->ExcelGenerator->gerarExcelTww($nome, $data, $grupo);

				$this->redirect("/files/excel/".$nome);
			}

			$this->set(compact('buscar', 'faturamentos', 'status', 'data', 'status_cliente'));
		}

	/***********************
				INADIMPLENTES			
	************************/
		public function inadimplentes(){
			$this->Permission->check(39, "leitura") ? "" : $this->redirect("/not_allowed");
			$data = [];
			$buscar = false;
			$having = '';
			$where = '';

			if(!empty($_GET["p"])){
				$valor_ini = !empty($_GET['valor_ini']) ? $this->Income->priceFormatBeforeSave($_GET['valor_ini']) : 0;
				if ($_GET["p"] == 'acima') {
					$having = 'having total > '.$valor_ini;
				} else if ($_GET["p"] == 'entre') {
					$valor_fim = !empty($_GET['valor_ini']) ? $this->Income->priceFormatBeforeSave($_GET['valor_fim']) : 0;
					$having = 'having total between '.$valor_ini.' and '.$valor_fim;
				}
				$buscar = true;
			}

			if (isset($_GET['e'])) {
				if ($_GET['e'] != "") {
					$where .= " and Customer.estado = '".$_GET['e']."'";
				}
				$buscar = true;
			}

			if (!empty($_GET['c'])) {
				$where .= " and Customer.cidade = '".$_GET['c']."'";
				$buscar = true;
			}

			if ($buscar) {
				$data = $this->Income->query("SELECT SUM(Income.valor_total) AS total, Income.*, Customer.*
																			FROM incomes AS Income
																			LEFT JOIN customers AS Customer ON (Income.customer_id = Customer.id)
																			WHERE Income.status_id = 15 AND Income.customer_id IS NOT NULL AND Income.data_cancel = '1901-01-01 00:00:00' $where
																			GROUP BY Income.customer_id
																			$having
																			ORDER BY total ASC");
			}

			
			$total_valores = 0;
			foreach ($data as  $value) {		
				$total_valores += $value[0]["total"]; 
			} 

			$estados = $this->CepbrEstado->find('list');

			if(isset($_GET['excel'])){
				$nome = 'Inadimplentes';	
				$this->ExcelGenerator->gerarExcelInadimplentes($nome, $data, $total_valores);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$action = 'Clientes inadimplentes';
	        $breadcrumb = ['Relatórios' => '', 'Clientes inadimplentes' => ''];
			$this->set(compact('buscar', 'status', 'data', 'estados', 'total_valores', 'action', 'breadcrumb'));
		}

		public function get_cidade(){
			$this->autoRender = false;
			$this->layout = 'ajax';

			$cidade = $this->CepbrCidade->find('all', ['conditions' => ['CepbrCidade.uf' => $_POST['estado']], 'recursive' => -1]);

			echo json_encode($cidade);
		}

	/*******************
				NOVA VIDA			
	********************/
		public function nova_vida(){
			$this->Permission->check(40, "leitura") ? "" : $this->redirect("/not_allowed");
			$this->Paginator->settings = $this->paginate;

			$exportar = false;

			$condition = ['and' => [], 'or' => []];

			if(isset($_GET['q']) and $_GET['q'] != ""){
				$condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%"]);
				$exportar = true;
			}

			if(isset($_GET["p"]) and $_GET["p"] != ""){
				$condition['and'] = array_merge($condition['and'], ['NovaVidaLogConsulta.product_id' => $_GET["p"]]);
				$exportar = true;
			}
			//Busca por Código do Associado
			if(isset($_GET["c"]) and $_GET["c"] != ""){
				$condition['and'] = array_merge($condition['and'], ['Customer.codigo_associado' => $_GET["c"]]);
				$exportar = true;
			}

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

			if ($get_de != "" and $get_ate != "") {
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$condition['and'] = array_merge($condition['and'], ["date_format(NovaVidaLogConsulta.created, '%Y-%m-%d') between '$de' and '$ate'"]);
				$exportar = true;
			}

			$data = [];
			if ($exportar) {
				$data = $this->Paginator->paginate('NovaVidaLogConsulta', $condition);
			}

			if (isset($_GET['exportar'])) {
				$nome = 'nova_vida_cliente_'.date('d_m_Y');

				$this->ExcelGenerator->gerarExcelNovaVida($nome, $data);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$produtos = $this->Product->find('list', ['conditions' => ['Product.status_id' => 1, 'Product.tipo' => 2], 'order' => ['Product.name' => 'asc']]);

			$this->set(compact('data', 'produtos', 'exportar'));
		}

		public function nova_vida_consolidado(){
			$this->Permission->check(41, "leitura") ? "" : $this->redirect("/not_allowed");
			$this->Paginator->settings = ['NovaVidaLogConsulta' => ['limit' => 20, 'order' => ['Product.name' => 'asc'], 'group' => ['Product.id'], 'fields' => ['count(1) as total', 'Product.name', 'NovaVidaLogConsulta.*']]];

			$exportar = false;

			$condition = ['and' => [], 'or' => []];

			if(isset($_GET['q']) and $_GET['q'] != ""){
				$condition['or'] = array_merge($condition['or'], ['Product.name LIKE' => "%".$_GET['q']."%"]);
				$exportar = true;
			}

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

			if ($get_de != "" and $get_ate != "") {
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$condition['and'] = array_merge($condition['and'], ["date_format(NovaVidaLogConsulta.created, '%Y-%m-%d') between '$de' and '$ate'"]);
				$exportar = true;
			}

			$data = [];
			if ($exportar) {
				$data = $this->Paginator->paginate('NovaVidaLogConsulta', $condition);
			}

			if (isset($_GET['exportar'])) {
				$nome = 'nova_vida_consolidado_'.date('d_m_Y');

				$this->ExcelGenerator->gerarExcelNovaVidaConsolidado($nome, $data);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$this->set(compact('data', 'produtos', 'exportar'));
		}

	/***********************
				COM DESCONTO			
	************************/
		public function clientes_desconto(){
			$this->Permission->check(42, "leitura")? "" : $this->redirect("/not_allowed");

			$condition = ["and" => ['Customer.desconto > 0'], "or" => []];

			$this->Customer->unbindModel(array('belongsTo' => array('Franquia', 'Seller', 'ActivityArea')));

			if(!empty($_GET['q'])){
				$condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.email LIKE' => "%".$_GET['q']."%", 'Customer.documento LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%", 'Customer.celular LIKE' => "%".$_GET['q']."%", 'Customer.celular1 LIKE' => "%".$_GET['q']."%", 'Customer.celular2 LIKE' => "%".$_GET['q']."%", 'Customer.celular3 LIKE' => "%".$_GET['q']."%", 'Customer.celular4 LIKE' => "%".$_GET['q']."%", 'Customer.celular5 LIKE' => "%".$_GET['q']."%"]);
			}

			$this->Paginator->settings = $this->paginate;

			if(!empty($_GET["t"])){
				$condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET['t']]);
			}

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

			if($get_de != "" and $get_ate != ""){
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$condition['and'] = array_merge($condition['and'], ['Customer.created >=' => $de.' 00:00:00', 'Customer.created <=' => $ate.' 23:59:59']);
			}

			$data = $this->Paginator->paginate('Customer', $condition);

			if(isset($_GET['excel'])){
				$nome = 'clientes_com_desconto';
				$this->ExcelGenerator->gerarExcelClientesDesconto($nome, $data);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

			$action = 'Clientes com desconto';
	        $breadcrumb = ['Relatórios' => '', 'Clientes com desconto' => ''];
			$this->set(compact('status', 'data', 'action', 'breadcrumb'));
		}

	/*************************
				STATUS CLIENTES			
	**************************/
		public function status_clientes(){
			$this->Permission->check(43, 'leitura') ? "" : $this->redirect("/not_allowed");
			
			$exportar = false;

			$condition = ['and' => ["LoginConsulta.status_id" => 1, "Customer.status_id IN (4,5,6,41)"], 'or' => []];

			if (isset($_GET['s']) && $_GET['s'] != '') {
				$condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET['s']]);
			}

			$dados = $this->LoginConsulta->find("all", ["conditions" => [$condition], "order" => ["Customer.nome_secundario" => "asc"] ] );

			if(isset($_GET['excel'])){
				$nome = 'status_clientes';	
				$this->ExcelGenerator->gerarExcelStatusClientes($nome, $dados);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$action = 'Status clientes';
	    	$breadcrumb = ['Relatórios' => '', 'Status clientes' => ''];
			$this->set(compact("dados", "action", "breadcrumb"));
		}

	/***************************
				LOGINS CONSULTAS			
	****************************/
		public function logins_consultas(){
			ini_set('memory_limit', '-1');
			set_time_limit(0);
			$this->Permission->check(45, 'leitura') ? "" : $this->redirect("/not_allowed");

			$exportar = false;

			$condition = ['and' => [], 'or' => []];

			$dados = '';
			if(!empty($_GET["s"]) || !empty($_GET["q"])){
				if(!empty($_GET["s"])){
					$condition['and'] = array_merge($condition['and'], ['LoginConsulta.status_id' => $_GET['s']]);
				}

				if(!empty($_GET["q"])){
					$condition['or'] = array_merge($condition['or'], ['Customer.codigo_associado LIKE' => "%". $_GET['q']."%", "Customer.nome_secundario LIKE" => "%".$_GET["q"]."%"   ]);
				}

				$this->LoginConsulta->recursive = 2;
				$this->Customer->unbindModel(["belongsTo" => ["Seller", "ActivityArea"]]);
				$dados = $this->Paginator->paginate("LoginConsulta", $condition);
			}
			
			if(isset($_GET['excel'])){
				$data = $this->LoginConsulta->find("all", ["conditions" => [$condition], "order" => ["Customer.nome_secundario" => "asc"] ] );
				$nome = 'logins_consultas';	
				$this->ExcelGenerator->gerarExcelLoginsConsulta($nome, $data);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$statuses = $this->Status->find("all", ["conditions" => ["Status.categoria" => 1], 'order' => ["Status.name" => "asc"] ]);

			$action = 'Logins de consultas';
        	$breadcrumb = ['Relatórios' => '', 'Logins de consultas' => ''];
			$this->set(compact("dados", "statuses", "action", "breadcrumb"));
		}

	/***************************
				LOGINS BLINDAGEM			
	****************************/
		public function logons_blindagem(){
			$this->Permission->check(46, 'leitura') ? "" : $this->redirect("/not_allowed");
			$this->Paginator->settings = $this->paginate;

			$exportar = false;

			$condition = ['and' => [], 'or' => []];

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

			$dados = "";
			
			if(!empty($_GET["s"]) && $_GET["s"] != ""){
				$condition['and'] = array_merge($condition['and'], ['LoginConsulta.login_blindado' => $_GET['s']]);
			}
			
			if(!empty($_GET["st"]) && $_GET["st"] != ""){
				$condition['and'] = array_merge($condition['and'], ['LoginConsulta.status_id' => $_GET['st']]);
			}
			
			if(!empty($_GET["logon"]) && $_GET["logon"] != ""){
				$condition['and'] = array_merge($condition['and'], ['LoginConsulta.login like' => "%".$_GET['logon']."%"]);
			}

			if($get_de != "" and $get_ate != ""){
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$condition['and'] = array_merge($condition['and'], ["LoginConsulta.created between '$de 00:00:00' and '$ate 23:59:59'"]);
				
				$dados = $this->Paginator->paginate("LoginConsulta", $condition);
			}

			if(isset($_GET['excel'])){
				$data = $this->LoginConsulta->find("all", ["conditions" => [$condition], "order" => ["Customer.nome_secundario" => "asc"] ] );
				$nome = 'logons_blindagem';	
				$this->ExcelGenerator->gerarExcelLogonBlindagem($nome, $data);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$status = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

			$action = 'Logons para blindagem';
	        $breadcrumb = ['Relatórios' => '', 'Logons para blindagem' => ''];
			$this->set(compact("dados", "status", "action", "breadcrumb"));
		}

	/*****************
				BLINDAR			
	******************/
		public function blindar(){
			$this->Permission->check(46, 'escrita') ? "" : $this->redirect("/not_allowed");
			$ids = substr($_GET['id'], 0, -1);

			$this->LoginConsulta->blinda_logon($ids);

			$this->Flash->set(__('Blindagem efetuada com sucesso!.'), ['params' => ['class' => "alert alert-success"]]);
			$this->redirect($this->referer());
		}

	/***************************
				PRODUTO DESCONTO			
	****************************/
		public function produto_desconto(){
			$this->Permission->check(50, "leitura")? "" : $this->redirect("/not_allowed");

			$condition = ["and" => ['Desconto.data_cancel' => '1901-01-01', 'Desconto.expire_date >' => date('Y-m-d')], "or" => []];

			$this->Customer->unbindModel(array('belongsTo' => array('Franquia', 'Seller', 'ActivityArea')));

			if(!empty($_GET['q'])){
				$condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.email LIKE' => "%".$_GET['q']."%", 'Customer.documento LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%", 'Customer.celular LIKE' => "%".$_GET['q']."%", 'Customer.celular1 LIKE' => "%".$_GET['q']."%", 'Customer.celular2 LIKE' => "%".$_GET['q']."%", 'Customer.celular3 LIKE' => "%".$_GET['q']."%", 'Customer.celular4 LIKE' => "%".$_GET['q']."%", 'Customer.celular5 LIKE' => "%".$_GET['q']."%"]);
			}

			$joins = [
				'joins' => [
					[
						'table' => 'customer_discounts',
						'alias' => 'Desconto',
						'type' => 'INNER',
						'conditions' => ['Desconto.customer_id = Customer.id']
					]
				],
				'group' => ['Customer.id']
			];
			$this->paginate['Customer'] = array_merge($this->paginate['Customer'], $joins);

			$this->Paginator->settings = $this->paginate;

			if(!empty($_GET["t"])){
				$condition['and'] = array_merge($condition['and'], ['Customer.status_id' => $_GET['t']]);
			}

			$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
			$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

			if($get_de != "" and $get_ate != ""){
				$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
				$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

				$condition['and'] = array_merge($condition['and'], ['Customer.created >=' => $de.' 00:00:00', 'Customer.created <=' => $ate.' 23:59:59']);
			}

			$data = $this->Paginator->paginate('Customer', $condition);

			if(isset($_GET['excel'])){
				$nome = 'produtos_com_desconto';
				$this->ExcelGenerator->gerarExcelClientesDesconto($nome, $data);
				$this->redirect("/files/excel/".$nome.".xlsx");
			}

			$status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);

			$action = 'Produtos com desconto';
	    	$breadcrumb = ['Relatórios' => '', 'Produtos com desconto' => ''];
			$this->set(compact('status', 'data', 'action', 'breadcrumb'));
		}

	/************************
				TRANSFERENCIA			
	*************************/
		public function transf_negativacoes(){
			$condition = ['and' => [], 'or' => []];

			$dados = [];
			$buscar = false;

			if(isset($_GET['q']) and $_GET['q'] != ""){
				$condition['or'] = array_merge($condition['or'], ['CadastroPefin.nome LIKE' => "%".$_GET['q']."%", 'NaturezaOperacao.nome LIKE' => "%".$_GET['q']."%", 'CadastroPefin.documento LIKE' => "%".$_GET['q']."%", 'CadastroPefin.numero_titulo LIKE' => "%".$_GET['q']."%", 'CadastroPefin.valor LIKE' => "%".$_GET['q']."%"]);
			}

			if(isset($_GET["s"]) and $_GET["s"] != ""){
				$condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['s']]);
			}

			if(isset($_GET["c"]) and $_GET["c"] != ""){
				$buscar = true;
				$condition['and'] = array_merge($condition['and'], ['CadastroPefin.customer_id' => $_GET['c']]);
			}

			if ($buscar) {
				$dados = $this->CadastroPefin->find("all", ["conditions" => [$condition], "order" => ["Customer.nome_secundario" => "asc"]]);
			}

			$statuses = $this->Status->find('all', array('conditions' => array('Status.categoria' => 7), 'order' => ['Status.name']));
			$customers = $this->Customer->find('all', array('order' => ['Customer.nome_secundario' => "asc"], 'recursive' => -1));
			
			$clientes = [];
			foreach ($customers as $key => $value) {
				$clientes[$value['Customer']['id']] = $value['Customer']['codigo_associado'].' - '.$value['Customer']['nome_secundario'];
			}

			$action = 'Transferência de Negativações';
	    	$breadcrumb = ['Relatórios' => '', 'Transferência de Negativações' => ''];
			$this->set(compact("dados", "statuses", "clientes", "action", "breadcrumb"));
		}

		public function save_transf(){

			if ($this->request->is(['post', 'put'])) {
				$ids = explode(',', substr($this->request->data['negativacoes_id'], 0,-1));

				$negativacoes = $this->CadastroPefin->find('all', ['conditions' => ['CadastroPefin.id' => $ids], 'recursive' => -1]);

				foreach ($negativacoes as $dados) {
					unset($dados['CadastroPefin']['id']);
					unset($dados['CadastroPefin']['motivo_baixa_id']);
					unset($dados['CadastroPefin']['cadastro_pefin_lote_id']);
					unset($dados['CadastroPefin']['n_remessa']);
					unset($dados['CadastroPefin']['n_sequencial']);
					unset($dados['CadastroPefin']['erro']);
					unset($dados['CadastroPefin']['data_inclusao']);
					unset($dados['CadastroPefin']['data_solic_baixa']);
					unset($dados['CadastroPefin']['data_baixa']);
					unset($dados['CadastroPefin']['status_id']);
					unset($dados['CadastroPefin']['created']);
					unset($dados['CadastroPefin']['user_creator_id']);
					unset($dados['CadastroPefin']['updated']);
					unset($dados['CadastroPefin']['user_updated_id']);
					unset($dados['CadastroPefin']['data_cancel']);
					unset($dados['CadastroPefin']['usuario_id_cancel']);
					unset($dados['CadastroPefin']['customer_user_id']);
					$novo_pefin = $dados;

					$novo_pefin['CadastroPefin']['status_id'] = 22;
					$novo_pefin['CadastroPefin']['customer_id'] = $this->request->data['novo_cliente'];
					$novo_pefin['CadastroPefin']['user_creator_id'] = CakeSession::read("Auth.User.id");

					$save_novo[] = $novo_pefin;
				}

				$this->CadastroPefin->saveMany($save_novo, ['validate' => false]);

				$this->Flash->set(__('As negativações foram copiadas com sucesso'), ['params' => ['class' => "alert alert-success"]]);
				$this->redirect($this->referer());
			}

		}

	public function baixa_manual(){

		$where = '';

		if(!empty($_GET['q'])){
			$where .= " AND (c.codigo_associado like '%{$_GET['q']}%' or c.nome_secundario like '%{$_GET['q']}%' or u.name like '%{$_GET['q']}%' or i.name like '%{$_GET['q']}%') ";
		}

		$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
		$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

		if($get_de != "" and $get_ate != ""){
			$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
			$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

			$where .= " AND i.data_baixa BETWEEN '{$de} 00:00:00' AND '{$ate} 23:59:59' ";
		}

		$data = [];
		if ($where != '') {
			$data = $this->Outcome->query("
				SELECT c.codigo_associado, 
							 c.nome_secundario, 
							 i.`name` AS mensalidade, 
							 i.vencimento, 
							 i.data_pagamento, 
							 i.valor_total, 
							 i.valor_pago,
							 i.data_baixa, 
							 u.`name` AS usuarioBaixa
				FROM incomes i
					INNER JOIN customers c ON c.id = i.customer_id
					LEFT JOIN users u ON u.id = i.usuario_id_baixa
				WHERE i.data_cancel = '1901-01-01' 
					AND i.status_id = 17 
					AND i.data_baixa IS NOT NULL 
					$where
				ORDER BY i.data_baixa desc
			");
		}

		if (isset($_GET['exportar'])) {
			$nome = 'baixa_manual.xlsx';

			$this->ExcelGenerator->gerarBaixaManual($nome, $data);
			$this->redirect("/files/excel/".$nome);
		}

		$action = 'Baixa manual';
		$this->set(compact('data', 'action'));
	}

	public function movimentacao_status(){

		$where = '';

		if(!empty($_GET['q'])){
			$where .= " AND (c.codigo_associado like '%{$_GET['q']}%' or c.nome_secundario like '%{$_GET['q']}%') ";
		}

		if(!empty($_GET['s'])){
			$where .= " AND sm.id = ".$_GET['s'];
		}

		if(!empty($_GET['v'])){
			$where .= " AND c.seller_id = ".$_GET['v'];
		}

		$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
		$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

		if($get_de != "" and $get_ate != ""){
			$de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
			$ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

			$where .= " AND mv.created BETWEEN '{$de} 00:00:00' AND '{$ate} 23:59:59' ";
		}

		$data = [];
		if ($where != '') {
			/*
			$data = $this->Customer->query("
				SELECT s.name AS statusAtual, c.codigo_associado, c.nome_secundario, sm.name AS statusAnterior, mv.created, u.name AS usuario, ve.nome_fantasia, c.created, (
					SELECT (pc.mensalidade)
					FROM plan_customers pc
					WHERE pc.customer_id = c.id AND pc.data_cancel = '1901-01-01'
					LIMIT 1) AS mensalidade
				FROM customers c
				INNER JOIN statuses s ON s.id = c.status_id
				INNER JOIN sellers ve ON ve.id = c.seller_id
				INNER JOIN movimentacao_credor mv ON mv.customer_id = c.id
				INNER JOIN statuses sm ON sm.id = mv.status_id
				INNER JOIN users u ON u.id = mv.user_created_id
				WHERE c.data_cancel = '1901-01-01' 
					{$where}
				ORDER BY c.nome_secundario, mv.created
			");
			*/
			$data = $this->Customer->query("
				SELECT s.name AS statusAtual,
							 c.codigo_associado,
							 c.nome_secundario,
							 sm.name AS statusAnterior,
							 mv.created,
							 u.name AS usuario,
			 				 ve.nome_fantasia, 
			 				 c.created, 
			 				 if(pc.mensalidade > 0, pc.mensalidade,  (
								SELECT (pca.mensalidade)
								FROM plan_customers pca
								WHERE pca.customer_id = c.id AND pca.data_cancel = '1901-01-01'
								order by pca.id desc LIMIT 1)) as mensalidade
				FROM customers c
					INNER JOIN statuses s ON s.id = c.status_id
					LEFT JOIN plan_customers pc ON pc.customer_id = c.id AND pc.data_cancel = '1901-01-01' AND pc.status_id = 1
					LEFT JOIN sellers ve ON ve.id = c.seller_id
					INNER JOIN movimentacao_credor mv ON mv.customer_id = c.id 
					INNER JOIN statuses sm ON sm.id = mv.status_id
					INNER JOIN users u ON u.id = mv.user_created_id
				WHERE c.data_cancel = '1901-01-01' 
					{$where}
				ORDER BY c.nome_secundario, mv.created
			");
		}

		if (isset($_GET['exportar'])) {
			$nome = 'movimentacao_status.xlsx';

			$this->ExcelGenerator->gerarMovimentacaoStatus($nome, $data);
			$this->redirect("/files/excel/".$nome);
		}

		$statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 2], 'order' => 'Status.name']);
		$sellers = $this->Seller->find('list', ['conditions' => ['Seller.status_id' => 1], 'order' => 'Seller.nome_fantasia']);

		$action = 'Movimentação status';
	    $breadcrumb = ['Relatórios' => '', 'Movimentação status' => ''];
		$this->set(compact('data', 'statuses', 'sellers', 'action', 'breadcrumb'));
	}

	public function demonstrativo_analitico()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        if ($this->request->is(['post', 'put'])) {
            $data_ini = array_reverse(explode('-', $this->request->data['de']));
            $data_fim = array_reverse(explode('-', $this->request->data['ate']));

            $msg = $this->Robo->analytical_statement([
                'client_logon' => $_POST['data']['Bot']['logon'],
                'data_ini' => $data_ini,
                'data_fim' => $data_fim
            ]);

            if ($msg != '') {
                $this->Flash->set(__($msg), 'default', array('class' => "alert alert-danger"));
            }
        }

        $this->set("form_action", "../reports/demonstrativo_analitico");
        $this->set("action", "Demonstrativo Analítico");
    }

    public function string()
    {
        $this->Permission->check(27, "leitura") ? "" : $this->redirect("/not_allowed");

        $this->Paginator->settings = $this->paginate;

        $get_de = isset($_GET["de"]) ? $_GET["de"] : '';
        $get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';

        $data = [];
        $condition = ["and" => ['Product.tipo' => 4], "or" => []];

        if ($get_de != "" and $get_ate != "") {
            $de = date('Y-m-d', strtotime(str_replace('/', '-', $get_de)));
            $ate = date('Y-m-d', strtotime(str_replace('/', '-', $get_ate)));

            $condition['and'] = array_merge($condition['and'], ["NovaVidaLogConsulta.created BETWEEN '" . $de . "' AND '" . $ate . " 23:59:59'"]);

            if (!empty($_GET['q'])) {
                $condition['or'] = array_merge($condition['or'], [
                    'Customer.nome_primario LIKE' => "%".$_GET['q']."%",
                    'Customer.nome_secundario LIKE' => "%".$_GET['q']."%",
                    'Customer.email LIKE' => "%".$_GET['q']."%",
                    'Customer.documento LIKE' => "%".$_GET['q']."%",
                    'Customer.codigo_associado LIKE' => "%".$_GET['q']."%"
                ]);
            }

            if (isset($_GET['exportar'])) {
                $nome = 'demonstrativo_strings';

                $dados = $this->NovaVidaLogConsulta->find('all', [
                    'fields' => [
                        "GROUP_CONCAT(concat(LogItens.tipo_campo, ': ', LogItens.campo) ORDER BY LogItens.tipo_campo SEPARATOR ';') as campo",
                        "(select GROUP_CONCAT(f.name SEPARATOR ' + ')
        				  from nova_vida_log_consulta_features lf
        					inner join features f on f.id = lf.feature_id
        				  where lf.nova_vida_log_consulta_id = NovaVidaLogConsulta.id) as features",
                        'NovaVidaLogConsulta.customer_id',
                        'NovaVidaLogConsulta.valor',
                        'NovaVidaLogConsulta.created',
                        'NovaVidaLogConsulta.id',
                        'Customer.codigo_associado',
                        'Customer.nome_secundario',
                        'Product.name'
                    ],
                    'joins' => $this->paginate['NovaVidaLogConsulta']['joins'],
                    'conditions' => $condition,
                    'group' => $this->paginate['NovaVidaLogConsulta']['group'],
                    'order' => $this->paginate['NovaVidaLogConsulta']['order'],
                ]);

                $this->ExcelGenerator->gerarDemonstrativoStrings($nome, $dados);
                $this->redirect("/files/excel/" . $nome . ".xlsx");
            } else {
                $data = $this->Paginator->paginate("NovaVidaLogConsulta", $condition);
            }
        }

		$action = 'String';
        $breadcrumb = ['Relatórios' => '', 'String' => ''];
        $this->set(compact('data', 'action', 'breadcrumb'));
    }
}