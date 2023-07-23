<?php
class ConsumoDiarioController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'LerConsumoDiario'];
    public $uses = ['ConsumoDiario', 'ConsumoDiarioItem'];

    public $paginate = [
        'ConsumoDiario' => ['limit' => 10, 'order' => ['Status.id' => 'asc', 'ConsumoDiario.created' => 'desc']],
        'ConsumoDiarioItem' => [
            'fields' => ['count(ConsumoDiarioItem.id) as qtde', 'ConsumoDiarioItem.data', 'Customer.nome_primario', 'Customer.codigo_associado', 'Customer.id'],
            'limit' => 10, 
            'order' => ['ConsumoDiarioItem.data' => 'asc'],
            'group' => ['ConsumoDiarioItem.data', 'ConsumoDiarioItem.customer_id']
        ],
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(61, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['ConsumoDiario.arquivo LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('ConsumoDiario', $condition);

        $action = 'Consumo diário';
        $breadcrumb = ['Financeiro' => '', 'Consumo diário' => ''];
        $this->set(compact('data', 'action', 'breadcrumb'));
    }
    
    public function add()
    {
        $this->Permission->check(61, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->ConsumoDiario->create();

            $this->request->data['ConsumoDiario']['user_creator_id'] = CakeSession::read("Auth.User.id");
            if ($this->ConsumoDiario->save($this->request->data)) {
                $arquivo = APP.'webroot/files/consumo_diario/arquivo/'.$this->ConsumoDiario->id.'/'.$this->request->data['ConsumoDiario']['arquivo']['name'];
                $this->LerConsumoDiario->ler($this->ConsumoDiario->id, $arquivo);

                $this->Flash->set(__('Arquivo importado com sucesso!'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('O arquivo não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $action = 'Consumo diário';
        $breadcrumb = ['Financeiro' => '', 'Consumo diário' => ['action' => 'index'],  'Novo' => ''];
        $this->set("form_action", "add");
        $this->set(compact('action', 'breadcrumb'));
    }

    public function detalhes($id)
    {
        $this->Permission->check(61, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $this->ConsumoDiario->id = $id;
        $retorno = $this->ConsumoDiario->read();

        $condition = ["and" => ['ConsumoDiario.id' => $id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Customer.nome_primario LIKE' => "%".$_GET['q']."%", 'Customer.codigo_associado' => $_GET['q']]);
        }

        $data = $this->Paginator->paginate('ConsumoDiarioItem', $condition);

        $action = 'Consumo diário';
        $breadcrumb = ['Financeiro' => '', 'Consumo diário' => ['action' => 'index'],  'Detalhes' => ''];
        $this->set(compact('id', 'data', 'retorno', 'action', 'breadcrumb'));
    }

    public function delete($id)
    {
        $this->Permission->check(61, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->ConsumoDiario->id = $id;

        $data = ['ConsumoDiario' => ['data_cancel' => date("Y-m-d H:i:s"), 'usuario_id_cancel' => CakeSession::read("Auth.User.id")]];

        if ($this->ConsumoDiario->save($data)) {
            $this->Flash->set(__('O arquivo foi excluida com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
