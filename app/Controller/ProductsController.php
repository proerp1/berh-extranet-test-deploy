<?php
class ProductsController extends AppController {
	public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Product', 'Status', 'Feature', 'Answer', 'AnswerItem', 'ItemOption', 'PriceTable', 'ProductPrice', 'NovaVidaFeature', 'ProductFeature'];

    public $paginate = [
        'Product' => ['limit' => 10, 'order' => ['Product.name' => 'asc']],
        'Feature' => ['limit' => 10, 'order' => ['Feature.name' => 'asc']],
        'Answer' => ['limit' => 10, 'order' => ['Answer.name' => 'asc']],
        'ItemOption' => ['limit' => 10, 'order' => ['ItemOption.name' => 'asc']]
    ];

/*******************
			PRODUTOS			
********************/
	public function index() {
		$this->Permission->check(5, "leitura")? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => [], "or" => []];

		if(!empty($_GET['q'])){
			$condition['or'] = array_merge($condition['or'], ['Product.name LIKE' => "%".$_GET['q']."%"]);
		}

		if(!empty($_GET["tipo"])){
			$condition['and'] = array_merge($condition['and'], ['Product.tipo' => $_GET['tipo']]);
		}

		if(!empty($_GET["t"])){
			$condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
		}

		$data = $this->Paginator->paginate('Product', $condition);
		$status = $this->Status->find('all', array('conditions' => array('Status.categoria' => 1)));    

