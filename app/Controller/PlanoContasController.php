<?php
class PlanoContasController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['PlanoConta', 'Status'];

    public $paginate = [
        'limit' => 10, 'order' => ['Status.id' => 'asc', 'PlanoConta.numero' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(42, "leitura") ? "" : $this->redirect("/not_allowed");

        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => [], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['PlanoConta.numero LIKE' => "%".$_GET['q']."%", 'PlanoConta.name LIKE' => "%".$_GET['q']."%", 'PlanoConta.referencia LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('PlanoConta', $condition);
        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $this->set(compact('status', 'data'));
    }


    public function index1($nivel)
    {
        $this->Permission->check(42, "leitura") ? "" : $this->redirect("/not_allowed");

        $condition = ["and" => ['PlanoConta.nivel' => $nivel], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['PlanoConta.numero LIKE' => "%".$_GET['q']."%", 'PlanoConta.name LIKE' => "%".$_GET['q']."%", 'PlanoConta.referencia LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('PlanoConta', $condition);

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Plano de contas';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Plano de contas' => ''];
        $pai_id = 0;
        $this->set(compact("status", 'data', 'nivel', 'action', 'breadcrumb', 'pai_id'));
    }


    public function index2($nivel, $pai_id)
    {
        $this->Permission->check(42, "leitura") ? "" : $this->redirect("/not_allowed");

        $condition = ["and" => ['PlanoConta.nivel' => $nivel, 'PlanoConta.pai_id' => $pai_id], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['PlanoConta.numero LIKE' => "%".$_GET['q']."%", 'PlanoConta.name LIKE' => "%".$_GET['q']."%", 'PlanoConta.referencia LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Status.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('PlanoConta', $condition);

        $status = $this->Status->find('all', ['conditions' => ['Status.categoria' => 1]]);

        $action = 'Plano de contas';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Plano de contas' => ''];
        $this->set(compact("status", 'data', 'nivel', 'action', 'breadcrumb', 'pai_id'));

        $this->render('index1');
    }
    
    public function add($nivel, $pai_id)
    {
        $this->Permission->check(42, "escrita") ? "" : $this->redirect("/not_allowed");

        if ($this->request->is(['post', 'put'])) {
            $this->PlanoConta->create();
            
            if ($this->PlanoConta->validates()) {
                $this->request->data['PlanoConta']['user_creator_id'] = CakeSession::read("Auth.User.id");
                $this->request->data['PlanoConta']['nivel'] = $nivel;
                $this->request->data['PlanoConta']['pai_id'] = $pai_id;
                
                $planocontas = $this->PlanoConta->find('list', ['conditions' => ['PlanoConta.numero' => $this->request->data['PlanoConta']['numero']]]);
                if (empty($planocontas)) {
                    if ($this->PlanoConta->save($this->request->data)) {
                        $this->Flash->set(__('O plano de conta foi salvo com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                        $this->redirect(['action' => 'edit/'.$this->PlanoConta->id]);
                    } else {
                        $this->Flash->set(__('O plano de conta não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                    }
                } else {
                    $this->Flash->set(__('O plano de conta não pode ser salvo, este número de identificação já existe.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O plano de conta não pode ser salvo, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $planocontasPai = $this->PlanoConta->find('list', ['conditions' => ['PlanoConta.nivel' => 1], 'order' => ['PlanoConta.numero' => 'asc']]);

        $action = 'Novo plano de contas';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Plano de contas' => '', 'Novo plano de contas' => ''];
        $this->set("action", "Novo plano de conta");
        $this->set("nivel", $nivel);
        $this->set("pai_id", $pai_id);
        $this->set("form_action", "add/".$nivel."/".$pai_id);
        $this->set(compact('statuses', 'planocontasPai', 'action', 'breadcrumb'));
    }

    public function edit($id = null)
    {
        $this->Permission->check(42, "escrita") ? "" : $this->redirect("/not_allowed");

        $this->PlanoConta->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->PlanoConta->validates();
            $this->request->data['PlanoConta']['user_updated_id'] = CakeSession::read("Auth.User.id");

            $planocontas = $this->PlanoConta->find('list', ['conditions' => ['PlanoConta.numero' => $this->request->data['PlanoConta']['numero']]]);

            if (empty($planocontas)) {
                if ($this->PlanoConta->save($this->request->data)) {
                    $this->Flash->set(__('O plano de conta foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'edit/'.$this->PlanoConta->id]);
                } else {
                    $this->Flash->set(__('O plano de conta não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('O plano de conta não pode ser salvo, este número de identificação já existe.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }
        $temp_errors = $this->PlanoConta->validationErrors;
        $this->request->data = $this->PlanoConta->read();
        $this->PlanoConta->validationErrors = $temp_errors;
        
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 1]]);
        $planocontasPai = $this->PlanoConta->find('list', ['conditions' => ['PlanoConta.nivel' => 1], 'order' => ['PlanoConta.numero' => 'asc']]);

        $action = 'Alterar plano de contas';
        $breadcrumb = ['Financeiro' => '', 'Configurações' => '', 'Plano de contas' => '', 'Alterar plano de contas' => ''];
        $this->set("action", $this->request->data['PlanoConta']['name']);
        $this->set("nivel", $this->request->data['PlanoConta']['nivel']);
        $this->set("id", $this->request->data['PlanoConta']['id']);
        $this->set("pai_id", $this->request->data['PlanoConta']['pai_id']);
        $this->set("form_action", "edit");
        $this->set(compact('statuses', 'id', 'planocontasPai', 'action', 'breadcrumb'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(42, "excluir") ? "" : $this->redirect("/not_allowed");
        
        $this->PlanoConta->id = $id;
        $this->request->data = $this->PlanoConta->read();

        $this->request->data['PlanoConta']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['PlanoConta']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->PlanoConta->save($this->request->data)) {
            $this->Flash->set(__('O plano de conta foi excluido com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect($this->referer());
        }
    }
}
