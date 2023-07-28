<?php
class BillingSalesController extends AppController {
	public $helpers = ['Html', 'Form'];
	public $components = ['Paginator', 'Permission', 'Email', 'ExcelGenerator'];
	public $uses = ['Status', 'BillingSale', 'Billing', 'Income', 'Customer', 'Seller', "Resale", "LogBillingSale", "Outcome"];

	public $paginate = [
		'limit' => 10, 'order' => ['BillingSale.mes_pagamento' => 'desc']
	];

	public function index(){
		$this->Permission->check(35, "leitura") ? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => [], "or" => []];

		if(isset($_GET['data']) and $_GET['data'] != ""){
			$de = date('Y-m-d', strtotime('01-'.str_replace('/', '-', $_GET['data'])));
			$condition['and'] = array_merge($condition['and'], ['Billing.date_billing' => $de]);
		}

		if(isset($_GET["t"]) and $_GET["t"] != ""){
			$condition['and'] = array_merge($condition['and'], ['BillingSale.status_id' => $_GET['t']]);
		}

		$dados = $this->Paginator->paginate('BillingSale', $condition);
		$status = $this->Status->find("all", ["conditions" => ["Status.categoria" => 1]]);

		$action = 'Faturamento de Vendas';
        $breadcrumb = ['Financeiro' => '', 'Faturamento de Vendas' => ''];
		$this->set(compact('status', 'dados', 'action', 'breadcrumb'));
	}

	public function add(){
		$this->Permission->check(35, "escrita") ? "" : $this->redirect("/not_allowed");

		if ($this->request->is(['post', 'put'])) {
			$this->request->data['BillingSale']['user_creator_id'] = CakeSession::read("Auth.User.id");
			$this->request->data["BillingSale"]["status_id"] = 2;

			$this->BillingSale->create();
			$this->BillingSale->validates();
			if ($this->BillingSale->save($this->request->data)) {
				$id = $this->BillingSale->id;

				$this->Flash->set(__('O faturamento foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
				$this->redirect(['action' => 'index']);
			} else {
				$this->Flash->set(__('O faturamento não pode ser salvo, Por favor tente de novo.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$billings = $this->Billing->query("SELECT b.id, b.date_billing
																				FROM billings b
																				WHERE b.status_id = 1 AND b.conta_gerada = 1 AND b.id NOT IN (
																				SELECT billing_id
																				FROM billing_sales bs WHERE bs.data_cancel = '1901-01-01' )
																				ORDER BY b.id DESC");

		$this->set("action", "Novo Faturamento de Vendas");
		$this->set("form_action", "add");
		$this->set(compact("billings"));
	}

	public function edit($id = null){
		$this->Permission->check(35, "escrita") ? "" : $this->redirect("/not_allowed");

		$this->BillingSale->id = $id;
		if ($this->request->is(['post', 'put'])) {
			$this->request->data['BillingSale']['user_updated_id'] = CakeSession::read("Auth.User.id");

			$this->BillingSale->validates();
			if ($this->BillingSale->save($this->request->data)) {
				$id = $this->BillingSale->id;

				$this->Flash->set(__('O faturamento foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
				$this->redirect(['action' => 'index']);
			} else {
				$this->Flash->set(__('O faturamento não pode ser alterado, Por favor tente de novo.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$temp_errors = $this->BillingSale->validationErrors;
		$this->request->data = $this->BillingSale->read();
		$this->BillingSale->validationErrors = $temp_errors;

		$statuses = $this->Status->find('list', array('conditions' => array('Status.categoria' => 1)));

		$this->set("action", "Editar Faturamento de Vendas");
		$this->set("form_action", "edit");
		$this->set(compact("billings", "id", "statuses"));

		$this->render('add');
	}

	public function delete($id){
		$this->Permission->check(35, "excluir") ? "" : $this->redirect("/not_allowed");
		$this->BillingSale->id = $id;

		$this->request->data['BillingSale']['data_cancel'] = date("Y-m-d H:i:s");
		$this->request->data['BillingSale']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

		if ($this->BillingSale->save($this->request->data)) {
			$this->Income->updateAll(
				['Income.billing_sales_id' => 'null', 'Income.updated' => 'current_timestamp', 'Income.user_updated_id' => CakeSession::read("Auth.User.id")], //set
				['Income.billing_sales_id' => $id] //where
			);

			$this->Outcome->updateAll(
				['Outcome.data_cancel' => 'current_timestamp', 'Outcome.usuario_id_cancel' => CakeSession::read("Auth.User.id")], //set
				['Outcome.billing_sales_id' => $id] //where
			);

			$this->Flash->set(__('O faturamento de vendas foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
			$this->redirect(array('action' => 'index'));
		}
	}

/*******************
			REVENDAS			
********************/
	public function revenda($id){
		$this->Permission->check(35, "leitura") ? "" : $this->redirect("/not_allowed");

		$this->BillingSale->id = $id;
		$billing_sale = $this->BillingSale->read();
		$data_ini = date('Y-m-d', strtotime(str_replace('/', '-', $billing_sale['BillingSale']['mes_pagamento'])));
		$data_fim = date('Y-m-t', strtotime(str_replace('/', '-', $billing_sale['BillingSale']['mes_pagamento'])));

		$sql = "SELECT r.id, r.nome_fantasia, sum(i.valor_pago) as total, r.valor_recebido_cliente, (sum(i.valor_pago)*(r.valor_recebido_cliente/100)) as valor_comissao
						FROM incomes i
						INNER JOIN customers c on c.id = i.customer_id
						INNER JOIN sellers s on s.id = c.seller_id
						INNER JOIN resales r on r.id = s.resale_id";

		$where = " WHERE i.status_id = 17 AND i.data_cancel = '1901-01-01' AND r.data_cancel = '1901-01-01' AND c.data_cancel = '1901-01-01' and s.resale_id not in (1)";

		$group = " GROUP BY s.resale_id";

		$previsao = $this->Income->query($sql.$where." AND i.data_pagamento between '".$data_ini."' and '".$data_fim."' ".$group);

		$realizado = $this->Income->query($sql.$where." and i.resale_id not in (1) and i.billing_sales_id = ".$id.$group);

		$qtde_previsao = 0;
		foreach ($previsao as $dados_previsao) {
			$qtde_previsao += $dados_previsao[0]['valor_comissao'];
		}

		$qtde_realizada = 0;
		foreach ($realizado as $dados_realizado) {
			$qtde_realizada += $dados_realizado[0]['valor_comissao'];
		}

		$refaturar = false;
		if ($qtde_previsao > $qtde_realizada) {
			$refaturar = true;
		}

		if (isset($_GET['excel'])) {
			$nome = 'faturamento_revenda_'.$data_ini;

			$dados = ['previsao' => $previsao,
								'realizado' => $realizado
							 ];

			$this->ExcelGenerator->gerarExcelFaturamentoRevenda($nome, $dados);
			$this->redirect("/files/excel/".$nome.".xlsx");
		}

		$action = "Gerar Faturamento das Revendas";
		$this->set(compact("id", "action", "previsao", "billing_sale", "realizado", "refaturar"));
	}

	public function faturar_revendas($id){
		$this->Permission->check(35, "escrita") ? "" : $this->redirect("/not_allowed");

		$this->BillingSale->id = $id;
		$this->BillingSale->save(['BillingSale' => ['faturado_revendas' => 1]]);

		$billing_sale = $this->BillingSale->read();
		$data_ini = date('Y-m-d', strtotime(str_replace('/', '-', $billing_sale['BillingSale']['mes_pagamento'])));
		$data_fim = date('Y-m-t', strtotime(str_replace('/', '-', $billing_sale['BillingSale']['mes_pagamento'])));

		$main_sql = "FROM incomes i
								INNER JOIN customers c on c.id = i.customer_id
								INNER JOIN sellers s on s.id = c.seller_id
								INNER JOIN resales r on r.id = s.resale_id
								INNER JOIN vencimentos v on v.id = r.vencimento_id
								WHERE i.status_id = 17 AND i.data_cancel = '1901-01-01' AND r.data_cancel = '1901-01-01' AND c.data_cancel = '1901-01-01' AND i.data_pagamento between '".$data_ini."' and '".$data_fim."' and s.resale_id not in (1) AND i.billing_sales_id IS NULL";

		$dados = $this->Income->query("SELECT r.id, r.nome_fantasia, sum(i.valor_pago) as total, r.valor_recebido_cliente,
																		round(sum(i.valor_pago)*(r.valor_recebido_cliente/100), 4) as valor_comissao, v.name as vencimento
																		".$main_sql."
																		GROUP BY s.resale_id");

		$contas_separadas = $this->Income->query("SELECT i.id, i.valor_total, s.resale_id ".$main_sql);

		$dados_incomes = [];
		foreach ($contas_separadas as $conta) {
			$dados_incomes[] = [
				'Income' => [
				 	'id' => $conta['i']['id'],
				 	'billing_sales_id' => $id,
				 	'resale_id' => $conta['s']['resale_id'],
				]
			];
		}
		$this->Income->saveMany($dados_incomes);
		
		$dados_outcome = [];
		foreach ($dados as $comissao) {
			$dados_outcome[] = [
				'Outcome' => [
					'billing_sales_id' => $id,
					'status_id' => 11,
					'expense_id' => 18,
					'bank_account_id' => 2,
					'cost_center_id' => 3,
					'name' => 'Comissão revenda '.$comissao['r']['nome_fantasia'],
					'observation' => 'Faturamento '.$billing_sale["Billing"]["date_billing"],
					'valor_bruto' => number_format($comissao[0]["valor_comissao"],2,',','.'),
					'valor_total' => number_format($comissao[0]["valor_comissao"],2,',','.'),
					'recorrencia' => 2,
					'parcela' => 1,
					'vencimento' => $comissao['v']['vencimento'].date('/m/Y'),
					'user_creator_id' => CakeSession::read('Auth.User.id')
				]
			];
		}
		//$this->Outcome->saveMany($dados_outcome);

		$this->Flash->set(__('Faturado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
		$this->redirect($this->referer());
	}

	public function detalhes_revenda($id, $resale_id){
		$this->Permission->check(35, "leitura") ? "" : $this->redirect("/not_allowed");

		$this->BillingSale->id = $id;
		$billing_sale = $this->BillingSale->read();

		$this->Resale->id = $resale_id;
		$resale = $this->Resale->read();

		$dados = $this->Income->query("SELECT c.nome_primario, i.valor_pago, r.valor_recebido_cliente, ROUND(i.valor_pago*(r.valor_recebido_cliente/100), 4) AS valor_comissao
																	 FROM incomes i
																	 INNER JOIN customers c ON c.id = i.customer_id
																	 INNER JOIN sellers s ON s.id = c.seller_id
																	 INNER JOIN resales r ON r.id = s.resale_id
																	 WHERE i.billing_sales_id = ".$id." AND i.resale_id = ".$resale_id." AND i.data_cancel = '1901-01-01' AND r.data_cancel = '1901-01-01' AND c.data_cancel = '1901-01-01'
																	 ORDER BY c.nome_primario");

		$action = "Detalhes Faturamento - ".$resale['Resale']['nome_fantasia'];
		$this->set(compact('dados', 'id', 'resale_id', 'action'));
	}

/*********************
			HIPERCHECK			
**********************/
	public function berh($id){
		$this->Permission->check(35, "leitura") ? "" : $this->redirect("/not_allowed");

		$this->BillingSale->id = $id;
		$billing_sale = $this->BillingSale->read();
		$data_ini = date('Y-m-d', strtotime(str_replace('/', '-', $billing_sale['BillingSale']['mes_pagamento'])));
		$data_fim = date('Y-m-t', strtotime(str_replace('/', '-', $billing_sale['BillingSale']['mes_pagamento'])));

		$sql = "SELECT s.id, s.nome_fantasia, COUNT(p.id) AS qtde, p.commission, COUNT(p.id)*p.commission AS valor_comissao
						FROM incomes i
						INNER JOIN customers c ON c.id = i.customer_id
						INNER JOIN sellers s ON s.id = c.seller_id
						INNER JOIN plan_customers pc ON pc.customer_id = c.id
						INNER JOIN plans p ON p.id = pc.plan_id";

		$where = " WHERE i.status_id = 17 AND i.data_cancel = '1901-01-01' AND s.data_cancel = '1901-01-01' AND c.data_cancel = '1901-01-01' AND s.resale_id IN (1)";

		$group = " GROUP BY s.id ORDER BY s.nome_fantasia";

		$previsao = $this->Income->query($sql.$where." AND i.data_pagamento between '".$data_ini."' and '".$data_fim."' ".$group);

		$realizado = $this->Income->query($sql.$where." and i.resale_id in (1) and i.billing_sales_id = ".$id.$group);
		
		$qtde_previsao = 0;
		foreach ($previsao as $dados_previsao) {
			$qtde_previsao += $dados_previsao[0]['qtde'];
		}

		$qtde_realizada = 0;
		foreach ($realizado as $dados_realizado) {
			$qtde_realizada += $dados_realizado[0]['qtde'];
		}

		$refaturar = false;
		if ($qtde_previsao > $qtde_realizada) {
			$refaturar = true;
		}

		if (isset($_GET['excel'])) {
			$nome = 'faturamento_berh_'.$data_ini;

			$dados = ['previsao' => $previsao,
								'realizado' => $realizado
							 ];

			$this->ExcelGenerator->gerarExcelFaturamentoHipercheck($nome, $dados);
			$this->redirect("/files/excel/".$nome.".xlsx");
		}

		$action = "Gerar Faturamento da BeRH";
		$this->set(compact('previsao', 'action', 'id', 'billing_sale', 'realizado', 'refaturar'));
	}

	public function faturar_berh($id){
		$this->Permission->check(35, "escrita") ? "" : $this->redirect("/not_allowed");

		$this->BillingSale->id = $id;
		$this->BillingSale->save(['BillingSale' => ['faturado_berh' => 1]]);

		$billing_sale = $this->BillingSale->read();
		$data_ini = date('Y-m-d', strtotime(str_replace('/', '-', $billing_sale['BillingSale']['mes_pagamento'])));
		$data_fim = date('Y-m-t', strtotime(str_replace('/', '-', $billing_sale['BillingSale']['mes_pagamento'])));

		$main_sql = "FROM incomes i
								INNER JOIN customers c ON c.id = i.customer_id
								INNER JOIN sellers s ON s.id = c.seller_id
								INNER JOIN plan_customers pc ON pc.customer_id = c.id
								INNER JOIN plans p ON p.id = pc.plan_id
								WHERE i.status_id = 17 AND i.data_cancel = '1901-01-01' AND s.data_cancel = '1901-01-01' AND c.data_cancel = '1901-01-01' AND i.data_pagamento between '".$data_ini."' and '".$data_fim."' AND s.resale_id IN (1) AND i.billing_sales_id IS NULL";

		$dados = $this->Income->query("SELECT s.id, s.nome_fantasia, COUNT(p.id) AS qtde, p.commission, COUNT(p.id)*p.commission AS valor_comissao
																		".$main_sql."
																		GROUP BY s.id");

		$contas_separadas = $this->Income->query("SELECT i.id, i.valor_total, s.id ".$main_sql);

		$dados_incomes = [];
		foreach ($contas_separadas as $conta) {
			$dados_incomes[] = [
				'Income' => [
				 	'id' => $conta['i']['id'],
				 	'billing_sales_id' => $id,
				 	'seller_id' => $conta['s']['id'],
				 	'resale_id' => 1,
				]
			];
		}
		$this->Income->saveMany($dados_incomes);
		
		$dados_outcome = [];
		foreach ($dados as $comissao) {
			$dados_outcome[] = [
				'Outcome' => [
					'status_id' => 11,
					'expense_id' => 18,
					'bank_account_id' => 2,
					'cost_center_id' => 3,
					'name' => 'Comissão vendedor '.$comissao['s']['nome_fantasia'],
					'observation' => 'Faturamento '.$billing_sale["Billing"]["date_billing"],
					'valor_bruto' => number_format($comissao[0]["valor_comissao"],2,',','.'),
					'valor_total' => number_format($comissao[0]["valor_comissao"],2,',','.'),
					'recorrencia' => 2,
					'parcela' => 1,
					'vencimento' => '20'.date('/m/Y'),
					'user_creator_id' => CakeSession::read('Auth.User.id')
				]
			];
		}
		$this->Outcome->saveMany($dados_outcome);

		$this->Flash->set(__('Faturado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
		$this->redirect($this->referer());
	}

	public function detalhes_berh($id, $seller_id){
		$this->Permission->check(35, "leitura") ? "" : $this->redirect("/not_allowed");

		$this->Seller->id = $seller_id;
		$seller = $this->Seller->read();

		$sql = "SELECT s.id, s.nome_fantasia, p.description, COUNT(p.id) AS qtde, p.commission, COUNT(p.id)*p.commission AS valor_comissao
						FROM incomes i
						INNER JOIN customers c ON c.id = i.customer_id
						INNER JOIN sellers s ON s.id = c.seller_id
						INNER JOIN plan_customers pc ON pc.customer_id = c.id
						INNER JOIN plans p ON p.id = pc.plan_id
						WHERE i.status_id = 17 AND i.data_cancel = '1901-01-01' AND s.data_cancel = '1901-01-01' AND c.data_cancel = '1901-01-01' AND s.id = ".$seller_id." and i.resale_id in (1) and i.billing_sales_id = ".$id."
						GROUP BY p.id
						ORDER BY s.nome_fantasia";

		$dados = $this->Income->query($sql);
		
		$action = "Detalhes Faturamento - ".$seller['Seller']['nome_fantasia'];
		$this->set(compact('dados', 'id', 'seller_id', 'action'));
	}
}