		$action = 'Produtos';
        $breadcrumb = ['Cadastros' => '', 'Produtos' => ''];
		$this->set(compact('status', 'data', 'action', 'breadcrumb'));
	}

	public function add() {
		$this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");
		if ($this->request->is(['post', 'put'])) {
			$this->Product->create();
			
			if($this->Product->validates()){
				$this->request->data['Product']['user_creator_id'] = CakeSession::read("Auth.User.id");
				if ($this->Product->save($this->request->data)) {
					$this->Session->setFlash(__('O produto foi salvo com sucesso'), 'default', array('class' => "alert alert-success"));
					$this->redirect(array('action' => 'edit/'.$this->Product->id));
				} else {
					$this->Session->setFlash(__('O produto não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
				}
			} else {
				$this->Session->setFlash(__('O produto não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			}
		}
		
		$statuses = $this->Status->find('list', array('conditions' => array('Status.categoria' => 1)));    

		$action = 'Produtos';
        $breadcrumb = ['Cadastros' => '', 'Produtos' => '', 'Novo produto' => ''];
		$this->set("form_action", "add");
		$this->set(compact('statuses', 'action', 'breadcrumb'));
	}

	public function edit($id = null) {
		$this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");
		$this->Product->id = $id;
		if ($this->request->is(['post', 'put'])) {
			if ($this->Product->save($this->request->data)) {
				$this->Session->setFlash(__('O produto foi alterado com sucesso'), 'default', array('class' => "alert alert-success"));
			} else {
				$this->Session->setFlash(__('O produto não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			} 
		}
		
		$this->request->data = $this->Product->read();
		$prices = $this->ProductPrice->find('all', ['conditions' => ['ProductPrice.product_id' => $id]]);

		$ids = '';
		foreach ($prices as $key => $value) {
			$ids .= $value['PriceTable']['id'].',';
		}
		$ids = substr($ids, 0, -1);
		//verifica se tem alguma tabela de preço ja cadastrada no produto, se tiver nao aparece no combo para cadastrar
		if ($ids != '') {
			$priceTables = $this->PriceTable->find('list', ['conditions' => ['PriceTable.status_id' => 1, ['not' => ['PriceTable.id in ('.$ids.')']]]]);
		} else {
			$priceTables = $this->PriceTable->find('list', ['conditions' => ['PriceTable.status_id' => 1]]);
		}

		$statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

		$action = 'Produtos';
        $breadcrumb = ['Cadastros' => '', 'Produtos' => '', 'Alterar produto' => ''];
		$this->set("form_action", "edit");
		$this->set("form_action_prices", "../products/add_price");
		$this->set(compact('statuses', 'priceTables', 'id', 'prices', 'action', 'breadcrumb'));
		
		$this->render("add");
	}

	public function delete($id){
		$this->Permission->check(5, "excluir") ? "" : $this->redirect("/not_allowed");
		$this->Product->id = $id;
		$this->request->data = $this->Product->read();

		$this->request->data['Product']['data_cancel'] = date("Y-m-d H:i:s");
		$this->request->data['Product']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

		if ($this->Product->save($this->request->data)) {
			$this->Session->setFlash(__('O produto foi excluido com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect(array('action' => 'index'));
		}
	}

	public function add_price() {
		if ($this->request->is(['post', 'put'])) {
			$this->ProductPrice->create();
			$this->request->data['ProductPrice']['user_creator_id'] = CakeSession::read("Auth.User.id");
			if ($this->ProductPrice->save($this->request->data)) {
				$this->Session->setFlash(__('O preço foi adicionado com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect(array('action' => 'edit/'.$this->request->data['ProductPrice']['product_id']));
			} else {
				$this->Session->setFlash(__('O preço não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			} 
		}
	}

	public function delete_price($product_id, $id){
		$this->Permission->check(5, "excluir") ? "" : $this->redirect("/not_allowed");
		$this->ProductPrice->id = $id;
		$this->request->data = $this->ProductPrice->read();

		$this->request->data['ProductPrice']['data_cancel'] = date("Y-m-d H:i:s");
		$this->request->data['ProductPrice']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

		if ($this->ProductPrice->save($this->request->data)) {
			$this->Session->setFlash(__('Excluido com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect(array('action' => 'edit/'.$product_id));
		}
	}

/*******************
			FEATURES			
********************/
	public function features($id) {
		$this->Permission->check(5, "leitura")? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		if ($this->request->is(['post', 'put'])) {
			$this->ProductFeature->create();
			$this->ProductFeature->validates();
			
			$this->request->data['ProductFeature']['user_creator_id'] = CakeSession::read("Auth.User.id");
			if ($this->ProductFeature->save($this->request->data)) {
				$this->Session->setFlash(__('A feature foi salva com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect("/products/features/".$id);
			} else {
				$this->Session->setFlash(__('A feature não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			}
		}
		
		$features_cadastradas = $this->ProductFeature->find('all', ['conditions' => ['ProductFeature.product_id' => $id], 'order' => ['NovaVidaFeature.name' => 'asc']]);

		$ids = '';
		foreach ($features_cadastradas as $value) {
			$ids .= $value['NovaVidaFeature']['id'].',';
		}
		$ids = substr($ids, 0, -1);

		$features = $this->NovaVidaFeature->find('all', ['order' => ['name' => 'asc'], 'conditions' => ['NovaVidaFeature.id not in ('.($ids != '' ? $ids : 0).')']]);
		foreach ($features as $feature) {
			switch ($feature["NovaVidaFeature"]["campo_pesquisa"]) {
				case '1':
					$tipo = "PF";
					break;
				case '2':
					$tipo = "PJ";
					break;
				case '3':
					$tipo = "Ambos";
					break;
				case '4':
					$tipo = "Núm benefício";
					break;
				case '5':
					$tipo = "Atributos";
					break;
				default:
					$tipo = '';
					break;
			}

			$novaVidaFeatures[$feature['NovaVidaFeature']['id']] = $feature['NovaVidaFeature']['name'].' - '.$tipo;
		}

		$this->Product->id = $id;
		$produtos = $this->Product->read();

		$action = $produtos['Product']['name'].' - Features';
		$form_action = '../products/features/'.$id;
		$this->set(compact('novaVidaFeatures', 'action', 'id', 'form_action', 'features_cadastradas', 'produtos'));
	}

	public function delete_feature($id, $feature_id){
		$this->Permission->check(5, "excluir") ? "" : $this->redirect("/not_allowed");

		$this->ProductFeature->id = $feature_id;

		$data = ['ProductFeature' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

		if ($this->ProductFeature->save($data)) {
			$this->Session->setFlash(__('A feature foi excluida com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect("/products/features/".$id);
		}
	}

	public function features_string($id){
		$this->Permission->check(5, "leitura")? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$this->Product->id = $id;
		$product = $this->Product->read();
		
		$condition = ["and" => ['Feature.product_id' => $id], "or" => []];

		if(isset($_GET['q']) and $_GET['q'] != ""){
			$condition['and'] = array_merge($condition['and'], ['Feature.name LIKE' => "%".$_GET['q']."%"]);
		}
		if(isset($_GET['t']) and $_GET['t'] != ""){
			$condition['and'] = array_merge($condition['and'], ['Feature.status_id' => $_GET['t'] ]);
		}

		$status  = $this->Status->find("all", ["conditions" => ["Status.categoria" => 1] ]);

		$dados = $this->Paginator->paginate("Feature", $condition);

		$action = $product['Product']['name']." - Features";

		$this->set(compact('action', 'status', 'dados', 'id', 'product'));
	}

	public function edit_feature_string($id, $featureID){
		$this->Permission->check(5, "escrita")? "" : $this->redirect("/not_allowed");
		$this->Feature->id = $featureID;
		if ($this->request->is(['post', 'put'])) {
			if ($this->Feature->save($this->request->data)) {
				$this->Session->setFlash(__('A feature foi salva com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash(__('A feature não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$temp_errors = $this->Feature->validationErrors;
		$this->request->data = $this->Feature->read();
		$this->Feature->validationErrors = $temp_errors;

		$this->Product->id = $id;
		$product = $this->Product->read();
		$form_action = "../products/edit_feature_string/".$id;
		$statuses = $this->Status->find("list", ["conditions" => ["Status.categoria" => 1] ]);

		$action = $product['Product']['name']." - Features";

		$this->set(compact("id", "form_action", "statuses", 'product', 'action'));

		$this->render("add_feature_string");
	}

/**************************
		FEATURES 		BACKUP
**************************/
	/*public function features($id) {
		$this->Permission->check(5, "leitura")? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => ['Feature.product_id' => $id], "or" => []];

		if(!empty($_GET['q'])){
			$condition['or'] = array_merge($condition['or'], ['Feature.name LIKE' => "%".$_GET['q']."%"]);
		}

		if(!empty($_GET["t"])){
			$condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
		}

		// similar to findAll(), but fetches paged results
		$data = $this->Paginator->paginate('Feature', $condition);
		$status = $this->Status->find('all', array('conditions' => array('Status.categoria' => 1)));    

		$this->set('status', $status);
		$this->set('data', $data);
		$this->set('id', $id);
	}

	public function add_feature($id) {
		$this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");

		if ($this->request->is(['post', 'put'])) {
			$this->Feature->create();
			
			if($this->Feature->validates()){        
				$this->request->data['Feature']['user_creator_id'] = CakeSession::read("Auth.User.id");
				if ($this->Feature->save($this->request->data)) {
					$this->Session->setFlash(__('A feature foi salva com sucesso'), 'default', array('class' => "alert alert-success"));
					$this->redirect("/products/features/".$this->request->data['Feature']['product_id']);
				} else {
					$this->Session->setFlash(__('A feature não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
				}
			} else {
				$this->Session->setFlash(__('A feature não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$statuses = $this->Status->find('list', array('conditions' => array('Status.categoria' => 1)));    

		$this->set('statuses', $statuses);
		$this->set("action", "Nova Feature");
		$this->set("form_action", "../products/add_feature");
		$this->set("id", $id);
	}

	public function edit_feature($id, $feature_id = null) {
		$this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");

		$this->Feature->id = $feature_id;
		
		if ($this->request->is(['post', 'put'])) {
			if ($this->Feature->save($this->request->data)) {
				$this->Session->setFlash(__('A feature foi alterada com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect("/products/features/".$id);
			} else {
				$this->Session->setFlash(__('A feature não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			} 
		}

		$this->request->data = $this->Feature->read();
		$statuses = $this->Status->find('list', array('conditions' => array('Status.categoria' => 1)));    

		$this->set('statuses', $statuses);
		$this->set("action", $this->request->data['Feature']['name']);
		$this->set("form_action", "../products/edit_feature");
		$this->set("id", $id);
		$this->set("feature_id", $feature_id);
		
		$this->render("add_feature");
	}

	public function delete_feature($id, $feature_id){
		$this->Permission->check(5, "excluir") ? "" : $this->redirect("/not_allowed");

		$this->Feature->id = $feature_id;
		$this->request->data = $this->Feature->read();

		$this->request->data['Feature']['data_cancel'] = date("Y-m-d H:i:s");
		$this->request->data['Feature']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

		if ($this->Feature->save($this->request->data)) {
			$this->Session->setFlash(__('A feature foi excluida com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect("/products/features/".$id);
		}
	}*/

/*******************
			RESPOSTAS			
********************/
	public function answer($id) {
		$this->Permission->check(5, "leitura")? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => ['Answer.product_id' => $id], "or" => []];

		if(!empty($_GET['q'])){
			$condition['or'] = array_merge($condition['or'], ['Answer.name LIKE' => "%".$_GET['q']."%"]);
		}

		if(!empty($_GET["t"])){
			$condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
		}

		// similar to findAll(), but fetches paged results
		$data = $this->Paginator->paginate('Answer', $condition);

		$this->set('data', $data);
		$this->set('id', $id);
	}

	public function add_answer($id) {
		$this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");

		if ($this->request->is(['post', 'put'])) {
			$this->Answer->create();
			
			if($this->Answer->validates()){        
				$this->request->data['Answer']['user_creator_id'] = CakeSession::read("Auth.User.id");
				if ($this->Answer->save($this->request->data)) {
					$this->Session->setFlash(__('A resposta foi salva com sucesso'), 'default', array('class' => "alert alert-success"));
					$this->redirect("/products/answer/".$this->request->data['Answer']['product_id']);
				} else {
					$this->Session->setFlash(__('A resposta não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
				}
			} else {
				$this->Session->setFlash(__('A resposta não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$this->set("action", "Nova Resposta");
		$this->set("form_action", "../products/add_answer");
		$this->set("id", $id);
	}

	public function edit_answer($id, $answer_id = null) {
		$this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");

		$this->Answer->id = $answer_id;
		
		if ($this->request->is(['post', 'put'])) {
			if ($this->Answer->save($this->request->data)) {
				$this->Session->setFlash(__('A resposta foi alterada com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect("/products/answer/".$id);
			} else {
				$this->Session->setFlash(__('A resposta não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			} 
		}

		$this->request->data = $this->Answer->read();
		$resp_pai = $this->Answer->find('all', array('conditions' => array('Answer.product_id' => $id, 'Answer.id !=' => $answer_id)));    
		$data_lista = $this->Answer->find('all', array('conditions' => array('Answer.pai_id' => $answer_id)));    

		$this->set('data_lista', $data_lista);
		$this->set('resp_pai', $resp_pai);
		$this->set("action", $this->request->data['Answer']['name']);
		$this->set("form_action", "../products/edit_answer");
		$this->set("id", $id);
		$this->set("answer_id", $answer_id);
		
		$this->render("add_answer");
	}

	public function delete_answer($id, $answer_id){
		$this->Permission->check(5, "excluir") ? "" : $this->redirect("/not_allowed");

		$this->Answer->id = $answer_id;
		$this->request->data = $this->Answer->read();

		$this->request->data['Answer']['data_cancel'] = date("Y-m-d H:i:s");
		$this->request->data['Answer']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

		if ($this->Answer->save($this->request->data)) {
			$this->Session->setFlash(__('A resposta foi excluida com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect("/products/answer/".$id);
		}
	}

/**************************
			ITENS RESPOSTAS 			
**************************/
	public function answer_item($id, $answer_id) {
		$this->Permission->check(5, "leitura")? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => ['AnswerItem.answer_id' => $answer_id], "or" => []];

		if(!empty($_GET['q'])){
			$condition['or'] = array_merge($condition['or'], ['AnswerItem.name LIKE' => "%".$_GET['q']."%"]);
		}

		if(!empty($_GET["t"])){
			$condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
		}

		// similar to findAll(), but fetches paged results
		$data = $this->Paginator->paginate('AnswerItem', $condition);

		$this->set('data', $data);
		$this->set('answer_id', $answer_id);
		$this->set('id', $id);
	}

	public function add_answer_item($id) {
		$this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");

		if ($this->request->is(['post', 'put'])) {
			$this->AnswerItem->create();
			
			if($this->AnswerItem->validates()){        
				$this->request->data['AnswerItem']['user_creator_id'] = CakeSession::read("Auth.User.id");
				if ($this->AnswerItem->save($this->request->data)) {
					$this->Session->setFlash(__('O item da resposta foi salvo com sucesso'), 'default', array('class' => "alert alert-success"));
					$this->redirect("/products/answer_item/".$this->request->data['AnswerItem']['product_id']);
				} else {
					$this->Session->setFlash(__('O item da resposta não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
				}
			} else {
				$this->Session->setFlash(__('O item da resposta não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$this->set("action", "Nova Resposta");
		$this->set("form_action", "../products/add_answer_item");
		$this->set("id", $id);
	}

	public function edit_answer_item($id, $answer_id, $answer_item_id = null) {
		$this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");

		$this->AnswerItem->id = $answer_item_id;
		
		if ($this->request->is(['post', 'put'])) {
			if(trim($this->request->data['AnswerItem']['itemNome2']) != null ){
				$str = $this->request->data['AnswerItem']['name'].'§'.$this->request->data['AnswerItem']['itemNome2'];
			} else {
				$str = $this->request->data['AnswerItem']['name'];
			}
			$this->request->data['AnswerItem']['name'] = $str;

			if ($this->AnswerItem->save($this->request->data)) {
				$this->Session->setFlash(__('O item da resposta foi alterado com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect("/products/answer_item/".$id."/".$answer_id);
			} else {
				$this->Session->setFlash(__('O item da resposta não pode ser salvo, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			} 
		}

		$this->request->data = $this->AnswerItem->read();  

		$this->set("action", $this->request->data['AnswerItem']['name']);
		$this->set("form_action", "../products/edit_answer_item");
		$this->set("id", $id);
		$this->set("answer_id", $answer_id);
		
		$this->render("add_answer_item");
	}

	public function delete_answer_item($id, $answer_id){
		$this->Permission->check(5, "excluir") ? "" : $this->redirect("/not_allowed");

		$this->AnswerItem->id = $answer_id;
		$this->request->data = $this->AnswerItem->read();

		$this->request->data['AnswerItem']['data_cancel'] = date("Y-m-d H:i:s");
		$this->request->data['AnswerItem']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

		if ($this->AnswerItem->save($this->request->data)) {
			$this->Session->setFlash(__('O item da resposta foi excluido com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect("/products/answer_item/".$id);
		}
	}

/*****************
			OPÇÕES			
******************/
	public function option($id, $answer_id, $answer_item_id) {
		$this->Permission->check(5, "leitura")? "" : $this->redirect("/not_allowed");
		$this->Paginator->settings = $this->paginate;

		$condition = ["and" => ['ItemOption.answer_item_id' => $answer_item_id], "or" => []];

		if(!empty($_GET['q'])){
			$condition['or'] = array_merge($condition['or'], ['ItemOption.name LIKE' => "%".$_GET['q']."%"]);
		}

		if(!empty($_GET["t"])){
			$condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
		}

		// similar to findAll(), but fetches paged results
		$data = $this->Paginator->paginate('ItemOption', $condition);

		$this->set('data', $data);
		$this->set('answer_item_id', $answer_item_id);
		$this->set('answer_id', $answer_id);
		$this->set('id', $id);
	}

	public function add_option($id, $answer_id, $answer_item_id) {
		$this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");

		if ($this->request->is(['post', 'put'])) {
			$this->ItemOption->create();
			
			if($this->ItemOption->validates()){        
				$this->request->data['ItemOption']['user_creator_id'] = CakeSession::read("Auth.User.id");
				if ($this->ItemOption->save($this->request->data)) {
					$this->Session->setFlash(__('A opção foi salva com sucesso'), 'default', array('class' => "alert alert-success"));
					$this->redirect("/products/option/".$id."/".$answer_id."/".$answer_item_id);
				} else {
					$this->Session->setFlash(__('A opção não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
				}
			} else {
				$this->Session->setFlash(__('A opção não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			}
		}

		$this->set("action", "Nova Opção");
		$this->set("form_action", "../products/add_option");
		$this->set("id", $id);
		$this->set("answer_id", $answer_id);
		$this->set("answer_item_id", $answer_item_id);
	}

	public function edit_option($id, $answer_id, $answer_item_id, $option_id = null) {
		$this->Permission->check(5, "escrita") ? "" : $this->redirect("/not_allowed");

		$this->ItemOption->id = $option_id;
		
		if ($this->request->is(['post', 'put'])) {
			if ($this->ItemOption->save($this->request->data)) {
				$this->Session->setFlash(__('A opção foi alterada com sucesso'), 'default', array('class' => "alert alert-success"));
				$this->redirect("/products/option/".$id."/".$answer_id."/".$answer_item_id);
			} else {
				$this->Session->setFlash(__('A opção não pode ser salva, por favor tente novamente.'), 'default', array('class' => "alert alert-danger"));
			} 
		}

		$this->request->data = $this->ItemOption->read();  

		$this->set("action", $this->request->data['ItemOption']['name']);
		$this->set("form_action", "../products/edit_option");
		$this->set("id", $id);
		$this->set("answer_id", $answer_id);
		$this->set("answer_item_id", $answer_item_id);
		
		$this->render("add_option");
	}

	public function delete_option($id, $answer_id, $answer_item_id, $option_id){
		$this->Permission->check(5, "excluir") ? "" : $this->redirect("/not_allowed");

		$this->ItemOption->id = $option_id;
		$this->request->data = $this->ItemOption->read();

		$this->request->data['ItemOption']['data_cancel'] = date("Y-m-d H:i:s");
		$this->request->data['ItemOption']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

		if ($this->ItemOption->save($this->request->data)) {
			$this->Session->setFlash(__('A opção foi excluida com sucesso'), 'default', array('class' => "alert alert-success"));
			$this->redirect("/products/option/".$id."/".$answer_id."/".$answer_item_id);
		}
	}
}
