<?php
class NegativacaoController extends AppController {
	public $helpers = ['Html', 'Form'];
	public $components = ['Paginator', 'Permission', 'GerarTxt', 'Uploader', 'LerErroSerasa', 'ExcelGenerator'];
	public $uses = ['CadastroPefin', 'CadastroPefinLote', 'Status', 'MotivoBaixa', 'NaturezaOperacao', 'SequencialPefin', 'LogLoteAntigo', 'Customer'];

	public $paginate = [
		'CadastroPefin' => ['limit' => 1000, 'order' => ['CadastroPefin.created' => 'asc', 'CadastroPefin.id' => 'asc']],
		'CadastroPefinLote' => ['limit' => 10, 'order' => ['CadastroPefinLote.remessa' => 'desc']]
	];

	public function beforeFilter() { 
		parent::beforeFilter(); 
	}

	public function inclusao() {
		$this->Permission->check(19, "leitura") ? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => ['Status.id' => 22, 'CadastroPefin.motivo_baixa_id is null', 'Customer.cod_franquia' => CakeSession::read("Auth.User.resales")], "or" => []];

		if(isset($_GET['q']) and $_GET['q'] != ""){
			$condition['or'] = array_merge($condition['or'], ['CadastroPefin.nome LIKE' => "%".$_GET['q']."%", 'NaturezaOperacao.nome LIKE' => "%".$_GET['q']."%", 'Customer.nome_primario LIKE' => "%".$_GET['q']."%"]);
		}

		$data = $this->Paginator->paginate('CadastroPefin', $condition);
		$status = $this->Status->find('all', array('conditions' => array('Status.categoria' => 7), 'order' => ['Status.name']));

