<?php
class PriceTablesController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['PriceTable', 'Status', 'ProductPrice'];

    public $paginate = [
        'PriceTable' => [
            'limit' => 10, 'order' => ['Status.id' => 'asc', 'PriceTable.descricao' => 'asc']
        ],
        'ProductPrice' => [
            'limit' => 50, 'order' => ['Product.name' => 'asc']
        ]
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(6, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['PriceTable.descricao LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('PriceTable', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Tabela de preços';
        $breadcrumb = ['Configurações' => '', 'Tabela de preços' => ''];
        $this->set(compact('status', 'data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(6, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->PriceTable->create();
            if ($this->PriceTable->validates()) {
                $this->request->data['PriceTable']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->PriceTable->save($this->request->data)) {
                    $this->Flash->set(__('O plano foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'edit/'.$this->PriceTable->id]);
                } else {
                    $this->Flash->set(__('O plano não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O plano não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Tabela de preços';
        $breadcrumb = ['Configurações' => '', 'Tabela de preços' => '', 'Nova tabela' => ''];
        $this->set(compact('statuses', 'action', 'breadcrumb'));
        $this->set("form_action", "add");
    }

    public function edit($id = null)
    {
        $this->Permission->check(6, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->PriceTable->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->PriceTable->validates();
            
            if ($this->PriceTable->save($this->request->data)) {
                $this->Flash->set(__('O plano foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            } else {
                $this->Flash->set(__('O plano não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->PriceTable->validationErrors;
        $this->request->data = $this->PriceTable->read();
        $this->PriceTable->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Tabela de preços';
        $breadcrumb = ['Configurações' => '', 'Tabela de preços' => '', 'Alterar tabela' => ''];
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(6, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->PriceTable->id = $id;
        $this->request->data = $this->PriceTable->read();

        $this->request->data['PriceTable']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['PriceTable']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->PriceTable->save($this->request->data)) {
            $this->Flash->set(__('O plano foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function products($id)
    {
        $this->Permission->check(6, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['ProductPrice.price_table_id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Product.name LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('ProductPrice', $condition);

        $action = 'Tabela de preços';
        $breadcrumb = ['Configurações' => '', 'Tabela de preços' => '', 'Produtos' => ''];
        $this->set(compact('data', 'id', 'action', 'breadcrumb'));
    }

    public function atualiza_precos($id)
    {
        if ($this->request->is(['post', 'put'])) {
            if (!empty($this->request->data['product_price_id'])) {
                foreach ($this->request->data['product_price_id'] as $key => $priceId) {
                    $this->ProductPrice->save([
                        'ProductPrice' => [
                            'id' => $priceId,
                            'value' => $this->request->data['value'][$key],
                            'user_updated_id' => CakeSession::read("Auth.User.id")
                        ]
                    ]);
                }

                $this->Flash->set(__('Preços atualizados com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            }
        }

        $this->redirect($this->referer());
    }
}