		$action = "Inclusão";
		$breadcrumb = ['Pefin' => '', 'Inclusão' => ''];
		$this->set(compact('status', 'data', 'action', 'breadcrumb'));
	}

	public function exclusao() {
		$this->Permission->check(19, "leitura") ? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => ['Status.id' => 33, 'CadastroPefin.motivo_baixa_id is not null', 'Customer.cod_franquia' => CakeSession::read("Auth.User.resales")], "or" => []];

		if(isset($_GET['q']) and $_GET['q'] != ""){
			$condition['or'] = array_merge($condition['or'], ['CadastroPefin.nome LIKE' => "%".$_GET['q']."%", 'NaturezaOperacao.nome LIKE' => "%".$_GET['q']."%", 'Customer.nome_primario LIKE' => "%".$_GET['q']."%"]);
		}

		$data = $this->Paginator->paginate('CadastroPefin', $condition);
		$status = $this->Status->find('all', array('conditions' => array('Status.categoria' => 7), 'order' => ['Status.name']));

		$action = "Exclusão";
		$breadcrumb = ['Pefin' => '', 'Exclusão' => ''];
		$this->set(compact('status', 'data', 'action', 'breadcrumb'));
		$this->render('inclusao');
	}

	public function view($id, $tipo = 'view') {
		$this->Permission->check(19, "leitura") ? "" : $this->redirect("/not_allowed");

		$this->request->data = $this->CadastroPefin->find('first', ['conditions' => ['CadastroPefin.id' => $id], 'recursive' => 2]);

		$naturezaOperacaos = $this->NaturezaOperacao->find('list', ['order' => ['NaturezaOperacao.nome']]);

		$this->set("action", $this->request->data['CadastroPefin']['nome']);
		$this->set("tipo", $tipo);
		$this->set("form_action", "../negativacao/edit");
		$this->set(compact('statuses', 'id', 'naturezaOperacaos'));
	}

	public function edit($id){
		if ($this->request->is(['post', 'put'])) {

			$this->CadastroPefin->id = $id;
			$old = $this->CadastroPefin->read();
			$lote_id = $old['CadastroPefin']['cadastro_pefin_lote_id'];

			$this->request->data['CadastroPefin']['user_updated_id'] = CakeSession::read("Auth.User.id");
			if (isset($this->request->data['CadastroPefin']['motivo_baixa_id'])) {
				$this->request->data['CadastroPefin']['status_id'] = 33;
			} else {
				$this->request->data['CadastroPefin']['status_id'] = 22;
			}
			
			$this->request->data['CadastroPefin']['cadastro_pefin_lote_id'] = null;
			// Se a Natureza for "Dividas Cheq" não precisa validar já que os campos não vão aparecer pro usuario
			if ($this->request->data['CadastroPefin']['natureza_operacao_id'] != 23) {
				unset($this->CadastroPefin->validate['num_banco']);
				unset($this->CadastroPefin->validate['num_agencia']);
				unset($this->CadastroPefin->validate['num_conta_corrente']);
				unset($this->CadastroPefin->validate['num_cheque']);
				unset($this->CadastroPefin->validate['alinea']);
			} else {
				unset($this->CadastroPefin->validate['nosso_numero']);
				unset($this->CadastroPefin->validate['numero_titulo']);
			}

			if ($this->CadastroPefin->save($this->request->data)) {
				$this->Flash->set(__('A negativação foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
				$this->redirect(['action' => 'detalhes_lote/'.$lote_id]);
			} else {
				$this->Flash->set(__('A negativação não pode ser alterada, Por favor tente de novo.'), 'default', array('class' => "alert alert-danger"));
				$this->redirect($this->referer());
			}
		}
	}

	public function imprimir($id){
		$this->layout = 'print';

		$this->view($id);

		$this->render('view');
	}

	public function baixa($id = null) {
		$this->CadastroPefin->id = $id;
		if ($this->request->is(['post', 'put'])) {
			$this->CadastroPefin->validates();
			$this->request->data['CadastroPefin']['user_updated_id'] = CakeSession::read("Auth.User.id");
			$this->request->data['CadastroPefin']['data_solic_baixa'] = date('Y-m-d H:i:s');
			$this->request->data['CadastroPefin']['status_id'] = 33;
			if ($this->CadastroPefin->save($this->request->data)) {
				$this->Flash->set(__('A negativação foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
				$this->redirect(['controller' => 'customers', 'action' => 'negativacoes/'.$this->request->data['CadastroPefin']['customer_id']]);
			} else {
				$this->Flash->set(__('A negativação não pode ser alterada, Por favor tente de novo.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$temp_errors = $this->CadastroPefin->validationErrors;
		$this->request->data = $this->CadastroPefin->read();
		$this->CadastroPefin->validationErrors = $temp_errors;
		
		$motivoBaixas = $this->MotivoBaixa->find('list', ['order' => ['MotivoBaixa.nome']]);

		$this->set("action", $this->request->data['CadastroPefin']['nome']);
		$this->set("form_action", "../negativacao/baixa");
		$this->set(compact('statuses', 'id', 'motivoBaixas'));
	}

	public function lotes() {
		$this->Permission->check(19, "leitura") ? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => [], "or" => []];

		if(isset($_GET['q']) and $_GET['q'] != ""){
			$condition['or'] = array_merge($condition['or'], ['CadastroPefinLote.tipo LIKE' => "%".$_GET['q']."%", 'CadastroPefinLote.remessa LIKE' => "%".$_GET['q']."%"]);
		}

		if(isset($_GET['t']) and $_GET['t'] != ""){
			$condition['or'] = array_merge($condition['or'], ['Status.id' => $_GET['t']]);
		}

		$data = $this->Paginator->paginate('CadastroPefinLote', $condition);

		$status = $this->Status->find('all', array('conditions' => array('Status.categoria' => 6), 'order' => ['Status.name']));
		
		$action = "Lotes";
		$breadcrumb = ['Pefin' => '', 'Lotes' => ''];
		$this->set(compact('status', 'data', 'action', 'breadcrumb'));
	}

	public function detalhes_lote($id){
		$this->Permission->check(19, "leitura") ? "" : $this->redirect("/not_allowed");

		$condition = ["and" => ['CadastroPefin.cadastro_pefin_lote_id' => $id], "or" => []];

		if(isset($_GET['q']) and $_GET['q'] != ""){
			$condition['or'] = array_merge($condition['or'], ['Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.documento LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%", 'CadastroPefin.nome LIKE' => "%".$_GET['q']."%",'CadastroPefin.documento LIKE' => "%".$_GET['q']."%"]);
		}

		if(isset($_GET['t']) and $_GET['t'] != ""){
			$condition['or'] = array_merge($condition['or'], ['Status.id' => $_GET['t']]);
		}

		$data = $this->CadastroPefin->find('all', ['conditions' => $condition, 'recursive' => 2]);
		
		$action = "Detalhes Lote";
		$breadcrumb = ['Pefin' => '', 'Detalhes Lote' => ''];
		$this->set(compact("data", "id", "action", "breadcrumb"));
	}

	public function export_detalhes($id) {
		$this->Permission->check(19, "leitura") ? "" : $this->redirect("/not_allowed");

		$condition = ["and" => ['cadastro_pefin_lote_id' => $id], "or" => []];

		if(isset($_GET['q']) and $_GET['q'] != ""){
			$condition['or'] = array_merge($condition['or'], ['Customer.nome_secundario LIKE' => "%".$_GET['q']."%", 'Customer.documento LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%", 'CadastroPefin.nome LIKE' => "%".$_GET['q']."%",
																												'CadastroPefin.documento LIKE' => "%".$_GET['q']."%"]);
		}

		if(isset($_GET['t']) and $_GET['t'] != ""){
			$condition['or'] = array_merge($condition['or'], ['Status.id' => $_GET['t']]);
		}

		$data = $this->CadastroPefin->find('all', ['conditions' => $condition, 'recursive' => 2]);

		$nome = 'detalhe_lote_'.$id;

		$this->ExcelGenerator->gerarExcelPefinDetalhes($nome, $data);
		$this->redirect("/files/excel/".$nome.".xlsx");
	}

	public function importar_retorno(){
		$this->Permission->check(19, "escrita") ? "" : $this->redirect("/not_allowed");

		$files = array();

		$files["nome"] = false;

		if($_FILES["retorno"]["name"] != ""){
			$tam = strlen($this->base);
			$url = substr($this->base,1,$tam);

			$path = APP."webroot/files/retorno_serasa/";

			$files = $this->Uploader->up($_FILES["retorno"],$path);
		}
		
		$this->LerErroSerasa->ler($path.$files['nome']);

		$this->Flash->set(__('Retorno importado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
		$this->redirect('/negativacao/lotes');
	}

	public function gerar_txt(){
		$ids = substr($_GET['id'], 0, -1);

		$pefin = $this->CadastroPefin->find('all', ['conditions' => ['CadastroPefin.id in ('.$ids.')']]);
		
		$sequencias = $this->SequencialPefin->find('first');
		$sequencia = isset($sequencias['SequencialPefin']) ? $sequencias['SequencialPefin']['sequencia'] + 1 : 1;

		$file = $this->GerarTxt->gerar($pefin, $sequencia, $_GET['tipo']);

		$this->SequencialPefin->updateAll(['SequencialPefin.sequencia' => $sequencia]);

		$data_pefin_lote = ['CadastroPefinLote' => ['remessa' => $sequencia,
																								'data' => date('Y-m-d H:i:s'),
																								'url_txt' => $file,
																								'status_id' => 20,
																								'tipo' => $_GET['tipo']
																							 ]];

		$this->CadastroPefinLote->create();
		$this->CadastroPefinLote->save($data_pefin_lote);

		$log_lote_antigo = [];
		foreach ($pefin as $dados) {
			if ($dados['CadastroPefin']['cadastro_pefin_lote_id'] != '') {
				$log_lote_antigo[] = ['LogLoteAntigo' => ['lote_antigo_id' => $dados['CadastroPefin']['cadastro_pefin_lote_id'], 'cadastro_pefin_id' => $dados['CadastroPefin']['id'], 'lote_novo_id' => $this->CadastroPefinLote->id, 'user_created_id' => CakeSession::read("Auth.User.id")]];
			}
		}

		$this->LogLoteAntigo->saveMany($log_lote_antigo);

		$this->CadastroPefin->updateAll(
			['CadastroPefin.cadastro_pefin_lote_id' => $this->CadastroPefinLote->id, 'CadastroPefin.user_updated_id' => CakeSession::read("Auth.User.id"), 'CadastroPefin.updated' => 'now()'], //set
			['CadastroPefin.id in ('.$ids.')'] //where
		);

		$this->Flash->set(__('Lote gerado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
		$this->redirect('/negativacao/lotes/');
	}

	public function delete($id){
		$this->Permission->check(1, "excluir")? "" : $this->redirect("/not_allowed");
		$this->CadastroPefin->id = $id;

		$this->request->data['CadastroPefin']['data_cancel'] = date("Y-m-d H:i:s");
		$this->request->data['CadastroPefin']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

		if ($this->CadastroPefin->save($this->request->data)) {
			$this->Flash->set(__('O registro foi excluído com sucesso'), ['params' => ['class' => "alert alert-success"]]);
			$this->redirect($this->referer());
		}
	}

	public function download_remessa($arquivo){
		$this->autoRender = false;
		header("Content-disposition: attachment; filename=".$arquivo);
		header("Content-type: application/pdf:");

		readfile('files/pefin_txt/'.$arquivo);
	}

	public function index(){
		$this->Permission->check(54, "leitura") ? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = [
			'CadastroPefin' => [
				'limit' => 20, 
				'order' => [
					'CadastroPefin.created' => 'asc', 
					'CadastroPefin.id' => 'asc'
				],
				'fields' => [
					'CadastroPefin.*',
					'NaturezaOperacao.nome',
					'Status.*',
				],
				'recursive' => 2
			]
		];

		$condition = ["and" => ['Customer.cod_franquia' => CakeSession::read("Auth.User.resales")], "or" => []];

		if(isset($_GET['q']) and $_GET['q'] != ""){
			$condition['or'] = array_merge($condition['or'], ['CadastroPefin.nome LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado LIKE' => "%".$_GET['q']."%", 'CadastroPefin.coobrigado_nome LIKE' => "%".$_GET['q']."%", 'NaturezaOperacao.nome LIKE' => "%".$_GET['q']."%", 'CadastroPefin.documento LIKE' => "%".$_GET['q']."%", 'CadastroPefin.numero_titulo LIKE' => "%".$_GET['q']."%", 'CadastroPefin.valor LIKE' => "%".$_GET['q']."%"]);
		}

		if(isset($_GET["t"]) and $_GET["t"] != ""){
			$condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
		}

		if(isset($_GET["c"]) and $_GET["c"] != ""){
			$condition['and'] = array_merge($condition['and'], ['CadastroPefin.customer_id' => $_GET["c"]]);
		}

		$get_de = isset($_GET["de"]) ? $_GET["de"] : '';
		$get_ate = isset($_GET["ate"]) ? $_GET["ate"] : '';
		
		if($get_de != "" and $get_ate != ""){
			
			$de = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['de'])));
			$ate = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['ate'])));
			$ate = $ate.' 23:59:59';

			
			$condition['and'] = array_merge($condition['and'], ['CadastroPefin.created >=' => $de, 'CadastroPefin.created <=' => $ate]);
			
		}

		if (isset($_GET["excel"])) {
			$this->CadastroPefin->unbindModel(['belongsTo' => ['CustomerUser', 'MotivoBaixa', 'CadastroPefinLote']]);
			$dados = $this->CadastroPefin->find('all', [
				'fields' => [
					'CadastroPefin.tipo_pessoa',
					'CadastroPefin.documento',
					'CadastroPefin.nome',
					'CadastroPefin.cep',
					'CadastroPefin.endereco',
					'CadastroPefin.numero',
					'CadastroPefin.complemento',
					'CadastroPefin.bairro',
					'CadastroPefin.cidade',
					'CadastroPefin.estado',
					'CadastroPefin.data_compra',
					'CadastroPefin.nosso_numero',
					'CadastroPefin.numero_titulo',
					'CadastroPefin.venc_divida',
					'CadastroPefin.valor',
					'CadastroPefin.created',
					'Customer.nome_secundario',
					'Status.id',
					'Status.name',
					'NaturezaOperacao.nome'
				],
				'conditions' => $condition
			]);

			$nome = 'negativacao_'.date('d_m_Y');

			$this->ExcelGenerator->gerarExcelNegativacao($nome, $dados);
			$this->redirect("/files/excel/".$nome.".xlsx");
		} else {
			$data = $this->Paginator->paginate('CadastroPefin', $condition);
		}

		$action = 'TODAS AS NEGATIVAÇÕES';

		$status = $this->Status->find('all', array('conditions' => array('Status.categoria' => 7), 'order' => ['Status.name']));
		$clientes = $this->Customer->find('list', ['order' => ['Customer.nome_secundario']]);
		$valor_total = $this->CadastroPefin->find('all', ['fields' => ['sum(CadastroPefin.valor) as total'], 'recursive' => -1]);

		$valor_total_inclusao = $this->CadastroPefin->find('all', ['conditions' => ['CadastroPefin.status_id' => 25], 'fields' => ['sum(CadastroPefin.valor) as total']]);
		$valor_total_baixado = $this->CadastroPefin->find('all', ['conditions' => ['CadastroPefin.status_id' => 24], 'fields' => ['sum(CadastroPefin.valor) as total']]);
		$valor_total_decurs = $this->CadastroPefin->find('all', ['conditions' => ['CadastroPefin.status_id' => 24, 'CadastroPefin.data_compra <' => date('Y-m-d', strtotime('-59 months'))], 'fields' => ['sum(CadastroPefin.valor) as total']]);

		$this->set(compact('status', 'data', 'id', 'action', 'valor_total', 'clientes', 'valor_total_inclusao', 'valor_total_baixado', 'valor_total_decurs'));
	}
}